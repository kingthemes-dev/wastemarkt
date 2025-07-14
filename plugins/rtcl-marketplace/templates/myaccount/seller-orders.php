<?php
/**
 *
 * @author        RadiusTheme
 * @package       classified-listing/templates
 * @version       3.1.16
 *
 * @var object $orders
 * @var int    $user_id
 * @var int    $pages
 * @var int    $post_per_page
 * @var int    $current_page
 */

use Rtcl\Helpers\Link;
use Rtcl\Helpers\Functions;

$details_url = rtrim( Link::get_account_endpoint_url( 'marketplace-orders' ), '/' );
?>
<div class="rtcl-payment-history-wrap">
	<?php
	if ( ! empty( $orders ) ) {
		?>
        <div class="rtcl-payment-table-wrap rtcl-MyAccount-content-inner">
            <h3><?php esc_html_e( 'Order History', 'rtcl-marketplace' ); ?></h3>
            <div class="rtcl-table-scroll-x rtcl-table-responsive-list">
                <table class="rtcl-table-striped-border">
                    <thead>
                    <tr>
                        <th><?php esc_html_e( '#', 'rtcl-marketplace' ); ?></th>
                        <th><?php esc_html_e( 'Total', 'rtcl-marketplace' ); ?></th>
                        <th><?php esc_html_e( 'Earning', 'rtcl-marketplace' ); ?></th>
                        <th><?php esc_html_e( 'Date', 'rtcl-marketplace' ); ?></th>
                        <th><?php esc_html_e( 'Status', 'rtcl-marketplace' ); ?></th>
                    </tr>
                    </thead>

                    <!-- the loop -->
					<?php foreach ( $orders as $order ) {
						$wc_order = wc_get_order( $order['order_id'] );
						$view_url = $details_url . '/' . $wc_order->get_id();
						?>
                        <tr>
                            <td data-heading="<?php esc_attr_e( 'Order ID:', 'rtcl-marketplace' ); ?>">
                                <a href="<?php echo esc_url( $view_url ); ?>">
									<?php echo $wc_order->get_order_number(); ?>
                                </a>
                            </td>
                            <td data-heading="<?php esc_attr_e( 'Total:', 'rtcl-marketplace' ); ?>">
								<?php echo wp_kses_post( $wc_order->get_formatted_order_total() ); ?>
                            </td>
                            <td data-heading="<?php esc_attr_e( 'Earning:', 'rtcl-marketplace' ); ?>">
								<?php echo wc_price( $order['seller_earning'] ); ?>
                            </td>
                            <td data-heading="<?php esc_attr_e( 'Date:', 'rtcl-marketplace' ); ?>">
								<?php echo esc_html( wc_format_datetime( $wc_order->get_date_created() ) ); ?>
                            </td>
                            <td data-heading="<?php esc_attr_e( 'Status:', 'rtcl-marketplace' ); ?>">
								<?php echo esc_html( wc_get_order_status_name( $order['order_status'] ) ); ?>
                            </td>
                        </tr>
					<?php } ?>
                </table>
            </div>
        </div>
		<?php
		if ( $pages > 1 ):
			Functions::get_template( 'marketplace/pagination',
				[
					'post_per_page' => $post_per_page,
					'pages'         => $pages,
					'current_page'  => $current_page
				], '', rtcl_marketplace()->get_plugin_template_path() );
		endif;
	} else {
		echo '<span class="no-results">' . esc_html__( 'No Results Found.', 'rtcl-marketplace' ) . '</span>';
	} ?>

</div>