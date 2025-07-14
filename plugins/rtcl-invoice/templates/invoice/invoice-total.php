<?php

/**
 *
 * @author        RadiusTheme
 * @package       classified-listing/templates
 * @version       2.0.6
 *
 * @var Payment $order
 */

use Rtcl\Helpers\Functions;
use RadiusTheme\COUPON\Models\Couponlookup;

$subtotal = $order->get_total();
$discount = 0;

if ( class_exists( '\RadiusTheme\COUPON\Models\Couponlookup' ) ) {
	$order_id   = $order->get_id();
	$coupon     = new Couponlookup( $order_id );
	$lookupdata = $coupon->get_applied_data();
	// get_coupon_lookup
	$coupon_summary = ! empty( $lookupdata['coupon_summary'] ) ? maybe_unserialize( $lookupdata['coupon_summary'] ) : [];
	$subtotal       = ! empty( $coupon_summary['subtotal'] ) ? $coupon_summary['subtotal'] : $order->get_total();
	$discount       = ! empty( $coupon_summary['discount_total'] ) ? $coupon_summary['discount_total'] : 0;
}
?>
<div class="invoice-total">
    <table>
        <tr>
            <th><?php esc_html_e( 'Sub Total:', 'rtcl-invoices' ); ?></th>
            <td><?php echo Functions::get_payment_formatted_price_html( $subtotal ); ?></td>
        </tr>
        <tr>
            <th><?php esc_html_e( 'Coupon(s):', 'rtcl-invoices' ); ?></th>
            <td>- <?php echo Functions::get_payment_formatted_price_html( $discount ); ?></td>
        </tr>
        <tr>
            <th><?php esc_html_e( 'Order Total:', 'rtcl-invoices' ); ?></th>
            <td><?php echo Functions::get_payment_formatted_price_html( $order->get_total() ); ?></td>
        </tr>
    </table>
</div>