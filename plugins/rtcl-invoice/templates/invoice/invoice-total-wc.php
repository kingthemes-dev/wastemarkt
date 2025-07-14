<?php

/**
 *
 * @author        RadiusTheme
 * @package       classified-listing/templates
 * @version       2.0.6
 *
 * @var WC_Order $order
 */

?>
<div class="invoice-total">
    <table>
        <tr>
            <th><?php esc_html_e( 'Sub Total:', 'rtcl-invoices' ); ?></th>
            <td>
				<?php echo wc_price( $order->get_subtotal(), array( 'currency' => $order->get_currency() ) ); ?>
            </td>
        </tr>
		<?php if ( 0 < $order->get_total_discount() ) : ?>
            <tr>
                <th><?php esc_html_e( 'Coupon(s):', 'rtcl-invoices' ); ?></th>
                <td>-
					<?php echo wc_price( $order->get_total_discount(), array( 'currency' => $order->get_currency() ) ); ?>
                </td>
            </tr>
		<?php endif; ?>
		<?php if ( wc_tax_enabled() ) : ?>
			<?php foreach ( $order->get_tax_totals() as $code => $tax_total ) : ?>
                <tr>
                    <th><?php echo esc_html( $tax_total->label ); ?>:</th>
                    <td>
						<?php
						// We use wc_round_tax_total here because tax may need to be round up or round down depending upon settings, whereas wc_price alone will always round it down.
						echo wc_price( wc_round_tax_total( $tax_total->amount ), array( 'currency' => $order->get_currency() ) );
						?>
                    </td>
                </tr>
			<?php endforeach; ?>
		<?php endif; ?>
        <tr>
            <th><?php esc_html_e( 'Order Total:', 'rtcl-invoices' ); ?></th>
            <td><?php echo wc_price( $order->get_total(), array( 'currency' => $order->get_currency() ) ); ?></td>
        </tr>
    </table>
</div>