<?php

/**
 * Main Elementor ListingPriceSettings Class
 *
 * ListingPriceSettings main class
 *
 * @author  RadiusTheme
 * @since   2.0.10
 * @package  RTCL_Elementor_Builder
 * @version 1.2
 */

namespace RtclElb\Widgets\WidgetSettings;

if (!defined('ABSPATH')) {
	exit; // Exit if accessed directly.
}

use Elementor\Controls_Manager;
use RtclElb\Abstracts\ElementorSingleListingBase;
use \Elementor\Group_Control_Typography;

/**
 * ListingPriceSettings class
 */
class ListingPriceSettings extends ElementorSingleListingBase {

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
			$this->price_text_style(),
			$this->divider_after_price_style(),
			$this->price_unit_style(),
			$this->price_type_style()
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
			array(
				'mode'  => 'section_start',
				'id'    => 'rtcl_sec_general',
				'label' => __('General', 'rtcl-elementor-builder'),
			),
			array(
				'type'    => Controls_Manager::SELECT,
				'id'      => 'rtcl_price_style',
				'label'   => __('Style', 'rtcl-elementor-builder'),
				'options' => $this->price_style(),
				'default' => 'style-1',
				'description'   => __('Select Price Style', 'rtcl-elementor-builder'),
			),
			[
				'type'      => Controls_Manager::TEXT,
				'id'        => 'rtcl_divider_price_after',
				'label'     => __('Divider After Price', 'rtcl-elementor-builder'),
				'default'   => __('/', 'rtcl-elementor-builder'),
			],
			array(
				'type'      => Controls_Manager::SWITCHER,
				'id'        => 'rtcl_show_price_unit',
				'label'     => __('Show Price Unit', 'rtcl-elementor-builder'),
				'label_on'  => __('Hide', 'rtcl-elementor-builder'),
				'label_off' => __('Show', 'rtcl-elementor-builder'),
				'default'   => 'yes',
				'description'   => __('Switch to Show Price Unit', 'rtcl-elementor-builder'),
			),
			array(
				'type'      => Controls_Manager::SWITCHER,
				'id'        => 'rtcl_show_price_type',
				'label'     => __('Show Price Type', 'rtcl-elementor-builder'),
				'label_on'  => __('Hide', 'rtcl-elementor-builder'),
				'label_off' => __('Show', 'rtcl-elementor-builder'),
				'default'   => 'yes',
				'description'   => __('Switch to Show Price Type', 'rtcl-elementor-builder'),
			),
			array(
				'mode' => 'section_end',
			),

		);

		return $fields;
	}

	/**
	 * Listings view function
	 *
	 * @return array
	 */
	public function price_style() {
		return rtcl()->has_pro() ? array(
			'style-1' => __('Style 1', 'rtcl-elementor-builder'),
			'style-2' => __('Style 2', 'rtcl-elementor-builder'),
		) : ['style-1' => __('Style 1', 'rtcl-elementor-builder')];
	}
	/**
	 * Set style controlls
	 *
	 * @return array
	 */
	public function price_text_style() {
		$fields = array(
			array(
				'mode'  => 'section_start',
				'id'    => 'Price',
				'tab'   => Controls_Manager::TAB_STYLE,
				'label' => __( 'Price', 'rtcl-elementor-builder' ),
			),
			array(
				'mode'     => 'group',
				'type'     => Group_Control_Typography::get_type(),
				'id'       => 'rtcl_price_typo',
				'label'    => __('Typography', 'rtcl-elementor-builder'),
				'selector' => '{{WRAPPER}} .el-single-addon .rtcl-price',
			),
			array(
				'type'      => Controls_Manager::COLOR,
				'id'        => 'rtcl_price_bg_color',
				'label'     => __('Background Color', 'rtcl-elementor-builder'),
				'selectors' => array(
					'{{WRAPPER}} .el-single-addon .rtcl-price' => 'background-color: {{VALUE}};',
				),
				'condition' => array('rtcl_price_style' => array('style-2')),
			),
			array(
				'type'      => Controls_Manager::COLOR,
				'id'        => 'rtcl_price_color',
				'label'     => __('Color', 'rtcl-elementor-builder'),
				'selectors' => array(
					'{{WRAPPER}} .el-single-addon .rtcl-price' => 'color: {{VALUE}};',
				),
			),
			[
				'type'    => Controls_Manager::CHOOSE,
				'mode'      => 'responsive',
				'id'      => 'text_alignment',
				'label'   => __('Price Alignment', 'rtcl-elementor-builder'),
				'options' => $this->alignment_options(),
				'default' => 'left',
				'selectors' => [
					'{{WRAPPER}} .el-single-addon .rtcl-price, {{WRAPPER}} .el-single-addon.item-price.style-2' => 'justify-content: {{VALUE}};',
				],
			],
			array(
				'mode' => 'section_end',
			),
		);
		return $fields;
	}

	/**
	 * Set style of divider after price
	 *
	 * @return array
	 */
	public function divider_after_price_style() {
		$fields = array(
			array(
				'mode'  => 'section_start',
				'id'    => 'divider_after_price',
				'tab'   => Controls_Manager::TAB_STYLE,
				'label' => __('Divider After Price', 'rtcl-elementor-builder'),
				'conditions' => [
					'relation' => 'or',
					'terms'    => [
						[
							'name'     => 'rtcl_show_price_unit',
							'operator' => '===',
							'value'    => 'yes',
						],
						[
							'name'     => 'rtcl_show_price_type',
							'operator' => '===',
							'value'    => 'yes',
						],
					],
				],
			),
			array(
				'type'      => Controls_Manager::COLOR,
				'id'        => 'divider_after_price_color',
				'label'     => __('Color', 'rtcl-elementor-builder'),
				'selectors' => array(
					'{{WRAPPER}} .el-single-addon .divider-after-price' => 'color: {{VALUE}};',
				),
			),
			[
				'type'       => Controls_Manager::SLIDER,
				'id'         => 'divider_after_price_size',
				'label'      => esc_html__('Size', 'rtcl-elementor-builder'),
				'size_units' => ['px'],
				'selectors'  => [
					'{{WRAPPER}} .el-single-addon .divider-after-price' => 'font-size: {{SIZE}}{{UNIT}};',
				],
			],
			[
				'mode'       => 'responsive',
				'label'      => __('Margin', 'rtcl-elementor-builder'),
				'type'       => Controls_Manager::DIMENSIONS,
				'id'         => 'divider_after_price_margin',
				'size_units' => ['px', 'em', '%'],
				'selectors'  => [
					'{{WRAPPER}} .el-single-addon .divider-after-price' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			],
			array(
				'mode' => 'section_end',
			),
		);
		return $fields;
	}

	/**
	 * Set style of price unit
	 *
	 * @return array
	 */
	public function price_unit_style() {
		$fields = array(
			array(
				'mode'  => 'section_start',
				'id'    => 'listing_price_unit',
				'tab'   => Controls_Manager::TAB_STYLE,
				'label' => __('Price Unit', 'rtcl-elementor-builder'),
				'condition'    => [
					'rtcl_show_price_unit' => 'yes',
				],
			),
			array(
				'type'      => Controls_Manager::COLOR,
				'id'        => 'listing_price_unit_color',
				'label'     => __('Color', 'rtcl-elementor-builder'),
				'selectors' => array(
					'{{WRAPPER}} .el-single-addon .price-unit' => 'color: {{VALUE}};',
				),
			),
			array(
				'mode'     => 'group',
				'type'     => Group_Control_Typography::get_type(),
				'id'       => 'listing_price_unit_typo',
				'label'    => __('Typography', 'rtcl-elementor-builder'),
				'selector' => '{{WRAPPER}} .el-single-addon .price-unit',
			),
			[
				'mode'       => 'responsive',
				'label'      => __('Margin', 'rtcl-elementor-builder'),
				'type'       => Controls_Manager::DIMENSIONS,
				'id'         => 'listing_price_unit_margin',
				'size_units' => ['px', 'em', '%'],
				'selectors'  => [
					'{{WRAPPER}} .el-single-addon .price-unit' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			],
			array(
				'mode' => 'section_end',
			),
		);
		return $fields;
	}

	/**
	 * Set style of price type
	 *
	 * @return array
	 */
	public function price_type_style() {
		$fields = array(
			array(
				'mode'  => 'section_start',
				'id'    => 'listing_price_type',
				'tab'   => Controls_Manager::TAB_STYLE,
				'label' => __('Price Type', 'rtcl-elementor-builder'),
				'condition'    => [
					'rtcl_show_price_type' => 'yes',
				],
			),
			array(
				'type'      => Controls_Manager::COLOR,
				'id'        => 'listing_price_type_color',
				'label'     => __('Color', 'rtcl-elementor-builder'),
				'selectors' => array(
					'{{WRAPPER}} .el-single-addon .price-type' => 'color: {{VALUE}};',
				),
			),
			array(
				'mode'     => 'group',
				'type'     => Group_Control_Typography::get_type(),
				'id'       => 'listing_price_type_typo',
				'label'    => __('Typography', 'rtcl-elementor-builder'),
				'selector' => '{{WRAPPER}} .el-single-addon .price-type',
			),
			[
				'mode'       => 'responsive',
				'label'      => __('Margin', 'rtcl-elementor-builder'),
				'type'       => Controls_Manager::DIMENSIONS,
				'id'         => 'listing_price_type_margin',
				'size_units' => ['px', 'em', '%'],
				'selectors'  => [
					'{{WRAPPER}} .el-single-addon .price-type' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			],
			array(
				'mode' => 'section_end',
			),
		);
		return $fields;
	}
	
}
