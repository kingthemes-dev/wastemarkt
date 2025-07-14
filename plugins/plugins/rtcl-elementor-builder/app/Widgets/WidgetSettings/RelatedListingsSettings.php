<?php
/**
 * Main Elementor RelatedListingsSettings Class.
 *
 * RelatedListingsSettings main class
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
use Elementor\Group_Control_Image_Size;
use RadisuTheme\ClassifiedListingToolkits\Admin\Elementor\ELWidgetsTraits\ElSliderTrait;
use RadisuTheme\ClassifiedListingToolkits\Admin\Elementor\ELWidgetsTraits\ListingStyleTrait;
use RtclElb\Abstracts\ElementorSingleListingBase;

/**
 * RelatedListingsSettings class.
 */
class RelatedListingsSettings extends ElementorSingleListingBase {
	// Slider Related traits
	use ListingStyleTrait;

	// Slider Related traits
	use ElSliderTrait;

	/**
	 * Set field controlls.
	 */
	public function widget_general_fields(): array {
		$fields = array_merge(
			$this->general_fields(),
			$this->slider_content_visiblity(),
			$this->slider_options()
			// $this->slider_responsive()
		);

		return $fields;
	}

	/**
	 * Set style controlls.
	 */
	public function widget_style_fields(): array {
		$fields = array_merge(
			$this->related_listing_wrapper(),
			$this->widget_style_image_wrapper(),
			$this->widget_style_sec_title(),
			$this->widget_style_sec_meta(),
			$this->widget_style_sec_description(),
			$this->sec_price(),
			$this->widget_style_badge_section(),
			$this->action_button(),
			$this->widget_style_slider_pagination_fields()
		);
		return apply_filters( 'rtcl_el_related_listing_widget_style_field', $fields, $this );

		return $fields;
	}

	/**
	 * Set style controlls.
	 */
	public function action_button(): array {
		$fields       = $this->widget_style_action_button();
		$after_remove = $this->remove_controls(
			[
				'rtcl_details_button_bg_color',
				'rtcl_details_button_text_color',
				'rtcl_action_button_text_color',
				'rtcl_details_button_bg_hover_color',
				'rtcl_details_button_text_hover_color',
				'rtcl_action_button_hover_text_color',
				'rtcl_button_typo',
			],
			$fields
		);
		$the_array    = [
			[
				'id'    => 'rtcl_button_bg_color',
				'unset' => [
					'conditions',
				],
			],
			[
				'id'    => 'rtcl_button_text_color',
				'unset' => [
					'conditions',
				],
			],
			[
				'id'        => 'rtcl_button_border_color',
				'unset'     => [
					'conditions',
				],
				'condition' => [ 'rtcl_listings_grid_style' => [ 'style-5' ] ],
			],
			[
				'id'    => 'rtcl_button_bg_hover_color',
				'unset' => [
					'conditions',
				],
			],
			[
				'id'    => 'rtcl_button_hover_text_color',
				'unset' => [
					'conditions',
				],
			],
			[
				'id'        => 'rtcl_button_hover_border_color',
				'unset'     => [
					'conditions',
				],
				'condition' => [ 'rtcl_listings_grid_style' => [ 'style-5' ] ],
			],
		];
		$modified     = $this->modify_controls( $the_array, $after_remove );

		return $modified;
	}

	/**
	 * Set style controlls.
	 */
	public function sec_price(): array {
		$fields    = $this->widget_style_sec_price();
		$the_array = [
			[
				'id'        => 'rtcl_amount_bg_color',
				'condition' => [
					'rtcl_listings_grid_style' => [ 'style-5' ],
				],
				'unset'     => [
					'conditions',
				],
			],
			[
				'id'        => 'rtcl_amount_wrapper_padding',
				'condition' => [
					'rtcl_listings_grid_style' => [ 'style-5' ],
				],
				'unset'     => [
					'conditions',
				],
			],
		];

		$modified = $this->modify_controls( $the_array, $fields );

		return $modified;
	}

	/**
	 * Set style controlls.
	 */
	public function related_listing_wrapper(): array {
		$fields    = $this->widget_listing_wrapper();
		$fields    = $this->remove_controls(
			[
				'rtcl_wrapper_gutter_spacing',
			],
			$fields
		);
		$the_array = [
			[
				'id'    => 'rtcl_wrapper_spacing',
				'unset' => [
					'conditions',
				],
			],
		];
		$modified  = $this->modify_controls( $the_array, $fields );

		return $modified;
	}

	/**
	 * Undocumented function.
	 *
	 * @return array
	 */
	public function general_fields() {
		$fields = [
			[
				'mode'  => 'section_start',
				'id'    => 'rtcl_sec_general',
				'label' => __( 'General', 'rtcl-elementor-builder' ),
			],
			[
				'id'      => 'rtcl_listings_per_page',
				'label'   => __( 'Listing Per page', 'rtcl-elementor-builder' ),
				'type'    => Controls_Manager::NUMBER,
				'min'     => 1,
				'max'     => 30,
				'default' => 10,
			],
			[
				'type'        => Controls_Manager::SELECT2,
				'id'          => 'rtcl_listings_filter',
				'label'       => __( 'Listing Criteria', 'rtcl-elementor-builder' ),
				'options'     => [
					'category'     => esc_html__( 'Same Category', 'rtcl-elementor-builder' ),
					'location'     => esc_html__( 'Same Location', 'rtcl-elementor-builder' ),
					'listing_type' => esc_html__( 'Same Type', 'rtcl-elementor-builder' ),
					'author'       => esc_html__( 'Same Author', 'rtcl-elementor-builder' ),
				],
				'default'     => [ 'category' ],
				'multiple'    => true,
				'label_block' => true,
				'description' => esc_html__( 'Default value: Same Category ', 'rtcl-elementor-builder' ),
			],
			[
				'type'            => Controls_Manager::RAW_HTML,
				'id'              => 'rtcl_el_layout_note',
				'raw'             => sprintf(
					'<h3 class="rtcl-elementor-group-heading">%s</h3>',
					__( 'View', 'classified-listing' )
				),
				'content_classes' => 'elementor-panel-heading-title',
			],
			[
				'type'    => 'rtcl-image-selector',
				'id'      => 'rtcl_listings_view',
				'options' => $this->listings_view(),
				'default' => 'grid',
			],
			[
				'type'            => Controls_Manager::RAW_HTML,
				'id'              => 'rtcl_el_style_note',
				'raw'             => sprintf(
					'<h3 class="rtcl-elementor-group-heading">%s</h3>',
					__( 'Style', 'rtcl-elementor-builder' )
				),
				'content_classes' => 'elementor-panel-heading-title',
			],
			[
				'type'    => 'rtcl-image-selector',
				'id'      => 'rtcl_listings_grid_style',
				'options' => $this->grid_style(),
				'default' => 'style-1',
			],
			[
				'type'    => Controls_Manager::SELECT,
				'mode'    => 'responsive',
				'id'      => 'rtcl_listings_column',
				'label'   => __( 'Column', 'rtcl-elementor-builder' ),
				'options' => $this->column_number(),
				'default' => '4',
				'devices' => [ 'desktop', 'tablet', 'mobile' ],
			],
			[
				'label'     => __( 'Image Size', 'rtcl-elementor-builder' ),
				'type'      => Group_Control_Image_Size::get_type(),
				'id'        => 'rtcl_thumb_image',
				'exclude'   => [ 'custom' ],
				'mode'      => 'group',
				'default'   => 'rtcl-thumbnail',
				'separator' => 'none',
			],
			[
				'type'        => Controls_Manager::SWITCHER,
				'id'          => 'rtcl_enable_slider',
				'label'       => __( 'Enable Slider', 'classified-listing' ),
				'label_on'    => __( 'On', 'classified-listing' ),
				'label_off'   => __( 'Off', 'classified-listing' ),
				'default'     => 'yes',
				'description' => __( 'Related listing slider only for grid layout. Default: On', 'rtcl-elementor-builder' ),
				'condition'   => [ 'rtcl_listings_view' =>'grid' ],
			],
			[
				'type'       => Controls_Manager::SLIDER,
				'id'         => 'related_glisting_list_item_gap',
				'label'      => esc_html__( 'Items Gap', 'rtcl-elementor-builder' ),
				'size_units' => [ 'px' ],
				'selectors'  => [
					'{{WRAPPER}} .related-listing-wrapper' => 'gap: {{SIZE}}{{UNIT}};',
					'{{WRAPPER}} .rtcl.rtcl-elementor-widget .listing-item' => 'gap: {{SIZE}}{{UNIT}};',
				],
				'condition'   => [ 'rtcl_enable_slider!' =>'yes' ],
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
	public function widget_style_slider_pagination_fields(): array {
		$fields = [
			[
				'mode'       => 'section_start',
				'id'         => 'rtcl_sec_navigation',
				'tab'        => Controls_Manager::TAB_STYLE,
				'label'      => __( 'Slider Navigation', 'rtcl-elementor-builder' ),
				'conditions' => [
					'relation' => 'or',
					'terms'    => [
						[
							'name'     => 'slider_dots',
							'operator' => '==',
							'value'    => 'yes',
						],
						[
							'name'     => 'slider_nav',
							'operator' => '==',
							'value'    => 'yes',
						],
					],
				],
			],

			[
				'label'      => __( 'Arrow Navigation Border Radius', 'rtcl-elementor-builder' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'mode'       => 'responsive',
				'id'         => 'rtcl_arrow_navigation_border_radius',
				'size_units' => [ 'px', 'em', '%' ],
				'selectors'  => [
					'{{WRAPPER}} .rtcl-el-slider-wrapper .rtcl-slider-btn' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
				'condition'  => [
					'slider_nav' => 'yes',
				],
			],

			[
				'type'      => Controls_Manager::COLOR,
				'id'        => 'rtcl_navigation_bg_color',
				'label'     => __( 'Background Color', 'rtcl-elementor-builder' ),
				'selectors' => [ '{{WRAPPER}} .rtcl-el-slider-wrapper .rtcl-slider-btn' => 'background: {{VALUE}}' ],
				'condition' => [
					'slider_nav' => 'yes',
				],
			],
			[
				'type'      => Controls_Manager::COLOR,
				'id'        => 'rtcl_navigation_bg_color_hover',
				'label'     => __( 'Hover Background Color', 'rtcl-elementor-builder' ),
				'selectors' => [ '{{WRAPPER}} .rtcl-el-slider-wrapper .rtcl-slider-btn:hover' => 'background: {{VALUE}}' ],
				'condition' => [
					'slider_nav' => 'yes',
				],
			],

			[
				'type'      => Controls_Manager::COLOR,
				'id'        => 'rtcl_navigation_icon_color',
				'label'     => __( 'Icon Color', 'rtcl-elementor-builder' ),
				'selectors' => [ '{{WRAPPER}} .rtcl-el-slider-wrapper .rtcl-slider-btn' => 'color: {{VALUE}}' ],
				'condition' => [
					'slider_nav' => 'yes',
				],
			],
			[
				'type'      => Controls_Manager::COLOR,
				'id'        => 'rtcl_navigation_icon_color_hover',
				'label'     => __( 'Icon Hover Color', 'rtcl-elementor-builder' ),
				'selectors' => [ '{{WRAPPER}} .rtcl-el-slider-wrapper .rtcl-slider-btn:hover' => 'color: {{VALUE}}' ],
				'condition' => [
					'slider_nav' => 'yes',
				],
			],

			[
				'label'     => esc_html__( 'Dot Navigation Settings', 'rtcl-elementor-builder' ),
				'id'        => 'navigation_control_heading',
				'type'      => \Elementor\Controls_Manager::HEADING,
				'separator' => 'before',
				'condition' => [
					'slider_dots' => 'yes',
				],
			],

			[
				'type'       => Controls_Manager::SLIDER,
				'separator'  => 'before',
				'id'         => 'rtcl_dot_navigation_spacing',
				'label'      => __( 'Dot Navigation Spacing', 'rtcl-elementor-builder' ),
				'size_units' => [ 'px' ],
				'range'      => [
					'px' => [
						'min' => 0,
						'max' => 200,
					],
				],
				'default'    => [
					'unit' => 'px',
					'size' => '30',
				],
				'selectors'  => [
					'{{WRAPPER}} .rtcl-el-slider-wrapper .rtcl-slider-pagination.swiper-pagination-bullets' => 'bottom: -{{SIZE}}{{UNIT}};',
				],
				'condition'  => [
					'slider_dots' => 'yes',
				],
			],

			[
				'type'      => Controls_Manager::COLOR,
				'id'        => 'rtcl_dot_navigation_bg_color',
				'label'     => __( 'Background Color', 'rtcl-elementor-builder' ),
				'selectors' => [
					'{{WRAPPER}} .rtcl-el-slider-wrapper .rtcl-slider-pagination .swiper-pagination-bullet'                => 'background: {{VALUE}}',
					'{{WRAPPER}} .rtcl-slider-pagination-style-2 .rtcl-slider-pagination .swiper-pagination-bullet'        => 'border-color: {{VALUE}}',
					'{{WRAPPER}} .rtcl-slider-pagination-style-4 .rtcl-slider-pagination .swiper-pagination-bullet::after' => 'background-color: {{VALUE}}',
				],
				'condition' => [
					'slider_dots' => 'yes',
				],
			],
			[
				'type'      => Controls_Manager::COLOR,
				'id'        => 'rtcl_dot_navigation_bg_color_hover',
				'label'     => __( 'Active Background Color', 'rtcl-elementor-builder' ),
				'selectors' => [
					'{{WRAPPER}}  .rtcl-el-slider-wrapper .rtcl-slider-pagination .swiper-pagination-bullet.swiper-pagination-bullet-active'               => 'background: {{VALUE}}',
					'{{WRAPPER}} .rtcl-slider-pagination-style-4 .rtcl-slider-pagination .swiper-pagination-bullet.swiper-pagination-bullet-active::after' => 'background-color: {{VALUE}}',
					'{{WRAPPER}} .rtcl-slider-pagination-style-2 .rtcl-slider-pagination .swiper-pagination-bullet.swiper-pagination-bullet-active'        => 'border-color: {{VALUE}}',
					'{{WRAPPER}} .rtcl-slider-pagination-style-4 .rtcl-slider-pagination .swiper-pagination-bullet.swiper-pagination-bullet-active'        => 'border-color: {{VALUE}}',
				],
				'condition' => [
					'slider_dots' => 'yes',
				],
			],

			[
				'mode' => 'section_end',
			],
		];
		return apply_filters( 'rtcl_el_listing_slider_widget_pagination_style_field', $fields, $this );
	}
}
