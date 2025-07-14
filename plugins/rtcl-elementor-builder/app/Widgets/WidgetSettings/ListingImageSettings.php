<?php
/**
 * Main Elementor ListingImageSettings Class
 *
 * ListingImageSettings main class
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
use RtclElb\Abstracts\ElementorSingleListingBase;

/**
 * ListingImageSettings class
 */
class ListingImageSettings extends ElementorSingleListingBase {
	/**
	 * Set style controlls
	 */
	public function widget_general_fields(): array {
		$fields = array_merge(
			$this->general_fields(),
		);

		return $fields;
	}

	/**
	 * Set style controlls
	 */
	public function widget_style_fields(): array {
		$fields = array_merge(
			$this->zoom_icon_style(),
			$this->arrow_icon_style(),
			$this->thumbnail_image_style()
		);

		return $fields;
	}

	/**
	 * Set style controlls
	 */
	public function general_fields(): array {
		$fields = [
			[
				'mode'  => 'section_start',
				'id'    => 'gallery_general',
				'label' => __( 'General', 'rtcl-elementor-builder' ),
			],
			[
				'type'        => Controls_Manager::SWITCHER,
				'id'          => 'rtcl_enable_feature_image',
				'label'       => __( 'Enable Feature Image', 'rtcl-elementor-builder' ),
				'label_on'    => __( 'On', 'rtcl-elementor-builder' ),
				'label_off'   => __( 'Off', 'rtcl-elementor-builder' ),
				'default'     => 'yes',
				'description' => __( 'Switch to Enable only feature image', 'rtcl-elementor-builder' ),
			],
			[
				'type'        => Controls_Manager::SWITCHER,
				'id'          => 'rtcl_enable_gallery_image',
				'label'       => __( 'Enable Gallery Image', 'rtcl-elementor-builder' ),
				'label_on'    => __( 'On', 'rtcl-elementor-builder' ),
				'label_off'   => __( 'Off', 'rtcl-elementor-builder' ),
				'default'     => 'yes',
				'description' => __( 'Switch to Enable only feature image', 'rtcl-elementor-builder' ),
			],
			[
				'type'        => Controls_Manager::SWITCHER,
				'id'          => 'rtcl_enable_slider',
				'label'       => __( 'Enable slider', 'rtcl-elementor-builder' ),
				'label_on'    => __( 'On', 'rtcl-elementor-builder' ),
				'label_off'   => __( 'Off', 'rtcl-elementor-builder' ),
				'default'     => 'yes',
				'description' => __( 'Switch to Enable Slider', 'rtcl-elementor-builder' ),
				//				'condition' => [ 'rtcl_enable_feature_image!' => 'yes' ]
			],
			[
				'type'        => Controls_Manager::SWITCHER,
				'id'          => 'rtcl_enable_thumb_slider',
				'label'       => __( 'Show Thumbnail', 'rtcl-elementor-builder' ),
				'label_on'    => __( 'On', 'rtcl-elementor-builder' ),
				'label_off'   => __( 'Off', 'rtcl-elementor-builder' ),
				'default'     => 'yes',
				'description' => __( 'Switch to Enable Slider Thumbnails ', 'rtcl-elementor-builder' ),
				//				'condition' => [ 'rtcl_enable_feature_image!' => 'yes' ]
			],
			[
				'type'        => Controls_Manager::SWITCHER,
				'id'          => 'rtcl_show_video',
				'label'       => __( 'Show Video', 'rtcl-elementor-builder' ),
				'label_on'    => __( 'On', 'rtcl-elementor-builder' ),
				'label_off'   => __( 'Off', 'rtcl-elementor-builder' ),
				'default'     => 'yes',
				'description' => __( 'Switch to Show Video', 'rtcl-elementor-builder' ),
			],
			[
				'type'        => Controls_Manager::SWITCHER,
				'id'          => 'rtcl_show_arrow',
				'label'       => __( 'Show Arrow', 'rtcl-elementor-builder' ),
				'label_on'    => __( 'On', 'rtcl-elementor-builder' ),
				'label_off'   => __( 'Off', 'rtcl-elementor-builder' ),
				'default'     => 'yes',
				'description' => __( 'Switch to Show Slider Arrow', 'rtcl-elementor-builder' ),
				//				'condition' => [ 'rtcl_enable_feature_image!' => 'yes' ]
			],
			[
				'type'        => Controls_Manager::SWITCHER,
				'id'          => 'rtcl_show_lightbox_icon',
				'label'       => __( 'Show Lightbox Icon', 'rtcl-elementor-builder' ),
				'label_on'    => __( 'On', 'rtcl-elementor-builder' ),
				'label_off'   => __( 'Off', 'rtcl-elementor-builder' ),
				'default'     => 'yes',
				'description' => __( 'Switch to Show Lightbox icon', 'rtcl-elementor-builder' ),
			],
			[
				'type'        => Controls_Manager::SWITCHER,
				'id'          => 'rtcl_enable_zoom',
				'label'       => __( 'Enable Image Zoom', 'rtcl-elementor-builder' ),
				'label_on'    => __( 'On', 'rtcl-elementor-builder' ),
				'label_off'   => __( 'Off', 'rtcl-elementor-builder' ),
				'default'     => 'yes',
				'description' => __( 'Switch to Enable Image Zoom ', 'rtcl-elementor-builder' ),
			],
			[
				'type'        => Controls_Manager::SWITCHER,
				'id'          => 'rtcl_show_badge',
				'label'       => __( 'Show Badge', 'rtcl-elementor-builder' ),
				'label_on'    => __( 'On', 'rtcl-elementor-builder' ),
				'label_off'   => __( 'Off', 'rtcl-elementor-builder' ),
				'default'     => 'yes',
				'description' => __( 'Switch to Show Badge', 'rtcl-elementor-builder' ),
			],

			[
				'label'       => __( 'Image Size', 'rtcl-elementor-builder' ),
				'type'        => Group_Control_Image_Size::get_type(),
				'id'          => 'rtcl_thumb_image',
				'exclude'     => [ 'custom' ],
				'mode'        => 'group',
				'default'     => 'rtcl-gallery',
				'separator'   => 'none',
				'description' => __( 'Select An Image Size', 'rtcl-elementor-builder' ),

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
	public function zoom_icon_style() {
		$fields = [
			[
				'mode'  => 'section_start',
				'id'    => 'gallery_zoom_icon',
				'tab'   => Controls_Manager::TAB_STYLE,
				'label' => __( 'Zoom', 'rtcl-elementor-builder' ),
			],
			[
				'type'      => Controls_Manager::COLOR,
				'id'        => 'gallery_zoom_icon_bg_color',
				'label'     => __( 'Icon Background Color', 'rtcl-elementor-builder' ),
				'selectors' => [
					'{{WRAPPER}} .rtcl-slider .rtcl-listing-gallery__trigger' => 'background: {{VALUE}};',
				],
			],
			[
				'type'      => Controls_Manager::COLOR,
				'id'        => 'gallery_zoom_icon_hover_bg_color',
				'label'     => __( 'Icon Hover Background', 'rtcl-elementor-builder' ),
				'selectors' => [
					'{{WRAPPER}} .rtcl-slider .rtcl-listing-gallery__trigger:hover' => 'background: {{VALUE}};',
				],
			],
			[
				'type'      => Controls_Manager::COLOR,
				'id'        => 'gallery_zoom_icon_color',
				'label'     => __( 'Icon color', 'rtcl-elementor-builder' ),
				'selectors' => [
					'{{WRAPPER}} .rtcl-slider .rtcl-listing-gallery__trigger i' => 'color: {{VALUE}};',
				],
			],
			[
				'type'      => Controls_Manager::COLOR,
				'id'        => 'gallery_zoom_icon_hover_color',
				'label'     => __( 'Icon Hover Color', 'rtcl-elementor-builder' ),
				'selectors' => [
					'{{WRAPPER}} .rtcl-slider .rtcl-listing-gallery__trigger:hover i' => 'color: {{VALUE}};',
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
	public function arrow_icon_style() {
		$fields = [
			[
				'mode'  => 'section_start',
				'id'    => 'gallery_carousel_icon',
				'tab'   => Controls_Manager::TAB_STYLE,
				'label' => __( 'Carousel Arrow', 'rtcl-elementor-builder' ),
			],
			[
				'type'      => Controls_Manager::COLOR,
				'id'        => 'gallery_carousel_icon_bg',
				'label'     => __( 'Icon Background Color', 'rtcl-elementor-builder' ),
				'selectors' => [
					'{{WRAPPER}} .swiper-button-prev, {{WRAPPER}} .swiper-container-rtl .swiper-button-next, {{WRAPPER}} .swiper-button-next, {{WRAPPER}} .swiper-container-rtl .swiper-button-prev'      => 'background: {{VALUE}};',
				],
			],
			[
				'type'      => Controls_Manager::COLOR,
				'id'        => 'gallery_carousel_icon_hover_bg',
				'label'     => __( 'Icon Hover Background', 'rtcl-elementor-builder' ),
				'selectors' => [
					'{{WRAPPER}} .swiper-button-prev:hover, {{WRAPPER}} .swiper-container-rtl .swiper-button-next:hover, {{WRAPPER}} .swiper-button-next:hover, {{WRAPPER}} .swiper-container-rtl .swiper-button-prev:hover'      => 'background: {{VALUE}};',
				],
			],
			[
				'type'      => Controls_Manager::COLOR,
				'id'        => 'gallery_carousel_icon_color',
				'label'     => __( 'Icon color', 'rtcl-elementor-builder' ),
				'selectors' => [
					'{{WRAPPER}} .swiper-button-prev:after, {{WRAPPER}} .swiper-rtl .swiper-button-next:after, {{WRAPPER}} .swiper-button-next:after, {{WRAPPER}} .swiper-rtl .swiper-button-prev:after '       => 'color: {{VALUE}};',
				],
			],
			[
				'type'      => Controls_Manager::COLOR,
				'id'        => 'gallery_carousel_icon_hover_color',
				'label'     => __( 'Icon Hover color', 'rtcl-elementor-builder' ),
				'selectors' => [
					'{{WRAPPER}} .swiper-button-prev:hover:after, {{WRAPPER}} .swiper-rtl .swiper-button-next:hover:after, {{WRAPPER}} .swiper-button-next:hover:after, {{WRAPPER}} .swiper-rtl .swiper-button-prev:hover:after'       => 'color: {{VALUE}};',
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
	public function thumbnail_image_style() {
		$fields = [
			[
				'mode'      => 'section_start',
				'id'        => 'gallery_thumbnail_style',
				'tab'       => Controls_Manager::TAB_STYLE,
				'label'     => __( 'Thumbnail Style', 'rtcl-elementor-builder' ),
				'condition' => [ 'rtcl_enable_thumb_slider' => [ 'yes' ] ],
			],
			[
				'type'       => Controls_Manager::SLIDER,
				'id'         => 'thumbnail_size_width',
				'label'      => esc_html__( 'Thumbnail Width', 'rtcl-elementor-builder' ),
				'size_units' => [ 'px', '%', 'em', 'rem', 'custom' ],
				'range' => [
					'px' => [
						'min' => 0,
						'max' => 200,
						'step' => 1,
					],
					'%' => [
						'min' => 0,
						'max' => 100,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .rtcl-slider-nav .rtcl-slider-thumb-item' => 'width: {{SIZE}}{{UNIT}};',
				],
			],
			[
				'type'       => Controls_Manager::SLIDER,
				'id'         => 'thumbnail_size_height',
				'label'      => esc_html__( 'Thumbnail Height', 'rtcl-elementor-builder' ),
				'size_units' => [ 'px', '%', 'em', 'rem', 'custom' ],
				'range' => [
					'px' => [
						'min' => 0,
						'max' => 200,
						'step' => 1,
					],
					'%' => [
						'min' => 0,
						'max' => 100,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .rtcl-slider-nav .rtcl-slider-thumb-item' => 'height: {{SIZE}}{{UNIT}};',
				],
			],
			[
				'mode'       => 'responsive',
				'label'      => __( 'Thumbnails Margin', 'rtcl-elementor-builder' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'id'         => 'rtcl_title_spacing',
				'size_units' => [ 'px', 'em', '%' ],
				'selectors'  => [
					'{{WRAPPER}} .rtcl-slider-nav .rtcl-slider-thumb-item' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			],
			[
				'type'       => Controls_Manager::SLIDER,
				'id'         => 'thumbnail_gap',
				'label'      => esc_html__( 'Thumbnail Gap', 'rtcl-elementor-builder' ),
				'size_units' => [ 'px' ],
				'selectors' => [
					'{{WRAPPER}} .rtcl-slider-nav .swiper-wrapper' => 'gap: {{SIZE}}{{UNIT}};',
				],
			],
			[
				'mode' => 'section_end',
			],
		];

		return $fields;
	}
}
