<?php

namespace RtclMarketplace\Hooks;

use Rtcl\Helpers\Functions;
use Rtcl\Helpers\Link;
use Rtcl\Models\Listing;
use RtclMarketplace\Helpers\Functions as MarketplaceFunctions;
use RtclMarketplace\Models\ListingProduct;
use WC_Order;

class ActionHooks {

	/**
	 * @return void
	 */
	public static function init() {
		add_action( 'rtcl_single_listing_inner_sidebar', [ __CLASS__, 'add_buy_button' ], 5 );
		add_action( 'rtcl_listing_loop_item', [ __CLASS__, 'add_buy_button' ], 90 );
		add_action( 'rtcl_builder_loop_item_content_end', [ __CLASS__, 'add_buy_button' ], 90 );
		add_action( 'woocommerce_before_order_itemmeta', [ __CLASS__, 'product_name_for_suborder' ], 10, 2 );
		add_action( 'woocommerce_after_order_itemmeta', [ __CLASS__, 'order_item_meta' ], 10, 2 );
		add_action( 'rtcl_before_main_content', [ __CLASS__, 'add_wc_notice' ], 5 );
		add_action( 'rtcl_builder_before_header', [ __CLASS__, 'add_wc_notice' ], 5 );
		add_action( 'woocommerce_checkout_create_order', [ __CLASS__, 'checkout_create_order' ], 20, 2 );
		if ( rtcl()->is_request( 'frontend' ) ) {
			add_action( 'rtcl_listing_form', [ __CLASS__, 'marketplace_form' ], 18 );
		}
		add_action( 'rtcl_listing_form_after_save_or_update', [ __CLASS__, 'save_marketplace_form_data' ], 10, 5 );
		add_action( 'woocommerce_checkout_order_created', [ __CLASS__, 'listing_stock_reduction' ], 20 );
		add_action( 'woocommerce_order_status_changed', [ __CLASS__, 'balance_order_stock' ], 20, 4 );

		add_action( 'rtcl_account_my-download_endpoint', [ __CLASS__, 'account_my_download_endpoint' ] );
		add_action( 'rtcl_account_marketplace-orders_endpoint', [ __CLASS__, 'account_orders_endpoint' ] );
		add_action( 'rtcl_account_marketplace-payout_endpoint', [ __CLASS__, 'account_payout_endpoint' ] );
		add_action( 'init', [ __CLASS__, 'handle_secure_download_request' ] );
		add_action( 'woocommerce_checkout_order_created', [ __CLASS__, 'vendor_sub_order' ], 99 );
		add_action( 'woocommerce_admin_order_data_after_payment_info', [ __CLASS__, 'sub_order_list' ], 99 );
		add_action( 'woocommerce_order_status_changed', [ __CLASS__, 'on_order_status_change' ], 12, 4 );
		add_action( 'woocommerce_order_status_changed', [ __CLASS__, 'manage_refunded_for_order' ], 15, 4 );
		add_action( 'rtcl_marketplace_before_payout_method', [ __CLASS__, 'add_payout_method' ] );
		add_action( 'rtcl_marketplace_payout_method', [ __CLASS__, 'payout_method_setup' ] );
		add_action( 'in_admin_header', [ __CLASS__, 'remove_all_notices' ], 999 );
	}

	/**
	 * @return void
	 */
	public static function add_payout_method() {
		if ( isset( $_POST['rtcl-payout-submit'] ) ) {
			$notice = MarketplaceFunctions::add_seller_payment_method(); ?>
            <div class="rtcl-message alert alert-<?php echo esc_attr( $notice['type'] ); ?>">
				<?php echo esc_html( $notice['message'] ); ?>
            </div>
			<?php
		}
	}

	/**
	 * @param $options
	 *
	 * @return void
	 */
	public static function payout_method_setup( $options ) {

		if ( ! MarketplaceFunctions::is_enable_payout() ) {
			return;
		}

		$selected_option = MarketplaceFunctions::get_current_selected_payout_method();

		if ( ! empty( $options ) ) {
			?>
            <div class="payout-method-options">
                <form action="#" method="post">
					<?php
					foreach ( $options as $option ) {
						$title = Functions::get_option_item( 'rtcl_marketplace_settings', $option . '_title', $option );
						$desc  = Functions::get_option_item( 'rtcl_marketplace_settings', $option . '_desc', '' );

						$checked = isset( $selected_option['method'] ) && $option === $selected_option['method'];
						$info    = $selected_option['details'] ?? '';
						?>
                        <div class="payout-method-option">
                            <input type="radio" id="title_<?php echo esc_attr( $option ); ?>" name="rtcl-marketplace-payout-title"
                                   value="<?php echo esc_attr( $option ); ?>" <?php echo $checked ? 'checked' : ''; ?>/>
                            <label for="title_<?php echo esc_attr( $option ); ?>"><?php echo esc_html( $title ); ?></label>
                            <p><?php echo esc_html( $desc ); ?></p>
							<?php if ( 'paypal' === $option ): ?>
                                <input type="email" placeholder="<?php _e( 'Enter email', 'rtcl-marketplace' ); ?>"
                                       name="<?php echo esc_attr( $option ); ?>-desc" class="form-control" value="<?php echo $checked
									? esc_html( $info ) : ''; ?>"/>
							<?php else: ?>
                                <textarea rows="5" cols="50" name="<?php echo esc_attr( $option ); ?>-desc" class="form-control"><?php echo $checked
										? esc_html( $info ) : ''; ?></textarea>
							<?php endif; ?>
                        </div>
						<?php
					}
					?>
                    <input class="btn btn-primary" type="submit" name="rtcl-payout-submit" value="<?php _e( 'Set payout method', 'rtcl-marketplace' ); ?>">
                </form>
            </div>
			<?php
		}
	}

	/**
	 * Update the child order status when a parent order status is changed
	 *
	 * @param int      $order_id
	 * @param string   $old_status
	 * @param string   $new_status
	 * @param WC_Order $order
	 *
	 * @return void
	 */
	public static function on_order_status_change( $order_id, $old_status, $new_status, $order ) {
		global $wpdb;

		if ( ! MarketplaceFunctions::is_enable_payout() ) {
			return;
		}

		// Split order if the order doesn't have parent and sub orders,
		// and the order is created from dashboard.
		if ( empty( $order->get_parent_id() ) && empty( $order->get_meta( '_rtcl_marketplace_has_sub_order' ) ) && is_admin() ) {
			// Remove the hook to prevent recursive callas.
			remove_action( 'woocommerce_order_status_changed', [ __CLASS__, 'on_order_status_change' ], 10 );

			// Split the order.
			self::vendor_sub_order( $order );

			// Add the hook back.
			add_action( 'woocommerce_order_status_changed', [ __CLASS__, 'on_order_status_change' ], 10, 4 );
		}

		// make sure order status contains "wc-" prefix
		if ( stripos( $new_status, 'wc-' ) === false ) {
			$new_status = 'wc-' . $new_status;
		}

		$table_name = $wpdb->prefix . 'rtcl_marketplace_orders';

		$wpdb->update(
			$table_name,
			[ 'order_status' => $new_status ],
			[ 'order_id' => $order_id ],
			[ '%s' ],
			[ '%d' ]
		);

		// Update sub-order statuses
		$sub_orders = \RtclMarketplace\Helpers\Functions::get_child_orders( $order_id );
		if ( $sub_orders ) {
			foreach ( $sub_orders as $sub_order ) {
				if ( is_callable( [ $sub_order, 'update_status' ] ) ) {
					$sub_order->update_status( $new_status );
				}
			}
		}
	}

	/**
	 * If order status is set to refunded update marketplace orders.
	 *
	 *
	 * @param int      $order_id
	 * @param string   $old_status
	 * @param string   $new_status
	 * @param WC_Order $order
	 *
	 * @return void
	 */
	public static function manage_refunded_for_order( $order_id, $old_status, $new_status, $order ) {
		global $wpdb;

		if ( ! MarketplaceFunctions::is_enable_payout() ) {
			return;
		}

		if ( $new_status !== 'refunded' ) {
			return;
		}

		if ( $order->get_meta( '_rtcl_marketplace_has_sub_order' ) ) {
			return;
		}

		if ( ! $order->get_parent_id() ) {
			return;
		}

		// update the order table with new refund amount
		$table_name = $wpdb->prefix . 'rtcl_marketplace_orders';

		if ( $order->get_meta( '_rtcl_marketplace_vendor_id' ) > 0 ) {
			$wpdb->update(
				$table_name,
				[
					'order_total'    => 0,
					'seller_earning' => 0,
					'admin_earning'  => 0,
				],
				[
					'order_id' => $order_id,
				],
				[
					'%f',
					'%f',
					'%f'
				],
				[
					'%d',
				]
			);
		}
	}

	/**
	 * @return void
	 */
	public static function account_payout_endpoint() {
		global $wp;

		if ( ! MarketplaceFunctions::is_enable_payout() ) {
			return;
		}

		$query_vars = $wp->query_vars;

		if ( $query_vars && ! empty( $query_vars['marketplace-payout'] ) ) {
			$settings = Functions::get_option_item( 'rtcl_marketplace_settings', 'payout_method', '' );
			$args     = [
				'options' => $settings
			];
			Functions::get_template( "myaccount/payout-method", $args, '', rtcl_marketplace()->get_plugin_template_path() );
		} else {
			Functions::get_template( "myaccount/seller-payout", '', '', rtcl_marketplace()->get_plugin_template_path() );
		}
	}

	/**
	 * @return void
	 */
	public static function account_orders_endpoint() {
		global $wpdb, $wp;

		if ( ! MarketplaceFunctions::is_enable_payout() ) {
			return;
		}

		$table_name = $wpdb->prefix . 'rtcl_marketplace_orders';
		$user_id    = get_current_user_id();
		$query_vars = $wp->query_vars;

		if ( $query_vars && ! empty( $query_vars['marketplace-orders'] ) ) {

			$order_id = absint( $query_vars['marketplace-orders'] );

			$args = [
				'order_id' => $order_id,
				'user_id'  => $user_id
			];

			Functions::get_template( "myaccount/seller-orders-details", $args, '', rtcl_marketplace()->get_plugin_template_path() );
		} else {

			$page           = isset( $_GET['item'] ) ? absint( $_GET['item'] ) : 1;
			$items_per_page = get_option( 'posts_per_page', 10 );
			$offset         = ( $page * $items_per_page ) - $items_per_page;

			$total = $wpdb->get_var( $wpdb->prepare( "SELECT count(*) from {$table_name} WHERE seller_id=%d", $user_id ) );

			$query   = $wpdb->prepare( "SELECT * from {$table_name} WHERE seller_id=%d ORDER BY order_id DESC LIMIT {$offset}, {$items_per_page}",
				$user_id );
			$results = $wpdb->get_results( $query, ARRAY_A );

			$total_page = ceil( $total / $items_per_page );

			$args = [
				'orders'        => $results,
				'user_id'       => $user_id,
				'pages'         => $total_page,
				'current_page'  => $page,
				'post_per_page' => $items_per_page
			];

			Functions::get_template( "myaccount/seller-orders", $args, '', rtcl_marketplace()->get_plugin_template_path() );
		}
	}

	/**
	 * Show sub order list in admin order details
	 *
	 * @param $order
	 *
	 * @return void
	 */
	public static function sub_order_list( $order ) {
		if ( is_a( $order, 'WC_Order' ) ) {
			$children = get_children( array(
				'post_parent' => $order->get_id(),
				'post_type'   => 'shop_order',
			) );
			if ( ! empty( $children ) ) {
				?>
                <span><?php _e( 'Sub order: ', 'rtcl-marketplace' ); ?></span>
				<?php
				foreach ( $children as $order_id => $order_post ) {
					$child_order = wc_get_order( $order_id );
					?>
                    <a href="<?php echo esc_url( $child_order->get_edit_order_url() ); ?>"><?php echo $child_order->get_id(); ?></a>
					<?php
				}
			} else {
				$parent_order_id = $order->get_parent_id();
				if ( $parent_order_id ) {
					$parent_order = wc_get_order( $parent_order_id );
					?>
                    <span><?php _e( 'Parent order: ', 'rtcl-marketplace' ); ?></span>
                    <a href="<?php echo esc_url( $parent_order->get_edit_order_url() ); ?>"><?php echo $parent_order->get_id(); ?></a>
					<?php
				}
			}
		}
	}

	/**
	 * @return void
	 */
	public static function handle_secure_download_request() {
		//phpcs:disable
		if ( isset( $_GET['file_url'], $_GET['user_id'] ) ) {
			$file_url = urldecode( sanitize_text_field( $_GET['file_url'] ) );
			$user_id  = intval( $_GET['user_id'] );

			// Check if the user is logged in and the token matches
			if ( is_user_logged_in() && get_current_user_id() === $user_id ) {
				// Clear the output buffer to avoid corrupting the file
				header( 'Content-Description: File Transfer' );
				header( 'Content-Type: application/octet-stream' );
				header( 'Content-Disposition: attachment; filename=' . basename( $file_url ) );
				header( 'Content-Transfer-Encoding: binary' );
				header( 'Expires: 0' );
				header( 'Cache-Control: must-revalidate, post-check=0, pre-check=0' );
				header( 'Pragma: public' );
				header( 'Content-Length: ' . filesize( $file_url ) );
				ob_clean();
				flush();
				readfile( $file_url );
				exit;
			}
		}
		//phpcs:enable
	}

	/**
	 * @return void
	 */
	public static function account_my_download_endpoint() {

		if ( ! MarketplaceFunctions::is_enable_download_product() ) {
			return;
		}
		$user_id = get_current_user_id();
		if ( 0 == $user_id ) {
			echo esc_html__( 'You need to be logged in to see your purchased products.', 'rtcl-marketplace' );

			return;
		}
		$customer_orders = wc_get_orders(
			[
				'limit'       => - 1,
				'customer_id' => $user_id,
				'status'      => array( 'completed' ),
			]
		);
		if ( empty( $customer_orders ) ) {
			echo esc_html__( 'You have not purchased any products yet.', 'rtcl-marketplace' );

			return;
		}
		$args = [
			'customer_orders' => $customer_orders,
			'user_id'         => $user_id,
		];
		Functions::get_template( 'myaccount/my-download', $args, '', rtcl_marketplace()->get_plugin_template_path() );
	}

	/**
	 * @return void
	 */
	public static function add_wc_notice() {
		if ( class_exists( 'WooCommerce' ) ) {
			wc_print_notices();
		}
	}

	/**
	 * @param $order_id
	 * @param $old_status
	 * @param $new_status
	 * @param $order
	 *
	 * @return void
	 * @throws \Exception
	 */
	public static function balance_order_stock( $order_id, $old_status, $new_status, $order ) {
		if ( ! MarketplaceFunctions::is_enable_stock_management() ) {
			return;
		}
		if ( 'cancelled' == $new_status ) {
			foreach ( $order->get_items() as $item ) {
				$listing_id = wc_get_order_item_meta( $item->get_id(), 'rtcl_listing_id', true );
				$listing    = new ListingProduct( $listing_id );

				if ( empty( $listing_id ) || get_post_type( $listing_id ) !== 'rtcl_listing' ) {
					continue;
				}

				$listing = new ListingProduct( $listing_id );

				if ( ! is_a( $listing, 'RtclMarketplace\Models\ListingProduct' ) && ! $listing->manage_stock() ) {
					continue;
				}

				$listing->add_stock( $item->get_quantity() );
			}
		}
	}

	/**
	 * @param $order
	 *
	 * @return void
	 * @throws \Exception
	 */
	public static function listing_stock_reduction( $order ) {
		if ( ! MarketplaceFunctions::is_enable_stock_management() ) {
			return;
		}
		foreach ( $order->get_items() as $item ) {
			$listing_id = wc_get_order_item_meta( $item->get_id(), 'rtcl_listing_id', true );
			if ( ! empty( $listing_id ) && get_post_type( $listing_id ) === 'rtcl_listing' ) {
				$listing = new ListingProduct( $listing_id );
				if ( is_a( $listing, '\RtclMarketplace\Models\ListingProduct' ) && $listing->manage_stock() ) {
					$listing->reduce_stock( $item->get_quantity() );
				}
			}
		}
	}

	/**
	 * Split orders based on seller product
	 *
	 * @param $order
	 *
	 * @return void
	 */
	public static function vendor_sub_order( $order ) {

		if ( $order->get_meta( '_rtcl_marketplace_has_sub_order' ) ) {
			return;
		}

		$bill_ship       = [
			'billing_country',
			'billing_first_name',
			'billing_last_name',
			'billing_company',
			'billing_address_1',
			'billing_address_2',
			'billing_city',
			'billing_state',
			'billing_postcode',
			'billing_email',
			'billing_phone',
			'shipping_country',
			'shipping_first_name',
			'shipping_last_name',
			'shipping_company',
			'shipping_address_1',
			'shipping_address_2',
			'shipping_city',
			'shipping_state',
			'shipping_postcode',
		];
		$vendors         = array();
		$commission_rate = MarketplaceFunctions::get_commission_rate();

		foreach ( $order->get_items() as $item_id => $item ) {
			$listing_id = wc_get_order_item_meta( $item->get_id(), 'rtcl_listing_id', true );
			if ( ! empty( $listing_id ) && get_post_type( $listing_id ) === rtcl()->post_type ) {
				$listing   = rtcl()->factory->get_listing( $listing_id );
				$vendor_id = $listing->get_owner_id();
				if ( ! isset( $vendors[ $vendor_id ] ) ) {
					$vendors[ $vendor_id ] = array(
						'items'    => array(),
						'subtotal' => 0,
					);
				}

				$vendors[ $vendor_id ]['items'][ $item_id ] = $item;
				$vendors[ $vendor_id ]['subtotal']          += $item->get_total();
			}
		}

		$order_id = $order->get_id();

		if ( ! empty( $vendors ) ) {

			$order->update_meta_data( '_rtcl_marketplace_has_sub_order', true );
			$order->update_meta_data( '_rtcl_marketplace_commission_rate', $commission_rate );
			$order->save();

			foreach ( $vendors as $vendor_id => $data ) {
				$vendor_items    = $data['items'];
				$vendor_subtotal = $data['subtotal'];

				$suborder = wc_create_order( array(
					'customer_id'   => $order->get_customer_id(),
					'status'        => 'pending', // Status for suborder
					'customer_note' => '', // Optional note
					'parent'        => $order_id, // Link to original order
				) );

				$suborder->set_created_via( 'rtcl-marketplace' );
				$suborder->set_currency( $order->get_currency() );
				$suborder->set_customer_ip_address( $order->get_customer_ip_address() );
				$suborder->set_payment_method( $order->get_payment_method() );
				$suborder->set_payment_method_title( $order->get_payment_method_title() );
				$suborder->update_meta_data( '_rtcl_marketplace_vendor_id', $vendor_id );

				// save billing and shipping address
				foreach ( $bill_ship as $key ) {
					if ( is_callable( [ $suborder, "set_{$key}" ] ) ) {
						$suborder->{"set_{$key}"}( $order->{"get_{$key}"}() );
					}
				}

				// Add items to the suborder
				foreach ( $vendor_items as $item_id => $item ) {
					$suborder_item_id = $suborder->add_product(
						$item->get_product(),
						$item->get_quantity(),
						array(
							'subtotal'     => $item->get_subtotal(),
							'total'        => $item->get_total(),
							'subtotal_tax' => $item->get_subtotal_tax(),
							'total_tax'    => $item->get_total_tax(),
						)
					);

					wc_add_order_item_meta( $suborder_item_id, 'rtcl_listing_id', wc_get_order_item_meta( $item_id, 'rtcl_listing_id' ) );
				}

				// Optionally, set shipping and taxes for the suborder
				// $suborder->set_shipping_total( $shipping_total );
				// $suborder->set_shipping_tax( $shipping_tax );

				// Save the suborder
				$suborder->calculate_totals();
				$suborder->save();

				MarketplaceFunctions::synchronize_sub_order( $suborder, $vendor_id );

				if ( MarketplaceFunctions::is_enable_seller_order_email() && is_a( $suborder, 'WC_Order' ) ) {
					rtcl()->mailer()->emails['ListingOwnerOrderEmail']->trigger( $suborder, [ 'vendor_id' => $vendor_id, 'vendor_items' => $vendor_items ] );
				}
			}
		}
	}

	/**
	 * @param $post_id
	 *
	 * @return void
	 */
	public static function marketplace_form( $post_id ) {

		if ( ! MarketplaceFunctions::is_enable_marketplace()
		     && ( ! MarketplaceFunctions::is_enable_stock_management()
		          || MarketplaceFunctions::is_enable_download_product() )
		) {
			return;
		}

		$data = [
			'post_id'         => $post_id,
			'manage_stock'    => get_post_meta( $post_id, '_manage_stock', true ),
			'stock'           => get_post_meta( $post_id, '_stock', true ),
			'allow_format'    => MarketplaceFunctions::get_allow_file_format(),
			'stock_enable'    => MarketplaceFunctions::is_enable_stock_management(),
			'download_enable' => MarketplaceFunctions::is_enable_download_product(),
		];

		Functions::get_template( 'listing-form/marketplace', $data, '', rtcl_marketplace()->get_plugin_template_path() );
	}

	/**
	 * Save marketplace front-end meta data
	 *
	 * @param $listing
	 * @param $type
	 * @param $cat_id
	 * @param $new_listing_status
	 * @param $request_data
	 *
	 * @return void
	 */
	public static function save_marketplace_form_data( $listing, $type, $cat_id, $new_listing_status, $request_data = [ 'data' => '' ] ) {
		/** @var array $data */
		$data = $request_data['data'];

		if ( is_a( $listing, Listing::class ) ) {
			if ( ! empty( $data['_rtcl_manage_stock'] ) ) {
				update_post_meta( $listing->get_id(), '_manage_stock', 'yes' );
				if ( isset( $data['_rtcl_stock'] ) ) {
					update_post_meta( $listing->get_id(), '_stock', absint( $data['_rtcl_stock'] ) );
				}
			} else {
				delete_post_meta( $listing->get_id(), '_manage_stock' );
				delete_post_meta( $listing->get_id(), '_stock' );
			}

			update_post_meta( $listing->get_id(), '_rtcl_enable_download', sanitize_text_field( wp_unslash( $data['_rtcl_enable_download'] ) ) );

			$download_files = [];

			if ( ! empty( $data['_rtcl_file_names'] ) && ! empty( $data['_rtcl_file_urls'] ) ) {

				$titles   = $data['_rtcl_file_names'];
				$contents = $data['_rtcl_file_urls'];

				foreach ( $titles as $index => $title ) {
					if ( empty( $title ) ) {
						continue;
					}
					$download_files[] = [
						'title' => sanitize_text_field( $title ),
						'url'   => sanitize_textarea_field( $contents[ $index ] ),
					];
				}
			}

			update_post_meta( $listing->get_id(), 'rtcl_download_files', $download_files );

		}
	}

	/**
	 * @return void
	 */
	public static function add_buy_button() {
		if ( ! MarketplaceFunctions::is_enable_marketplace() ) {
			return;
		}

		$options = [
			'is_enable_buy_button' => MarketplaceFunctions::is_enable_buy_button(),
			'button_text'          => MarketplaceFunctions::buy_button_text(),
			'is_enable_quantity'   => MarketplaceFunctions::is_enable_quantity(),
		];

		Functions::get_template( 'buy-button', $options, '', rtcl_marketplace()->get_plugin_template_path() );
	}

	/**
	 * @param $item_id
	 * @param $item
	 *
	 * @return void
	 * @throws \Exception
	 */
	public static function order_item_meta( $item_id, $item ) {
		$listing_id = wc_get_order_item_meta( $item_id, 'rtcl_listing_id', true );
		if ( ! empty( $listing_id ) && get_post_type( $listing_id ) === 'rtcl_listing' ) {
			$listing = rtcl()->factory->get_listing( $listing_id );
			if ( is_object( $listing ) ) {
				?>
                <div class="rtcl-listing-data">
                    <a href="<?php echo $listing->get_the_permalink(); ?>" target="_blank">
						<?php esc_html_e( 'See Ad', 'rtcl-marketplace' ); ?>
                    </a>
                </div>
				<?php
			}
		}
	}

	/**
	 * @param $item_id
	 * @param $item
	 *
	 * @return void
	 * @throws \Exception
	 */
	public static function product_name_for_suborder( $item_id, $item ) {
		$order      = $item->get_order();
		$listing_id = wc_get_order_item_meta( $item_id, 'rtcl_listing_id' );
		if ( $order->get_parent_id() && $listing_id ) {
			$listing = rtcl()->factory->get_listing( $listing_id );
			if ( is_object( $listing ) ) {
				?>
                <div class="wc-order-item-name"><?php echo $listing->get_the_title(); ?></div>
				<?php
			}
		}
	}

	/**
	 * @param $order
	 * @param $data
	 *
	 * @return void
	 */
	public static function checkout_create_order( $order, $data ) {
		foreach ( $order->get_items() as $item ) {
			if ( isset( $item->legacy_values ) ) {
				$values = $item->legacy_values;
				if ( ! empty( $values['product_id'] ) ) {
					$listing_id = $values['product_id'];
					if ( get_post_type( $listing_id ) === 'rtcl_listing' ) {
						$item->update_meta_data( 'rtcl_listing_id', $values['product_id'] );
					}
				}
			}
		}
	}

	/**
	 * @return void
	 */
	public static function remove_all_notices() {
		if ( isset( $_GET['page'] ) && ( 'rtcl-marketplace-commission' == $_GET['page'] || 'rtcl-marketplace-payouts' == $_GET['page'] ) ) {
			remove_all_actions( 'admin_notices' );
			remove_all_actions( 'all_admin_notices' );
		}
	}

}