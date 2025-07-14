<?php
/**
 * Main Scripts Class
 *
 * The main class that initiates all scripts.
 *
 * @package RadiusTheme\COUPON
 * @since    1.0.0
 */

namespace RadiusTheme\COUPON\Hooks;

use Rtcl\Helpers\Functions;
use RadiusTheme\COUPON\Models\Couponlookup;

/**
 * FilterHooks Class
 */
class FilterHooks {
	/**
	 * Initialize Function
	 *
	 * @return void
	 */
	public static function init() {
		add_filter( 'rtcl_checkout_process_new_order_args', [ __CLASS__, 'checkout_process_with_coupon' ], 15, 1 );
		add_filter( 'manage_rtcl_coupon_posts_columns', [ __CLASS__, 'set_custom_edit_coupon_columns' ] );
		add_filter( 'rtcl_payment_table_total_amount', [ __CLASS__, 'rtcl_payment_table_total_amount' ], 10, 2 );
		add_filter( 'rtcl_licenses', [ __CLASS__, 'license' ], 15 );

	}
	/**
	 * License Field
	 *
	 * @param [array] $licenses settings object.
	 * @return array
	 */
	public static function license( $licenses ) {
		$licenses[] = [
			'plugin_file' => RTCL_COUPON_FILE_NAME,
			'api_data'    => [
				'key_name'    => 'license_rtclcoupon_key',
				'status_name' => 'license_rtclcoupon_status',
				'action_name' => 'rtclcoupon_manage_licensing',
				'product_id'  => 190642, // Original Number.
				'version'     => RTCL_COUPON_VERSION,
			],
			'settings'    => [
				'title' => esc_html__( 'Coupon plugin license key', 'rtcl-coupon' ),
			],
		];
		return $licenses;
	}
	/**
	 * Undocument Function
	 *
	 * @param array $new_payment_args payment args.
	 * @return array
	 */
	public static function checkout_process_with_coupon( $new_payment_args ) {
		$checkout_totals = rtcl()->session->get( 'rtcl_checkout_totals', [] );
		if ( ! empty( $checkout_totals['total'] ) ) {
			$new_payment_args['meta_input']['amount'] = $checkout_totals['total'];
		}
		return $new_payment_args;
	}
	/**
	 * New column.
	 *
	 * @param array $columns Column name.
	 * @return array
	 */
	public static function set_custom_edit_coupon_columns( $columns ) {
		$date = $columns['date'];
		unset( $columns['date'] );
		$columns['title']               = esc_html__( 'Coupon', 'rtcl-coupon' );
		$columns['coupon_pricing_type'] = esc_html__( 'Pricing Type', 'rtcl-coupon' );
		$columns['coupon_type']         = esc_html__( 'Coupon Type', 'rtcl-coupon' );
		$columns['coupon_discount']     = esc_html__( 'Discount', 'rtcl-coupon' );
		$columns['coupon_uses']         = esc_html__( 'Applied', 'rtcl-coupon' );
		$columns['coupon_expiry_date']  = esc_html__( 'Expiry Date', 'rtcl-coupon' );
		$columns['date']                = $date;
		return $columns;
	}
	/**
	 * Payment Table
	 *
	 * @param [type] $main_amount_html total amount paid.
	 * @param [type] $order object.
	 * @return string
	 */
	public static function rtcl_payment_table_total_amount( $main_amount_html, $order ) {
		$coupon         = new Couponlookup( $order->get_id() );
		$lookupdata     = $coupon->get_applied_data();
		$coupon_summary = ! empty( $lookupdata['coupon_summary'] ) ? maybe_unserialize( $lookupdata['coupon_summary'] ) : [];
		$subtotal       = ! empty( $coupon_summary['subtotal'] ) ? $coupon_summary['subtotal'] : 0;
		$discount_total = ! empty( $coupon_summary['discount_total'] ) ? $coupon_summary['discount_total'] : 0;
		$applied_coupon = ! empty( $coupon_summary['applied_coupon'] ) ? $coupon_summary['applied_coupon'] : '';
		$before_main    = '';
		if ( $subtotal ) {
			$before_main     .= esc_html__( 'Coupon: ', 'rtcl-coupon' ) . '<span>' . $applied_coupon . ' </span><br/>';
			$before_main     .= esc_html__( 'Subtotal: ', 'rtcl-coupon' ) . '<span>' . Functions::get_payment_formatted_price_html( $subtotal ) . ' </span><br/>';
			$before_main     .= esc_html__( 'Discount: ', 'rtcl-coupon' ) . '<span> -' . Functions::get_payment_formatted_price_html( $discount_total ) . ' </span><br/>';
			$main_amount_html = esc_html__( 'Total: ', 'rtcl-coupon' ) . $main_amount_html;
		}
		return $before_main . $main_amount_html;
	}


}
