<?php

/**
 * Main Elementor ListingBookingSettings Class
 *
 * ListingBookingSettings main class
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
 * ListingBookingSettings class
 */
class ListingBookingSettings extends ElementorSingleListingBase2 {

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
		$fields = array_merge(
			$this->box_style(),
			$this->heading_style(),
			$this->form_field_style(),
			$this->booking_btn_style(),
			$this->booking_info_style(),
		);

		return $fields;
	}

	/**
	 * Set Query controlls
	 *
	 * @return array
	 */
	public function general_fields() {
		$fields = array(
			'rtcl_sec_general_start'      => array(
				'mode'  => 'section_start',
				'label' => __( 'General', 'rtcl-elementor-builder' ),
			),
			'rtcl_show_heading'           => array(
				'type'        => 'switch',
				'label'       => __( 'Show Heading', 'rtcl-elementor-builder' ),
				'label_on'    => __( 'Hide', 'rtcl-elementor-builder' ),
				'label_off'   => __( 'Show', 'rtcl-elementor-builder' ),
				'default'     => 'yes',
				'description' => __( 'Switch to Show Heading', 'rtcl-elementor-builder' ),
			),
			'rtcl_show_heading_indicator' => array(
				'type'        => 'switch',
				'label'       => __( 'Show Heading Indicator', 'rtcl-elementor-builder' ),
				'label_on'    => __( 'Hide', 'rtcl-elementor-builder' ),
				'label_off'   => __( 'Show', 'rtcl-elementor-builder' ),
				'default'     => 'yes',
				'description' => __( 'Switch to Show Heading Indicator', 'rtcl-elementor-builder' ),
				'condition'   => [
					'rtcl_show_heading' => 'yes'
				]
			),
			'rtcl_sec_general_end'        => array(
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
					'{{WRAPPER}} .rtcl-listing-booking-wrap' => 'background-color: {{VALUE}};',
				),
			),
			'rtcl_form_box_padding'       => [
				'mode'       => 'responsive',
				'label'      => __( 'Padding', 'rtcl-elementor-builder' ),
				'type'       => 'dimensions',
				'size_units' => [ 'px', 'em', '%' ],
				'selectors'  => [
					'{{WRAPPER}} .rtcl-listing-booking-wrap' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			],
			'rtcl_form_box_border'        => [
				'type'     => 'border',
				'label'    => __( 'Border', 'rtcl-elementor-builder' ),
				'mode'     => 'group',
				'selector' => '{{WRAPPER}} .rtcl-listing-booking-wrap',
			],

			'rtcl_form_box_radius'      => [
				'mode'       => 'responsive',
				'label'      => __( 'Border Radius', 'rtcl-elementor-builder' ),
				'type'       => 'dimensions',
				'size_units' => [ 'px', 'em', '%' ],
				'selectors'  => [
					'{{WRAPPER}} .rtcl-listing-booking-wrap' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			],
			'rtcl_form_box_section_end' => array(
				'mode' => 'section_end',
			),

		);

		return $fields;
	}


	/**
	 * Set style controlls
	 *
	 * @return array
	 */
	public function heading_style() {
		$fields = array(
			'rtcl_heading_section_start' => array(
				'mode'      => 'section_start',
				'tab'       => 'style',
				'label'     => __( 'Heading', 'rtcl-elementor-builder' ),
				'condition' => [
					'rtcl_show_heading' => 'yes',
				]
			),

			'rtcl_heading_typo'   => array(
				'mode'     => 'group',
				'type'     => 'typography',
				'label'    => __( 'Typography', 'rtcl-elementor-builder' ),
				'selector' => '{{WRAPPER}} .rtcl-listing-side-title h3',
			),
			'rtcl_heading_color'  => array(
				'type'      => 'color',
				'id'        => 'rtcl_heading_color',
				'label'     => __( 'Color', 'rtcl-elementor-builder' ),
				'selectors' => array(
					'{{WRAPPER}} .rtcl-listing-side-title h3' => 'color: {{VALUE}};',
				),
			),
			'rtcl_heading_margin' => array(
				'mode'       => 'responsive',
				'label'      => __( 'Spacing', 'rtcl-elementor-builder' ),
				'type'       => 'dimensions',
				'size_units' => [ 'px', 'em', '%' ],
				'selectors'  => [
					'{{WRAPPER}} .rtcl-listing-side-title h3' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			),

			'rtcl_heading_indicator_color' => array(
				'type'      => 'color',
				'label'     => __( 'Indicator Color', 'rtcl-elementor-builder' ),
				'selectors' => array(
					'{{WRAPPER}} .rtcl-listing-side-title h3:after' => 'background-color: {{VALUE}};',
				),
				'condition' => [
					'rtcl_show_heading_indicator' => 'yes',
				],
			),

			'rtcl_heading_section_end' => array(
				'mode' => 'section_end',
			),
		);

		return $fields;
	}

	/**
	 * Set form field style controls
	 *
	 * @return array
	 */
	public function form_field_style() {
		$fields = array(
			'rtcl_form_field_section_start' => array(
				'mode'  => 'section_start',
				'tab'   => 'style',
				'label' => __( 'Form Field', 'rtcl-elementor-builder' ),
			),
			'rtcl_form_field_typo'          => array(
				'mode'     => 'group',
				'type'     => 'typography',
				'label'    => __( 'Label Typography', 'rtcl-elementor-builder' ),
				'selector' => '{{WRAPPER}} .rtcl-listing-booking-wrap .form-group label,{{WRAPPER}} .rtcl-listing-booking-wrap .available-slots',
			),
			'rtcl_form_field_color'         => array(
				'type'      => 'color',
				'label'     => __( 'Label Color', 'rtcl-elementor-builder' ),
				'selectors' => array(
					'{{WRAPPER}} .rtcl-listing-booking-wrap .form-group label,{{WRAPPER}} .rtcl-listing-booking-wrap .available-slots' => 'color: {{VALUE}};',
				),
			),
			'rtcl_form_field_bg_color'      => array(
				'type'      => 'color',
				'label'     => __( 'Field Background', 'rtcl-elementor-builder' ),
				'selectors' => array(
					'{{WRAPPER}} .rtcl-listing-booking-wrap .form-group .form-control' => 'background-color: {{VALUE}};',
				),
			),
			'rtcl_form_field_height'        => [
				'type'       => 'slider',
				'label'      => esc_html__( 'Field Height', 'rtcl-elementor-builder' ),
				'size_units' => [ 'px' ],
				'selectors'  => [
					'{{WRAPPER}} .rtcl-listing-booking-wrap .form-group .form-control' => 'height: {{SIZE}}{{UNIT}};',
				],
			],
			'rtcl_form_field_border'        => [
				'type'     => 'border',
				'label'    => __( 'Border', 'rtcl-elementor-builder' ),
				'mode'     => 'group',
				'selector' => '{{WRAPPER}} .rtcl-listing-booking-wrap .form-group .form-control',
			],

			'rtcl_form_field_radius' => [
				'mode'       => 'responsive',
				'label'      => __( 'Border Radius', 'rtcl-elementor-builder' ),
				'type'       => 'dimensions',
				'size_units' => [ 'px', 'em', '%' ],
				'selectors'  => [
					'{{WRAPPER}} .rtcl-listing-booking-wrap .form-group .form-control' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			],

			'rtcl_form_field_section_end' => array(
				'mode' => 'section_end',
			),

		);

		return $fields;
	}

	/**
	 * Set booking button style controls
	 *
	 * @return array
	 */
	public function booking_btn_style() {

		$fields = [
			'rtcl_booking_btn_section_start' => [
				'mode'  => 'section_start',
				'tab'   => 'style',
				'label' => __( 'Booking Button', 'rtcl-elementor-builder' ),
			],
			'rtcl_form_button_typo'          => [
				'mode'     => 'group',
				'type'     => 'typography',
				'label'    => __( 'Typography', 'rtcl-elementor-builder' ),
				'selector' => '{{WRAPPER}} .rtcl-listing-booking-wrap .btn.btn-primary',
			],
			'rtcl_form_button_padding'       => [
				'mode'       => 'responsive',
				'label'      => __( 'Padding', 'rtcl-elementor-builder' ),
				'type'       => 'dimensions',
				'size_units' => [ 'px', 'em', '%' ],
				'selectors'  => [
					'{{WRAPPER}} .rtcl-listing-booking-wrap .btn.btn-primary' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
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
					'{{WRAPPER}} .rtcl-listing-booking-wrap .btn.btn-primary' => 'background-color: {{VALUE}};',
				],
			],

			'form_button_text_color' => [
				'type'      => 'color',
				'label'     => __( 'Text Color', 'rtcl-elementor-builder' ),
				'selectors' => [
					'{{WRAPPER}} .rtcl-listing-booking-wrap .btn.btn-primary' => 'color: {{VALUE}};',
				],
			],
			'form_button_border'     => [
				'type'     => 'border',
				'mode'     => 'group',
				'label'    => __( 'Border', 'rtcl-elementor-builder' ),
				'selector' => '{{WRAPPER}} .rtcl-listing-booking-wrap .btn.btn-primary',
			],

			'rtcl_form_button_radius' => [
				'mode'       => 'responsive',
				'label'      => __( 'Border Radius', 'rtcl-elementor-builder' ),
				'type'       => 'dimensions',
				'size_units' => [ 'px', 'em', '%' ],
				'selectors'  => [
					'{{WRAPPER}} .rtcl-listing-booking-wrap .btn.btn-primary' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
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
					'{{WRAPPER}} .rtcl-listing-booking-wrap .btn.btn-primary:hover' => 'background-color: {{VALUE}};',
				],
			],
			'form_button_text_color_hover' => [
				'type'      => 'color',
				'label'     => __( 'Text Color', 'rtcl-elementor-builder' ),
				'selectors' => [
					'{{WRAPPER}} .rtcl-listing-booking-wrap .btn.btn-primary:hover' => 'color: {{VALUE}};',
				],
			],
			'form_button_border_hover'     => [
				'type'      => 'color',
				'label'     => __( 'Border Color', 'rtcl-elementor-builder' ),
				'selectors' => [
					'{{WRAPPER}} .rtcl-listing-booking-wrap .btn.btn-primary:hover' => 'border-color: {{VALUE}};',
				],
			],
			'form_button_tab_hover_end'    => [
				'mode' => 'tab_end',
			],
			'form_button_tabs_end'         => [
				'mode' => 'tabs_end',
			],

			'rtcl_booking_btn_section_end' => [
				'mode' => 'section_end',
			],
		];

		return $fields;
	}


	/**
	 * Set booking info style controls
	 *
	 * @return array
	 */
	public function booking_info_style() {
		$fields = array(
			'booking_info_section_start' => array(
				'mode'  => 'section_start',
				'tab'   => 'style',
				'label' => __( 'Booking Info', 'rtcl-elementor-builder' ),
			),
			'rtcl_booking_info_typo'     => array(
				'mode'     => 'group',
				'type'     => 'typography',
				'label'    => __( 'Typography', 'rtcl-elementor-builder' ),
				'selector' => '{{WRAPPER}} .rtcl-listing-booking-wrap .rtcl-booking-info',
			),
			'rtcl_booking_info_color'    => array(
				'type'      => 'color',
				'label'     => __( 'Color', 'rtcl-elementor-builder' ),
				'selectors' => array(
					'{{WRAPPER}} .rtcl-listing-booking-wrap .rtcl-booking-info' => 'color: {{VALUE}};',
				),
			),
			'rtcl_booking_info_margin'   => array(
				'mode'       => 'responsive',
				'label'      => __( 'Spacing', 'rtcl-elementor-builder' ),
				'type'       => 'dimensions',
				'size_units' => [ 'px', 'em', '%' ],
				'selectors'  => [
					'{{WRAPPER}} .rtcl-listing-booking-wrap .rtcl-booking-info' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			),
			'booking_info_section_end'   => array(
				'mode' => 'section_end',
			),
		);

		return $fields;
	}
}
