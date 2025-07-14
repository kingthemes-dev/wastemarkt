<?php
/**
 * Modal
 *
 * @author     RadiusTheme
 * @package    rtcl-elementor-builder/templates
 * @version    1.0.0
 *
 * @var Store  $store
 * @var string $store_oh_type
 * @var array  $store_oh_hours
 * @var string $today
 */

use Rtcl\Helpers\Functions;
use RtclStore\Models\Store;
use RtclStore\Resources\Options;
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}
if ( empty( $store ) ) {
	global $store;
}

$store_oh_type  = get_post_meta( $store->get_id(), 'oh_type', true );
$store_oh_hours = get_post_meta( $store->get_id(), 'oh_hours', true );
$store_oh_hours = is_array( $store_oh_hours ) ? $store_oh_hours : ( $store_oh_hours ? (array) $store_oh_hours : [] );
$today          = strtolower( date( 'l' ) );
$days           = Options::store_open_hour_days();
?>
<!-- Modal -->
<div class="rtcl-popup-wrapper" id="rtcl-store-details-modal">
	<div class="rtcl-popup">
		<div class="rtcl-popup-content">
			<div class="rtcl-popup-header">
				<h5 class="rtcl-popup-title" id="rtcl-report-abuse-modal-label">
					<?php esc_html_e( 'Open Hours', 'rtcl-elementor-builder' ); ?>
				</h5>
				<a href="#" class="rtcl-popup-close">×</a>
			</div>
			<div class="rtcl-popup-body">
				<div class="store-more-details">
					<div class="more-item store-hours-list-wrap">
						<div class="store-hours-list">
							<?php if ( $store_oh_type == "selected" ): ?>
								<?php if ( is_array( $store_oh_hours ) && ! empty( $store_oh_hours ) ): ?>
									<?php foreach ( $store_oh_hours as $hKey => $oh_hour ): ?>
										<div class="store-hour<?php echo esc_attr( ( strtolower( $days[ $hKey ] ?? $hKey ) == $today )
											? ' current-store-hour' : '' ); ?>">
											<div class="col-day">
												<span class="hour-day"><?php echo esc_html( $days[ $hKey ] ?? $hKey ); ?></span>
											</div>
											<div class="col-time oh-hours-wrap">
												<?php if ( isset( $oh_hour['active'] ) ): ?>
													<div class="oh-hours">
                                                        <span class="open-hour"><?php echo isset( $oh_hour['open'] ) ? esc_html( $oh_hour['open'] )
																: ''; ?></span>
														<span class="close-hour"><?php echo isset( $oh_hour['close'] ) ? esc_html( $oh_hour['close'] )
																: ''; ?></span>
													</div>
												<?php else: ?>
													<span class="off-day"><?php esc_html_e( "Closed", "classified-listing-store" ) ?></span>
												<?php endif; ?>
											</div>
										</div>
									<?php endforeach; ?>
								<?php else: ?>
									<div class="always-open"><?php esc_html_e( "Permanently Close", "classified-listing-store" ) ?></div>
								<?php endif; ?>
							<?php elseif ( $store_oh_type == 'always' ): ?>
								<div class="always-open"><?php esc_html_e( "Always Open", "classified-listing-store" ) ?></div>
							<?php endif; ?>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>
