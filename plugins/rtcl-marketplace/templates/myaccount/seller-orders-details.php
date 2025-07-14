<?php
/**
 *
 * @author        RadiusTheme
 * @package       classified-listing/templates
 * @version       3.1.16
 *
 * @var $order_id
 * @var $user_id
 */

if ( ! $order_id ) {
	return;
}

$order = wc_get_order( $order_id );
?>
<div class="rtcl-payment-history-wrap">
	<?php
	if ( is_a( $order, 'WC_Order' ) ) {
	$order_items        = $order->get_items( apply_filters( 'woocommerce_purchase_order_item_types', 'line_item' ) );
	$show_purchase_note = $order->has_status( apply_filters( 'woocommerce_purchase_note_order_statuses', array( 'completed', 'processing' ) ) );
	?>
    <div class="rtcl-payment-table-wrap rtcl-MyAccount-content-inner">
        <p>
			<?php
			printf(
			/* translators: 1: order number 2: order date 3: order status */
				esc_html__( 'Order #%1$s was placed on %2$s and is currently %3$s.', 'rtcl-marketplace' ),
				'<mark class="order-number">' . $order->get_order_number() . '</mark>',
				'<mark class="order-date">' . wc_format_datetime( $order->get_date_created() ) . '</mark>',
				// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
				'<mark class="order-status">' . wc_get_order_status_name( $order->get_status() )
				. '</mark>'
			);
			?>
        </p>
		<?php do_action( 'woocommerce_order_details_before_order_table', $order ); ?>
        <h3 class="woocommerce-order-details__title"><?php esc_html_e( 'Order details', 'rtcl-marketplace' ); ?></h3>
        <table class="woocommerce-table woocommerce-table--order-details shop_table order_details">

            <thead>
            <tr>
                <th class="woocommerce-table__product-name product-name"><?php esc_html_e( 'Product', 'woocommerce' ); ?></th>
                <th class="woocommerce-table__product-table product-total"><?php esc_html_e( 'Total', 'woocommerce' ); ?></th>
            </tr>
            </thead>

            <tbody>
			<?php
			do_action( 'woocommerce_order_details_before_order_table_items', $order );

			foreach ( $order_items as $item_id => $item ) {
				$product = $item->get_product();

				wc_get_template(
					'order/order-details-item.php',
					array(
						'order'              => $order,
						'item_id'            => $item_id,
						'item'               => $item,
						'show_purchase_note' => $show_purchase_note,
						'purchase_note'      => $product ? $product->get_purchase_note() : '',
						'product'            => $product,
					)
				);
			}

			do_action( 'woocommerce_order_details_after_order_table_items', $order );
			?>
            </tbody>

            <tfoot>
			<?php
			foreach ( $order->get_order_item_totals() as $key => $total ) {
				?>
                <tr>
                    <th scope="row"><?php echo esc_html( $total['label'] ); ?></th>
                    <td><?php echo wp_kses_post( $total['value'] ); ?></td>
                </tr>
				<?php
			}
			?>
			<?php if ( $order->get_customer_note() ) : ?>
                <tr>
                    <th><?php esc_html_e( 'Note:', 'woocommerce' ); ?></th>
                    <td><?php echo wp_kses( nl2br( wptexturize( $order->get_customer_note() ) ), array() ); ?></td>
                </tr>
			<?php endif; ?>
            </tfoot>
        </table>
		<?php do_action( 'woocommerce_order_details_after_order_table', $order ); ?>

        <div class="marketplace-billing-shipping-address">
            <div class="order-billing-address">
                <h4><?php esc_html_e( 'Billing Address', 'rtcl-marketplace' ); ?></h4>
                <address>
					<?php
					if ( $order->get_formatted_billing_address() ) {
						echo wp_kses_post( $order->get_formatted_billing_address() );
					} else {
						esc_html_e( 'No billing address set.', 'rtcl-marketplace' );
					}
					?>
                </address>
            </div>
            <address class="order-shipping-address">
                <h4><?php esc_html_e( 'Shipping Address', 'rtcl-marketplace' ); ?></h4>
                <address>
					<?php
					if ( $order->get_formatted_shipping_address() ) {
						echo wp_kses_post( $order->get_formatted_shipping_address() );
					} else {
						esc_html_e( 'No shipping address set.', 'rtcl-marketplace' );
					}
					?>
                </address>
        </div>
        <div class="marketplace-order-note-wrap">
            <h4><?php esc_html_e( 'Order Notes', 'rtcl-marketplace' ); ?></h4>
			<?php
			$args = [
				'post_id' => $order_id,
				'approve' => 'approve',
				'type'    => 'order_note',
				'status'  => 1,
			];

			remove_filter( 'comments_clauses', array( 'WC_Comments', 'exclude_order_comments' ), 10, 1 );

			$notes = get_comments( $args );

			if ( $notes ) {
				?>
                <div class="marketplace-order-note-list">
                    <ul>
						<?php
						foreach ( $notes as $note ) {
							$note_classes = get_comment_meta( $note->comment_ID, 'is_customer_note', true ) ? array( 'customer-note', 'note' )
								: array( 'note' );
							?>
                            <li rel="<?php echo esc_attr( absint( $note->comment_ID ) ); ?>" class="<?php echo esc_attr( implode( ' ', $note_classes ) ); ?>">
                                <div class="note-content">
									<?php echo wp_kses_post( wpautop( wptexturize( $note->comment_content ) ) ); ?>
                                </div>
                                <p class="meta">
									<?php
									printf( esc_html__( 'Added %s ago', 'rtcl-marketplace' ),
										human_time_diff( strtotime( $note->comment_date_gmt ), current_time( 'timestamp', true ) ) );
									?>
                                </p>
                            </li>
						<?php } ?>
                    </ul>
                </div>
				<?php
			}

			add_filter( 'comments_clauses', array( 'WC_Comments', 'exclude_order_comments' ), 10, 1 );
			?>
            <div class="marketplace-order-note-form-wrap">
                <h4><?php esc_html_e( 'Add Note', 'rtcl-marketplace' ); ?></h4>
                <form class="marketplace-order-note-form">
                    <div class="rtcl-form-group">
                        <textarea id="order-note-content" name="note" class="rtcl-form-control" cols="19" rows="3"></textarea>
                    </div>
                    <div class="rtcl-form-group">
                        <select name="note_type" id="order-note-type" class="rtcl-form-control">
                            <option value="customer"><?php esc_html_e( 'Note to Customer', 'rtcl-marketplace' ); ?></option>
                            <option value=""><?php esc_html_e( 'Note to Admin', 'rtcl-marketplace' ); ?></option>
                        </select>
                    </div>
                    <input type="hidden" name="post_id" value="<?php echo esc_attr( $order_id ); ?>">
                    <input type="hidden" name="user_id" value="<?php echo esc_attr( get_current_user_id() ); ?>">
                    <input type="submit" name="add_order_note" class="rtcl-btn" value="<?php esc_attr_e( 'Add Note', 'rtcl-marketplace' ); ?>">
                </form>
            </div>
        </div>
    </div>
</div>
<?php

} else {
	echo '<span>' . esc_html__( 'No data found.', 'rtcl-marketplace' ) . '</span>';
} ?>

</div>