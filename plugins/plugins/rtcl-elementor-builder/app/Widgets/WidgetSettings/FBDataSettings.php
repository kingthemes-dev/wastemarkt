<?php
/**
 * Main Elementor ListingBusinessHoursSettings Class
 *
 * ListingBusinessHoursSettings main class
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

use RtclElb\Helpers\Fns;
use RtclElb\Abstracts\ElementorSingleListingBase2;

/**
 * ListingBusinessHoursSettings class
 */
class FBDataSettings extends ElementorSingleListingBase2 {

	/**
	 * Set style controlls
	 *
	 * @return array
	 */
	public function widget_general_fields(): array {
		return $this->general_fields() + $this->general_repeater();
	}
	/**
	 * Set style controlls
	 *
	 * @return array
	 */
	public function widget_style_fields(): array {
		return $this->general_style() + $this->list_item_style() + $this->image_file_style() + $this->repeater_control();
	}
	/**
	 * Set style controlls
	 *
	 * @return array
	 */
	public function general_fields() {
		//return [];;
		$defaults    = [];
		$keyVal      = [];
		$customfield = Fns::formBuilderData( $this->listing );
		if ( ! empty( $customfield ) ) {
			foreach ( $customfield as $key => $value ) {
				$keyVal[ $key ] = $value['label'] ?? '';
				$defaults[]     = [
					'select_form_data_fields_name'         => $key,
					'select_form_data_fields_label'        => $value['label'],
					'show_in_frontend'                     => 'yes',
					'select_form_data_fields_element_type' => 'url' === ( $value['element'] ?? '' ) ? 'url' : 'others',
				];
			}
		}
		return [
			'rtcl_fbdata_settings'                 => [
				'mode'  => 'section_start',
				'label' => __( 'General Settings', 'rtcl-elementor-builder' ),
			],
			'show_icon'                            => [
				'type'        => 'switch',
				'label'       => __( 'Show Icon', 'rtcl-elementor-builder' ),
				'label_on'    => __( 'Hide', 'rtcl-elementor-builder' ),
				'label_off'   => __( 'Show', 'rtcl-elementor-builder' ),
				'default'     => 'yes',
				'description' => __( 'Switch to show/hide.', 'rtcl-elementor-builder' ),
			],
			'fb_general_item_column'               => [
				'type'       => 'slider',
				'label'      => esc_html__( 'Default Element Column', 'rtcl-elementor-builder' ),
				'size_units' => [ 'px' ],
				'range'      => [
					'px' => [
						'min'  => 1,
						'max'  => 10,
						'step' => 1,
					],
				],
				'default'    => [
					'unit' => 'px',
					'size' => 1,
				],
				'selectors'  => [
					'{{WRAPPER}} .el-single-addon.form-builder-data-wrapper' => '--element-columns: {{SIZE}};',
				],
			],
			'fb_general_item_gap'                  => [
				'type'       => 'slider',
				'label'      => esc_html__( 'Item Gap', 'rtcl-elementor-builder' ),
				'size_units' => [ 'px' ],
				'selectors'  => [
					'{{WRAPPER}} .el-single-addon.form-builder-data-wrapper' => '--element-gap: {{SIZE}}{{UNIT}};',
				],
			],
			'custom_form_field_data_note'          => [
				'type'            => 'html',
				'raw'             => sprintf(
					'<h3 class="rtcl-elementor-group-heading">%s</h3>',
					__( 'Add or remove items as needed, and organize them into sections based on data type, or you can organize as you wish.', 'rtcl-elementor-builder' )
				),
				'content_classes' => 'elementor-panel-heading-title',
			],
			'custom_form_field_data'               => [
				'mode'        => 'repeater',
				'type'        => 'repeater',
				'label'       => esc_html__( 'Custom Fields', 'rtcl-elementor-builder' ),
				'fields'      => [
					'select_form_data_fields_label'        => [
						'type'  => 'text',
						'label' => __( 'Field Name', 'rtcl-elementor-builder' ),
					],
					'show_in_frontend' => [
						'type'        => 'switch',
						'label'       => __( 'Show/Hide Field', 'rtcl-elementor-builder' ),
						'label_on'    => __( 'Hide', 'rtcl-elementor-builder' ),
						'label_off'   => __( 'Show', 'rtcl-elementor-builder' ),
						'default'     => 'yes',
						'description' => __( 'Switch to show/hide field.', 'rtcl-elementor-builder' ),
					],
					'show_in_frontend_label' => [
						'type'        => 'switch',
						'label'       => __( 'Show/Hide Label', 'rtcl-elementor-builder' ),
						'label_on'    => __( 'Hide', 'rtcl-elementor-builder' ),
						'label_off'   => __( 'Show', 'rtcl-elementor-builder' ),
						'default'     => 'yes',
						'description' => __( 'Switch to show/hide Label.', 'rtcl-elementor-builder' ),
					],
					'select_form_data_fields_name'         => [
						'type'        => 'select',
						'label'       => __( 'Select Field', 'rtcl-elementor-builder' ),
						'options'     => $keyVal,
						'description' => __( 'Select Field To Show', 'rtcl-elementor-builder' ),
					],
					'select_form_data_fields_element_type' => [
						'type'        => 'hidden',
						'label'       => __( 'Element Type', 'rtcl-elementor-builder' ),
						'default'     => 'others',
						'description' => __( 'Select Field To Show', 'rtcl-elementor-builder' ),
					],
					'fb_note'                              => [
						'type'      => 'html',
						'raw'       => sprintf(
							'<p class="rtcl-elementor-group-heading">%s</p>',
							__( 'Below control apply only to URL elements.', 'rtcl-elementor-builder' )
						),
						'condition' => [
							'select_form_data_fields_element_type!' => 'others',
						],
					],
					'select_form_data_fields_for'          => [
						'type'        => 'select',
						'label'       => __( 'Field For', 'rtcl-elementor-builder' ),
						'options'     => [
							'image' => __( 'Display As Image', 'rtcl-elementor-builder' ),
							'video' => __( 'Display As Video', 'rtcl-elementor-builder' ),
							'audio' => __( 'Display As Audio', 'rtcl-elementor-builder' ),
							'link'  => __( 'Display As Link', 'rtcl-elementor-builder' ),
							'text'  => __( 'Display As Text', 'rtcl-elementor-builder' ),
						],
						'default'     => 'text',
						'description' => __( 'Select Field To Show', 'rtcl-elementor-builder' ),
						'condition'   => [
							'select_form_data_fields_element_type!' => 'others',
						],
					],
				],
				'default'     => $defaults,
				'title_field' => '{{{ select_form_data_fields_label }}}',
			],

			'rtcl_fbdata_settings_end'             => [
				'mode' => 'section_end',
			],
			'rtcl_fbdata_list_item_settings_start' => [
				'mode'  => 'section_start',
				'label' => __( 'List Item', 'rtcl-elementor-builder' ),
			],
			'rtcl_fbdata_list_main_icon'           => [
				'type'        => 'switch',
				'label'       => __( 'Show Main Icon', 'rtcl-elementor-builder' ),
				'label_on'    => __( 'Hide', 'rtcl-elementor-builder' ),
				'label_off'   => __( 'Show', 'rtcl-elementor-builder' ),
				'default'     => 'yes',
				'description' => __( 'Switch to show/hide.', 'rtcl-elementor-builder' ),
			],
			'rtcl_fbdata_list_item_show_icon'      => [
				'type'        => 'switch',
				'label'       => __( 'Show List Icon', 'rtcl-elementor-builder' ),
				'label_on'    => __( 'Hide', 'rtcl-elementor-builder' ),
				'label_off'   => __( 'Show', 'rtcl-elementor-builder' ),
				'default'     => 'yes',
				'description' => __( 'Switch to show/hide.', 'rtcl-elementor-builder' ),
			],
			'rtcl_fbdata_list_item_settings_end'   => [
				'mode' => 'section_end',
			],
		];
	}

	/**
	 * Set style controlls
	 *
	 * @return array
	 */
	public function general_style() {
		return [
			'rtcl_fb_general_style'                   => [
				'mode'  => 'section_start',
				'label' => __( 'General Style', 'rtcl-elementor-builder' ),
				'tab'   => 'style',
			],
			'fb_general_item_icon'                    => [
				'type'            => 'html',
				'raw'             => sprintf(
					'<h3 class="rtcl-elementor-group-heading">%s</h3>',
					__( 'Icon', 'rtcl-elementor-builder' )
				),
				'content_classes' => 'elementor-panel-heading-title',
			],

			'fb_general_item_icon_area_size'          => [
				'type'       => 'slider',
				'label'      => esc_html__( 'Icon Area Size', 'rtcl-elementor-builder' ),
				'size_units' => [ 'px' ],
				'selectors'  => [
					'{{WRAPPER}} .el-single-addon' => '--fb-field-icon-area-size: {{SIZE}}{{UNIT}};',
				],
			],
			'fb_general_item_icon_size'               => [
				'type'       => 'slider',
				'label'      => esc_html__( 'Icon Font Size', 'rtcl-elementor-builder' ),
				'size_units' => [ 'px' ],
				'selectors'  => [
					'{{WRAPPER}} .el-single-addon .rtcl-field-icon i' => 'font-size: {{SIZE}}{{UNIT}};',
				],
			],
			'fb_general_item_icon_color'              => [
				'label'     => esc_html__( 'Icon Color', 'rtcl-elementor-builder' ),
				'type'      => 'color',
				'separator' => 'default',
				'selectors' => [
					'{{WRAPPER}} .el-single-addon .rtcl-field-icon i' => 'color: {{VALUE}};',
				],
			],

			'fb_general_item_icon_bg_color'           => [
				'label'     => esc_html__( 'Background Color', 'rtcl-elementor-builder' ),
				'type'      => 'color',
				'separator' => 'default',
				'selectors' => [
					'{{WRAPPER}} .el-single-addon .rtcl-field-icon' => 'background-color: {{VALUE}};',
				],
			],
			'fb_general_item_icon_border_radius'      => [
				'mode'       => 'responsive',
				'label'      => __( 'Border Radius', 'rtcl-elementor-builder' ),
				'type'       => 'dimensions',
				'size_units' => [ 'px', 'em', '%' ],
				'selectors'  => [
					'{{WRAPPER}} .el-single-addon .rtcl-field-icon' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			],
			'fb_general_item_icon_border'             => [
				'type'     => 'border',
				'label'    => __( 'Border', 'rtcl-elementor-builder' ),
				'mode'     => 'group',
				'selector' => '{{WRAPPER}} .el-single-addon .rtcl-field-icon',
			],
			'rtcl_listing_wrapper_icon_box_shadow'    => [
				'label'    => __( 'Box Shadow', 'rtcl-elementor-builder' ),
				'type'     => 'box-shadow',
				'mode'     => 'group',
				'selector' => '{{WRAPPER}} .el-single-addon .rtcl-field-icon',
			],
			'fb_general_item_icon_margin'             => [
				'mode'       => 'responsive',
				'label'      => __( 'Icon Margin', 'rtcl-elementor-builder' ),
				'type'       => 'dimensions',
				'size_units' => [ 'px', 'em', '%' ],
				'selectors'  => [
					'{{WRAPPER}} .el-single-addon' => '--fb-field-icon-area-margin-r:{{RIGHT}}{{UNIT}}',
					'{{WRAPPER}} .el-single-addon .rtcl-field-icon' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			],
			'fb_general_item_label_start'             => [
				'type'            => 'html',
				'raw'             => sprintf(
					'<h3 class="rtcl-elementor-group-heading">%s</h3>',
					__( 'Label', 'rtcl-elementor-builder' )
				),
				'content_classes' => 'elementor-panel-heading-title',
			],
			'fb_general_item_label_typo'              => [
				'mode'     => 'group',
				'type'     => 'typography',
				'label'    => __( 'Label Typography', 'rtcl-elementor-builder' ),
				'selector' => '{{WRAPPER}} .el-single-addon.form-builder-data-wrapper .cfp-label',
			],
			'fb_general_item_label_color'             => [
				'label'     => esc_html__( 'Label Color', 'rtcl-elementor-builder' ),
				'type'      => 'color',
				'separator' => 'default',
				'selectors' => [
					'{{WRAPPER}} .el-single-addon.form-builder-data-wrapper .cfp-label' => 'color: {{VALUE}};',
				],
			],
			'fb_general_item_label_and_value_display' => [
				'type'        => 'select',
				'label'       => __( 'Display', 'rtcl-elementor-builder' ),
				'options'     => [
					'row'    => esc_html__( 'Inline', 'rtcl-elementor-builder' ),
					'column' => esc_html__( 'New Line', 'rtcl-elementor-builder' ),
				],
				'default'     => 'column',
				'description' => __( 'Display Inline Or New Line', 'rtcl-elementor-builder' ),
				'selectors'   => [
					'{{WRAPPER}} .el-single-addon.form-builder-data-wrapper .cfp-label-and-value' => 'flex-direction: {{VALUE}};',
				],
			],
			'fb_general_item_label_and_value_gap'     => [
				'type'       => 'slider',
				'label'      => esc_html__( 'Label And Value Gap', 'rtcl-elementor-builder' ),
				'size_units' => [ 'px' ],
				'selectors'  => [
					'{{WRAPPER}} .el-single-addon.form-builder-data-wrapper .cfp-label-and-value' => 'gap: {{SIZE}}{{UNIT}};',
				],
			],
			'fb_general_item_value_start'             => [
				'type'            => 'html',
				'raw'             => sprintf(
					'<h3 class="rtcl-elementor-group-heading">%s</h3>',
					__( 'Value', 'rtcl-elementor-builder' )
				),
				'content_classes' => 'elementor-panel-heading-title',
			],
			'fb_general_item_value_typo'              => [
				'mode'     => 'group',
				'type'     => 'typography',
				'label'    => __( 'Value Typography', 'rtcl-elementor-builder' ),
				'selector' => '{{WRAPPER}}  .el-single-addon.form-builder-data-wrapper .cfp-value',
			],
			'fb_general_item_value_color'             => [
				'label'     => esc_html__( 'Value Color', 'rtcl-elementor-builder' ),
				'type'      => 'color',
				'separator' => 'default',
				'selectors' => [
					'{{WRAPPER}} .el-single-addon.form-builder-data-wrapper .cfp-value' => 'color: {{VALUE}};',
				],
			],

			'fb_general_items'                        => [
				'type'            => 'html',
				'raw'             => sprintf(
					'<h3 class="rtcl-elementor-group-heading">%s</h3>',
					__( 'General Items', 'rtcl-elementor-builder' )
				),
				'content_classes' => 'elementor-panel-heading-title',
			],

			'fb_general_items_bg_color'               => [
				'label'     => esc_html__( 'Background Color', 'rtcl-elementor-builder' ),
				'type'      => 'color',
				'separator' => 'default',
				'selectors' => [
					'{{WRAPPER}} .el-single-addon .rtcl-fb-element.rtcl-default-element' => 'background-color: {{VALUE}};',
				],
			],
			'fb_general_items_border_radius'          => [
				'mode'       => 'responsive',
				'label'      => __( 'Border Radius', 'rtcl-elementor-builder' ),
				'type'       => 'dimensions',
				'size_units' => [ 'px', 'em', '%' ],
				'selectors'  => [
					'{{WRAPPER}} .el-single-addon .rtcl-fb-element.rtcl-default-element' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			],
			'fb_general_items_border'                 => [
				'type'     => 'border',
				'label'    => __( 'Border', 'rtcl-elementor-builder' ),
				'mode'     => 'group',
				'selector' => '{{WRAPPER}} .el-single-addon .rtcl-fb-element.rtcl-default-element',
			],
			'fb_general_items_box_shadow'             => [
				'label'    => __( 'Box Shadow', 'rtcl-elementor-builder' ),
				'type'     => 'box-shadow',
				'mode'     => 'group',
				'selector' => '{{WRAPPER}} .el-single-addon .rtcl-fb-element.rtcl-default-element',
			],
			'fb_general_items_padding'                => [
				'mode'       => 'responsive',
				'label'      => __( 'Padding', 'rtcl-elementor-builder' ),
				'type'       => 'dimensions',
				'size_units' => [ 'px', 'em', '%' ],
				'selectors'  => [
					'{{WRAPPER}} .el-single-addon .rtcl-fb-element.rtcl-default-element' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			],
			'fb_general_items_margin'                 => [
				'mode'       => 'responsive',
				'label'      => __( 'margin', 'rtcl-elementor-builder' ),
				'type'       => 'dimensions',
				'size_units' => [ 'px', 'em', '%' ],
				'selectors'  => [
					'{{WRAPPER}} .el-single-addon .rtcl-fb-element.rtcl-default-element' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			],

			'fb_general_item_label_value_end'         => [
				'mode' => 'section_end',
			],
		];
	}

	/**
	 * Set style controlls
	 *
	 * @return array
	 */
	public function list_item_style() {
		return [
			'text_style_settings'             => [
				'mode'  => 'section_start',
				'label' => __( 'List Items', 'rtcl-elementor-builder' ),
				'tab'   => 'style',
			],
			'fb_cf_list_heading_note'         => [
				'type'            => 'html',
				'raw'             => sprintf(
					'<p class="rtcl-elementor-group-heading">%s</p>',
					__( 'Note: These settings apply only to fields with multiple selections, such as checkboxes and multiselect fields.', 'rtcl-elementor-builder' )
				),
				'content_classes' => 'elementor-panel-heading-title',
			],

			/* === General/Common Style === */
			'fb_list_item_wrapper_margin'     => [
				'mode'       => 'responsive',
				'label'      => __( 'Margin', 'rtcl-elementor-builder' ),
				'type'       => 'dimensions',
				'size_units' => [ 'px', 'em', '%' ],
				'selectors'  => [
					'{{WRAPPER}} .el-single-addon > .rtcl-fb-checkbox' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			],
			'fb_cf_list_item_heading'         => [
				'type'            => 'html',
				'raw'             => sprintf(
					'<h3 class="rtcl-elementor-group-heading">%s</h3>',
					__( 'Heading ', 'rtcl-elementor-builder' )
				),
				'content_classes' => 'elementor-panel-heading-title',
			],
			'heading_font_size_typo'          => [
				'mode'     => 'group',
				'type'     => 'typography',
				'label'    => __( 'Heading Text', 'rtcl-elementor-builder' ),
				'selector' => '{{WRAPPER}} .el-single-addon .rtcl-list-item-heading',
			],
			'heading_text_color'              => [
				'label'     => esc_html__( 'Text Color', 'rtcl-elementor-builder' ),
				'type'      => 'color',
				'separator' => 'default',
				'selectors' => [
					'{{WRAPPER}} .el-single-addon :is(.rtcl-fb-checkbox) .rtcl-list-item-heading' => 'color: {{VALUE}};',
				],
			],

			'heading_bg_color'                => [
				'label'     => esc_html__( 'Background Color', 'rtcl-elementor-builder' ),
				'type'      => 'color',
				'separator' => 'default',
				'selectors' => [
					'{{WRAPPER}} .el-single-addon :is(.rtcl-fb-checkbox) .rtcl-icon-label-wrapper' => 'background-color: {{VALUE}};',
				],
			],
			'border_radius'                   => [
				'mode'       => 'responsive',
				'label'      => __( 'Border Radius', 'rtcl-elementor-builder' ),
				'type'       => 'dimensions',
				'size_units' => [ 'px', 'em', '%' ],
				'selectors'  => [
					'{{WRAPPER}} .el-single-addon :is(.rtcl-fb-checkbox) .rtcl-icon-label-wrapper' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			],
			'fb_list_header_border'           => [
				'type'     => 'border',
				'label'    => __( 'Border', 'rtcl-elementor-builder' ),
				'mode'     => 'group',
				'selector' => '{{WRAPPER}} .el-single-addon :is(.rtcl-fb-checkbox) .rtcl-icon-label-wrapper',
			],
			'fb_cf_list_item_heading_padding' => [
				'mode'       => 'responsive',
				'label'      => __( 'Padding', 'rtcl-elementor-builder' ),
				'type'       => 'dimensions',
				'size_units' => [ 'px', 'em', '%' ],
				'selectors'  => [
					'{{WRAPPER}} .el-single-addon :is(.rtcl-fb-checkbox) .rtcl-icon-label-wrapper' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			],
			'fb_cf_list_item_heading_margin'  => [
				'mode'       => 'responsive',
				'label'      => __( 'Margin', 'rtcl-elementor-builder' ),
				'type'       => 'dimensions',
				'size_units' => [ 'px', 'em', '%' ],
				'selectors'  => [
					'{{WRAPPER}} .el-single-addon :is(.rtcl-fb-checkbox) .rtcl-icon-label-wrapper' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			],

			/* === Items Style === */
			'fb_cf_list_item'                 => [
				'type'            => 'html',
				'raw'             => sprintf(
					'<h3 class="rtcl-elementor-group-heading">%s</h3>',
					__( 'Item', 'rtcl-elementor-builder' )
				),
				'content_classes' => 'elementor-panel-heading-title',
			],
			'list_item_column'                => [
				'type'       => 'slider',
				'label'      => esc_html__( 'List Item Column', 'rtcl-elementor-builder' ),
				'size_units' => [ 'px' ],
				'default'    => [
					'unit' => 'px',
					'size' => 3,
				],
				'selectors'  => [
					'{{WRAPPER}} .el-single-addon .rtcl-fb-checkbox' => '--multiselect-columns: {{SIZE}};',
				],
			],
			'list_item_column_gap'            => [
				'type'       => 'slider',
				'label'      => esc_html__( 'Items Gap', 'rtcl-elementor-builder' ),
				'size_units' => [ 'px' ],
				'selectors'  => [
					'{{WRAPPER}} .el-single-addon .rtcl-fb-checkbox' => '--multiselect-gap: {{SIZE}}{{UNIT}};',
				],
			],
			'list_item_text_typo'             => [
				'mode'     => 'group',
				'type'     => 'typography',
				'label'    => __( 'Text Typography', 'rtcl-elementor-builder' ),
				'selector' => '{{WRAPPER}} .el-single-addon .rtcl-list-text',
			],
			'list_item_text_color'            => [
				'label'     => esc_html__( 'Text Color', 'rtcl-elementor-builder' ),
				'type'      => 'color',
				'separator' => 'default',
				'selectors' => [
					'{{WRAPPER}} .el-single-addon .rtcl-list-text' => 'color: {{VALUE}};',
				],
			],

			/* === Icon Style === */
			'list_item_icon_heading'          => [
				'type'     => 'heading',
				'label'    => __( 'Icon Settings', 'rtcl-elementor-builder' ),
				'separator' => 'before',
			],
			'list_item_icon_size'             => [
				'type'       => 'slider',
				'label'      => esc_html__( 'Icon Size', 'rtcl-elementor-builder' ),
				'size_units' => [ 'px' ],
				'selectors'  => [
					'{{WRAPPER}} .el-single-addon .rtcl-list-group-item i' => 'font-size: {{SIZE}}{{UNIT}};',
				],
			],
			'list_item_icon_color'            => [
				'label'     => esc_html__( 'Icon Color', 'rtcl-elementor-builder' ),
				'type'      => 'color',
				'separator' => 'default',
				'selectors' => [
					'{{WRAPPER}} .el-single-addon .rtcl-list-group-item i' => 'color: {{VALUE}};',
				],
			],
			'list_item_icon_area_size'          => [
				'type'       => 'slider',
				'label'      => esc_html__( 'Icon Area Size', 'rtcl-elementor-builder' ),
				'size_units' => [ 'px' ],
				'selectors'  => [
					'{{WRAPPER}} .el-single-addon .rtcl-list-group-item .list-icon' => 'width: {{SIZE}}{{UNIT}}; height: {{SIZE}}{{UNIT}};',
				],
			],
			'list_item_icon_area_border'          => [
				'type'     => 'border',
				'label'    => __( 'Border', 'rtcl-elementor-builder' ),
				'mode'     => 'group',
				'selector' => '{{WRAPPER}} .el-single-addon .rtcl-list-group-item .list-icon',
			],
			'list_item_icon_area_border_radius'  => [
				'mode'       => 'responsive',
				'label'      => __( 'Border Radius', 'rtcl-elementor-builder' ),
				'type'       => 'dimensions',
				'size_units' => [ 'px', 'em', '%' ],
				'selectors'  => [
					'{{WRAPPER}} .el-single-addon .rtcl-list-group-item .list-icon' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			],
			'fb_cf_list_item_icon_gap'        => [
				'mode'       => 'responsive',
				'label'      => __( 'Icon Margin', 'rtcl-elementor-builder' ),
				'type'       => 'dimensions',
				'size_units' => [ 'px', 'em', '%' ],
				'selectors'  => [
					'{{WRAPPER}} .el-single-addon .rtcl-list-group-item i' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			],

			/* === Content Style === */
			'fb_cf_list_area_content'         => [
				'type'            => 'html',
				'raw'             => sprintf(
					'<h3 class="rtcl-elementor-group-heading">%s</h3>',
					__( 'List Area Content', 'rtcl-elementor-builder' )
				),
				'content_classes' => 'elementor-panel-heading-title',
			],
			'fb_cf_list_area_padding'         => [
				'mode'       => 'responsive',
				'label'      => __( 'Padding', 'rtcl-elementor-builder' ),
				'type'       => 'dimensions',
				'size_units' => [ 'px', 'em', '%' ],
				'selectors'  => [
					'{{WRAPPER}} .el-single-addon .rtcl-list-group' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			],
			'fb_cf_list_area_margin'          => [
				'mode'       => 'responsive',
				'label'      => __( 'Margin', 'rtcl-elementor-builder' ),
				'type'       => 'dimensions',
				'size_units' => [ 'px', 'em', '%' ],
				'selectors'  => [
					'{{WRAPPER}} .el-single-addon .rtcl-list-group' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			],
			'fb_cf_list_area_border'          => [
				'type'     => 'border',
				'label'    => __( 'Border', 'rtcl-elementor-builder' ),
				'mode'     => 'group',
				'selector' => '{{WRAPPER}} .el-single-addon .rtcl-list-group',
			],
			'text_style_settings_end'         => [
				'mode' => 'section_end',
			],
		];
	}

	/**
	 * @return array[]
	 */
	public function image_file_style() {
		return [
			'file_style_settings'         => [
				'mode'  => 'section_start',
				'label' => __( 'File', 'rtcl-elementor-builder' ),
				'tab'   => 'style',
			],
			'file_heading'                => [
				'type'            => 'html',
				'raw'             => sprintf(
					'<h3 class="rtcl-elementor-group-heading">%s</h3>',
					__( 'Heading ', 'rtcl-elementor-builder' )
				),
				'content_classes' => 'elementor-panel-heading-title',
			],
			'file_heading_font_size_typo' => [
				'mode'     => 'group',
				'type'     => 'typography',
				'label'    => __( 'Heading Text', 'rtcl-elementor-builder' ),
				'selector' => '{{WRAPPER}} .el-single-addon .rtcl-file-heading',
			],
			'file_heading_text_color'     => [
				'label'     => esc_html__( 'Text Color', 'rtcl-elementor-builder' ),
				'type'      => 'color',
				'separator' => 'default',
				'selectors' => [
					'{{WRAPPER}} .el-single-addon .rtcl-file-heading' => 'color: {{VALUE}};',
				],
			],

			'file_heading_bg_color'       => [
				'label'     => esc_html__( 'Background Color', 'rtcl-elementor-builder' ),
				'type'      => 'color',
				'separator' => 'default',
				'selectors' => [
					'{{WRAPPER}} .el-single-addon .rtcl-wrapper-fb-file .rtcl-icon-label-wrapper' => 'background-color: {{VALUE}};',
				],
			],
			'file_heading_border_radius'  => [
				'mode'       => 'responsive',
				'label'      => __( 'Border Radius', 'rtcl-elementor-builder' ),
				'type'       => 'dimensions',
				'size_units' => [ 'px', 'em', '%' ],
				'selectors'  => [
					'{{WRAPPER}} .el-single-addon .rtcl-wrapper-fb-file .rtcl-icon-label-wrapper' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			],			
			'fb_file_header_border'       => [
				'type'     => 'border',
				'label'    => __( 'Border', 'rtcl-elementor-builder' ),
				'mode'     => 'group',
				'selector' => '{{WRAPPER}} .el-single-addon .rtcl-wrapper-fb-file .rtcl-icon-label-wrapper',
			],

			'fb_file_item_gap'            => [
				'type'       => 'slider',
				'label'      => esc_html__( 'File Item Gap', 'rtcl-elementor-builder' ),
				'size_units' => [ 'px' ],
				'selectors'  => [
					'{{WRAPPER}} .el-single-addon' => '--file-item-gap: {{SIZE}}{{UNIT}};',
				],
			],
			'fb_file_item_title_padding'  => [
				'mode'       => 'responsive',
				'label'      => __( 'Padding', 'rtcl-elementor-builder' ),
				'type'       => 'dimensions',
				'size_units' => [ 'px', 'em', '%' ],
				'selectors'  => [
					'{{WRAPPER}} .el-single-addon .rtcl-wrapper-fb-file .rtcl-icon-label-wrapper' => 'padding : {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			],
			'fb_file_item_wrapper_margin' => [
				'mode'       => 'responsive',
				'label'      => __( 'Margin', 'rtcl-elementor-builder' ),
				'type'       => 'dimensions',
				'size_units' => [ 'px', 'em', '%' ],
				'selectors'  => [
					'{{WRAPPER}} .el-single-addon .rtcl-fb-element.rtcl-wrapper-fb-file' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			],
			'image'                       => [
				'type'            => 'html',
				'raw'             => sprintf(
					'<h3 class="rtcl-elementor-group-heading">%s</h3>',
					__( 'Image', 'rtcl-elementor-builder' )
				),
				'content_classes' => 'elementor-panel-heading-title',
			],
			'image_width'                 => [
				'type'       => 'slider',
				'label'      => esc_html__( 'Image Width', 'rtcl-elementor-builder' ),
				'size_units' => [ 'px' ,'%' ],
				'selectors'  => [
					'{{WRAPPER}} .el-single-addon.form-builder-data-wrapper .rtcl-fb-file img' => 'width: {{SIZE}}{{UNIT}}',
				],
			],
			'image_border'                => [
				'type'     => 'border',
				'label'    => __( 'Border', 'rtcl-elementor-builder' ),
				'mode'     => 'group',
				'selector' => '{{WRAPPER}} .el-single-addon.form-builder-data-wrapper .rtcl-fb-file img',
			],
			
			'image_border_radius'         => [
				'mode'       => 'responsive',
				'label'      => __( 'Border Radius', 'rtcl-elementor-builder' ),
				'type'       => 'dimensions',
				'size_units' => [ 'px', 'em', '%' ],
				'selectors'  => [
					'{{WRAPPER}} .el-single-addon.form-builder-data-wrapper .rtcl-fb-file img' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			],

			'file_style_settings_end'     => [
				'mode' => 'section_end',
			],
		];
	}

	/**
	 * Set style controlls
	 *
	 * @return array
	 */
	public function general_repeater() {
		return [
			'fb_repeater_general_item_start' => [
				'mode'  => 'section_start',
				'label' => __( 'Repeater', 'rtcl-elementor-builder' ),
			],
			'repeater_main_title_show_icon'  => [
				'type'        => 'switch',
				'label'       => __( 'Show Main Icon', 'rtcl-elementor-builder' ),
				'label_on'    => __( 'Hide', 'rtcl-elementor-builder' ),
				'label_off'   => __( 'Show', 'rtcl-elementor-builder' ),
				'default'     => 'yes',
				'description' => __( 'Switch to show/hide.', 'rtcl-elementor-builder' ),
			],
			'repeater_show_list_item'        => [
				'type'    => 'select',
				'label'   => __( 'Show Element', 'rtcl-elementor-builder' ),
				'options' => [
					'all'    => __( 'All', 'rtcl-elementor-builder' ),
					'list'   => __( 'Only List Element', 'rtcl-elementor-builder' ),
					'file'   => __( 'Only File Element', 'rtcl-elementor-builder' ),
					'others' => __( 'Others Element', 'rtcl-elementor-builder' ),
				],
				'default' => 'all',
			],
			'repeater_item_column'           => [
				'type'       => 'slider',
				'label'      => esc_html__( 'Repeater Element Column', 'rtcl-elementor-builder' ),
				'size_units' => [ 'px' ],
				'range'      => [
					'px' => [
						'min'  => 1,
						'max'  => 10,
						'step' => 1,
					],
				],
				'default'    => [
					'unit' => 'px',
					'size' => 1,
				],
				'selectors'  => [
					'{{WRAPPER}} .el-single-addon .rtcl-fb-repeater-fields-content' => '--rp-columns: {{SIZE}};',
				],
			],
			'repeater_item_gap'              => [
				'type'       => 'slider',
				'label'      => esc_html__( 'Repeater Item Gap', 'rtcl-elementor-builder' ),
				'size_units' => [ 'px' ],
				'selectors'  => [
					'{{WRAPPER}} .el-single-addon .rtcl-fb-repeater-fields-content' => '--rp-gap: {{SIZE}}{{UNIT}};',
				],
			],
			'fb_repeater_general_item_end'   => [
				'mode' => 'section_end',
			],
		];
	}

	/**
	 * @return array[]
	 */
	public function repeater_control() {
		return [
			'fb_repeater_style_settings_start' => [
				'mode'  => 'section_start',
				'label' => __( 'Repeater Fields', 'rtcl-elementor-builder' ),
				'tab'   => 'style',
			],

			/* === Title Area === */
			'repeater_main_title' => [
				'type'            => 'html',
				'raw'             => sprintf(
					'<h3 class="rtcl-elementor-group-heading">%s</h3>',
					__( 'Repeater Main Title', 'rtcl-elementor-builder' )
				),
				'content_classes' => 'elementor-panel-heading-title',
			],
			'repeater_main_title_heading_area' => [
				'type'      => 'heading',
				'label'     => __( 'Title Area', 'rtcl-elementor-builder' ),
				'separator' => 'before',
			],
			'repeater_main_heading_typo'   => [
				'mode'     => 'group',
				'type'     => 'typography',
				'label'    => __( 'Heading Text', 'rtcl-elementor-builder' ),
				'selector' => '{{WRAPPER}} .el-single-addon .rtcl-repeater-main-heading',
			],
			'repeater_main_heading_color'         => [
				'label'     => esc_html__( 'Text Color', 'rtcl-elementor-builder' ),
				'type'      => 'color',
				'separator' => 'default',
				'selectors' => [
					'{{WRAPPER}} .el-single-addon .rtcl-repeater-main-heading-icon-label .rtcl-repeater-main-heading' => 'color: {{VALUE}};',
				],
			],
			'repeater_main_heading_bg_color'      => [
				'label'     => esc_html__( 'Background Color', 'rtcl-elementor-builder' ),
				'type'      => 'color',
				'separator' => 'default',
				'selectors' => [
					'{{WRAPPER}} .el-single-addon .rtcl-repeater-main-heading-icon-label' => 'background-color: {{VALUE}};',
				],
			],
			'repeater_main_heading_border_radius' => [
				'mode'       => 'responsive',
				'label'      => __( 'Border Radius', 'rtcl-elementor-builder' ),
				'type'       => 'dimensions',
				'size_units' => [ 'px', 'em', '%' ],
				'selectors'  => [
					'{{WRAPPER}} .el-single-addon .rtcl-repeater-main-heading-icon-label' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			],
			'repeater_main_heading_padding'       => [
				'mode'       => 'responsive',
				'label'      => __( 'Padding', 'rtcl-elementor-builder' ),
				'type'       => 'dimensions',
				'size_units' => [ 'px', 'em', '%' ],
				'selectors'  => [
					'{{WRAPPER}} .el-single-addon .rtcl-repeater-main-heading-icon-label' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			],
			'repeater_main_heading_margin'        => [
				'mode'       => 'responsive',
				'label'      => __( 'Margin', 'rtcl-elementor-builder' ),
				'type'       => 'dimensions',
				'size_units' => [ 'px', 'em', '%' ],
				'selectors'  => [
					'{{WRAPPER}} .el-single-addon .rtcl-repeater-main-heading-icon-label' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			],

			/* === Icon Area === */
			'repeater_main_heading_icon_heading' => [
				'type'            => 'heading',
				'label'    => __( 'Icon Area', 'rtcl-elementor-builder' ),
				'separator' => 'before',
			],
			'repeater_main_heading_icon_area_size' => [
				'type'       => 'slider',
				'label'      => esc_html__( 'Icon Area Size', 'rtcl-elementor-builder' ),
				'size_units' => [ 'px' ],
				'selectors'  => [
					'{{WRAPPER}} .el-single-addon .rtcl-repeater-main-heading-icon-label .rtcl-field-icon' => 'width: {{SIZE}}{{UNIT}};height: {{SIZE}}{{UNIT}};',
				],
			],
			'repeater_main_heading_icon_size'  => [
				'type'       => 'slider',
				'label'      => esc_html__( 'Icon Font Size', 'rtcl-elementor-builder' ),
				'size_units' => [ 'px' ],
				'selectors'  => [
					'{{WRAPPER}} .el-single-addon .rtcl-repeater-main-heading-icon-label .rtcl-field-icon i' => 'font-size: {{SIZE}}{{UNIT}};',
				],
			],
			'repeater_main_heading_icon_color' => [
				'label'     => esc_html__( 'Icon Color', 'rtcl-elementor-builder' ),
				'type'      => 'color',
				'separator' => 'default',
				'selectors' => [
					'{{WRAPPER}} .el-single-addon .rtcl-repeater-main-heading-icon-label .rtcl-field-icon i' => 'color: {{VALUE}};',
				],
			],
			'repeater_main_heading_icon_bg_color' => [
				'label'     => esc_html__( 'Background Color', 'rtcl-elementor-builder' ),
				'type'      => 'color',
				'separator' => 'default',
				'selectors' => [
					'{{WRAPPER}} .el-single-addon .rtcl-repeater-main-heading-icon-label .rtcl-field-icon' => 'background-color: {{VALUE}};',
				],
			],
			'repeater_main_heading_icon_border_radius' => [
				'mode'       => 'responsive',
				'label'      => __( 'Border Radius', 'rtcl-elementor-builder' ),
				'type'       => 'dimensions',
				'size_units' => [ 'px', 'em', '%' ],
				'selectors'  => [
					'{{WRAPPER}} .el-single-addon .rtcl-repeater-main-heading-icon-label .rtcl-field-icon' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			],
			'repeater_main_heading_icon_border' => [
				'type'     => 'border',
				'label'    => __( 'Border', 'rtcl-elementor-builder' ),
				'mode'     => 'group',
				'selector' => '{{WRAPPER}} .el-single-addon .rtcl-repeater-main-heading-icon-label .rtcl-field-icon',
			],
			'repeater_main_heading_icon_box_shadow' => [
				'label'    => __( 'Box Shadow', 'rtcl-elementor-builder' ),
				'type'     => 'box-shadow',
				'mode'     => 'group',
				'selector' => '{{WRAPPER}} .el-single-addon .rtcl-repeater-main-heading-icon-label .rtcl-field-icon',
			],
			'repeater_main_heading_icon_margin' => [
				'mode'       => 'responsive',
				'label'      => __( 'Icon Margin', 'rtcl-elementor-builder' ),
				'type'       => 'dimensions',
				'size_units' => [ 'px', 'em', '%' ],
				'selectors'  => [
					'{{WRAPPER}} .el-single-addon .rtcl-repeater-main-heading-icon-label .rtcl-field-icon' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			],

			/* ====== Content Area ====== */
			'repeater_main_content'                  => [
				'type'            => 'html',
				'raw'             => sprintf(
					'<h3 class="rtcl-elementor-group-heading">%s</h3>',
					__( 'Repeater Main Content', 'rtcl-elementor-builder' )
				),
				'content_classes' => 'elementor-panel-heading-title',
			],

			/* === Repeater Wrapper Area === */
			'repeater_main_wrapper_heading' => [
				'type'            => 'heading',
				'label'    => __( 'Main Wrapper Area', 'rtcl-elementor-builder' ),
				'separator' => 'before',
			],
			'repeater_main_content_padding'          => [
				'mode'       => 'responsive',
				'label'      => __( 'Padding', 'rtcl-elementor-builder' ),
				'type'       => 'dimensions',
				'size_units' => [ 'px', 'em', '%' ],
				'selectors'  => [
					'{{WRAPPER}} .el-single-addon .rtcl-fb-repeater-fields-content' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			],
			'repeater_main_content_border'           => [
				'type'     => 'border',
				'label'    => __( 'Border', 'rtcl-elementor-builder' ),
				'mode'     => 'group',
				'selector' => '{{WRAPPER}} .el-single-addon .rtcl-fb-repeater-fields-content',
			],

			/* === Repeater Icon Area === */
			'repeater_item_icon_heading' => [
				'type'            => 'heading',
				'label'    => __( 'Icon Area', 'rtcl-elementor-builder' ),
				'separator' => 'before',
			],
			'repeater_item_icon_area_size' => [
				'type'       => 'slider',
				'label'      => esc_html__( 'Icon Area Size', 'rtcl-elementor-builder' ),
				'size_units' => [ 'px' ],
				'selectors'  => [
					'{{WRAPPER}} .el-single-addon .rtcl-fb-repeater-fields-content .rtcl-field-icon' => 'width: {{SIZE}}{{UNIT}};height: {{SIZE}}{{UNIT}};',
				],
			],
			'repeater_item_icon_size' => [
				'type'       => 'slider',
				'label'      => esc_html__( 'Icon Font Size', 'rtcl-elementor-builder' ),
				'size_units' => [ 'px' ],
				'selectors'  => [
					'{{WRAPPER}} .el-single-addon .rtcl-fb-repeater-fields-content .rtcl-field-icon i' => 'font-size: {{SIZE}}{{UNIT}};',
				],
			],
			'repeater_item_icon_color' => [
				'label'     => esc_html__( 'Icon Color', 'rtcl-elementor-builder' ),
				'type'      => 'color',
				'separator' => 'default',
				'selectors' => [
					'{{WRAPPER}} .el-single-addon .rtcl-fb-repeater-fields-content .rtcl-field-icon i' => 'color: {{VALUE}};',
				],
			],
			'repeater_item_icon_bg_color'           => [
				'label'     => esc_html__( 'Background Color', 'rtcl-elementor-builder' ),
				'type'      => 'color',
				'separator' => 'default',
				'selectors' => [
					'{{WRAPPER}} .el-single-addon .rtcl-fb-repeater-fields-content .rtcl-field-icon' => 'background-color: {{VALUE}};',
				],
			],
			'repeater_item_icon_border'             => [
				'type'     => 'border',
				'label'    => __( 'Border', 'rtcl-elementor-builder' ),
				'mode'     => 'group',
				'selector' => '{{WRAPPER}} .el-single-addon .rtcl-fb-repeater-fields-content .rtcl-field-icon',
			],
			'repeater_item_icon_border_radius'      => [
				'mode'       => 'responsive',
				'label'      => __( 'Border Radius', 'rtcl-elementor-builder' ),
				'type'       => 'dimensions',
				'size_units' => [ 'px', 'em', '%' ],
				'selectors'  => [
					'{{WRAPPER}} .el-single-addon .rtcl-fb-repeater-fields-content .rtcl-field-icon' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			],
			'repeater_item_icon_box_shadow'         => [
				'label'    => __( 'Box Shadow', 'rtcl-elementor-builder' ),
				'type'     => 'box-shadow',
				'mode'     => 'group',
				'selector' => '{{WRAPPER}} .el-single-addon .rtcl-fb-repeater-fields-content .rtcl-field-icon',
			],
			'repeater_item_icon_margin'             => [
				'mode'       => 'responsive',
				'label'      => __( 'Icon Margin', 'rtcl-elementor-builder' ),
				'type'       => 'dimensions',
				'size_units' => [ 'px', 'em', '%' ],
				'selectors'  => [
					'{{WRAPPER}} .el-single-addon .rtcl-fb-repeater-fields-content .rtcl-field-icon' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			],

			/* === Repeater Item Label === */
			'repeater_item_label_heading' => [
				'type'      => 'heading',
				'label'     => __( 'Label Text Style', 'rtcl-elementor-builder' ),
				'separator' => 'before',
			],
			'repeater_item_label_color' => [
				'label'     => esc_html__( 'Label Color', 'rtcl-elementor-builder' ),
				'type'      => 'color',
				'separator' => 'default',
				'selectors' => [
					'{{WRAPPER}} .el-single-addon .rtcl-fb-repeater-fields-content .cfp-label-and-value .cfp-label' => 'color: {{VALUE}};',
				],
			],
			'repeater_item_label_typo' => [
				'mode'     => 'group',
				'type'     => 'typography',
				'label'    => __( 'Label Typography', 'rtcl-elementor-builder' ),
				'selector' => '{{WRAPPER}} .el-single-addon .rtcl-fb-repeater-fields-content .cfp-label-and-value .cfp-label',
			],
			'repeater_item_label_padding' => [
				'mode'       => 'responsive',
				'label'      => __( 'Label Padding', 'rtcl-elementor-builder' ),
				'type'       => 'dimensions',
				'size_units' => [ 'px', 'em', '%' ],
				'selectors'  => [
					'{{WRAPPER}} .el-single-addon .rtcl-fb-repeater-fields-content .cfp-label-and-value .cfp-label' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			],
			'repeater_item_label_margin' => [
				'mode'       => 'responsive',
				'label'      => __( 'Label Margin', 'rtcl-elementor-builder' ),
				'type'       => 'dimensions',
				'size_units' => [ 'px', 'em', '%' ],
				'selectors'  => [
					'{{WRAPPER}} .el-single-addon .rtcl-fb-repeater-fields-content .cfp-label-and-value .cfp-label' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			],

			/* === Repeater Item Value === */
			'repeater_item_value_heading' => [
				'type'      => 'heading',
				'label'     => __( 'Value Text Style', 'rtcl-elementor-builder' ),
				'separator' => 'before',
			],
			'repeater_item_value_color' => [
				'label'     => esc_html__( 'Value Color', 'rtcl-elementor-builder' ),
				'type'      => 'color',
				'separator' => 'default',
				'selectors' => [
					'{{WRAPPER}} .el-single-addon .rtcl-fb-repeater-fields-content .cfp-label-and-value .cfp-value' => 'color: {{VALUE}};',
				],
			],
			'repeater_item_value_typo' => [
				'mode'     => 'group',
				'type'     => 'typography',
				'label'    => __( 'Value Typography', 'rtcl-elementor-builder' ),
				'selector' => '{{WRAPPER}} .el-single-addon .rtcl-fb-repeater-fields-content .cfp-label-and-value .cfp-value',
			],
			'repeater_item_value_padding' => [
				'mode'       => 'responsive',
				'label'      => __( 'Value Padding', 'rtcl-elementor-builder' ),
				'type'       => 'dimensions',
				'size_units' => [ 'px', 'em', '%' ],
				'selectors'  => [
					'{{WRAPPER}} .el-single-addon .rtcl-fb-repeater-fields-content .cfp-label-and-value .cfp-value' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			],
			'repeater_item_value_margin' => [
				'mode'       => 'responsive',
				'label'      => __( 'Value Margin', 'rtcl-elementor-builder' ),
				'type'       => 'dimensions',
				'size_units' => [ 'px', 'em', '%' ],
				'selectors'  => [
					'{{WRAPPER}} .el-single-addon .rtcl-fb-repeater-fields-content .cfp-label-and-value .cfp-value' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			],

			'fb_repeater_style_settings_end'   => [
				'mode' => 'section_end',
			],
		];
	}
}