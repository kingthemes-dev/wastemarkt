<?php

/**
 * @author     RadiusTheme
 * @package    classified-listing/templates
 * @version    1.0.0
 *
 * @var \Rtcl\Models\Listing $listing
 */

use RtclBooking\Helpers\Functions as BookingFunctions;
use Rtcl\Helpers\Functions;

$listing_id = $listing->get_id();
$wrapClass  = 'rtcl rtcl-listing-booking el-single-addon';
if ( \Elementor\Plugin::$instance->editor->is_edit_mode() ) {
	$wrapClass .= ' widget';
}

if ( empty( $settings['rtcl_show_heading'] ) ) {
	$wrapClass .= ' rtcl-title-hide';
}
if ( empty( $settings['rtcl_show_heading_indicator'] ) ) {
	$wrapClass .= ' rtcl-title-hide-indicator';
}

?>
<div class="<?php echo esc_attr( $wrapClass ); ?>">
	<?php
	$post_status = get_post_status( $listing_id );

	if ( ( BookingFunctions::is_active_booking( $listing_id ) && BookingFunctions::is_enable_booking() ) && 'publish' === $post_status ) {
		$type = BookingFunctions::get_booking_type( $listing_id );
		if ( ! empty( $type ) ) {
			Functions::get_template(
				'booking/listing-booking-form',
				[
					'type'       => $type,
					'listing_id' => $listing_id
				],
				'',
				rtclBooking()->get_plugin_template_path()
			);
		}
	} ?>
</div>