<?php

/**
 * Main Elementor ListingBooking Class
 *
 * ListingBooking main class
 *
 * @author   RadiusTheme
 * @since    2.0.10
 * @package  RTCL_Elementor_Builder
 * @version  1.2
 */

namespace RtclElb\Widgets;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

use Rtcl\Helpers\Functions;
use RtclElb\Widgets\WidgetSettings\ListingBookingSettings;

/**
 * ListingBooking class
 */
class ListingBooking extends ListingBookingSettings {

	/**
	 * Construct function
	 *
	 * @param array  $data Some data.
	 * @param [type] $args some arg.
	 */
	public function __construct( $data = [], $args = null ) {
		$this->rtcl_name = __( 'Listing Booking', 'rtcl-elementor-builder' );
		$this->rtcl_base = 'rt-listing-booking';
		parent::__construct( $data, $args );
	}

	/**
	 * Display Output.
	 *
	 * @return mixed
	 */
	protected function render() {
		$settings = $this->get_settings();

		$template_style = 'single/booking';

		$data = [
			'template'              => $template_style,
			'settings'              => $settings,
			'listing'               => $this->listing,
			'default_template_path' => rtclElb()->get_plugin_template_path(),
		];

		$data = apply_filters( 'rtcl_el_listing_booking_data', $data );

		Functions::get_template( $data['template'], $data, '', $data['default_template_path'] );

		if ( ( empty( $this->listing ) || ! \RtclBooking\Helpers\Functions::is_active_booking( $this->listing->get_id() )
			   || ! \RtclBooking\Helpers\Functions::is_enable_booking() )
			 && \Elementor\Plugin::$instance->editor->is_edit_mode()
		) {
			?>
			<p>
			<?php
			echo esc_html__(
				'Please activate the booking feature from the Classified Listing plugin settings. Then check whether the booking is available for the corresponding listing. If a listing has booking enabled, the booking form will be displayed on the listing details page.',
				'rtcl-elementor-builder'
			);
			?>
					</p>
			<?php
		}
	}
}
