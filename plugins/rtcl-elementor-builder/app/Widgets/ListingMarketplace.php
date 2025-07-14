<?php

/**
 * Main Elementor ListingMarketplace Class
 *
 * ListingMarketplace main class
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
use RtclElb\Widgets\WidgetSettings\ListingMarketplaceSettings;

/**
 * ListingMarketplace class
 */
class ListingMarketplace extends ListingMarketplaceSettings {

	/**
	 * Construct function
	 *
	 * @param array $data Some data.
	 * @param [type] $args some arg.
	 */
	public function __construct( $data = [], $args = null ) {
		$this->rtcl_name = __( 'Marketplace: Add to cart', 'rtcl-elementor-builder' );
		$this->rtcl_base = 'rtcl-listing-marketplace-button';
		parent::__construct( $data, $args );
	}

	/**
	 * Display Output.
	 *
	 * @return mixed
	 */
	protected function render() {
		$settings = $this->get_settings();

		$template_style = 'single/marketplace';

		$data = [
			'template'              => $template_style,
			'settings'              => $settings,
			'listing'               => $this->listing,
			'default_template_path' => rtclElb()->get_plugin_template_path(),
		];

		$data = apply_filters( 'rtcl_el_listing_marketplace_data', $data );

		Functions::get_template( $data['template'], $data, '', $data['default_template_path'] );

		if ( ( empty( $this->listing ) || ! \RtclMarketplace\Helpers\Functions::is_enable_marketplace() )
			 && \Elementor\Plugin::$instance->editor->is_edit_mode()
		) {
			?>
			<p><?php echo esc_html__( "Kindly activate the Marketplace feature from Classified Listing plugin settings.",
					"rtcl-elementor-builder" ); ?></p>
			<?php
		}
	}
}
