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

use RadiusTheme\COUPON\Controllers\Metaboxes;
use RadiusTheme\COUPON\Helpers\Fns;
use Rtcl\Helpers\Functions;
use RadiusTheme\COUPON\Models\Coupon;
use RadiusTheme\COUPON\Models\CouponDB;
use RadiusTheme\COUPON\Models\Couponlookup;

/**
 * ActionHooks Class
 */
class ActionHooks {
	/**
	 * Initialize Function
	 *
	 * @return void
	 */
	public static function init() {
		add_action( 'rtcl_after_register_post_type', [ __CLASS__, 'after_register_post_type' ] );
		add_action( 'edit_form_after_title', [ __CLASS__, 'edit_form_after_title' ] );
		add_action( 'add_meta_boxes', [ __CLASS__, 'coupon_meta_boxes' ] );
		/* Save post meta on the 'save_post' hook. */
		add_action( 'save_post', [ __CLASS__, 'save_coupon_meta_box_data' ], 10, 2 );
		add_action( 'rtcl_checkout_form_start', [ __CLASS__, 'coupon_checkout_form' ], 99 );
		add_action( 'rtcl_payment_receipt_details_before_total_amount', [ __CLASS__, 'details_before_total_amount' ] );
		add_action( 'rtcl_after_payment_items', [ __CLASS__, 'rtcl_after_payment_items' ], 10, 1 );
		add_action( 'rtcl_checkout_process_success', [ __CLASS__, 'checkout_process_success' ], 10, 1 );
		add_action( 'manage_rtcl_coupon_posts_custom_column', [ __CLASS__, 'coupon_custom_column_values' ], 10, 2 );
		add_action( 'before_delete_post', [ __CLASS__, 'delete_coupon_data' ], 10, 2 );
	}
	/**
	 * Delete post
	 *
	 * @param array $post_id post id.
	 * @return void
	 */
	public static function delete_coupon_data( $post_id, $post ) {
		if ( ! in_array( $post->post_type, [ rtcl_coupon()->post_type_coupon, rtcl()->post_type_payment ] ) ) {
			return;
		}
		$coupondb = new CouponDB();
		if ( rtcl_coupon()->post_type_coupon === $post->post_type ) {
			$coupondb->delete_coupon( $post_id );
		}
		if ( rtcl()->post_type_payment === $post->post_type ) {
			$coupondb->delete_lookup( $post_id );
		}

	}
	/**
	 * Register Post types
	 *
	 * @return void
	 */
	public static function after_register_post_type() {
		$coupon_args = [
			'labels'              => [
				'name'                  => __( 'Coupons', 'rtcl-coupon' ),
				'singular_name'         => __( 'Coupon', 'rtcl-coupon' ),
				'menu_name'             => _x( 'Coupons', 'Admin menu name', 'rtcl-coupon' ),
				'add_new'               => __( 'Add coupon', 'rtcl-coupon' ),
				'add_new_item'          => __( 'Add new coupon', 'rtcl-coupon' ),
				'edit'                  => __( 'Edit', 'rtcl-coupon' ),
				'edit_item'             => __( 'Edit coupon', 'rtcl-coupon' ),
				'new_item'              => __( 'New coupon', 'rtcl-coupon' ),
				'view_item'             => __( 'View coupon', 'rtcl-coupon' ),
				'search_items'          => __( 'Search coupons', 'rtcl-coupon' ),
				'not_found'             => __( 'No coupons found', 'rtcl-coupon' ),
				'not_found_in_trash'    => __( 'No coupons found in trash', 'rtcl-coupon' ),
				'parent'                => __( 'Parent coupon', 'rtcl-coupon' ),
				'filter_items_list'     => __( 'Filter coupons', 'rtcl-coupon' ),
				'items_list_navigation' => __( 'Coupons navigation', 'rtcl-coupon' ),
				'items_list'            => __( 'Coupons list', 'rtcl-coupon' ),
			],
			'description'         => __( 'This is where you can add new coupons that customers can use in your store.', 'rtcl-coupon' ),
			'public'              => false,
			'show_ui'             => true,
			'capability_type'     => rtcl()->post_type,
			'map_meta_cap'        => true,
			'publicly_queryable'  => true,
			'exclude_from_search' => true,
			'show_in_menu'        => 'edit.php?post_type=' . rtcl()->post_type,
			'hierarchical'        => false,
			'rewrite'             => false,
			'query_var'           => false,
			'supports'            => [ 'title' ],
			'show_in_nav_menus'   => true,
			'show_in_admin_bar'   => true,
		];
		register_post_type( rtcl_coupon()->post_type_coupon, $coupon_args );
	}
	/**
	 * Print coupon description textarea field.
	 *
	 * @param WP_Post $post Current post object.
	 */
	public static function edit_form_after_title( $post ) {
		// phpcs:disable WordPress.Security.EscapeOutput.OutputNotEscaped
		if ( rtcl_coupon()->post_type_coupon === $post->post_type ) {
			?>
			<textarea id="rtcl-coupon-description" name="excerpt" cols="5" rows="2" placeholder="<?php esc_attr_e( 'Description (optional)', 'rtcl-coupon' ); ?>"><?php echo $post->post_excerpt; ?></textarea>
			<?php
		}
		// phpcs:enable WordPress.Security.EscapeOutput.OutputNotEscaped
	}
	/**
	 * Print coupon description textarea field.
	 */
	public static function coupon_checkout_form() {
		wp_enqueue_style( 'rtcl-coupon' );
		wp_enqueue_script( 'rtcl-coupon' );
		?>

		<div class="apply-coupon-form" style="display: flex; gap: 5px;margin: 15px 0;">
			<div class="coupon-input-field">
				<input type="text" id="coupon_key" value="" style="height: 45px;padding: 5px 10px;" autocomplete="off"/>
			</div>
			<div class="coupon-input-field">
				<button id="rtcl_apply_coupon" style="height: 45px;padding: 5px 30px;" disabled > <?php esc_html_e( 'Apply Coupon', 'rtcl-coupon' ); ?><span class="loader"></span> </button>
			</div>
		</div>

			<div id="cart-collaterals"></div>
		<?php
	}

	/**
	 * Register Post types
	 *
	 * @return void
	 */
	public static function coupon_meta_boxes() {
		add_meta_box(
			rtcl_coupon()->post_type_coupon,
			__( 'Coupon Options', 'rtcl-coupon' ),
			[ Metaboxes::class, 'coupon_metabox' ],
			rtcl_coupon()->post_type_coupon,
			'normal',
			'high'
		);
	}

	/**
	 * Register Post types
	 *
	 * @return void
	 */
	public static function save_coupon_meta_box_data( $post_id, $post ) {

		// Autosaving, bail.
		if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
			return;
		}

		$nonce_action = 'update-post_' . $post_id;
		if ( ! isset( $_REQUEST['_wpnonce'] ) || ! wp_verify_nonce( $_REQUEST['_wpnonce'], $nonce_action ) ) {
			return;
		}

		if ( rtcl_coupon()->post_type_coupon !== $post->post_type ) {
			return;
		}
		global $wpdb;
		$coupon_table  = $wpdb->prefix . 'rtcl_coupon';
		$coupon_insert = [];
		/* OK, it's safe for us to save the data now. */

		// Make sure that it is set.
		if ( isset( $_POST['rtcl_pricing_type'] ) ) {
			$pricing_type                  = sanitize_text_field( wp_unslash( $_POST['rtcl_pricing_type'] ) );
			$coupon_insert['pricing_type'] = $pricing_type;
		}

		// Make sure that it is set.
		if ( isset( $_POST['rtcl_discount_type'] ) ) {
			$discount_type                  = sanitize_text_field( wp_unslash( $_POST['rtcl_discount_type'] ) );
			$coupon_insert['discount_type'] = $discount_type;
		}

		// Make sure that it is set.
		if ( isset( $_POST['rtcl_coupon_amount'] ) ) {
			$discount_amount                  = sanitize_text_field( wp_unslash( $_POST['rtcl_coupon_amount'] ) );
			$coupon_insert['discount_amount'] = $discount_amount;
		}

		// Make sure that it is set.
		if ( isset( $_POST['rtcl_coupon_expiry'] ) ) {
			$expiry                       = Fns::date_to_timestamp( $_POST['rtcl_coupon_expiry'] );
			$coupon_insert['expire_date'] = $expiry;
		}

		// Make sure that it is set.
		if ( isset( $_POST['rtcl_usage_limit_per_user'] ) ) {
			$usage_limit                     = absint( $_POST['rtcl_usage_limit_per_user'] );
			$coupon_insert['per_user_limit'] = $usage_limit;
		}

		// Make sure that it is set.
		if ( isset( $_POST['rtcl_usage_limit'] ) ) {
			$per_user_limit               = absint( $_POST['rtcl_usage_limit'] );
			$coupon_insert['usage_limit'] = $per_user_limit;
		}

		// Make sure that it is set.
		if ( isset( $_POST['rtcl_pricing_include'] ) ) {
			$pricing_include = array_map( 'absint', $_POST['rtcl_pricing_include'] );
		} else {
			$pricing_include = [];
		}
		$coupon_insert['include_pricing'] = maybe_serialize( $pricing_include );
		// Make sure that it is set.
		if ( isset( $_POST['rtcl_pricing_exclude'] ) ) {
			$pricing_exclude = array_map( 'absint', $_POST['rtcl_pricing_exclude'] );
			$pricing_exclude = array_diff( $pricing_exclude, $pricing_include );
		} else {
			$pricing_exclude = [];
		}
		$coupon_insert['exclude_pricing'] = maybe_serialize( $pricing_exclude );
		if ( ! empty( $coupon_insert ) ) {
			// Need set caching.
			$rowcount = $wpdb->get_var( "SELECT COUNT(*) FROM $coupon_table WHERE `coupon_id` = $post_id " );
			if ( $rowcount > 0 ) {
				$wpdb->update( $coupon_table, $coupon_insert, [ 'coupon_id' => $post_id ] );
			} else {
				$coupon_insert['coupon_id'] = $post_id;
				$wpdb->insert( $coupon_table, $coupon_insert );
			}
		}

	}

	/**
	 * Register Post types
	 *
	 * @return void
	 */
	public static function details_before_total_amount( $payment ) {
		$payment_id = $payment->get_id();
		$coupon     = new Couponlookup( $payment_id );
		$lookupdata = $coupon->get_applied_data();
		// get_coupon_lookup
		$coupon_summary = ! empty( $lookupdata['coupon_summary'] ) ? maybe_unserialize( $lookupdata['coupon_summary'] ) : [];
		$subtotal       = ! empty( $coupon_summary['subtotal'] ) ? $coupon_summary['subtotal'] : 0;
		$discount       = ! empty( $coupon_summary['discount_total'] ) ? $coupon_summary['discount_total'] : 0;
		if ( ! $subtotal ) {
			return;
		}
		?>
			<tr>
				<td class="text-right rtcl-vertical-middle">
					<?php esc_html_e( 'Subtotal ', 'rtcl-coupon' ); ?>
				</td>
				<td>
				<?php
					echo esc_html( $subtotal );
					echo Functions::get_currency_symbol( Functions::get_order_currency() );
				?>
				</td>
			</tr>
			<tr>
				<td class="text-right rtcl-vertical-middle">
					<?php esc_html_e( 'Discount ', 'rtcl-coupon' ); ?>
				</td>
				<td>
				-
				<?php
					echo esc_html( $discount );
					echo Functions::get_currency_symbol( Functions::get_order_currency() );
				?>
				</td>
			</tr>
		<?php
	}
	/**
	 * Undocumented function
	 *
	 * @param integer $order_id Order id.
	 * @return void
	 */
	public static function rtcl_after_payment_items( $order_id ) {
		$coupon         = new Couponlookup( $order_id );
		$lookupdata     = $coupon->get_applied_data();
		$coupon_summary = ! empty( $lookupdata['coupon_summary'] ) ? maybe_unserialize( $lookupdata['coupon_summary'] ) : [];
		$subtotal       = ! empty( $coupon_summary['subtotal'] ) ? $coupon_summary['subtotal'] : 0;
		$discount       = ! empty( $coupon_summary['discount_total'] ) ? $coupon_summary['discount_total'] : 0;
		$applied_coupon = ! empty( $coupon_summary['applied_coupon'] ) ? $coupon_summary['applied_coupon'] : '';
		$total_payable  = ! empty( $coupon_summary['total_payable'] ) ? $coupon_summary['total_payable'] : '';
		if ( ! $subtotal ) {
			return;
		}

		?>
		<tr>
			<td>
				<div class="coupon-section">
					<style>
						.coupons-calculate {
							display: flex;
							flex-direction: column;
						}
					</style>
					<span><?php esc_html_e( 'Applied Coupons:', 'rtcl-coupon' ); ?> </span>
					<strong style="border: 1px solid #eee;padding: 3px 5px;"><?php echo esc_html( $applied_coupon ); ?></strong>
				</div>
			</td>
			<td></td>
			<td></td>
			<td></td>
			<td class="rtcl_payment_item_details">
				<div class="coupons-calculate">
					<div style="line-height: 25px;display: flex;">
						<span style="flex: 0 0 90px;"><?php esc_html_e( 'Subtotal: ', 'rtcl-coupon' ); ?></span>
						<span style="font-weight: 700;">
						<?php
						echo esc_html( $subtotal );
						echo Functions::get_currency_symbol( Functions::get_order_currency() );
						?>
						</span>
					</div>
					<div style="line-height: 25px;display: flex;">
						<span style="flex: 0 0 90px;"><?php esc_html_e( 'Discount: ', 'rtcl-coupon' ); ?></span>
						<span style="font-weight: 700;" >-
						<?php
								echo esc_html( $discount );
								echo Functions::get_currency_symbol( Functions::get_order_currency() );
						?>
						</span>
					</div>
					<div style="line-height: 25px;display: flex;">
						<span style="flex: 0 0 90px;"><?php esc_html_e( 'Order Total: ', 'rtcl-coupon' ); ?></span>
						<span style="font-weight: 700;" >
						<?php
							echo esc_html( $total_payable );
							echo Functions::get_currency_symbol( Functions::get_order_currency() );
						?>
						</span>
					</div>
				</div>
			</td>
		</tr>
		<?php
	}
	/**
	 * After Checkout Process Success.
	 *
	 * @param object $order Order.
	 * @return void
	 */
	public static function checkout_process_success( $order ) {
		global $wpdb;
		$coupon_table    = $wpdb->prefix . 'rtcl_coupon';
		$lookup_table    = $wpdb->prefix . 'rtcl_order_coupon_lookup';
		$checkout_totals = rtcl()->session->get( 'rtcl_checkout_totals', [] );
		$applied_coupon  = rtcl()->session->get( 'rtcl_applied_coupon', '' );

		$coupon      = new Coupon( $applied_coupon );
		$coupon_meta = $coupon->get_coupon_meta();
		$order_id    = $order->get_id();
		$coupon_id   = ! empty( $coupon_meta['coupon_id'] ) ? $coupon_meta['coupon_id'] : null;
		if ( ! $coupon_id ) {
			return;
		}
		// Need add cache.
		$lookup_count                  = $wpdb->get_var( "SELECT COUNT(*) FROM $lookup_table WHERE `order_id` = $order_id " );
		$lookup_data                   = [];
		$lookup_data['coupon_id']      = $coupon_id;
		$lookup_data['order_id']       = $order_id;
		$lookup_data['user_id']        = get_current_user_id();
		$coupon_summary                = [
			'applied_coupon' => $applied_coupon,
			'subtotal'       => $checkout_totals['subtotal'],
			'total_payable'  => $checkout_totals['total'],
			'discount_total' => $checkout_totals['discount_total'],
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

	/**
	 * Undocumented function
	 *
	 * @param string  $column Column.
	 * @param integer $post_id post id.
	 * @return void
	 */
	public static function coupon_custom_column_values( $column, $post_id ) {
		$coupon_code = get_the_title( $post_id );
		$coupon      = new Coupon( $coupon_code );
		$coupon_meta = $coupon->get_coupon_meta();
		switch ( $column ) {
			case 'coupon_uses':
				$count = ! empty( $coupon_meta['usage_count'] ) ? intval( $coupon_meta['usage_count'] ) : 0;
				/* translators: %s: count */
				$text = sprintf( _n( '%s Time', '%s Times', $count, 'rtcl-coupon' ), $count );
				echo esc_html( $text );
				break;
			case 'coupon_pricing_type':
				$pricing_type = ! empty( $coupon_meta['pricing_type'] ) ? $coupon_meta['pricing_type'] : '';
				echo esc_html( ucwords( $pricing_type ) );
				break;
			case 'coupon_type':
				$type = ! empty( $coupon_meta['discount_type'] ) ? $coupon_meta['discount_type'] : '';
				if ( 'fixed_discount' === $type ) {
					esc_html_e( 'Fixed', 'rtcl-coupon' );
				} elseif ( 'percent_discount' === $type ) {
					esc_html_e( 'Percentage', 'rtcl-coupon' );
				}
				break;
			case 'coupon_discount':
				$amount = ! empty( $coupon_meta['discount_amount'] ) ? $coupon_meta['discount_amount'] : 0;
				if ( $amount ) {
					echo intval( $amount );
				}
				break;
			case 'coupon_expiry_date':
				$expiry = ! empty( $coupon_meta['expire_date'] ) ? $coupon_meta['expire_date'] : 0;
				if ( $expiry ) {
					echo esc_html( date( 'Y-m-d', $expiry ) );
				}
				break;
		}
	}
}






