<?php

/**
 *
 * @author        RadiusTheme
 * @package       classified-listing/templates
 * @version       2.0.6
 *
 * @var Payment $order
 * @var string $pricing_type
 */

use Rtcl\Helpers\Functions;
use Rtcl\Models\Payment;
use RadiusTheme\COUPON\Models\Couponlookup;

$total = $order->get_total();

if ( class_exists( '\RadiusTheme\COUPON\Models\Couponlookup' ) ) {
	$order_id   = $order->get_id();
	$coupon     = new Couponlookup( $order_id );
	$lookupdata = $coupon->get_applied_data();
	// get_coupon_lookup
	$coupon_summary = ! empty( $lookupdata['coupon_summary'] ) ? maybe_unserialize( $lookupdata['coupon_summary'] ) : [];
	$total          = ! empty( $coupon_summary['subtotal'] ) ? $coupon_summary['subtotal'] : $order->get_total();
}
?>
<div class="invoice-pricing-info-table">
    <table style="border: 1px solid #dee2e6; width: 100%; text-align: left;">
        <tr>
            <th><?php esc_html_e( 'SL.', 'rtcl-invoices' ); ?></th>
            <th><?php esc_html_e( 'Title', 'rtcl-invoices' ); ?></th>
            <th><?php esc_html_e( 'Pricing Type', 'rtcl-invoices' ); ?></th>
            <th><?php esc_html_e( 'Price', 'rtcl-invoices' ); ?></th>
        </tr>
        <tr>
            <td><?php esc_html_e( '1', 'rtcl-invoices' ); ?></td>
            <td><?php echo esc_html( $order->pricing->getTitle() ); ?></td>
            <td style="text-transform: capitalize"><?php echo esc_html( $pricing_type ); ?></td>
            <td><?php echo Functions::get_payment_formatted_price_html( $total ); ?></td>
        </tr>
    </table>
</div>