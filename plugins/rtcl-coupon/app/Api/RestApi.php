<?php

namespace RadiusTheme\COUPON\Api;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

use RadiusTheme\COUPON\Api\V1\V1_CouponApi;
use RadiusTheme\COUPON\Hooks\ActionHooks;
use RadiusTheme\COUPON\Models\Coupon;
use RadiusTheme\COUPON\Models\Couponlookup;
use RadiusTheme\COUPON\Traits\SingletonTrait;
use Rtcl\Helpers\Functions as RtclFunctions;
use Rtcl\Models\Payment;
use Rtcl\Models\Pricing;
use WP_REST_Request;

if ( ! class_exists( 'RestApi' ) ) {

	class RestApi {

		use SingletonTrait;

		public function __construct() {
			add_action( 'rest_api_init', [ &$this, 'register_rest_api' ] );
			add_filter( 'rtcl_rest_api_checkout_process_new_order_args', [
				&$this,
				'checkout_process_with_coupon'
			], 15, 4 );
			add_filter( 'rtcl_rest_api_config_data', [ &$this, 'coupon_config' ] );
			add_action( 'rtcl_rest_checkout_process_success', [ &$this, 'update_coupon_data' ], 15, 3 );
			add_action( 'rtcl_rest_api_order_data', [ &$this, 'add_coupon_data_at_order' ], 15, 2 );
		}

		/**
		 * @param array   $order_data
		 * @param Payment $order
		 *
		 * @return array
		 */
		public function add_coupon_data_at_order( $order_data, $order ) {
			$coupon     = new Couponlookup( $order->get_id() );
			$lookupData = $coupon->get_applied_data();
			if ( ! empty( $lookupData ) ) {
				$couponData     = ! empty( $lookupData['coupon_summary'] ) ? maybe_unserialize( $lookupData['coupon_summary'] ) : [];
				$coupon_code    = ! empty( $couponData['applied_coupon'] ) ? $couponData['applied_coupon'] : '';
				$subtotal       = ! empty( $couponData['subtotal'] ) ? $couponData['subtotal'] : 0;
				$discount       = ! empty( $couponData['discount_total'] ) ? $couponData['discount_total'] : 0;
				$applied_coupon = ! empty( $couponData['applied_coupon'] ) ? $couponData['applied_coupon'] : '';
				$total_payable  = ! empty( $couponData['total_payable'] ) ? $couponData['total_payable'] : '';
				if ( ! empty( $coupon_code ) && ! empty( $subtotal ) && ! empty( $discount ) ) {
					$order_data['coupon'] = [
						'coupon_code' => $coupon_code,
						'original'    => $subtotal,
						'discount'    => $discount,
						'subtotal'    => $total_payable,
					];
				}
			}

			return $order_data;
		}


		/**
		 * After Checkout Process Success.
		 *
		 * @param Payment         $order Order.
		 * @param WP_REST_Request $request
		 *
		 * @return void
		 */
		public static function update_coupon_data( $order, $processed_data, $request ) {

			if ( is_a( $request, WP_REST_Request::class ) && $order ) {
				$coupon_code = sanitize_text_field( wp_unslash( trim( $request->get_param( "coupon_code" ) ) ) );
				if ( $coupon_code ) {
					$coupon = new Coupon( $coupon_code, $order->get_pricing_id() );
					if ( $coupon->is_valid() ) {
						global $wpdb;
						$coupon_table = $wpdb->prefix . 'rtcl_coupon';
						$lookup_table = $wpdb->prefix . 'rtcl_order_coupon_lookup';

						$coupon_meta = $coupon->get_coupon_meta();
						$coupon_data = $coupon->get_coupon_data();
						$order_id    = $order->get_id();
						$coupon_id   = ! empty( $coupon_meta['coupon_id'] ) ? $coupon_meta['coupon_id'] : null;

						// Need add cache.
						$lookup_count                  = $wpdb->get_var( "SELECT COUNT(*) FROM $lookup_table WHERE `order_id` = $order_id " );
						$lookup_data                   = [];
						$lookup_data['coupon_id']      = $coupon_id;
						$lookup_data['order_id']       = $order_id;
						$lookup_data['user_id']        = get_current_user_id();
						$coupon_summary                = [
							'applied_coupon' => $coupon_data['coupon_code'],
							'subtotal'       => $coupon_data['original'],
							'total_payable'  => $coupon_data['subtotal'],
							'discount_total' => $coupon_data['discount'],
						];
						$lookup_data['coupon_summary'] = maybe_serialize( $coupon_summary );
						if ( $lookup_count > 0 ) {
							$wpdb->update( $lookup_table, $lookup_data, [ 'order_id' => $order_id ] );
							wp_cache_delete( 'rtcl_coupon_lookup_' . $order_id, 'coupon-lookup' );
						} else {
							$wpdb->insert( $lookup_table, $lookup_data );
						}
						$coupon_count = $wpdb->get_var( "SELECT COUNT(*) FROM $lookup_table WHERE `coupon_id` = $coupon_id " );
						$wpdb->update( $coupon_table, [ 'usage_count' => $coupon_count ], [ 'coupon_id' => $coupon_id ] );
						wp_cache_delete( 'rtcl_coupon_meta_' . $coupon_id, 'coupon-details' );
					}
				}
			}

		}


		/**
		 * @param array $config
		 *
		 * @return array
		 */
		public static function coupon_config( $config ) {
			$config['coupon'] = (object) [];

			return $config;
		}

		/**
		 * @param array           $order_args
		 * @param Pricing         $plan
		 * @param WP_REST_Request $request
		 *
		 * @return array
		 */
		public function checkout_process_with_coupon( $order_args, $plan, $gateway, $request ) {
			if ( is_a( $request, WP_REST_Request::class ) && $plan ) {
				$coupon_code = sanitize_text_field( wp_unslash( trim( $request->get_param( "coupon_code" ) ) ) );
				if ( $coupon_code ) {
					$coupon = new Coupon( $coupon_code, $plan->getId() );
					if ( $coupon->is_valid() ) {
						$coupon_data = $coupon->get_coupon_data();
						if ( ! empty( $coupon_data['subtotal'] ) ) {
							$order_args['meta_input']['amount'] = $coupon_data['subtotal'];
						}
					}
				}
			}

			return $order_args;
		}


		public function register_rest_api() {
			( new V1_CouponApi() )->register_route();
		}

	}
}
