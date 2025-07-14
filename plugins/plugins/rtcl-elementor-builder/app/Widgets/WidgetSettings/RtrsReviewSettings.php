<?php
/**
 * Main Elementor ListingReviewSettings Class
 *
 * ListingReviewSettings main class
 *
 * @author  RadiusTheme
 *
 * @since   2.0.10
 *
 * @version 1.2
 */

namespace RtclElb\Widgets\WidgetSettings;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

use Elementor\Controls_Manager;
use RtclElb\Abstracts\ElementorSingleListingBase;

/**
 * ListingReviewSettings class
 */
class RtrsReviewSettings extends ElementorSingleListingBase {
	/**
	 * Set style controlls
	 */
	public function widget_general_fields(): array {
		return [
			[
				'mode'  => 'section_start',
				'id'    => 'rtcl_sec_content_visibility',
				'label' => __( 'General', 'rtcl-elementor-builder' ),
			],
			[
				'type'      => Controls_Manager::RAW_HTML,
				'id'        => 'rtcl_el_badge_sold_note',
				'separator' => 'before',
				'raw'       => sprintf(
					'<h3 class="rtcl-elementor-group-heading">%s</h3>',
					__( 'Review schema widgets are provided by the Review Schema plugin and come pre-designed.', 'rtcl-elementor-builder' )
				),
			],
			[
				'mode' => 'section_end',
			],
		];
	}

	/**
	 * Set style controlls
	 */
	public function widget_style_fields(): array {
		return [];
	}
}
