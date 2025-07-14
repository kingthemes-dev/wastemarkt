<?php
/**
 * Main Elementor ListingMetaSettings Class
 *
 * ListingMetaSettings main class
 *
 * @author  RadiusTheme
 * @since   2.0.10
 * @package  RTCL_Elementor_Builder
 * @version 1.2
 */

namespace RtclElb\Widgets\WidgetSettings;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

use Elementor\Controls_Manager;
use RtclElb\Abstracts\ElementorSingleListingBase;
use \Elementor\Group_Control_Typography;

/**
 * ListingMetaSettings class
 */
class FeaturesListSettings extends ElementorSingleListingBase {

	/**
	 * Set style controlls
	 *
	 * @return array
	 */
	public function widget_general_fields(): array {
		return $this->content_visibility();
	}

	/**
	 * Set style controlls
	 *
	 * @return array
	 */
	public function widget_style_fields(): array {
		return [];
	}

	/**
	 * Set style controlls
	 *
	 * @return array
	 */
	public function content_visibility() {
		$fields = [
			[
				'mode'  => 'section_start',
				'id'    => 'rtcl_sec_content_visibility',
				'label' => __( 'Content Visibility', 'rtcl-elementor-builder' ),
			],
			[
				'type'        => Controls_Manager::SWITCHER,
				'id'          => 'rtcl_show_title',
				'label'       => __( 'Show Title', 'rtcl-elementor-builder' ),
				'label_on'    => __( 'On', 'rtcl-elementor-builder' ),
				'label_off'   => __( 'Off', 'rtcl-elementor-builder' ),
				'default'     => 'yes',
				'description' => __( 'Switch to Show Title', 'rtcl-elementor-builder' ),
			],
			[
				'type'       => Controls_Manager::SLIDER,
				'id'         => 'features_list_gap',
				'label'      => esc_html__( 'Items Gap', 'rtcl-elementor-builder' ),
				'size_units' => [ 'px' ],
				'selectors'  => [
					'{{WRAPPER}} .classima-listing-single .classima-single-details .rtin-specs .rtin-spec-items' => 'gap: {{SIZE}}{{UNIT}};',
				],
			],
			[
				'mode'            => 'responsive',
				'id'              => 'features_list_column',
				'label'           => esc_html__( 'List Column', 'rtcl-elementor-builder' ),
				'description'     => esc_html__( 'The control only work for grid view. This field not effect for product per page.', 'rtcl-elementor-builder' ),
				'type'            => Controls_Manager::NUMBER,
				'min'             => 1,
				'max'             => 10,
				'step'            => 1,
				'default'         => 2,
				'devices'         => [ 'desktop', 'tablet', 'mobile' ],
				'desktop_default' => 2,
				'tablet_default'  => 2,
				'mobile_default'  => 1,
				'selectors'       => [
					'{{WRAPPER}} .classima-listing-single .classima-single-details .rtin-specs .rtin-spec-items li' => 'width: calc(100%/{{VALUE}} - ( {{features_list_gap.size}}{{features_list_gap.unit}} / {{VALUE}} ) *  ({{VALUE}} - 1 ) );',
				],
			],
			[
				'mode' => 'section_end',
			],

		];
		return $fields;
	}


}

