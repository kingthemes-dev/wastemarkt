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
class ListingMetaSettings extends ElementorSingleListingBase {

	/**
	 * Set style controlls
	 *
	 * @return array
	 */
	public function widget_general_fields(): array {
		$fields = array_merge(
			$this->content_visibility(),
		);
		return $fields;
	}
	/**
	 * Set style controlls
	 *
	 * @return array
	 */
	public function widget_style_fields(): array {
		$fields = array_merge(
			$this->meta_style()
		);
		return $fields;
	}

	/**
	 * Set style controlls
	 *
	 * @return array
	 */
	public function meta_style() {
		$fields = [
			[
				'mode'  => 'section_start',
				'id'    => 'rtcl_sec_meta',
				'tab'   => Controls_Manager::TAB_STYLE,
				'label' => __( 'Meta', 'rtcl-elementor-builder' ),
			],

			[
				'mode'     => 'group',
				'type'     => Group_Control_Typography::get_type(),
				'id'       => 'rtcl_meta_typo',
				'label'    => __( 'Typography', 'rtcl-elementor-builder' ),
				'selector' => '{{WRAPPER}} .rtcl-listing-meta-data li',
			],

			[
				'type'      => Controls_Manager::COLOR,
				'id'        => 'rtcl_meta_color',
				'label'     => __( 'Color', 'rtcl-elementor-builder' ),
				'selectors' => [
					'{{WRAPPER}} .rtcl-elementor-widget' => '--meta-color: {{VALUE}}',
					'{{WRAPPER}} .rtcl-listing-meta-data li' => 'color: {{VALUE}}',
				],
			],
			[
				'type'      => Controls_Manager::COLOR,
				'id'        => 'rtcl_meta_icon_color',
				'label'     => __( 'Meta Icon Color', 'rtcl-elementor-builder' ),
				'selectors' => [
					'{{WRAPPER}} .rtcl-elementor-widget' => '--meta-icon-color: {{VALUE}}',
					'{{WRAPPER}} .rtcl-listing-meta-data li i' => 'color: {{VALUE}}',
				],
			],
			[
				'type'       => Controls_Manager::SLIDER,
				'id'         => 'rtcl_meta_icon_gap',
				'label'      => esc_html__( 'Icon Gap', 'rtcl-elementor-builder' ),
				'size_units' => [ 'px' ],
				'default'    => [
					'unit' => 'px',
					'size' => 5,
				],
				'selectors'  => [
					'{{WRAPPER}} .rtcl-listing-meta-data li' => 'display: inline-flex;gap: {{SIZE}}{{UNIT}};',
				],
			],
			[
				'mode' => 'section_end',
			],
		];
		return $fields;
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
				'label' => __( 'Content Visibility ', 'rtcl-elementor-builder' ),
			],
			[
				'type'        => Controls_Manager::SWITCHER,
				'id'          => 'rtcl_show_types',
				'label'       => __( 'Show Types', 'rtcl-elementor-builder' ),
				'label_on'    => __( 'On', 'rtcl-elementor-builder' ),
				'label_off'   => __( 'Off', 'rtcl-elementor-builder' ),
				'default'     => 'yes',
				'description' => __( 'Show or Hide Types. Default: On', 'rtcl-elementor-builder' ),
			],
			[
				'type'        => Controls_Manager::SWITCHER,
				'id'          => 'rtcl_show_date',
				'label'       => __( 'Show date', 'rtcl-elementor-builder' ),
				'label_on'    => __( 'On', 'rtcl-elementor-builder' ),
				'label_off'   => __( 'Off', 'rtcl-elementor-builder' ),
				'default'     => 'yes',
				'description' => __( 'Show or Hide date. Default: On', 'rtcl-elementor-builder' ),
			],
			[
				'type'        => Controls_Manager::SWITCHER,
				'id'          => 'rtcl_show_user',
				'label'       => __( 'Show User', 'rtcl-elementor-builder' ),
				'label_on'    => __( 'On', 'rtcl-elementor-builder' ),
				'label_off'   => __( 'Off', 'rtcl-elementor-builder' ),
				'default'     => '',
				'description' => __( 'Show or Hide user. Default: On', 'rtcl-elementor-builder' ),
			],
			[
				'type'        => Controls_Manager::SWITCHER,
				'id'          => 'rtcl_show_category',
				'label'       => __( 'Show Category', 'rtcl-elementor-builder' ),
				'label_on'    => __( 'On', 'rtcl-elementor-builder' ),
				'label_off'   => __( 'Off', 'rtcl-elementor-builder' ),
				'default'     => 'yes',
				'description' => __( 'Show or Hide Category. Default: On', 'rtcl-elementor-builder' ),
			],
			[
				'type'        => Controls_Manager::SWITCHER,
				'id'          => 'rtcl_show_location',
				'label'       => __( 'Show Location', 'rtcl-elementor-builder' ),
				'label_on'    => __( 'On', 'rtcl-elementor-builder' ),
				'label_off'   => __( 'Off', 'rtcl-elementor-builder' ),
				'default'     => 'yes',
				'description' => __( 'Show or Hide Location. Default: On', 'rtcl-elementor-builder' ),
			],
			[
				'type'        => Controls_Manager::SWITCHER,
				'id'          => 'rtcl_show_views',
				'label'       => __( 'Show Views', 'rtcl-elementor-builder' ),
				'label_on'    => __( 'On', 'rtcl-elementor-builder' ),
				'label_off'   => __( 'Off', 'rtcl-elementor-builder' ),
				'default'     => 'yes',
				'description' => __( 'Show or Hide Views. Default: On', 'rtcl-elementor-builder' ),
			],
			[
				'type'       => Controls_Manager::SLIDER,
				'id'         => 'rtcl_meta_gap',
				'label'      => esc_html__( 'Gap Between Meta Items', 'rtcl-elementor-builder' ),
				'size_units' => [ 'px' ],
				'default'    => [
					'unit' => 'px',
					'size' => 10,
				],
				'selectors'  => [
					'{{WRAPPER}} .el-single-addon .rtcl-listing-meta-data' => 'gap: {{SIZE}}{{UNIT}};',
				],
			],
			[
				'type'        => Controls_Manager::SELECT,
				'id'          => 'rtcl_meta_display',
				'label'       => __( 'Display', 'rtcl-elementor-builder' ),
				'default'     => 'inline',
				'options' => [
					'inline' => esc_html__( 'Inline', 'rtcl-elementor-builder' ),
					'block' => esc_html__( 'Block', 'rtcl-elementor-builder' ),
				],
				'description' => __( 'Meta list display. Default: Inline', 'rtcl-elementor-builder' ),
			],
			[
				'mode' => 'section_end',
			],

		];
		return $fields;
	}
}