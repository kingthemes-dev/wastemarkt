<?php
/**
 * Main Elementor ListingCustomFieldsSettings Class
 *
 * ListingCustomFieldsSettings main class
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

use Elementor\{
	Controls_Manager,
	Group_Control_Border,
	Group_Control_Typography
};
use Rtcl\Helpers\Functions;
use RtclElb\Abstracts\ElementorSingleListingBase;

/**
 * ListingCustomFieldsSettings class
 */
class ListingCustomFieldsSettings extends ElementorSingleListingBase {
	/**
	 * Custom field group list
	 *
	 * @return array
	 */
	public function custom_field_group_list() {
		$group_ids = Functions::get_cfg_ids();

		$list = [
			'0' => esc_html__( 'All Group', 'rtcl-elementor-builder' ),
		];
		foreach ( $group_ids as $id ) {
			$list[ $id ] = get_the_title( $id );
		}

		return $list;
	}
	/**
	 * Set style controlls
	 *
	 * @return array
	 */
	public function widget_general_fields(): array {
		return $this->general_fields();
	}
	/**
	 * Set style controlls
	 *
	 * @return array
	 */
	public function widget_style_fields(): array {
		return $this->text_style();
	}
	/**
	 * Set style controlls
	 *
	 * @return array
	 */
	public function general_fields(): array {
		$fields = [
			[
				'mode'  => 'section_start',
				'id'    => 'rtcl_sec_general_settings',
				'label' => __( 'General Settings', 'rtcl-elementor-builder' ),
			],
			[
				'type'    => Controls_Manager::SELECT,
				'id'      => 'rtcl_dispaly_style',
				'label'   => __( 'Style', 'rtcl-elementor-builder' ),
				'options' => [
					'style-1' => __( 'Style 1', 'rtcl-elementor-builder' ),
					'style-2' => __( 'Style 2', 'rtcl-elementor-builder' ),
				],
				'default' => 'style-1',
				'description'   => __('Select Style', 'rtcl-elementor-builder'),
			],
			array(
				'type'      => Controls_Manager::SWITCHER,
				'id'        => 'rtcl_show_new_line',
				'label'     => __( 'Label and Value in New Line?', 'rtcl-elementor-builder' ),
				'label_on'  => __( 'On', 'rtcl-elementor-builder' ),
				'label_off' => __( 'Off', 'rtcl-elementor-builder' ),
				'default'   => '',
				'description'   => __('Switch to Label and value will block line', 'rtcl-elementor-builder'),
			),
			[
				'type'     => Controls_Manager::SELECT2,
				'id'       => 'custom_field_group_list',
				'label'    => __( 'Custom Field Group\'s', 'rtcl-elementor-builder' ),
				'options'  => $this->custom_field_group_list(),
				'multiple' => true,
				'default'  => array_key_first( $this->custom_field_group_list() ),
				'description'   => __('Select Specific Custom field Group', 'rtcl-elementor-builder'),

			],
			[
				'type'       => Controls_Manager::SLIDER,
				'id'         => 'column-gap',
				'label'      => esc_html__( 'Column Gap', 'rtcl-elementor-builder' ),
				'size_units' => [ 'px' ],
				'selectors'  => [
					'{{WRAPPER}} .el-single-addon .list-group' => ' --custom-field-column-gap: {{SIZE}}{{UNIT}};',
					'{{WRAPPER}} .el-single-addon .rtcl-cf-properties' => 'gap: {{SIZE}}{{UNIT}};',
					'{{WRAPPER}} .form-builder-data-wrapper' => 'gap: {{SIZE}}{{UNIT}};',
				],
			],
			
			/* === List Items === */
			[
				'type'       => Controls_Manager::HEADING,
				'id'         => 'list-item-heading',
				'label'      => esc_html__( 'List Item', 'rtcl-elementor-builder' ),
			],
			array(
				'type'      => Controls_Manager::SWITCHER,
				'id'        => 'rtcl_show_list_item_separator',
				'label'     => __( 'List item separator?', 'rtcl-elementor-builder' ),
				'label_on'  => __( 'On', 'rtcl-elementor-builder' ),
				'label_off' => __( 'Off', 'rtcl-elementor-builder' ),
				'default'   => '',
				'description'   => __('List item separator display condition.', 'rtcl-elementor-builder'),
			),
			[
				'type'       => Controls_Manager::SLIDER,
				'id'         => 'list-item-column-gap',
				'label'      => esc_html__( 'List Item Column Gap', 'rtcl-elementor-builder' ),
				'size_units' => [ 'px' ],
				'selectors'  => [
					'{{WRAPPER}} .el-single-addon.custom-field-content-area .rtcl-cf-properties .cfp-value' => 'column-gap: {{SIZE}}{{UNIT}};',
				],
				'condition' => [ 'rtcl_show_list_item_separator!' => 'yes' ],
			],
			[
				'type'       => Controls_Manager::SLIDER,
				'id'         => 'list-item-column-gap-separator',
				'label'      => esc_html__( 'List Item Column Gap', 'rtcl-elementor-builder' ),
				'size_units' => [ 'px' ],
				'selectors'  => [
					'{{WRAPPER}} .custom-field-content-area .rtcl-cf-properties .cfp-value span.delimiter' => 'margin-right: {{SIZE}}{{UNIT}};',
				],
				'condition' => [ 'rtcl_show_list_item_separator' => 'yes' ],
				'description'   => __('List item column gap added after separator.', 'rtcl-elementor-builder'),
			],
			[
				'type'       => Controls_Manager::SLIDER,
				'id'         => 'list-item-row-gap',
				'label'      => esc_html__( 'List Item Row Gap', 'rtcl-elementor-builder' ),
				'size_units' => [ 'px' ],
				'selectors'  => [
					'{{WRAPPER}} .el-single-addon.custom-field-content-area .rtcl-cf-properties .cfp-value' => 'row-gap: {{SIZE}}{{UNIT}};',
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
	public function text_style() {
		$fields = [
			[
				'mode'  => 'section_start',
				'id'    => 'description',
				'tab'   => Controls_Manager::TAB_STYLE,
				'label' => __( 'Style', 'rtcl-elementor-builder' ),
			],
			[
				'mode'     => 'group',
				'type'     => Group_Control_Typography::get_type(),
				'id'       => 'rtcl_label_typo',
				'label'    => __( 'Label Typography', 'rtcl-elementor-builder' ),
				'selector' => '{{WRAPPER}} .el-single-addon .cfp-label',
			],
			[
				'mode'     => 'group',
				'type'     => Group_Control_Typography::get_type(),
				'id'       => 'rtcl_value_typo',
				'label'    => __( 'Data Typography', 'rtcl-elementor-builder' ),
				'selector' => '{{WRAPPER}} .el-single-addon .cfp-value',
			],
			[
				'type'      => Controls_Manager::COLOR,
				'id'        => 'rtcl_label_color',
				'label'     => __( 'Label Color', 'rtcl-elementor-builder' ),
				'selectors' => [
					'{{WRAPPER}} .el-single-addon .cfp-label' => 'color: {{VALUE}};',
				],
			],
			[
				'type'      => Controls_Manager::COLOR,
				'id'        => 'rtcl_value_color',
				'label'     => __( 'Data Color', 'rtcl-elementor-builder' ),
				'selectors' => [
					'{{WRAPPER}} .el-single-addon .cfp-value' => 'color: {{VALUE}} !important;',
				],
			],
			[
				'type'           => Group_Control_Border::get_type(),
				'label'          => __( 'Border', 'rtcl-elementor-builder' ),
				'mode'           => 'group',
				'id'             => 'rtcl_listing_border',
				'fields_options' => [
					'border' => [
						'default' => 'solid',
					],
					'width'  => [
						'default' => [
							'top'      => '0',
							'right'    => '0',
							'bottom'   => '1',
							'left'     => '0',
							'isLinked' => false,
						],
					],
					'color'  => [
						'default' => 'rgba(0, 0, 0, 0.125)',
					],
				],
				'selector'       => '{{WRAPPER}} .el-single-addon .list-group-item, {{WRAPPER}} .el-single-addon .rtcl-cf-properties .rtcl-cfp-item, {{WRAPPER}} .form-builder-data-wrapper.el-single-addon .rtcl-fb-element',
			],
			[
				'mode'       => 'responsive',
				'label'      => __( 'Padding', 'rtcl-elementor-builder' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'id'         => 'rtcl_field_padding',
				'size_units' => [ 'px', 'em', '%' ],
				'selectors'  => [
					'{{WRAPPER}} .el-single-addon .list-group-item' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
					'{{WRAPPER}} .el-single-addon .rtcl-cf-properties .rtcl-cfp-item' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			],
			[
				'mode' => 'section_end',
			],
		];
		return $fields;
	}



}

