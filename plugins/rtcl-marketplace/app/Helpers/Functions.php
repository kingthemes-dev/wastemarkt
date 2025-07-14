<?php

namespace RtclMarketplace\Helpers;

use Rtcl\Helpers\Functions as RtclFunctions;
use RtclMarketplace\Models\ListingProduct;
use WC_Order;

class Functions {
	/**
	 * @return bool|int|mixed|null
	 */
	public static function is_enable_marketplace() {
		return RtclFunctions::get_option_item( 'rtcl_marketplace_settings', 'marketplace_enable', false, 'checkbox' );
	}

	/**
	 * @return bool|int|mixed|null
	 */
	public static function is_enable_payout() {
		return RtclFunctions::get_option_item( 'rtcl_marketplace_settings', 'enable_payout_commission', false, 'checkbox' );
	}

	/**
	 * @return bool|int|mixed|null
	 */
	public static function get_marketplace_categories() {
		return RtclFunctions::get_option_item( 'rtcl_marketplace_settings', 'marketplace_categories', [] );
	}

	/**
	 * @return bool|int|mixed|null
	 */
	public static function is_enable_buy_button() {
		global $is_listings, $listing_term;

		if ( RtclFunctions::is_listings() || RtclFunctions::is_listing_taxonomy() || $is_listings || $listing_term ) {
			return RtclFunctions::get_option_item( 'rtcl_marketplace_settings', 'enable_in_listings_page', false, 'checkbox' );
		} elseif ( RtclFunctions::is_listing() ) {
			return ! self::is_disable_buy_button_in_listing_details();
		}

		return false;
	}

	/**
	 * @return bool|int|mixed|null
	 */
	public static function is_disable_buy_button_in_listing_details() {
		return RtclFunctions::get_option_item( 'rtcl_marketplace_settings', 'disable_in_listing_page', false, 'checkbox' );
	}

	/**
	 * @return bool|int|mixed|null
	 */
	public static function buy_button_text() {
		global $is_listings, $listing_term;

		if ( RtclFunctions::is_listings() || RtclFunctions::is_listing_taxonomy() || $is_listings || $listing_term ) {
			$text = RtclFunctions::get_option_item( 'rtcl_marketplace_settings', 'buy_button_text', __( 'Add to cart', 'rtcl-marketplace' ), 'text' );
		} else {
			$text = RtclFunctions::get_option_item( 'rtcl_marketplace_settings', 'details_buy_button_text', __( 'Buy Now', 'rtcl-marketplace' ), 'text' );
		}

		return $text;
	}

	/**
	 * @return bool|int|mixed|null
	 */
	public static function is_enable_quantity() {
		global $is_listings, $listing_term;
		$enable = false;

		if ( RtclFunctions::is_listings() || RtclFunctions::is_listing_taxonomy() || $is_listings || $listing_term ) {
			$enable = RtclFunctions::get_option_item( 'rtcl_marketplace_settings', 'enable_quantity_in_listings_page', false, 'checkbox' );
		} elseif ( RtclFunctions::is_listing() ) {
			$enable = ! self::is_disable_quantity_listing_details();
		}

		return $enable;
	}

	/**
	 * @return bool|int|mixed|null
	 */
	public static function is_disable_quantity_listing_details() {
		return RtclFunctions::get_option_item( 'rtcl_marketplace_settings', 'disable_quantity_in_listing_page', false, 'checkbox' );
	}

	/**
	 * @return bool|int|mixed|null
	 */
	public static function is_enable_stock_management() {
		return RtclFunctions::get_option_item( 'rtcl_marketplace_settings', 'stock_enable', false, 'checkbox' );
	}

	/**
	 * @return bool|int|mixed|null
	 */
	public static function is_enable_download_product() {
		return RtclFunctions::get_option_item( 'rtcl_marketplace_settings', 'download_enable', 'yes', 'checkbox' );
	}

	/**
	 * @return mixed|null
	 */
	public static function get_allow_file_format() {
		$allow_format_opt = get_option( 'rtcl_marketplace_settings' );
		$allow_format     = 'application/pdf, image/jpg, image/jpeg, image/png';
		if ( ! empty( $allow_format_opt['download_allow_file_format'] ) ) {
			$allow_format = join( ',', $allow_format_opt['download_allow_file_format'] );
		}

		return apply_filters( 'rtcl_marketplace_allow_file_format', $allow_format );
	}


	/**
	 * @param $listing_id
	 *
	 * @return string
	 * @throws \Exception
	 */
	public static function get_max_attribute( $listing_id = 0 ) {
		$attribute = '';

		$listing = new ListingProduct( $listing_id );

		if ( is_a( $listing, 'RtclMarketplace\Models\ListingProduct' ) ) {
			if ( $listing->manage_stock() && ! empty( $quantity = $listing->get_stock_quantity() ) ) {
				$attribute .= "max=$quantity";
			}
		}

		return $attribute;
	}

	/**
	 * @param $listing_id
	 *
	 * @return bool
	 */
	public static function disable_cart_button( $listing_id = 0 ) {
		$listing = new ListingProduct( $listing_id );

		if ( is_a( $listing, 'RtclMarketplace\Models\ListingProduct' ) ) {
			if ( $listing->manage_stock() ) {
				return $listing->get_stock_quantity() < 1;
			}
		}

		return false;
	}

	/**
	 * @param $file_url
	 * @param $user_id
	 *
	 * @return string
	 */
	public static function generate_secure_download_link( $file_url, $user_id ) {
		$secure_link = add_query_arg(
			array(
				'file_url' => urlencode( $file_url ),
				'user_id'  => $user_id,
			),
			home_url( 'download-handler' )
		);

		return $secure_link;
	}

	/**
	 * @return array
	 */
	public static function get_first_level_category_array() {
		$terms    = [];
		$termObjs = RtclFunctions::get_sub_terms( rtcl()->category );
		if ( ! empty( $termObjs ) ) {
			$terms = wp_list_pluck( $termObjs, 'name', 'term_id' );
		}

		return $terms;
	}

	/**
	 * @return bool|int|mixed|null
	 */
	public static function get_commission_rate() {
		return RtclFunctions::get_option_item( 'rtcl_marketplace_settings', 'commission_rate', 0, 'number' );
	}

	/**
	 * @return bool|int|mixed|null
	 */
	public static function get_minimum_payout() {
		return RtclFunctions::get_option_item( 'rtcl_marketplace_settings', 'minimum_payout_amount', 0, 'number' );
	}

	/**
	 * @param $order
	 *
	 * @return array|\WP_Error
	 */
	public static function get_earning_by_order( $order ) {
		if ( is_numeric( $order ) ) {
			$order = wc_get_order( $order );
		}

		$earning = [];

		if ( ! $order ) {
			return new \WP_Error( __( 'Order not found', 'rtcl-marketplace' ), 404 );
		}

		if ( $order->get_meta( '_rtcl_marketplace_has_sub_order' ) ) {
			return $earning;
		}

		$commission_rate = self::get_commission_rate();
		$subtotal        = $order->get_subtotal();

		if ( $commission_rate ) {
			$admin_commission  = ( $subtotal * $commission_rate ) / 100;
			$seller_earning    = $subtotal - $admin_commission;
			$earning['seller'] = $seller_earning;
			$earning['admin']  = $admin_commission;
		}

		return $earning;
	}

	/**
	 * @param $order_id
	 *
	 * @return bool
	 */
	public static function is_order_already_synced( $order_id ) {
		global $wpdb;

		if ( is_a( $order_id, 'WC_Order' ) ) {
			$order_id = $order_id->get_id();
		}

		if ( ! $order_id || ! is_numeric( $order_id ) ) {
			return false;
		}

		// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching
		$order_id = $wpdb->get_var( $wpdb->prepare( "SELECT 1 FROM {$wpdb->prefix}rtcl_marketplace_orders WHERE order_id=%d LIMIT 1", $order_id ) );

		return wc_string_to_bool( $order_id );
	}

	/**
	 * @param $suborder_id
	 * @param $vendor_id
	 *
	 * @return void
	 */
	public static function synchronize_sub_order( $suborder_id, $vendor_id ) {
		global $wpdb;

		if ( is_a( $suborder_id, 'WC_Order' ) ) {
			$order    = $suborder_id;
			$order_id = $order->get_id();
		} else {
			$order = wc_get_order( $suborder_id );
		}

		if ( self::is_order_already_synced( $order ) ) {
			return;
		}

		if ( (int) $order->get_meta( '_rtcl_marketplace_has_sub_order', true ) === 1 ) {
			return;
		}

		$earning = self::get_earning_by_order( $order );

		$wpdb->insert(
			$wpdb->prefix . 'rtcl_marketplace_orders',
			[
				'order_id'       => $order->get_id(),
				'seller_id'      => $vendor_id,
				'order_total'    => $order->get_total(),
				'seller_earning' => $earning['seller'] ?? 0,
				'admin_earning'  => $earning['admin'] ?? 0,
				'order_status'   => 'wc-' . $order->get_status(),
			],
			[
				'%d',
				'%d',
				'%f',
				'%f',
				'%f',
				'%s',
			]
		);
	}

	/**
	 * @param int|WC_Order $parent_order
	 *
	 * @return WC_Order[]
	 */
	public static function get_child_orders( $parent_order ) {
		$parent_order_id = is_numeric( $parent_order ) ? $parent_order : $parent_order->get_id();

		return wc_get_orders(
			[
				'type'   => 'shop_order',
				'parent' => $parent_order_id,
				'limit'  => - 1,
			]
		);
	}

	/**
	 * @param $user_id
	 *
	 * @return int|string
	 */
	public static function get_user_total_earning( $user_id = 0 ) {
		global $wpdb;

		if ( ! $user_id ) {
			$user_id = get_current_user_id();
		}

		$table_name = $wpdb->prefix . 'rtcl_marketplace_orders';

		$query = $wpdb->prepare( "SELECT SUM(seller_earning) AS total FROM {$table_name} WHERE seller_id = %d AND order_status=%s",
			[ $user_id, 'wc-completed' ] );

		$total = $wpdb->get_var( $query );

		return $total ?? 0;
	}

	/**
	 * @param $user_id
	 *
	 * @return int|string
	 */
	public static function get_user_total_withdraw( $user_id = 0 ) {
		global $wpdb;

		if ( ! $user_id ) {
			$user_id = get_current_user_id();
		}

		$table_name = $wpdb->prefix . 'rtcl_marketplace_withdraw';

		$query = $wpdb->prepare( "SELECT SUM(amount) AS total FROM {$table_name} WHERE seller_id = %d", $user_id );

		$total = $wpdb->get_var( $query );

		return $total ?? 0;
	}

	/**
	 * @param $user_id
	 *
	 * @return int|string
	 */
	public static function get_available_balance( $user_id = 0 ) {
		$total_earning  = self::get_user_total_earning( $user_id );
		$total_withdraw = self::get_user_total_withdraw( $user_id );

		return $total_earning - $total_withdraw;
	}

	/**
	 * @param $user_id
	 *
	 * @return array|object|\stdClass|null
	 */
	public static function get_current_selected_payout_method( $user_id = 0 ) {
		global $wpdb;

		$table_name = $wpdb->prefix . 'rtcl_marketplace_payout_method';

		if ( ! $user_id ) {
			$user_id = get_current_user_id();
		}

		$query = $wpdb->prepare( "SELECT * FROM {$table_name} WHERE seller_id = %d ORDER BY id DESC", $user_id );

		return $wpdb->get_row( $query, ARRAY_A );
	}

	/**
	 * @return array
	 */
	public static function add_seller_payment_method() {
		global $wpdb;

		$msg         = '';
		$notice_type = 'error';

		if ( $_POST['rtcl-payout-submit'] ) {
			$method = ! empty( $_POST['rtcl-marketplace-payout-title'] ) ? sanitize_text_field( $_POST['rtcl-marketplace-payout-title'] )
				: 'direct_bank_transfer';

			if ( $method ) {
				$key = $method . '-desc';

				if ( 'paypal' === $method ) {
					$desc = isset( $_POST[ $key ] ) ? sanitize_email( $_POST[ $key ] ) : '';
				} else {
					$desc = isset( $_POST[ $key ] ) ? sanitize_textarea_field( $_POST[ $key ] ) : '';
				}

				if ( ! empty( $desc ) ) {
					$table_name = $wpdb->prefix . 'rtcl_marketplace_payout_method';

					$record_id = $wpdb->get_var( $wpdb->prepare( "SELECT id FROM {$table_name} WHERE seller_id = %d", get_current_user_id() ) );

					if ( $record_id ) {
						$updated = $wpdb->update( $table_name, [
							'method'  => $method,
							'details' => $desc,
							'date'    => current_time( 'mysql' )
						], [ 'id' => $record_id ] );

						if ( is_wp_error( $updated ) ) {
							$msg = __( 'Error during update information.', 'rtcl-marketplace' );
						} else {
							$notice_type = 'success';
							$msg         = __( 'Updated payout method successfully.', 'rtcl-marketplace' );
						}
					} else {
						$insert_id = $wpdb->insert( $table_name, [
							'seller_id' => get_current_user_id(),
							'method'    => $method,
							'details'   => $desc,
							'date'      => current_time( 'mysql' )
						] );

						if ( is_wp_error( $insert_id ) ) {
							$msg = __( 'Error to insert information.', 'rtcl-marketplace' );
						} else {
							$notice_type = 'success';
							$msg         = __( 'Added payout method successfully.', 'rtcl-marketplace' );
						}
					}
				} else {
					$msg = __( 'Please, enter description.', 'rtcl-marketplace' );
				}
			}

		}

		return [ 'type' => $notice_type, 'message' => $msg ];
	}

	/**
	 * @param $seller_id
	 * @param $balance
	 * @param $payout_method
	 *
	 * @return bool|int|\mysqli_result|null
	 */
	public static function add_withdraw_request( $seller_id, $balance, $payout_method ) {
		global $wpdb;

		$table_name = $wpdb->prefix . 'rtcl_marketplace_withdraw';

		$insert_id = $wpdb->insert( $table_name, [
			'seller_id' => $seller_id,
			'amount'    => $balance,
			'date'      => current_time( 'mysql' ),
			'status'    => 'pending',
			'method'    => sanitize_text_field( $payout_method['method'] ),
			'details'   => sanitize_textarea_field( $payout_method['details'] ),
		] );

		return $insert_id;

	}

	/**
	 * @param $payout_id
	 * @param $status
	 *
	 * @return array
	 */
	public static function update_withdraw_status( $payout_id, $status ) {
		global $wpdb;

		$table_name = $wpdb->prefix . 'rtcl_marketplace_withdraw';

		$update_column = [ 'status' => $status ];

		if ( 'paid' === $status ) {
			$update_column['paid_date'] = current_time( 'mysql' );
		}

		$updated = $wpdb->update( $table_name, $update_column, [ 'id' => $payout_id ] );

		$success = false;
		$msg     = __( 'Something wrong!.', 'rtcl-marketplace' );

		if ( ! is_wp_error( $updated ) ) {
			$success = true;
			$msg     = __( 'Updated payout status successfully.', 'rtcl-marketplace' );
		}

		return [ 'success' => $success, 'message' => $msg ];
	}

	/**
	 * @param $user_id
	 *
	 * @return array|object|\stdClass[]|null
	 */
	public static function get_withdraw_history( $user_id = 0 ) {
		global $wpdb;

		$table_name = $wpdb->prefix . 'rtcl_marketplace_withdraw';

		if ( $user_id ) {
			$query = $wpdb->prepare( "SELECT * FROM {$table_name} WHERE seller_id = %d", $user_id );
		} else {
			$query = "SELECT * FROM {$table_name}";
		}

		return $wpdb->get_results( $query, ARRAY_A );
	}

	/**
	 * @param $payout_id
	 *
	 * @return array|object|\stdClass|null
	 */
	public static function get_payout_by_id( $payout_id = 0 ) {
		global $wpdb;

		$table_name = $wpdb->prefix . 'rtcl_marketplace_withdraw';

		$query = $wpdb->prepare( "SELECT * FROM {$table_name} WHERE id = %d", $payout_id );

		return $wpdb->get_row( $query, ARRAY_A );
	}

	/**
	 * @return mixed|null
	 */
	public static function get_payout_options() {
		$options = [
			'direct_bank_transfer' => __( 'Direct Bank Transfer', 'rtcl-marketplace' ),
			'paypal'               => __( 'Paypal', 'rtcl-marketplace' ),
			'offline'              => __( 'Offline Payment', 'rtcl-marketplace' ),
		];

		return apply_filters( 'rtcl_marketplace_payout_options', $options );
	}

	/**
	 * @param $option
	 *
	 * @return mixed|string
	 */
	public static function get_payout_option_text( $option = '' ) {
		$options = self::get_payout_options();

		return $options[ $option ] ?? '';
	}

	/**
	 * @return mixed|null
	 */
	public static function get_payout_status() {
		$statuses = [
			'processing' => __( 'Processing', 'rtcl-marketplace' ),
			'hold'       => __( 'On hold', 'rtcl-marketplace' ),
			'pending'    => __( 'Pending', 'rtcl-marketplace' ),
			'paid'       => __( 'Paid', 'rtcl-marketplace' ),
		];

		return apply_filters( 'rtcl_marketplace_payout_status', $statuses );
	}

	/**
	 * @param $status
	 *
	 * @return mixed|string
	 */
	public static function get_payout_status_text( $status = '' ) {
		$options = self::get_payout_status();

		return $options[ $status ] ?? '';
	}

	/**
	 * @return bool|int|mixed|null
	 */
	public static function is_enable_seller_order_email() {
		return RtclFunctions::get_option_item( 'rtcl_email_settings', 'notify_users', 'seller_product_order_email', 'multi_checkbox' );
	}

	/**
	 * @return bool|int|mixed|null
	 */
	public static function is_enable_payout_request_email() {
		return RtclFunctions::get_option_item( 'rtcl_email_settings', 'notify_admin', 'payout_request_email', 'multi_checkbox' );
	}

	/**
	 * @return bool|int|mixed|null
	 */
	public static function is_enable_payout_paid_email() {
		return RtclFunctions::get_option_item( 'rtcl_email_settings', 'notify_users', 'payout_request_paid_email', 'multi_checkbox' );
	}

}
