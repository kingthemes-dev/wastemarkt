<?php
/**
 * @var $customer_orders ;
 * @var $user_id         ;
 */

use RtclMarketplace\Helpers\Functions;

// @formatter:off
// phpcs:disable
?>
<div class="rtcl-my-download-area">
    <div class="rtcl-download-items">
        <table class="rtcl-my-listing-table">
			<?php
			$loop1                = 1;
			$download_empty       = [];
			foreach ( $customer_orders as $customer_order ) :
				$orders = $customer_order->get_items();
				$orders_id        = $customer_order->get_id();
				$loop2            = 1;
				foreach ( $orders as $item ) :
					$listing_id = wc_get_order_item_meta( $item->get_id(), 'rtcl_listing_id' );
					$my_downlaods = get_post_meta( $listing_id, 'rtcl_download_files', true );

					$download_empty[] = empty( $my_downlaods );

					if ( 1 == $loop1 && 1 == $loop2 && ! empty( $my_downlaods ) ) {
						?>
                        <thead>
                        <tr>
                            <th width="30%"><?php echo esc_html__( 'Product Name', 'rtcl-marketplace' ); ?></th>
                            <th><?php echo esc_html__( 'File Name', 'rtcl-marketplace' ); ?></th>
                            <th width="160px"><?php echo esc_html__( 'Download', 'rtcl-marketplace' ); ?></th>
                        </tr>
                        </thead>
						<?php
					}

					if ( ! empty( $my_downlaods ) ) { ?>
                        <tbody>
						<?php
						$index = 1;
						foreach ( $my_downlaods as $downlaod ) {
							$secure_link = Functions::generate_secure_download_link( $downlaod['url'], $user_id ); ?>
                            <tr>
								<?php if ( 1 == $index ) : ?>
                                    <td rowspan="0">
                                        <a href="<?php echo esc_url( get_permalink( $listing_id ) ); ?>"><?php echo esc_html( '#' . $orders_id . ' - '
										                                                                                      . $item->get_name() ); ?></a>
                                    </td>
								<?php endif; ?>
                                <td><?php echo esc_html( $downlaod['title'] ); ?></td>
                                <td><a class="btn btn-download-file"
                                       href="<?php echo esc_url( $secure_link ); ?>"><?php echo esc_html__( 'Download', 'rtcl-marketplace' ); ?></a>
                                </td>
                            </tr>
							<?php
							$index ++;
						}
						?>
                        </tbody>
						<?php
					}
					$loop2 ++;
				endforeach;
				$loop1 ++;
			endforeach;
			?>
        </table>
		<?php
		if ( ! in_array( false, $download_empty, true ) === true ) {
			esc_html_e( 'There is nothing to download.', 'rtcl-marketplace' );
		}
		?>
    </div>
</div>