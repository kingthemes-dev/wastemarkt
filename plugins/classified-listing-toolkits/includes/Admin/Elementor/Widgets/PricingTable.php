<?php
/**
 * Main Elementor PricingTable Class
 *
 * PricingTable main class
 *
 * @author  RadiusTheme
 * @since   2.0.9
 * @package  Classifid-listing
 * @version 1.2
 */

namespace RadisuTheme\ClassifiedListingToolkits\Admin\Elementor\Widgets;

use Elementor\Controls_Manager;
use Elementor\Group_Control_Typography;
use RadisuTheme\ClassifiedListingToolkits\Hooks\Helper;
use RadisuTheme\ClassifiedListingToolkits\Abstracts\ElementorWidgetBase;
use Rtcl\Helpers\Functions;
use \Elementor\Icons_Manager;
use Elementor\Group_Control_Border;
use Rtcl\Helpers\Link;

/**
 * PricingTable Class
 */
class PricingTable extends ElementorWidgetBase {
	/**
	 * Construct function
	 *
	 * @param array  $data Some data.
	 * @param [type] $args some arg.
	 */
	public function __construct( $data = array(), $args = null ) {
		$this->rtcl_name = __( 'Pricing Table', 'classified-listing-toolkits' );
		$this->rtcl_base = 'rtcl-pricing-table';
		parent::__construct( $data, $args );
	}
	/**
	 * Defandancy style
	 *
	 * @return array
	 */
	public function get_style_depends() {
		return array( 'elementor-icons-shared-0', 'elementor-icons-fa-regular', 'elementor-icons-fa-solid' );
	}

	/**
	 * Set Query controlls
	 *
	 * @return array
	 */
	public function widget_general_fields(): array {
		$fields = array_merge(
			$this->general_fields(),
			$this->general_feature_fields(),
			$this->general_button_fields(),
		);
		return $fields;
	}
	/**
	 * Set Query controlls
	 *
	 * @return array
	 */
	public function widget_style_fields(): array {
		$fields = array_merge(
			$this->style_wrapper_fields(),
			$this->style_badge_fields(),
			$this->style_header_icon_fields(),
			$this->style_header_fields(),
			$this->style_body_fields(),
			$this->style_footer_fields(),
			$this->style_title_fields(),
			$this->style_price_fields(),
			$this->style_feature_fields(),
			$this->style_button_fields(),
		);
		return $fields;
	}

	/**
	 * General Functionality.
	 *
	 * @return array
	 */
	public function general_fields() {

		$fields = array(
			array(
				'mode'  => 'section_start',
				'id'    => 'sec_general',
				'label' => __( 'General', 'classified-listing-toolkits' ),
			),
			array(
				'type'    => Controls_Manager::SELECT,
				'id'      => 'style',
				'label'   => __( 'Style', 'classified-listing-toolkits' ),
				'options' => array(
					'view-1' => __( 'Style 1', 'classified-listing-toolkits' ),
					'view-2' => __( 'Style 2', 'classified-listing-toolkits' ),
					'view-3' => __( 'Style 3', 'classified-listing-toolkits' ),
				),
				'default' => 'view-1',
			),
			array(
				'type'    => Controls_Manager::TEXT,
				'id'      => 'title',
				'label'   => __( 'Title', 'classified-listing-toolkits' ),
				'default' => 'Combo Bundle',
			),
			array(
				'type'        => Controls_Manager::TEXT,
				'id'          => 'currency',
				'label'       => __( 'Currency Symbol', 'classified-listing-toolkits' ),
				'default'     => '$',
				'description' => __( 'Currency sign eg. $', 'classified-listing-toolkits' ),
			),
			// Currency position just flex reverse.
			array(
				'type'    => Controls_Manager::SELECT,
				'id'      => 'currency_position',
				'label'   => __( 'Currency Position', 'classified-listing-toolkits' ),
				'options' => array(
					'left'  => __( 'Left', 'classified-listing-toolkits' ),
					'right' => __( 'Right', 'classified-listing-toolkits' ),
				),
				'default' => 'left',
			),
			array(
				'type'    => Controls_Manager::TEXT,
				'id'      => 'price',
				'label'   => __( 'Price', 'classified-listing-toolkits' ),
				'default' => '0',
			),
			array(
				'type'        => Controls_Manager::SWITCHER,
				'id'          => 'show_per_sign',
				'label'       => __( 'Show Per Sign', 'classified-listing-toolkits' ),
				'label_on'    => __( 'On', 'classified-listing-toolkits' ),
				'label_off'   => __( 'Off', 'classified-listing-toolkits' ),
				'default'     => 'yes',
				'description' => __( 'Show or Hide Per Sign. Default: On', 'classified-listing-toolkits' ),
				'condition' => array(
					'style!' => 'view-3',
				),
			),
			array(
				'type'        => Controls_Manager::TEXT,
				'id'          => 'unit',
				'label'       => __( 'Unit Name', 'classified-listing-toolkits' ),
				'default'     => 'mo',
				'description' => __( "eg. month or year. Keep empty if you don't want to show unit", 'classified-listing-toolkits' ),
			),
			array(
				'type'    => Controls_Manager::TEXT,
				'id'      => 'badge',
				'label'   => __( 'Badge', 'classified-listing-toolkits' ),
				'default' => '',
			),
			array(
				'type'      => Controls_Manager::ICONS,
				'id'        => 'box_icon',
				'label'     => esc_html__( 'Header Icon', 'classified-listing-toolkits' ),
				'default'   => array(
					'value'   => 'far fa-paper-plane',
					'library' => 'solid',
				),
				'condition' => array(
					'style' => 'view-3',
				),
			),
			array(
				'type'    => Controls_Manager::CHOOSE,
				'id'      => 'content_alignment',
				'label'   => __( 'Content Alignment', 'classified-listing-toolkits' ),
				'options' => $this->alignment_options(),
				'default' => '',
			),
			array(
				'mode' => 'section_end',
			),
		);
		return $fields;
	}

	/**
	 * Feature Section
	 *
	 * @return array
	 */
	public function general_feature_fields() {

		$fields = array(
			array(
				'mode'  => 'section_start',
				'id'    => 'sec_general_feature',
				'label' => __( 'Feature', 'classified-listing-toolkits' ),
			),
			array(
				'type'    => Controls_Manager::SELECT,
				'id'      => 'features_type',
				'label'   => __( 'Features Text As', 'classified-listing-toolkits' ),
				'options' => array(
					'liststyle'   => __( 'List Style', 'classified-listing-toolkits' ),
					'description' => __( 'Description', 'classified-listing-toolkits' ),
				),
				'default' => 'liststyle',
			),

			array(
				'type'        => Controls_Manager::REPEATER,
				'id'          => 'features_list',
				'mode'        => 'repeater',
				'fields'      => array(
					'list_title' => array(
						'label'       => esc_html__( 'Title', 'classified-listing-toolkits' ),
						'type'        => Controls_Manager::TEXT,
						'default'     => esc_html__( 'List Title', 'classified-listing-toolkits' ),
						'label_block' => true,
					),
					'list_icon'  => array(
						'type'    => Controls_Manager::ICONS,
						'label'   => esc_html__( 'Icon Before Title', 'classified-listing-toolkits' ),
						'default' => array(),
					),
				),
				'default'     => array(
					array(
						'list_title' => '3 Regular Ads ',
					),
					array(
						'list_title' => 'No Featured Ads',
					),
					array(
						'list_title' => 'No Ads will be bumped up',
					),
					array(
						'list_title' => 'Limited Support ',
					),
				),
				'title_field' => '{{{ elementor.helpers.renderIcon( this, list_icon, {}, "i", "panel" ) || \'<i class="{{ icon }} " aria-hidden="true"></i>\' }}} {{{ list_title }}}',
				'condition'   => array(
					'features_type' => 'liststyle',
				),
			),
			array(
				'type'        => Controls_Manager::TEXTAREA,
				'id'          => 'features',
				'label'       => __( 'Features', 'classified-listing-toolkits' ),
				'default'     => "3 Regular Ads  \r\n No Featured Ads  \r\n No Top Ads  \r\nNo Ads will be bumped up  \r\nLimited Support",
				'rows'        => 10,
				'description' => __( 'One line per feature eg. 10 Ads per month Featured on first week', 'classified-listing-toolkits' ),
				'condition'   => array(
					'features_type' => 'description',
				),
			),
			array(
				'type'      => Controls_Manager::ICONS,
				'id'        => 'list_icon',
				'label'     => esc_html__( 'Icon Before List', 'classified-listing-toolkits' ),
				'default'   => array(),
				'condition' => array(
					'features_type' => 'description',
				),
			),

			array(
				'type'       => Controls_Manager::SLIDER,
				'id'         => 'list_icon_size',
				'label'      => esc_html__( 'Icon Size', 'classified-listing-toolkits' ),
				'size_units' => array( 'px' ),
				'mode'       => 'responsive',
				'range'      => array(
					'px' => array(
						'min' => 5,
						'max' => 50,
					),
				),
				'default'    => array(
					'unit' => 'px',
					'size' => 15,
				),
				'selectors'  => array(
					'{{WRAPPER}} .rtcl-el-pricing-box .rtcl-el-pricing-features i' => 'font-size: {{SIZE}}{{UNIT}};',
					'{{WRAPPER}} .rtcl-el-pricing-box .rtcl-el-pricing-features svg' => 'width: {{SIZE}}{{UNIT}}; height: {{SIZE}}{{UNIT}};',
				),
			),
			array(
				'type'       => Controls_Manager::SLIDER,
				'id'         => 'list_icon_gap',
				'label'      => esc_html__( 'Icon Gap', 'classified-listing-toolkits' ),
				'size_units' => array( 'px' ),
				'mode'       => 'responsive',
				'range'      => array(
					'px' => array(
						'min' => 5,
						'max' => 50,
					),
				),
				'default'    => array(
					'unit' => 'px',
					'size' => 10,
				),
				'selectors'  => array(
					'{{WRAPPER}} .rtcl-el-pricing-box .rtcl-el-pricing-features i' => 'margin-right: {{SIZE}}{{UNIT}};',
					'{{WRAPPER}} .rtcl-el-pricing-box .rtcl-el-pricing-features svg' => 'margin-right: {{SIZE}}{{UNIT}};',
				),
			),
			array(
				'type'       => Controls_Manager::SLIDER,
				'id'         => 'space_between_list',
				'label'      => esc_html__( 'Space Between List', 'classified-listing-toolkits' ),
				'size_units' => array( 'px' ),
				'mode'       => 'responsive',
				'range'      => array(
					'px' => array(
						'min' => 5,
						'max' => 50,
					),
				),
				'default'    => array(
					'unit' => 'px',
					'size' => 10,
				),
				'selectors'  => array(
					'{{WRAPPER}} .rtcl-el-pricing-box .rtcl-el-pricing-features ul' => 'gap: {{SIZE}}{{UNIT}};',
				),
			),
			array(
				'mode' => 'section_end',
			),
		);
		return $fields;
	}
	/**
	 * Button Section
	 *
	 * @return array
	 */
	public function general_button_fields() {
		$args           = array(
			'post_type'        => 'rtcl_pricing',
			'posts_per_page'   => -1,
			'suppress_filters' => false,
			'orderby'          => 'title',
			'order'            => 'ASC',
			'post_status'      => 'publish',
		);
		$posts          = get_posts( $args );
		$posts_dropdown = array( '0' => __( '--Select--', 'classified-listing-toolkits' ) );
		foreach ( $posts as $post ) {
			$posts_dropdown[ $post->ID ] = $post->post_title;
		}

		$fields = array(
			array(
				'mode'  => 'section_start',
				'id'    => 'sec_button_section',
				'label' => __( 'Button', 'classified-listing-toolkits' ),
			),

			array(
				'type'    => Controls_Manager::TEXT,
				'id'      => 'btntext',
				'label'   => __( 'Button Text', 'classified-listing-toolkits' ),
				'default' => 'Buy now',
			),
			array(
				'type'    => Controls_Manager::SELECT,
				'id'      => 'btntype',
				'label'   => __( 'Button Link Type', 'classified-listing-toolkits' ),
				'options' => array(
					'page'   => __( 'Pricing Page Link', 'classified-listing-toolkits' ),
					'custom' => __( 'Custom Link', 'classified-listing-toolkits' ),
				),
				'default' => 'custom',
			),
			array(
				'type'        => Controls_Manager::URL,
				'id'          => 'buttonurl',
				'label'       => __( 'Button URL', 'classified-listing-toolkits' ),
				'placeholder' => 'https://your-link.com',
				'condition'   => array( 'btntype' => array( 'custom' ) ),
			),
			array(
				'type'      => Controls_Manager::SELECT,
				'id'        => 'page',
				'label'     => __( 'Select Pricing', 'classified-listing-toolkits' ),
				'options'   => $posts_dropdown,
				'default'   => '0',
				'condition' => array( 'btntype' => array( 'page' ) ),
			),
			array(
				'type'    => Controls_Manager::ICONS,
				'id'      => 'button_icon',
				'label'   => esc_html__( 'Button Icon', 'classified-listing-toolkits' ),
				'default' => array(),
			),
			array(
				'type'       => Controls_Manager::SLIDER,
				'id'         => 'button_icon_size',
				'label'      => esc_html__( 'Icon Size', 'classified-listing-toolkits' ),
				'size_units' => array( 'px' ),
				'mode'       => 'responsive',
				'range'      => array(
					'px' => array(
						'min' => 5,
						'max' => 50,
					),
				),
				'default'    => array(
					'unit' => 'px',
					'size' => 15,
				),
				'selectors'  => array(
					'{{WRAPPER}} .rtcl-el-pricing-box .rtcl-el-pricing-button a i' => 'font-size: {{SIZE}}{{UNIT}};',
				),
				'condition'  => array(
					'features_type' => 'liststyle',
				),
			),
			array(
				'type'       => Controls_Manager::SLIDER,
				'id'         => 'nutton_icon_gap',
				'label'      => esc_html__( 'Icon Gap', 'classified-listing-toolkits' ),
				'size_units' => array( 'px' ),
				'mode'       => 'responsive',
				'range'      => array(
					'px' => array(
						'min' => 5,
						'max' => 50,
					),
				),
				'default'    => array(
					'unit' => 'px',
					'size' => 10,
				),
				'selectors'  => array(
					'{{WRAPPER}} .rtcl-el-pricing-box .rtcl-el-pricing-button a i' => 'margin-left: {{SIZE}}{{UNIT}};',
				),
				'condition'  => array(
					'features_type' => 'liststyle',
				),
			),
			array(
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
	public function style_wrapper_fields() {

		$fields = array(
			array(
				'mode'  => 'section_start',
				'id'    => 'sec_pricing_wrapper',
				'tab'   => Controls_Manager::TAB_STYLE,
				'label' => __( 'Wrapper', 'classified-listing-toolkits' ),
			),
			array(
				'mode'       => 'responsive',
				'label'      => __( 'Wrapper Spacing', 'classified-listing-toolkits' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'id'         => 'rtcl_wrapper_spacing',
				'size_units' => array( 'px', 'em', '%' ),
				'selectors'  => array(
					'{{WRAPPER}} .rtcl-el-pricing-box' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			),
			array(
				'type'           => Group_Control_Border::get_type(),
				'label'          => __( 'Border', 'classified-listing-toolkits' ),
				'mode'           => 'group',
				'id'             => 'rtcl_pricing_border',
				'fields_options' => array(
					'border' => array(
						'default' => 'solid',
					),
					'width'  => array(
						'default' => array(
							'top'      => '1',
							'right'    => '1',
							'bottom'   => '1',
							'left'     => '1',
							'isLinked' => false,
						),
					),
					'color'  => array(
						'default' => 'rgb(206 206 206 / 75%)',
					),
				),
				'selector'       => '{{WRAPPER}} .rtcl-el-pricing-box',

			),
			array(
				'label'      => __( 'Border Radius', 'classified-listing-toolkits' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'id'         => 'rtcl_wrapper_border_radius',
				'size_units' => array( 'px', 'em', '%' ),
				'selectors'  => array(
					'{{WRAPPER}} .rtcl-el-pricing-box' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			),
			array(
				'type'      => Controls_Manager::COLOR,
				'id'        => 'bgcolor',
				'label'     => __( 'Background', 'classified-listing-toolkits' ),
				'default'   => '',
				'selectors' => array( '{{WRAPPER}} .rtcl-el-pricing-box' => 'background-color: {{VALUE}}' ),
			),
			array(
				'mode' => 'section_end',
			),

		);
		return $fields;
	}
	/**
	 * Title Settings
	 *
	 * @return array
	 */
	public function style_header_fields() {
		$fields = array(
			array(
				'mode'      => 'section_start',
				'id'        => 'sec_style_Header',
				'tab'       => Controls_Manager::TAB_STYLE,
				'label'     => __( 'Header', 'classified-listing-toolkits' ),
				'condition' => array(
					'style' => 'view-2',
				),
			),
			array(
				'label'      => __( 'Header Spacing', 'classified-listing-toolkits' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'id'         => 'rtcl_header_spacing',
				'size_units' => array( 'px' ),
				'selectors'  => array(
					'{{WRAPPER}} .rtcl-el-pricing-box .pricing-header' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			),
			array(
				'type'      => Controls_Manager::COLOR,
				'id'        => 'header_bgcolor',
				'label'     => __( 'Background', 'classified-listing-toolkits' ),
				'selectors' => array( '{{WRAPPER}} .rtcl-el-pricing-box .pricing-header' => 'background-color: {{VALUE}}' ),
			),
			array(
				'mode' => 'section_end',
			),
		);
		return $fields;
	}
	/**
	 * Title Settings
	 *
	 * @return array
	 */
	public function style_header_icon_fields() {
		$fields = array(
			array(
				'mode'      => 'section_start',
				'id'        => 'sec_style_header_icon',
				'tab'       => Controls_Manager::TAB_STYLE,
				'label'     => __( 'Header Icon', 'classified-listing-toolkits' ),
				'condition' => array(
					'style' => 'view-3',
				),
			),
			array(
				'type'      => Controls_Manager::COLOR,
				'id'        => 'header_icon_bgcolor',
				'label'     => __( 'Background', 'classified-listing-toolkits' ),
				'selectors' => array( '{{WRAPPER}} .rtcl-el-pricing-box .box-icon:after, {{WRAPPER}} .rtcl-el-pricing-box .box-icon:before' => 'background-color: {{VALUE}}' ),
			),
			array(
				'type'      => Controls_Manager::COLOR,
				'id'        => 'header_icon_color',
				'label'     => __( 'Color', 'classified-listing-toolkits' ),
				'selectors' => array( '{{WRAPPER}} .rtcl-el-pricing-box .box-icon i' => 'color: {{VALUE}}' ),
			),
			array(
				'type'       => Controls_Manager::SLIDER,
				'id'         => 'header_icon_size',
				'label'      => esc_html__( 'Icon Size', 'classified-listing-toolkits' ),
				'size_units' => array( 'px' ),
				'mode'       => 'responsive',
				'range'      => array(
					'px' => array(
						'min' => 20,
						'max' => 100,
					),
				),
				'default'    => array(
					'unit' => 'px',
					'size' => 36,
				),
				'selectors'  => array(
					'{{WRAPPER}} .rtcl-el-pricing-box .box-icon i' => 'font-size: {{SIZE}}{{UNIT}};',
				),
			),
			array(
				'mode' => 'section_end',
			),
		);
		return $fields;
	}
	/**
	 * Body Settings
	 *
	 * @return array
	 */
	public function style_body_fields() {
		$fields = array(
			array(
				'mode'      => 'section_start',
				'id'        => 'sec_style_body',
				'tab'       => Controls_Manager::TAB_STYLE,
				'label'     => __( 'Body', 'classified-listing-toolkits' ),
				'condition' => array(
					'style' => 'view-2',
				),
			),
			array(
				'label'      => __( 'Body Spacing', 'classified-listing-toolkits' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'id'         => 'rtcl_body_spacing',
				'size_units' => array( 'px' ),
				'selectors'  => array(
					'{{WRAPPER}} .rtcl-el-pricing-box .pricing-body' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			),
			array(
				'mode' => 'section_end',
			),
		);
		return $fields;
	}
	/**
	 * Body Settings
	 *
	 * @return array
	 */
	public function style_footer_fields() {
		$fields = array(
			array(
				'mode'      => 'section_start',
				'id'        => 'sec_style_footer',
				'tab'       => Controls_Manager::TAB_STYLE,
				'label'     => __( 'Footer', 'classified-listing-toolkits' ),
				'condition' => array(
					'style' => 'view-2',
				),
			),
			array(
				'label'      => __( 'Pricing Footer Spacing', 'classified-listing-toolkits' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'id'         => 'rtcl_footer_spacing',
				'size_units' => array( 'px' ),
				'selectors'  => array(
					'{{WRAPPER}} .rtcl-el-pricing-box .pricing-footer' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			),
			array(
				'type'      => Controls_Manager::COLOR,
				'id'        => 'footer_bgcolor',
				'label'     => __( 'Background', 'classified-listing-toolkits' ),
				'selectors' => array( '{{WRAPPER}} .rtcl-el-pricing-box .pricing-footer' => 'background-color: {{VALUE}}' ),
			),
			array(
				'mode' => 'section_end',
			),
		);
		return $fields;
	}
	/**
	 * Title Settings
	 *
	 * @return array
	 */
	public function style_badge_fields() {
		$fields = array(
			array(
				'mode'      => 'section_start',
				'id'        => 'sec_style_badge',
				'tab'       => Controls_Manager::TAB_STYLE,
				'label'     => __( 'Badge', 'classified-listing-toolkits' ),
				'condition' => array(
					'badge!' => '',
				),
			),
			array(
				'mode'     => 'group',
				'type'     => Group_Control_Typography::get_type(),
				'id'       => 'badge_typo',
				'label'    => __( 'Typography', 'classified-listing-toolkits' ),
				'selector' => '{{WRAPPER}} .rtcl-el-pricing-box .pricing-label',
			),
			array(
				'type'      => Controls_Manager::COLOR,
				'id'        => 'badge_bgcolor',
				'label'     => __( 'Background', 'classified-listing-toolkits' ),
				'selectors' => array( '{{WRAPPER}} .rtcl-el-pricing-box .pricing-label' => 'background-color: {{VALUE}}' ),
			),
			array(
				'type'      => Controls_Manager::COLOR,
				'id'        => 'badge_color',
				'label'     => __( 'Color', 'classified-listing-toolkits' ),
				'selectors' => array( '{{WRAPPER}} .rtcl-el-pricing-box .pricing-label' => 'color: {{VALUE}}' ),
			),
			array(
				'label'      => __( 'Width', 'classified-listing-toolkits' ),
				'type'       => Controls_Manager::SLIDER,
				'id'         => 'badge_width',
				'size_units' => array( 'px' ),
				'range'      => array(
					'px' => array(
						'min' => 150,
						'max' => 350,
					),
				),
				'selectors'  => array(
					'{{WRAPPER}} .rtcl-el-pricing-box .pricing-label' => 'width: {{SIZE}}{{UNIT}};',
				),
			),
			array(
				'label'      => __( 'Height', 'classified-listing-toolkits' ),
				'type'       => Controls_Manager::SLIDER,
				'id'         => 'badge_height',
				'size_units' => array( 'px' ),
				'range'      => array(
					'px' => array(
						'min' => 80,
						'max' => 200,
					),
				),
				'selectors'  => array(
					'{{WRAPPER}} .rtcl-el-pricing-box .pricing-label' => 'height: {{SIZE}}{{UNIT}};',
				),
			),
			array(
				'mode' => 'section_end',
			),
		);
		return $fields;
	}
	/**
	 * Title Settings
	 *
	 * @return array
	 */
	public function style_title_fields() {
		$fields = array(
			array(
				'mode'  => 'section_start',
				'id'    => 'sec_style_title',
				'tab'   => Controls_Manager::TAB_STYLE,
				'label' => __( 'Title', 'classified-listing-toolkits' ),
			),
			array(
				'mode'     => 'group',
				'type'     => Group_Control_Typography::get_type(),
				'id'       => 'title_typo',
				'label'    => __( 'Typography', 'classified-listing-toolkits' ),
				'selector' => '{{WRAPPER}} .rtcl-el-pricing-box .rtcl-el-pricing-title',
			),
			array(
				'label'      => __( 'Title Spacing', 'classified-listing-toolkits' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'id'         => 'rtcl_title_spacing',
				'size_units' => array( 'px', 'em', '%' ),
				'selectors'  => array(
					'{{WRAPPER}} .rtcl-el-pricing-box .rtcl-el-pricing-title' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			),
			array(
				'type'      => Controls_Manager::COLOR,
				'id'        => 'title_color',
				'label'     => __( 'Color', 'classified-listing-toolkits' ),
				'selectors' => array( '{{WRAPPER}} .rtcl-el-pricing-box .rtcl-el-pricing-title' => 'color: {{VALUE}}' ),
			),
			array(
				'mode' => 'section_end',
			),
		);
		return $fields;
	}
	/**
	 * Price Settings
	 *
	 * @return array
	 */
	public function style_price_fields() {
		$fields = array(
			array(
				'mode'  => 'section_start',
				'id'    => 'sec_style_price',
				'tab'   => Controls_Manager::TAB_STYLE,
				'label' => __( 'Price', 'classified-listing-toolkits' ),
			),

			array(
				'type'      => Controls_Manager::COLOR,
				'id'        => 'price_color',
				'label'     => __( 'Color', 'classified-listing-toolkits' ),
				'selectors' => array( '{{WRAPPER}} .rtcl-el-pricing-box .rtcl-el-price' => 'color: {{VALUE}}' ),
			),
			array(
				'mode'     => 'group',
				'type'     => Group_Control_Typography::get_type(),
				'id'       => 'price_typo',
				'label'    => __( 'Typography', 'classified-listing-toolkits' ),
				'selector' => '{{WRAPPER}} .rtcl-el-pricing-box .rtcl-el-price',
			),
			array(
				'mode'     => 'group',
				'type'     => Group_Control_Typography::get_type(),
				'id'       => 'price_currency_typo',
				'label'    => __( 'Currency Typography', 'classified-listing-toolkits' ),
				'selector' => '{{WRAPPER}} .rtcl-el-pricing-box .rtcl-el-pricing-currency',
			),
			array(
				'label'      => __( 'Spacing', 'classified-listing-toolkits' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'id'         => 'rtcl_price_spacing',
				'size_units' => array( 'px', 'em', '%' ),
				'selectors'  => array(
					'{{WRAPPER}} .rtcl-el-pricing-box .rtcl-el-pricing-price' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			),
			array(
				'mode'     => 'group',
				'type'     => Group_Control_Typography::get_type(),
				'id'       => 'unit_typo',
				'label'    => __( 'Unit Typography', 'classified-listing-toolkits' ),
				'selector' => '{{WRAPPER}} .rtcl-el-pricing-box .rtcl-el-pricing-price .rtcl-el-pricing-duration',
			),
			array(
				'type'      => Controls_Manager::COLOR,
				'id'        => 'unit_Color',
				'label'     => __( 'Unit Color', 'classified-listing-toolkits' ),
				'selectors' => array( '{{WRAPPER}} .rtcl-el-pricing-box .rtcl-el-pricing-price .rtcl-el-pricing-duration' => 'color: {{VALUE}}' ),
			),
			array(
				'mode' => 'section_end',
			),
		);
		return $fields;
	}
	/**
	 * Feature Settings
	 *
	 * @return array
	 */
	public function style_feature_fields() {
		$fields = array(
			array(
				'mode'  => 'section_start',
				'id'    => 'sec_style_feature',
				'tab'   => Controls_Manager::TAB_STYLE,
				'label' => __( 'Feature', 'classified-listing-toolkits' ),
			),
			array(
				'mode'     => 'group',
				'type'     => Group_Control_Typography::get_type(),
				'id'       => 'features_typo',
				'label'    => __( 'Typography', 'classified-listing-toolkits' ),
				'selector' => '{{WRAPPER}} .rtcl-el-pricing-box .rtcl-el-pricing-features, {{WRAPPER}} .rtcl-el-pricing-box .rtcl-el-pricing-features li',
			),
			array(
				'type'      => Controls_Manager::COLOR,
				'id'        => 'features_color',
				'label'     => __( 'Color', 'classified-listing-toolkits' ),
				'selectors' => array(
					'{{WRAPPER}} .rtcl-el-pricing-box .rtcl-el-pricing-features' => 'color: {{VALUE}}',
				),
			),
			array(
				'label'      => __( 'Features Margin', 'classified-listing-toolkits' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'id'         => 'rtcl_feature_spacing',
				'size_units' => array( 'px', 'em', '%' ),
				'selectors'  => array(
					'{{WRAPPER}} .rtcl-el-pricing-box .rtcl-el-pricing-features' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			),
			array(
				'type'      => Controls_Manager::COLOR,
				'id'        => 'features_icon_color',
				'label'     => __( 'Icon Color', 'classified-listing-toolkits' ),
				'selectors' => array(
					'{{WRAPPER}} .rtcl-el-pricing-box .rtcl-el-pricing-features i' => 'color: {{VALUE}}',
					'{{WRAPPER}} .rtcl-el-pricing-box .rtcl-el-pricing-features svg path' => 'fill: {{VALUE}}'
				),
			),

			array(
				'mode' => 'section_end',
			),
		);
		return $fields;
	}
	/**
	 * Button Settings
	 *
	 * @return array
	 */
	public function style_button_fields() {
		$fields = array(
			array(
				'mode'  => 'section_start',
				'id'    => 'sec_style_button',
				'tab'   => Controls_Manager::TAB_STYLE,
				'label' => __( 'Button', 'classified-listing-toolkits' ),
			),
			array(
				'mode'     => 'group',
				'type'     => Group_Control_Typography::get_type(),
				'id'       => 'btn_typo',
				'label'    => __( 'Typography', 'classified-listing-toolkits' ),
				'selector' => '{{WRAPPER}} .rtcl-el-pricing-box .rtcl-el-pricing-button a',
			),
			array(
				'label'      => __( 'Button Spacing', 'classified-listing-toolkits' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'id'         => 'rtcl_button_spacing',
				'size_units' => array( 'px', 'em', '%' ),
				'selectors'  => array(
					'{{WRAPPER}} .rtcl-el-pricing-box .rtcl-el-pricing-button' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			),
			array(
				'label'      => __( 'Button Padding', 'classified-listing-toolkits' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'id'         => 'rtcl_button_padding',
				'size_units' => array( 'px', 'em', '%' ),
				'selectors'  => array(
					'{{WRAPPER}} .rtcl-el-pricing-box .rtcl-el-pricing-button a' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			),

			array(
				'label'      => __( 'Min width', 'classified-listing-toolkits' ),
				'type'       => Controls_Manager::SLIDER,
				'id'         => 'rtcl_button_width',
				'size_units' => array( 'px', '%' ),
				'range'      => array(
					'px' => array(
						'min' => 50,
						'max' => 250,
					),
					'%'  => array(
						'min' => 1,
						'max' => 100,
					),
				),
				'selectors'  => array(
					'{{WRAPPER}} .rtcl-el-pricing-box .rtcl-el-pricing-button a' => 'min-width: {{SIZE}}{{UNIT}};',
				),
			),
			array(
				'label'      => __( 'Border Radius', 'classified-listing-toolkits' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'id'         => 'rtcl_button_border_radius',
				'size_units' => array( 'px', 'em', '%' ),
				'selectors'  => array(
					'{{WRAPPER}} .rtcl-el-pricing-box .rtcl-el-pricing-button a' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			),
			array(
				'mode' => 'tabs_start',
				'id'   => 'button_tabs_start',
			),
			// Tab For normal view.
			array(
				'mode'  => 'tab_start',
				'id'    => 'rtcl_button_normal',
				'label' => esc_html__( 'Normal', 'classified-listing-toolkits' ),
			),
			array(
				'type'      => Controls_Manager::COLOR,
				'id'        => 'btn_bg_color',
				'label'     => __( 'Background', 'classified-listing-toolkits' ),
				'selectors' => array( '{{WRAPPER}} .rtcl-el-pricing-box .rtcl-el-pricing-button a' => 'background-color: {{VALUE}}' ),
			),
			array(
				'type'      => Controls_Manager::COLOR,
				'id'        => 'btn_text_color',
				'label'     => __( 'Color', 'classified-listing-toolkits' ),
				'selectors' => array( '{{WRAPPER}} .rtcl-el-pricing-box .rtcl-el-pricing-button a' => 'color: {{VALUE}}' ),
			),
			array(
				'type'           => Group_Control_Border::get_type(),
				'label'          => __( 'Border', 'classified-listing-toolkits' ),
				'mode'           => 'group',
				'id'             => 'rtcl_button_border',
				'fields_options' => array(
					'border' => array(
						'default' => 'solid',
					),
					'width'  => array(
						'default' => array(
							'top'      => '1',
							'right'    => '1',
							'bottom'   => '1',
							'left'     => '1',
							'isLinked' => false,
						),
					),
					'color'  => array(
						'default' => '#5a49f8',
					),
				),
				'selector'       => '{{WRAPPER}} .rtcl-el-pricing-box .rtcl-el-pricing-button a',
			),
			array(
				'mode' => 'tab_end',
			),
			// Tab For Hover view.
			array(
				'mode'  => 'tab_start',
				'id'    => 'rtcl_button_hover',
				'label' => esc_html__( 'Hover', 'classified-listing-toolkits' ),
			),
			array(
				'type'      => Controls_Manager::COLOR,
				'id'        => 'btn_bg_hover_color',
				'label'     => __( 'Background Hover', 'classified-listing-toolkits' ),
				'selectors' => array( '{{WRAPPER}} .rtcl-el-pricing-box .rtcl-el-pricing-button a:hover' => 'background-color: {{VALUE}}' ),
			),
			array(
				'type'      => Controls_Manager::COLOR,
				'id'        => 'btn_text_color_hover',
				'label'     => __( 'Color', 'classified-listing-toolkits' ),
				'selectors' => array( '{{WRAPPER}} .rtcl-el-pricing-box .rtcl-el-pricing-button a:hover' => 'color: {{VALUE}}' ),
			),
			array(
				'type'     => Group_Control_Border::get_type(),
				'label'    => __( 'Border', 'classified-listing-toolkits' ),
				'mode'     => 'group',
				'id'       => 'rtcl_button_border_hover',
				'selector' => '{{WRAPPER}} .rtcl-el-pricing-box .rtcl-el-pricing-button a:hover',
			),
			array(
				'mode' => 'tab_end',
			),
			array(
				'mode' => 'tabs_end',
			),
			array(
				'mode' => 'section_end',
			),
		);
		return $fields;
	}
	/**
	 * Undocumented function
	 *
	 * @param [type] $settings settings.
	 * @return string
	 */
	public function button( $settings ) {
		$btn  = '';
		$attr = '';
		ob_start();
		Icons_Manager::render_icon( $settings['button_icon'], array( 'aria-hidden' => 'true' ) );
		$button_icon = ob_get_clean();

		if ( $settings['btntype'] == 'page' ) {
			$url = '#';
			if ( ! empty( $settings['page'] ) ) {
				$pricing = rtcl()->factory->get_pricing( $settings['page'] );
				$url     = add_query_arg( 'option', $pricing->getId(), Link::get_checkout_endpoint_url( 'membership' ) );
			}
			$attr = 'href="' . $url . '"';
		} else {
			if ( ! empty( $settings['buttonurl']['url'] ) ) {
				$attr  = 'href="' . $settings['buttonurl']['url'] . '"';
				$attr .= ! empty( $settings['buttonurl']['is_external'] ) ? ' target="_blank"' : '';
				$attr .= ! empty( $settings['buttonurl']['nofollow'] ) ? ' rel="nofollow"' : '';
			}
		}

		if ( $settings['btntext'] ) {
			$btn = '<a ' . $attr . '>' . $settings['btntext'] . $button_icon . '</a>';
		}
		return $btn;
	}
	/**
	 * Return all feature list.
	 *
	 * @param [type] $settings main settings.
	 * @return mixed
	 */
	public function feature_html( $settings ) {
		$feature_html = null;
		ob_start();
		Icons_Manager::render_icon( $settings['list_icon'], array( 'aria-hidden' => 'true' ) );
		$icon = ob_get_clean();
		if ( 'liststyle' === $settings['features_type'] ) {
			$features_list = $settings['features_list'];
			foreach ( $features_list as $feature ) {
				if ( ! empty( $feature ) ) {
					ob_start();
					Icons_Manager::render_icon( $feature['list_icon'], array( 'aria-hidden' => 'true' ) );
					$icon          = ob_get_clean();
					$feature_html .= '<li>' . $icon . $feature['list_title'] . '</li>';
				}
			}
			if ( $feature_html ) {
				$feature_html = '<ul>' . $feature_html . '</ul>';
			}
		}
		if ( 'description' === $settings['features_type'] ) {
			$features = preg_split( '/\R/', $settings['features'] ); // string to array
			foreach ( $features as $feature ) {
				if ( ! empty( $feature ) ) {
					$feature_html .= '<li>' . $icon . $feature . '</li>';
				}
			}
			if ( $feature_html ) {
				$feature_html = '<ul>' . $feature_html . '</ul>';
			}
		}
		return $feature_html;
	}
	/**
	 * Render output.
	 *
	 * @return void
	 */
	protected function render() {
		$settings = $this->get_settings();
		$style    = isset( $settings['style'] ) ? $settings['style'] : 'view-1';

		ob_start();
		if ( 'view-3' === $style ) {
			Icons_Manager::render_icon( $settings['box_icon'], array( 'aria-hidden' => 'true' ) );
		}
		$box_icon = ob_get_clean();

		$pricing_label     = ! empty( $settings['badge'] ) ? $settings['badge'] : null;
		$content_alignment = ! empty( $settings['content_alignment'] ) ? $settings['content_alignment'] : 'center';
		$currency_position = 'right' === $settings['currency_position'] ? 'currency-right' : 'currency-left';
		$template_style    = 'elementor/pricing-table/' . $style;
		$data              = array(
			'template'              => $template_style,
			'style'                 => $style,
			'settings'              => $settings,
			'feature_html'          => $this->feature_html( $settings ),
			'btn'                   => $this->button( $settings ),
			'pricing_label'         => $pricing_label,
			'content_alignment'     => $content_alignment,
			'currency_position'     => $currency_position,
			'default_template_path' => Helper::get_plugin_template_path(),
		);
		$data['box_icon']  = $box_icon;
		$data              = apply_filters( 'rtcl_el_pricint_table_data', $data );
		Functions::get_template( $data['template'], $data, '', $data['default_template_path'] );
	}
}
