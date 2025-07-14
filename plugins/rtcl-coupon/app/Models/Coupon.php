<?php
/**
 * Main initialization class.
 *
 * @package RadiusTheme\COUPON
 */

namespace RadiusTheme\COUPON\Models;

use RadiusTheme\COUPON\Helpers\Fns;

/**
 * Main initialization Models Coupon.
 */
class Coupon extends CouponDB {
	/**
	 * Coupon id
	 *
	 * @var integer $id
	 */
	protected $id;
	/**
	 * Coupon Code
	 *
	 * @var String $coupon_code pricing.
	 */
	protected $coupon_code;
	/**
	 * Coupon for
	 *
	 * @var integer $coupon_for pricing.
	 */
	protected $coupon_for;
	/**
	 * Coupon Object
	 *
	 * @var object
	 */
	protected $coupon;
	/**
	 * Coupon Meta
	 *
	 * @var array
	 */
	protected $coupon_meta;
	/**
	 * Coupon validation
	 *
	 * @var boolean $is_valid
	 */
	protected $is_valid = false;
	/**
	 * Coupon Related Message
	 *
	 * @var array $messages
	 */
	protected $errors = [];

	/**
	 * Class Construct
	 *
	 * @param string  $coupon_code Coupon code.
	 * @param integer $coupon_for  Pricing Id.
	 */
	public function __construct( $coupon_code, $coupon_for = 0 ) {
		parent::__construct();
		$this->coupon_for  = $coupon_for;
		$this->coupon_code = $coupon_code;
		$this->coupon_init();
	}

	/**
	 * Set ID.
	 *
	 * @param int $id ID.
	 *
	 * @since 1.0.0
	 */
	private function set_id( $id ) {
		$this->id = absint( $id );
	}

	/**
	 * Get ID.
	 *
	 * @since 1.0.0
	 */
	public function get_id() {
		return absint( $this->id );
	}

	/**
	 * Get Coupon.
	 *
	 * @since 1.0.0
	 */
	private function coupon_init() {
		$coupon = get_page_by_title( $this->coupon_code, OBJECT, rtcl_coupon()->post_type_coupon );
		if ( is_object( $coupon ) ) {
			$this->coupon = $coupon;
			$this->set_id( $coupon->ID );
			$this->coupon_meta = $this->coupon_data();
		}
		$this->set_errors();
	}

	/**
	 * Undocumented function
	 *
	 * @return array
	 */
	private function coupon_data() {
		$coupon_id        = $this->get_id();
		$coupon_cache_key = 'rtcl_coupon_meta_' . $coupon_id;
		$coupon_data      = wp_cache_get( $coupon_cache_key, 'coupon-details' );
		if ( false === $coupon_data ) {
			$coupon_data = $this->db->get_results(
				$this->db->prepare(
					"SELECT * FROM `{$this->coupon_table}` WHERE `coupon_id` = %d ORDER BY `coupon_id` DESC LIMIT 1",
					$coupon_id
				),
				ARRAY_A
			);
			wp_cache_set( $coupon_cache_key, $coupon_data, 'coupon-details', 12 * HOUR_IN_SECONDS );
		}

		return $coupon_data;
	}

	public function get_coupon_data() {
		return [
			'coupon_code' => $this->coupon_code,
			'original'    => $this->pricing_amount(),
			'discount'    => $this->coupon_amount(),
			'subtotal'    => $this->total_amount(),
		];
	}

	/**
	 * Get ID.
	 *
	 * @since 1.0.0
	 */
	public function get_coupon_meta() {
		return ! empty( $this->coupon_meta[0] ) ? $this->coupon_meta[0] : [];
	}

	/**
	 * Return Formated number
	 *
	 * @param float $number Number.
	 *
	 * @return float
	 */
	private function formated_number( $number ) {
		return number_format( floatval( $number ), 2, '.', '' );
	}

	/**
	 * Set Session.
	 *
	 * @since 1.0.0
	 */
	private function set_session() {
		rtcl()->session->set( 'rtcl_applied_coupon', $this->coupon_code );
		$total = [
			'subtotal'       => $this->pricing_amount(),
			'discount_total' => $this->coupon_amount(),
			'total'          => $this->total_amount(),
		];
		rtcl()->session->set( 'rtcl_checkout_totals', $total );
	}

	/**
	 * Set message.
	 *
	 * @since 1.0.0
	 */
	private function set_errors() {
		if ( ! $this->coupon || 'publish' !== $this->coupon->post_status ) {
			$this->errors['invalid_coupon'] = esc_html__( 'Invalid coupon ', 'rtcl-coupon' );
		}
		if ( $this->is_expired() ) {
			$this->errors['expired'] = esc_html__( 'Coupon expired', 'rtcl-coupon' );
		}
		if ( ! $this->is_pricing_allowed_coupon() ) {
			$this->errors['invalid_coupon_given_plan'] = esc_html__( 'Coupon is not valid for this pricing', 'rtcl-coupon' );
		}
		if ( ! $this->has_limit() ) {
			$this->errors['coupon_limit_over'] = esc_html__( 'Coupon uses limit over', 'rtcl-coupon' );
		}
		if ( ! $this->has_limit_per_user() ) {
			$this->errors['coupon_user_limit_over'] = esc_html__( 'Coupon uses limit over for you', 'rtcl-coupon' );
		}
	}

	/**
	 * Get all messages.
	 *
	 * @return array
	 */
	public function get_errors() {
		return $this->errors;
	}

	/**
	 * Coupon validity check.
	 *
	 * @return boolean
	 */
	public function is_valid() {
		if ( empty( $this->get_errors() ) ) {
			$this->is_valid = true;
			$this->set_session();
		} else {
			Fns::reset_coupons_session();
		}

		return $this->is_valid;
	}

	/**
	 * Coupon expiration check.
	 *
	 * @return boolean
	 */
	public function has_limit_per_user() {
		$has_limit   = true;
		$coupon_meta = $this->get_coupon_meta();
		$uses_coupon = intval( $this->get_current_user_applied_count() );
		$usage_limit = ! empty( $coupon_meta['per_user_limit'] ) ? intval( $coupon_meta['per_user_limit'] ) : 0;
		if ( $usage_limit && $usage_limit <= $uses_coupon ) {
			$has_limit = false;
		}
		return $has_limit;
	}

	/**
	 * Get ID.
	 *
	 * @since 1.0.0
	 */
	public function get_current_user_applied_count() {
		$current_user = get_current_user_id();
		$lookup_count = $this->db->get_var( "SELECT COUNT(*) FROM {$this->lookup_table} WHERE `coupon_id` = {$this->get_id()} AND `user_id` = {$current_user} " );
		return $lookup_count;
	}

	/**
	 * Coupon expiration check.
	 *
	 * @return boolean
	 */
	public function has_limit() {
		$has_limit   = true;
		$coupon_meta = $this->get_coupon_meta();
		$count       = ! empty( $coupon_meta['usage_count'] ) ? absint( $coupon_meta['usage_count'] ) : 0;
		$usage_limit = ! empty( $coupon_meta['usage_limit'] ) ? absint( $coupon_meta['usage_limit'] ) : 0;

		if ( $usage_limit && $count >= $usage_limit ) {
			$has_limit = false;
		}

		return $has_limit;
	}

	/**
	 * Coupon expiration check.
	 *
	 * @return boolean
	 */
	public function is_expired() {
		$expired     = false;
		$coupon_meta = $this->get_coupon_meta();
		$expiry      = ! empty( $coupon_meta['expire_date'] ) ? $coupon_meta['expire_date'] : false;
		if ( $expiry && strtotime( 'today' ) > $expiry ) {
			$expired = true;
		}

		return $expired;
	}

	/**
	 * Coupon validation check.
	 *
	 * @return boolean
	 */
	public function is_pricing_allowed_coupon() {
		$allowed              = true;
		$coupon_meta          = $this->get_coupon_meta();
		$allowed_pricing_type = ! empty( $coupon_meta['pricing_type'] ) ? $coupon_meta['pricing_type'] : false;
		if ( $this->coupon_for ) {
			$pricing_type = get_post_meta( $this->coupon_for, 'pricing_type', true );
			if ( ! $pricing_type ) {
				$pricing_type = 'regular';
			}
			if ( $allowed_pricing_type && $allowed_pricing_type !== $pricing_type ) {
				$allowed = false;
			}
			if ( $allowed ) {
				$pricing_allowed = ! empty( $coupon_meta['include_pricing'] ) ? maybe_unserialize( $coupon_meta['include_pricing'] ) : [];
				$pricing_exclude = ! empty( $coupon_meta['exclude_pricing'] ) ? maybe_unserialize( $coupon_meta['exclude_pricing'] ) : [];
				if ( ( ! empty( $pricing_allowed ) && ! in_array( $this->coupon_for, $pricing_allowed ) ) ||
				     ( ! empty( $pricing_exclude ) && in_array( $this->coupon_for, $pricing_exclude ) )
				) {
					$allowed = false;
				}
			}
		}

		return $allowed;
	}

	/**
	 * Coupon Type.
	 *
	 * @return string
	 */
	public function coupon_type() {
		$coupon_meta   = $this->get_coupon_meta();
		$discount_type = ! empty( $coupon_meta['discount_type'] ) ? $coupon_meta['discount_type'] : false;

		return $discount_type;
	}

	/**
	 * Coupon Amount.
	 *
	 * @return float
	 */
	public function coupon_amount() {
		$coupon_meta    = $this->get_coupon_meta();
		$coupon_amount  = ! empty( $coupon_meta['discount_amount'] ) ? absint( $coupon_meta['discount_amount'] ) : 0;
		$pricing_amount = $this->pricing_amount();
		if ( 'percent_discount' === $this->coupon_type() ) {
			$coupon_amount = ( $pricing_amount * $coupon_amount ) / 100;
		}
		if ( $pricing_amount < $coupon_amount ) {
			$coupon_amount = $pricing_amount;
		}

		return $this->formated_number( $coupon_amount );
	}

	/**
	 * Pricing Amount.
	 *
	 * @return float
	 */
	public function pricing_amount() {
		if ( $this->coupon_for ) {
			$price = get_post_meta( $this->coupon_for, 'price', true );

			return $this->formated_number( $price );
		}

		return null;
	}

	/**
	 * Total Amount.
	 *
	 * @return float
	 */
	public function total_amount() {
		$total_amount = $this->pricing_amount() - $this->coupon_amount();

		return $this->formated_number( $total_amount );
	}

}
