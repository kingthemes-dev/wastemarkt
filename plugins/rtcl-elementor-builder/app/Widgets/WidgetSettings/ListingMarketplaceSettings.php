<?php

/**
 * Main Elementor ListingMarketplaceSettings Class
 *
 * ListingMarketplaceSettings main class
 *
 * @author   RadiusTheme
 * @since    2.0.10
 * @package  RTCL_Elementor_Builder
 * @version  1.2
 */

namespace RtclElb\Widgets\WidgetSettings;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

use RtclElb\Abstracts\ElementorSingleListingBase2;

/**
 * ListingMarketplaceSettings class
 */
class ListingMarketplaceSettings extends ElementorSingleListingBase2 {

	/**
	 * Set style controlls
	 *
	 * @return array
	 */
	public function widget_general_fields(): array {
		return $this->general_fields();
	}

	/**
	 * Set style controls
	 *
	 * @return array
	 */
	public function widget_style_fields(): array {
		$fields = array_merge(
			$this->box_style(),
			$this->quantity_field_style(),
			$this->add_to_cart_btn_style()
		);

		return $fields;
	}

	/**
	 * Set general controls
	 *
	 * @return array
	 */
	public function general_fields() {
		$fields = array(
			'rtcl_sec_general_start'    => array(
				'mode'  => 'section_start',
				'label' => __( 'General', 'rtcl-elementor-builder' ),
			),
			'rtcl_show_quantity'        => array(
				'type'        => 'switch',
				'label'       => __( 'Show Quantity Field', 'rtcl-elementor-builder' ),
				'label_on'    => __( 'Hide', 'rtcl-elementor-builder' ),
				'label_off'   => __( 'Show', 'rtcl-elementor-builder' ),
				'default'     => 'yes',
				'description' => __( 'Switch to show/hide quantity field.', 'rtcl-elementor-builder' ),
			),
			'rtcl_show_add_to_cart_btn' => array(
				'type'        => 'switch',
				'label'       => __( 'Show Add to Cart Button', 'rtcl-elementor-builder' ),
				'label_on'    => __( 'Hide', 'rtcl-elementor-builder' ),
				'label_off'   => __( 'Show', 'rtcl-elementor-builder' ),
				'default'     => 'yes',
				'description' => __( 'Switch to show/hide add to cart button', 'rtcl-elementor-builder' ),
			),
			'rtcl_sec_general_end'      => array(
				'mode' => 'section_end',
			),
		);

		return $fields;
	}

	public function box_style() {
		$fields = array(
			'rtcl_form_box_section_start' => array(
				'mode'  => 'section_start',
				'tab'   => 'style',
				'label' => __( 'Form Box', 'rtcl-elementor-builder' ),
			),
			'rtcl_form_box_bg_color'      => array(
				'type'      => 'color',
				'label'     => __( 'Background Color', 'rtcl-elementor-builder' ),
				'selectors' => array(
					'{{WRAPPER}} .rtcl-add-to-cart-form-wrapper' => 'background-color: {{VALUE}};',
				),
			),
			'rtcl_form_box_padding'       => [
				'mode'       => 'responsive',
				'label'      => __( 'Padding', 'rtcl-elementor-builder' ),
				'type'       => 'dimensions',
				'size_units' => [ 'px', 'em', '%' ],
				'selectors'  => [
					'{{WRAPPER}} .rtcl-add-to-cart-form-wrapper' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			],
			'rtcl_form_box_border'        => [
				'type'     => 'border',
				'label'    => __( 'Border', 'rtcl-elementor-builder' ),
				'mode'     => 'group',
				'selector' => '{{WRAPPER}} .rtcl-add-to-cart-form-wrapper',
			],

			'rtcl_form_box_radius'      => [
				'mode'       => 'responsive',
				'label'      => __( 'Border Radius', 'rtcl-elementor-builder' ),
				'type'       => 'dimensions',
				'size_units' => [ 'px', 'em', '%' ],
				'selectors'  => [
					'{{WRAPPER}} .rtcl-add-to-cart-form-wrapper' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			],
			'rtcl_form_box_section_end' => array(
				'mode' => 'section_end',
			),

		);

		return $fields;
	}

	/**
	 * Set quantity field style controls
	 *
	 * @return array
	 */
	public function quantity_field_style() {
		$fields = array(
			'rtcl_form_field_section_start' => array(
				'mode'  => 'section_start',
				'tab'   => 'style',
				'label' => __( 'Quantity Field', 'rtcl-elementor-builder' ),
			),
			'rtcl_form_field_typo'          => array(
				'mode'     => 'group',
				'type'     => 'typography',
				'label'    => __( 'Typography', 'rtcl-elementor-builder' ),
				'selector' => '{{WRAPPER}} .rtcl-add-to-cart-form-wrapper input[type="number"]"]',
			),
			'rtcl_form_field_bg_color'      => array(
				'type'      => 'color',
				'label'     => __( 'Background', 'rtcl-elementor-builder' ),
				'selectors' => array(
					'{{WRAPPER}} .rtcl-add-to-cart-form-wrapper input[type="number"]' => 'background-color: {{VALUE}};',
				),
			),
			'rtcl_form_field_height'        => [
				'type'       => 'slider',
				'label'      => esc_html__( 'Height', 'rtcl-elementor-builder' ),
				'size_units' => [ 'px' ],
				'selectors'  => [
					'{{WRAPPER}} .rtcl-add-to-cart-form-wrapper input[type="number"]' => 'height: {{SIZE}}{{UNIT}};',
				],
			],
			'rtcl_form_field_border'        => [
				'type'     => 'border',
				'label'    => __( 'Border', 'rtcl-elementor-builder' ),
				'mode'     => 'group',
				'selector' => '{{WRAPPER}} .rtcl-add-to-cart-form-wrapper input[type="number"]',
			],

			'rtcl_form_field_radius' => [
				'mode'       => 'responsive',
				'label'      => __( 'Border Radius', 'rtcl-elementor-builder' ),
				'type'       => 'dimensions',
				'size_units' => [ 'px', 'em', '%' ],
				'selectors'  => [
					'{{WRAPPER}} .rtcl-add-to-cart-form-wrapper input[type="number"]' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			],

			'rtcl_form_field_section_end' => array(
				'mode' => 'section_end',
			),

		);

		return $fields;
	}

	/**
	 * Set add to cart button style controls
	 *
	 * @return array
	 */
	public function add_to_cart_btn_style() {

		$fields = [
			'rtcl_btn_section_start'   => [
				'mode'  => 'section_start',
				'tab'   => 'style',
				'label' => __( 'Add to Cart Button', 'rtcl-elementor-builder' ),
			],
			'rtcl_form_button_typo'    => [
				'mode'     => 'group',
				'type'     => 'typography',
				'label'    => __( 'Typography', 'rtcl-elementor-builder' ),
				'selector' => '{{WRAPPER}} .rtcl-add-to-cart-form-wrapper .btn.btn-primary',
			],
			'rtcl_form_button_padding' => [
				'mode'       => 'responsive',
				'label'      => __( 'Padding', 'rtcl-elementor-builder' ),
				'type'       => 'dimensions',
				'size_units' => [ 'px', 'em', '%' ],
				'selectors'  => [
					'{{WRAPPER}} .rtcl-add-to-cart-form-wrapper .btn.btn-primary' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			],

			'form_button_tabs_start'       => [
				'mode' => 'tabs_start',
			],

			// Tab For Hover view.
			'form_button_tab_normal_start' => [
				'mode'  => 'tab_start',
				'label' => esc_html__( 'Normal', 'rtcl-elementor-builder' ),
			],

			'form_button_bg_color' => [
				'type'      => 'color',
				'label'     => __( 'Background Color', 'rtcl-elementor-builder' ),
				'selectors' => [
					'{{WRAPPER}} .rtcl-add-to-cart-form-wrapper .btn.btn-primary' => 'background-color: {{VALUE}};',
				],
			],

			'form_button_text_color' => [
				'type'      => 'color',
				'label'     => __( 'Text Color', 'rtcl-elementor-builder' ),
				'selectors' => [
					'{{WRAPPER}} .rtcl-add-to-cart-form-wrapper .btn.btn-primary' => 'color: {{VALUE}};',
				],
			],
			'form_button_border'     => [
				'type'     => 'border',
				'mode'     => 'group',
				'label'    => __( 'Border', 'rtcl-elementor-builder' ),
				'selector' => '{{WRAPPER}} .rtcl-add-to-cart-form-wrapper .btn.btn-primary',
			],

			'rtcl_form_button_radius' => [
				'mode'       => 'responsive',
				'label'      => __( 'Border Radius', 'rtcl-elementor-builder' ),
				'type'       => 'dimensions',
				'size_units' => [ 'px', 'em', '%' ],
				'selectors'  => [
					'{{WRAPPER}} .rtcl-add-to-cart-form-wrapper .btn.btn-primary' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			],

			'form_button_tab_normal_end'   => [
				'mode' => 'tab_end',
			],
			'form_button_tab_hover_start'  => [
				'mode'  => 'tab_start',
				'label' => esc_html__( 'Hover', 'rtcl-elementor-builder' ),
			],
			'form_button_bg_color_hover'   => [
				'type'      => 'color',
				'label'     => __( 'Background Color', 'rtcl-elementor-builder' ),
				'selectors' => [
					'{{WRAPPER}} .rtcl-add-to-cart-form-wrapper .btn.btn-primary:hover' => 'background-color: {{VALUE}};',
				],
			],
			'form_button_text_color_hover' => [
				'type'      => 'color',
				'label'     => __( 'Text Color', 'rtcl-elementor-builder' ),
				'selectors' => [
					'{{WRAPPER}} .rtcl-add-to-cart-form-wrapper .btn.btn-primary:hover' => 'color: {{VALUE}};',
				],
			],
			'form_button_border_hover'     => [
				'type'      => 'color',
				'label'     => __( 'Border Color', 'rtcl-elementor-builder' ),
				'selectors' => [
					'{{WRAPPER}} .rtcl-add-to-cart-form-wrapper .btn.btn-primary:hover' => 'border-color: {{VALUE}};',
				],
			],
			'form_button_tab_hover_end'    => [
				'mode' => 'tab_end',
			],
			'form_button_tabs_end'         => [
				'mode' => 'tabs_end',
			],

			'rtcl_btn_section_end' => [
				'mode' => 'section_end',
			],
		];

		return $fields;
	}
}
