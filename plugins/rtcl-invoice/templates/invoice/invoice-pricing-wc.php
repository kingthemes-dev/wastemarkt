<?php

/**
 *
 * @author        RadiusTheme
 * @package       classified-listing/templates
 * @version       2.0.6
 *
 * @var WC_Order $order
 * @var string $pricing_type
 */

$line_items = $order->get_items( 'line_item' );
?>
<div class="invoice-pricing-info-table">
    <table style="border: 1px solid #dee2e6; width: 100%; text-align: left;">
        <tr>
            <th><?php esc_html_e( 'SL.', 'rtcl-invoices' ); ?></th>
            <th><?php esc_html_e( 'Title', 'rtcl-invoices' ); ?></th>
            <th><?php esc_html_e( 'Pricing Type', 'rtcl-invoices' ); ?></th>
            <th><?php esc_html_e( 'Price', 'rtcl-invoices' ); ?></th>
        </tr>
		<?php foreach ( $line_items as $item_id => $item ) { ?>
            <tr>
                <td><?php esc_html_e( '1', 'rtcl-invoices' ); ?></td>
                <td><?php echo esc_html( $item->get_name() ); ?></td>
                <td style="text-transform: capitalize"><?php echo esc_html( $pricing_type ); ?></td>
                <td>
					<?php
					echo wc_price( $order->get_item_subtotal( $item, false, true ), array( 'currency' => $order->get_currency() ) );
					?>
                </td>
            </tr>
		<?php } ?>
    </table>
</div>