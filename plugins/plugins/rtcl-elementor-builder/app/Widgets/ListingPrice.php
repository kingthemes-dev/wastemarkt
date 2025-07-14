<?php
/**
 * Main Elementor ListingPrice Class
 *
 * ListingPrice main class
 *
 * @author  RadiusTheme
 * @since   2.0.10
 * @package  RTCL_Elementor_Builder
 * @version 1.2
 */

namespace RtclElb\Widgets;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

use Rtcl\Controllers\Hooks\AppliedBothEndHooks;
use Rtcl\Helpers\Functions;
use RtclElb\Widgets\WidgetSettings\ListingPriceSettings;

/**
 * ListingPrice class
 */
class ListingPrice extends ListingPriceSettings {

	/**
	 * Construct function
	 *
	 * @param array  $data Some data.
	 * @param [type] $args some arg.
	 */
	public function __construct( $data = [], $args = null ) {
		$this->rtcl_name = __( 'Listing Price', 'rtcl-elementor-builder' );
		$this->rtcl_base = 'rt-listing-price';
		parent::__construct( $data, $args );
	}

	/**
	 *
	 * @return string
	 */
	public function add_price_type_to_price() {
		$settings = $this->get_settings();
		$price_units_type = '';
		if ( $settings['rtcl_divider_price_after'] ) {
			$price_units_type = '<span class="divider-after-price">' . $settings['rtcl_divider_price_after'] . '</span>';
		}
		if ( $settings['rtcl_show_price_unit'] ) {
			$price_units_type .= '<span class="price-unit">' . $this->listing->get_price_unit() . '</span>';
		}
		if ( $settings['rtcl_show_price_type'] ) {
			$price_units_type .= '<span class="price-type">'.$this->listing->get_price_type().'</span>';
		}
		return $price_units_type;
	}

	/**
	 * Display Output.
	 *
	 * @return mixed
	 */
	protected function render() {
		$settings = $this->get_settings();
		
		if ( $settings['rtcl_show_price_type'] ) {
			add_filter( 'rtcl_price_meta_html', [ $this, 'add_price_type_to_price' ], 20 );
		}

		$template_style = 'single/price';
		$data           = [
			'template'              => $template_style,
			'instance'              => $settings,
			'listing'               => $this->listing,
			'default_template_path' => rtclElb()->get_plugin_template_path(),
		];
		$data           = apply_filters( 'rtcl_el_listing_page_price_data', $data );
		Functions::get_template( $data['template'], $data, '', $data['default_template_path'] );
		
	}

}

