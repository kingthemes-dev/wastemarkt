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
class Couponlookup extends CouponDB {
	/**
	 * Coupon Meta
	 *
	 * @var array
	 */
	protected $order_id;
	/**
	 * Coupon Meta
	 *
	 * @var array
	 */
	protected $lookup_data;
	/**
	 * Class Construct
	 *
	 * @param integer $order_id Pricing Id.
	 */
	public function __construct( $order_id ) {
		parent::__construct();
		$this->order_id    = $order_id;
		$this->lookup_data = $this->coupon_applied_data();
	}
	/**
	 * Undocumented function
	 *
	 * @return array
	 */
	private function coupon_applied_data() {
		$lookup_cache_key = 'rtcl_coupon_lookup_' . $this->order_id;
		$lookup_data      = wp_cache_get( $lookup_cache_key, 'coupon-lookup' );
		if ( false === $lookup_data ) {
			$lookup_data = $this->db->get_results( // phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery
				$this->db->prepare(
					"SELECT * FROM `{$this->lookup_table}` WHERE `order_id` = %d ORDER BY `order_id` DESC LIMIT 1",
					$this->order_id
				),
				ARRAY_A
			);
			wp_cache_set( $lookup_cache_key, $lookup_data, 'coupon-lookup', 12 * HOUR_IN_SECONDS );
		}
		return $lookup_data;
	}

	/**
	 * Get ID.
	 *
	 * @since 1.0.0
	 */
	public function get_applied_data() {
		return ! empty( $this->lookup_data[0] ) ? $this->lookup_data[0] : [];
	}
}
