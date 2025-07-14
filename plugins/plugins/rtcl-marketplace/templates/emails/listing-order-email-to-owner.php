<?php
/**
 * Listing order email to owner
 *
 * An email sent to the vendor when a new order is created by customer.
 *
 * @class       ListingOwnerOrderEmail
 * @version     1.0.1
 *
 * @var RtclEmail $email
 * @var object    $order
 * @var object    $items
 * @var boolean   $show_image
 * @var array     $image_size
 */

use Rtcl\Models\RtclEmail;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * @hooked RtclEmails::email_header() Output the email header
 */
do_action( 'rtcl_email_header', $email );
?>
    <p>
		<?php
		// translators: 1) order formatted billing full name
		printf( esc_html__( 'You have received an order from %s.', 'rtcl-marketplace' ), esc_html( $order->get_formatted_billing_full_name() ) );
		?>
    </p>

    <h2>
		<?php
		$before = '<a class="link" href="' . esc_url( $order->get_checkout_order_received_url() ) . '">';
		$after  = '</a>';
		/* translators: %s: Order ID. */
		echo wp_kses_post( $before . sprintf( __( '[Order #%s]', 'rtcl-marketplace' ) . $after . ' (<time datetime="%s">%s</time>)', $order->get_order_number(),
				$order->get_date_created()->format( 'c' ), wc_format_datetime( $order->get_date_created() ) ) );
		?>
    </h2>

    <div style="margin-bottom: 40px;">
        <table class="td" cellspacing="0" cellpadding="6" style="width: 100%; font-family: 'Helvetica Neue', Helvetica, Roboto, Arial, sans-serif;" border="1">
            <thead>
            <tr>
                <th class="td" scope="col"><?php esc_html_e( 'Product', 'rtcl-marketplace' ); ?></th>
                <th class="td" scope="col"><?php esc_html_e( 'Quantity', 'rtcl-marketplace' ); ?></th>
                <th class="td" scope="col"><?php esc_html_e( 'Price', 'rtcl-marketplace' ); ?></th>
            </tr>
            </thead>
            <tbody>
			<?php
			foreach ( $items as $item_id => $item ) :
				$product = $item->get_product();
				$image = '';

				if ( ! apply_filters( 'woocommerce_order_item_visible', true, $item ) ) {
					continue;
				}

				if ( is_object( $product ) ) {
					$image = $product->get_image( $image_size );
				}

				?>
                <tr class="<?php echo esc_attr( apply_filters( 'woocommerce_order_item_class', 'order_item', $item, $order ) ); ?>">
                    <td class="td"
                        style="vertical-align: middle; font-family: 'Helvetica Neue', Helvetica, Roboto, Arial, sans-serif; word-wrap:break-word;">
						<?php

						// Show title/image etc.
						if ( $show_image ) {
							echo wp_kses_post( apply_filters( 'woocommerce_order_item_thumbnail', $image, $item ) );
						}

						// Product name.
						echo wp_kses_post( apply_filters( 'woocommerce_order_item_name', $item->get_name(), $item, false ) );

						// allow other plugins to add additional product information here.
						do_action( 'woocommerce_order_item_meta_start', $item_id, $item, $order, false );

						wc_display_item_meta(
							$item,
							array(
								'label_before' => '<strong class="wc-item-meta-label" style="float: left; margin-right: .25em; clear: both">',
							)
						);

						// allow other plugins to add additional product information here.
						do_action( 'woocommerce_order_item_meta_end', $item_id, $item, $order, false );

						?>
                    </td>
                    <td class="td" style="vertical-align:middle; font-family: 'Helvetica Neue', Helvetica, Roboto, Arial, sans-serif;">
						<?php
						$qty          = $item->get_quantity();
						$refunded_qty = $order->get_qty_refunded_for_item( $item_id );

						if ( $refunded_qty ) {
							$qty_display = '<del>' . esc_html( $qty ) . '</del> <ins>' . esc_html( $qty - ( $refunded_qty * - 1 ) ) . '</ins>';
						} else {
							$qty_display = esc_html( $qty );
						}
						echo wp_kses_post( apply_filters( 'woocommerce_email_order_item_quantity', $qty_display, $item ) );
						?>
                    </td>
                    <td class="td" style="vertical-align:middle; font-family: 'Helvetica Neue', Helvetica, Roboto, Arial, sans-serif;">
						<?php echo wp_kses_post( $order->get_formatted_line_subtotal( $item ) ); ?>
                    </td>
                </tr>
			<?php endforeach; ?>
            </tbody>
            <tfoot>
			<?php
			$item_totals = $order->get_order_item_totals();

			if ( $item_totals ) {
				$i = 0;
				foreach ( $item_totals as $total ) {
					$i ++;
					?>
                    <tr>
                        <th class="td" scope="row" colspan="2"
                            style="<?php echo ( 1 === $i ) ? 'border-top-width: 4px;'
							    : ''; ?>"><?php echo wp_kses_post( $total['label'] ); ?></th>
                        <td class="td" style="<?php echo ( 1 === $i ) ? 'border-top-width: 4px;'
							: ''; ?>"><?php echo wp_kses_post( $total['value'] ); ?></td>
                    </tr>
					<?php
				}
			}
			if ( $order->get_customer_note() ) {
				?>
                <tr>
                    <th class="td" scope="row" colspan="2"><?php esc_html_e( 'Note:', 'rtcl-marketplace' ); ?></th>
                    <td class="td"><?php echo wp_kses_post( nl2br( wptexturize( $order->get_customer_note() ) ) ); ?></td>
                </tr>
				<?php
			}
			?>
            </tfoot>
        </table>
    </div>
<?php

wc()->mailer()->customer_details( $order );
wc()->mailer()->email_addresses( $order );

/**
 * @hooked RtclEmails::email_footer() Output the email footer
 */
do_action( 'rtcl_email_footer', $email );
