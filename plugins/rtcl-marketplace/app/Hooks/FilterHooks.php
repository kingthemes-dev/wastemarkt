<?php

namespace RtclMarketplace\Hooks;

use Rtcl\Helpers\Functions;
use RtclMarketplace\Emails\ListingOwnerOrderEmail;
use RtclMarketplace\Emails\OrderNoteEmail;
use RtclMarketplace\Emails\PayoutPaidEmail;
use RtclMarketplace\Emails\PayoutRequestEmail;
use RtclMarketplace\Models\ListingProduct;
use RtclMarketplace\Helpers\Functions as MarketplaceFunctions;

class FilterHooks {

	/**
	 * @return void
	 */
	public static function init() {
		add_filter( 'woocommerce_data_stores', array( __CLASS__, 'data_stores' ) );
		add_filter( 'woocommerce_product_get_price', array( __CLASS__, 'woocommerce_get_price' ), 20, 2 );
		add_filter( 'woocommerce_cart_item_thumbnail', array( __CLASS__, 'woocommerce_cart_item_thumbnail' ), 10, 3 );
		add_filter( 'body_class', [ __CLASS__, 'body_class' ] );
		add_filter(
			'woocommerce_order_item_get_formatted_meta_data',
			array(
				__CLASS__,
				'hide_listing_item_meta',
			),
			10,
			2
		);
		add_filter( 'woocommerce_order_item_name', array( __CLASS__, 'add_link_to_listing_title' ), 10, 2 );
		add_filter( 'woocommerce_add_to_cart_validation', array( __CLASS__, 'protect_listing_add_to_cart' ), 10, 5 );

		add_filter( 'rtcl_account_menu_items', [ __CLASS__, 'add_marketplace_menu_item_at_account_menu' ] );
		add_filter( 'rtcl_my_account_endpoint', [ __CLASS__, 'add_my_account_marketplace_end_points' ] );

		add_filter(
			'postbox_classes_' . rtcl()->post_type . '_rtcl_marketplace',
			[
				__CLASS__,
				'add_meta_box_classes',
			]
		);
		add_filter( 'rtcl_email_services', [ __CLASS__, 'email_services' ] );
		add_filter( 'post_class', [ __CLASS__, 'admin_shop_order_row_classes' ], 10, 3 );
		add_filter( 'woocommerce_shop_order_list_table_order_css_classes', [ __CLASS__, 'hpos_order_row_classes' ], 10, 2 );
		add_filter( 'rtcl_account_menu_items', [ __CLASS__, 'add_orders_menu_item_at_account_menu' ], 99 );
		add_filter( 'rtcl_my_account_endpoint', [ __CLASS__, 'add_my_account_orders_end_points' ], 99 );
		add_filter( 'rtcl_get_user_email_notification_options', [ __CLASS__, 'seller_email_settings' ] );
		add_filter( 'rtcl_get_admin_email_notification_options', [ __CLASS__, 'admin_email_settings' ] );
	}

	/**
	 * @param $options
	 *
	 * @return mixed
	 */
	public static function seller_email_settings( $options ) {
		$options['seller_product_order_email'] = esc_html__( 'Listing owner order email', 'rtcl-marketplace' );
		$options['payout_request_paid_email']  = esc_html__( 'Payout paid email to seller', 'rtcl-marketplace' );

		return $options;
	}

	/**
	 * @param $options
	 *
	 * @return mixed
	 */
	public static function admin_email_settings( $options ) {
		$options['payout_request_email'] = esc_html__( 'Payout request email to admin', 'rtcl-marketplace' );

		return $options;
	}

	/**
	 * @param $endpoints
	 *
	 * @return mixed
	 */
	public static function add_my_account_orders_end_points( $endpoints ) {

		if ( ! MarketplaceFunctions::is_enable_payout() ) {
			return $endpoints;
		}

		$endpoints['marketplace-orders'] = Functions::get_option_item( 'rtcl_marketplace_settings', 'myaccount_orders_endpoint', 'my-orders' );
		$endpoints['marketplace-payout'] = Functions::get_option_item( 'rtcl_marketplace_settings', 'myaccount_payout_endpoint', 'payout' );

		return $endpoints;
	}

	/**
	 * @param $items
	 *
	 * @return mixed
	 */
	public static function add_orders_menu_item_at_account_menu( $items ) {

		if ( ! MarketplaceFunctions::is_enable_payout() ) {
			return $items;
		}

		$position = array_search( 'payments', array_keys( $items ) );

		$marketplace_items = [
			'marketplace-orders' => apply_filters( 'rtcl_marketplace_myaccount_orders_title', esc_html__( 'Orders', 'rtcl-marketplace' ) ),
			'marketplace-payout' => apply_filters( 'rtcl_marketplace_myaccount_payout_title', esc_html__( 'Payout', 'rtcl-marketplace' ) ),
		];

		if ( $position > - 1 ) {
			Functions::array_insert( $items, $position, $marketplace_items );
		}

		return $items;
	}

	/**
	 * @param $classes
	 * @param $css_class
	 * @param $post_id
	 *
	 * @return mixed
	 */
	public static function admin_shop_order_row_classes( $classes, $css_class, $post_id ) {

		if ( ! is_admin() || ! current_user_can( 'manage_woocommerce' ) ) {
			return $classes;
		}

		$order = wc_get_order( $post_id );
		if ( ! $order ) {
			return $classes;
		}

		if ( $order->get_parent_id() !== 0 && $order->get_meta( '_rtcl_marketplace_vendor_id' ) ) {
			$classes[] = 'rtcl-marketplace-sub-order parent-' . $order->get_parent_id();
		}

		return $classes;
	}

	/**
	 * @param $classes
	 * @param $order
	 *
	 * @return mixed
	 */
	public static function hpos_order_row_classes( $classes, $order ) {
		if ( ! $order ) {
			return $classes;
		}

		if ( $order->get_parent_id() !== 0 && $order->get_meta( '_rtcl_marketplace_vendor_id' ) ) {
			$classes[] = 'rtcl-marketplace-sub-order parent-' . $order->get_parent_id();
		}

		return $classes;
	}


	/**
	 * @param $services
	 *
	 * @return mixed
	 */
	public static function email_services( $services ) {
		$services['ListingOwnerOrderEmail'] = new ListingOwnerOrderEmail();
		$services['PayoutRequestEmail']     = new PayoutRequestEmail();
		$services['PayoutPaidEmail']        = new PayoutPaidEmail();
		$services['OrderNoteEmail']         = new OrderNoteEmail();

		return $services;
	}

	/**
	 * Add menu items
	 *
	 * @param array $items
	 *
	 * @return array
	 */
	public static function add_marketplace_menu_item_at_account_menu( $items ) {
		$position            = array_search( 'edit-account', array_keys( $items ) );
		$menu['my-download'] = apply_filters( 'rtcl_my_download_title', esc_html__( 'My Download', 'rtcl-marketplace' ) );
		if ( $position > - 1 ) {
			Functions::array_insert( $items, $position, $menu );
		}

		return $items;
	}

	/**
	 * Add endpoints
	 *
	 * @param array $endpoints
	 *
	 * @return array
	 */
	public static function add_my_account_marketplace_end_points( $endpoints ) {
		$endpoints['my-download'] = Functions::get_option_item( 'rtcl_marketplace_settings', 'myaccount_mydownload_endpoint', 'my-download' );

		return $endpoints;
	}

	/**
	 * @param array $classes
	 *
	 * @return array
	 */
	static function add_meta_box_classes( $classes = [] ) {
		array_push( $classes, sanitize_html_class( 'rtcl' ) );

		return $classes;
	}

	/**
	 * @param $passed
	 * @param $product_id
	 * @param $quantity
	 * @param $variation_id
	 * @param $variations
	 *
	 * @return false|mixed
	 * @throws \Exception
	 */
	public static function protect_listing_add_to_cart( $passed, $product_id, $quantity, $variation_id = 0, $variations = null ) {
		if ( ! empty( $product_id ) && get_post_type( $product_id ) === 'rtcl_listing' ) {
			$listing = new ListingProduct( $product_id );

			if ( is_a( $listing, '\RtclMarketplace\Models\ListingProduct' ) && $listing->manage_stock() ) {
				$available_stock = $listing->get_stock_quantity();
				if ( $quantity > $available_stock ) {
					wc_add_notice(
						sprintf( esc_html__( 'Cant have more than %1$s x %2$s in cart.', 'rtcl-marketplace' ), $available_stock, $listing->get_the_title() ),
						'error'
					);
					$passed = false;
				}
			}
		}

		return $passed;
	}

	/**
	 * @param $html
	 * @param $item
	 *
	 * @return mixed|string
	 * @throws \Exception
	 */
	public static function add_link_to_listing_title( $html, $item ) {
		$listing_id = wc_get_order_item_meta( $item->get_id(), 'rtcl_listing_id', true );
		if ( $listing_id ) {
			$listing = rtcl()->factory->get_listing( $listing_id );
			if ( is_a( $listing, '\Rtcl\Models\Listing' ) ) {
				$html = sprintf( '<a href="%s">%s</a>', $listing->get_the_permalink(), $listing->get_the_title() );
			}
		}

		return $html;
	}

	/**
	 * @param $formatted_meta
	 * @param $item
	 *
	 * @return mixed
	 */
	public static function hide_listing_item_meta( $formatted_meta, $item ) {
		// remove listing item meta from customer email & order details.
		$is_resend = isset( $_POST['wc_order_action'] ) && wc_clean( wp_unslash( $_POST['wc_order_action'] ) ) === 'send_order_details';
		if ( ! $is_resend && is_admin() ) {
			return $formatted_meta;
		}

		foreach ( $formatted_meta as $key => $meta ) {
			if ( in_array( $meta->key, array( 'rtcl_listing_id' ) ) ) {
				unset( $formatted_meta[ $key ] );
			}
		}

		return $formatted_meta;
	}

	/**
	 * @param $classes
	 *
	 * @return array
	 */
	public static function body_class( $classes ) {
		$classes = (array) $classes;

		if ( Functions::is_listing() ) {
			$classes[] = 'rtcl-marketplace';
		}

		return array_unique( $classes );
	}

	/**
	 * @param $stores
	 *
	 * @return mixed
	 */
	public static function data_stores( $stores ) {
		require_once RTCL_MARKETPLACE_PLUGIN_PATH . 'app/RtclMarketplaceDataStore.php';

		$stores['product'] = 'RtclMarketplaceDataStore';

		return $stores;
	}

	/**
	 * @param $price
	 * @param $product
	 *
	 * @return mixed
	 */
	public static function woocommerce_get_price( $price, $product ) {

		if ( get_post_type( $product->get_id() ) === 'rtcl_listing' ) {
			$price = get_post_meta( $product->get_id(), 'price', true );
		}

		return $price;
	}

	/**
	 * @param $product_image
	 * @param $cart_item
	 * @param $cart_item_key
	 *
	 * @return mixed|string|null
	 */
	public static function woocommerce_cart_item_thumbnail( $product_image, $cart_item, $cart_item_key ) {

		if ( ! empty( $cart_item ) && get_post_type( $cart_item['product_id'] ) === 'rtcl_listing' ) {
			$listing       = rtcl()->factory->get_listing( $cart_item['product_id'] );
			$product_image = $listing->get_the_thumbnail();
		}

		return $product_image;
	}
}
