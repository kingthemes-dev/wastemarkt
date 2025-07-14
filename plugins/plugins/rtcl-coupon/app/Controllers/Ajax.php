<?php
/**
 * Main initialization class.
 *
 * @package RadiusTheme\COUPON
 */

namespace RadiusTheme\COUPON\Controllers;

use Rtcl\Helpers\Functions;
use RadiusTheme\COUPON\Helpers\Fns;
use RadiusTheme\COUPON\Models\Coupon;
use RadiusTheme\COUPON\Traits\SingletonTrait;

/**
 * Metaboxes class.
 */
class Ajax {
	/**
	 * Singleton Function.
	 */
	use SingletonTrait;
	/**
	 * Initial Function.
	 *
	 * @return void
	 */
	public function init() {
		add_action( 'wp_ajax_rtcl_pricing_type_query', [ $this, 'rtcl_pricing_type_query' ] );
		add_action( 'wp_ajax_rtcl_apply_coupon', [ $this, 'rtcl_apply_coupon' ] );
		add_action( 'wp_ajax_rtcl_remove_coupons', [ $this, 'rtcl_remove_coupons' ] );
	}

	/**
	 * Share option Modal content
	 *
	 * @return void
	 */
	public function rtcl_pricing_type_query() {
		if ( ! Functions::verify_nonce() ) {
			$return = [
				'content' => '<p class="form-field"><label></label>' . esc_html__( 'Something Went wrong. Refresh And try again', 'rtcl-coupon' ) . '</p>',
			];
			wp_send_json_error( $return );
		}
		$pricing_type = isset( $_POST['pricing_type'] ) ? sanitize_text_field( wp_unslash( $_POST['pricing_type'] ) ) : null;
		$coupon_meta  = isset( $_POST['coupon_meta'] ) ? json_decode( wp_unslash( $_POST['coupon_meta'] ), true ) : [];
		if ( $pricing_type ) {
			add_filter(
				'rtcl_coupon_get_all_pricing_query_args',
				function( $args ) use ( $pricing_type ) {
					$args['meta_query'] = [
						[
							[
								'key'   => 'pricing_type',
								'value' => $pricing_type,
							],
						],
					];
					if ( 'regular' === $pricing_type ) {
						$args['meta_query'] = [
							[
								[
									'key'   => 'pricing_type',
									'value' => $pricing_type,
								],
								[
									'key'     => 'pricing_type',
									'compare' => 'NOT EXISTS',
								],
								'relation' => 'OR',
							],
						];
					}
					return $args;
				}
			);
		}
		ob_start();
		Fns::pricing_type_dependent_field( $coupon_meta );
		$content = ob_get_clean();
		$return  = [
			'content' => $content,
		];
		wp_send_json_success( $return );
	}
	/**
	 * Share option Modal content
	 *
	 * @return void
	 */
	public function rtcl_apply_coupon() {
		if ( ! Functions::verify_nonce() ) {
			$return = [
				'content' => '<p class="form-field error-message" style="color:red"><label></label>' . esc_html__( 'Something Went wrong. Refresh And try again', 'rtcl-coupon' ) . '</p>',
			];
			wp_send_json_error( $return );
		}

		$pricing_id  = isset( $_POST['pricing_id'] ) ? absint( $_POST['pricing_id'] ) : 0;
		$coupon_code = isset( $_POST['coupon_code'] ) ? sanitize_text_field( wp_unslash( trim( $_POST['coupon_code'] ) ) ) : '';

		$coupon          = new Coupon( $coupon_code, $pricing_id );
		$currency        = Functions::get_order_currency();
		$currency_symbol = Functions::get_currency_symbol( $currency );

		if ( ! $coupon->is_valid() ) {
			$content = '';
			foreach ( $coupon->get_errors() as $value ) {
				$content .= '<p class="form-field error-message" style="color:red"><label></label>' . esc_html( $value ) . '</p>';
			}
			$return = [
				'content' => $content,
			];
			wp_send_json_error( $return );
		}
		ob_start();
		$checkout_totals = rtcl()->session->get( 'rtcl_checkout_totals', [] );
		$applied_coupons = rtcl()->session->get( 'rtcl_applied_coupons', '' );
		// $coupon_discount = rtcl()->session->get( 'rtcl_coupon_discount_totals', 0 );
		?>
		<div class="cart_totals">
			<?php printf( '<h3 style="margin-bottom: 10px;">%s</h3>', esc_html__( 'After Apply Coupon', 'rtcl-coupon' ) ); ?>
			<table cellspacing="0" class="shop_table shop_table_responsive">
				<tbody>
					<tr class="cart-subtotal">
						<th><?php esc_html_e( 'Subtotal', 'rtcl-coupon' ); ?></th>
						<td data-title="Subtotal">
							<span class="rtcl-Price-amount amount">
								<bdi><?php echo esc_html( $checkout_totals['subtotal'] ); ?><span class="rtcl-Price-currencySymbol"><?php echo esc_html( $currency_symbol ); ?>&nbsp;</span>
								</bdi>
							</span>
						</td>
					</tr>
					
					<tr class="cart-discount coupon-hellortcl">
						<th>
						<?php printf( esc_html__( 'Coupon: %s', 'rtcl-coupon' ), $applied_coupons ); ?>
						</th>
						<td>-
							<span class="rtcl-Price-amount amount"> <?php echo esc_html( $checkout_totals['discount_total'] ); ?>
								<span class="rtcl-Price-currencySymbol"><?php echo esc_html( $currency_symbol ); ?>&nbsp;</span>
							</span> 
							<a href="#" class="rtcl-remove-coupon" >[<?php esc_html_e( 'Remove', 'rtcl-coupon' ); ?>]</a>
						</td>
					</tr>
					<tr class="order-total">
						<th><?php esc_html_e( 'Total', 'rtcl-coupon' ); ?></th>
						<td><strong><span class="rtcl-Price-amount amount"><bdi><?php echo esc_html( $checkout_totals['total'] ); ?><span class="rtcl-Price-currencySymbol"><?php echo esc_html( $currency_symbol ); ?>&nbsp;</span></bdi></span></strong> </td>
					</tr>
				</tbody>
			</table>
		</div>
		<?php
		$content = ob_get_clean();
		$return  = [
			'content' => $content,
		];
		wp_send_json_success( $return );
	}
	/**
	 * Share option Modal content
	 *
	 * @return void
	 */
	public function rtcl_remove_coupons() {
		if ( ! Functions::verify_nonce() ) {
			$return = [
				'content' => '<p class="form-field error-message" style="color:red"><label></label>' . esc_html__( 'Something Went wrong. Refresh And try again', 'rtcl-coupon' ) . '</p>',
			];
			wp_send_json_error( $return );
		}
		Fns::reset_coupons_session();
		$return = [
			'content' => '',
		];
		wp_send_json_success( $return );
	}

}
