<?php

use RtclElb\Helpers\Fns;
use RtclBooking\Helpers\Functions as BookingFunctions;
use Rtcl\Helpers\Functions;

$wrap_class = Fns::get_block_wrapper_class( $settings );


$listing_id = ! empty( $listing ) ? $listing->get_id() : $settings['listingId'];

?>
<?php if ( ! empty( $listing_id ) ) { ?>

	<?php if ( ! empty( $settings['wrapClass'] ) ) : ?>
		<div class="<?php echo esc_attr( $wrap_class ); ?>">
	<?php endif; ?>

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
	<?php if ( ! empty( $settings['wrapClass'] ) ) : ?>
		</div><?php endif; ?>
<?php } ?>