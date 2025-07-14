<?php
/**
 * Main Elementor ListingArchiveSettings Class
 *
 * ListingArchiveSettings main class
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
use Elementor\Group_Control_Border;
use Elementor\Group_Control_Box_Shadow;
use Elementor\Group_Control_Typography;
use RadisuTheme\ClassifiedListingToolkits\Abstracts\ElListingsWidgetBase;
use RadisuTheme\ClassifiedListingToolkits\Admin\Elementor\ELWidgetsTraits\ArchiveGeneralTrait;
use RadisuTheme\ClassifiedListingToolkits\Admin\Elementor\ELWidgetsTraits\ListingStyleTrait;

/**
 * ListingArchiveSettings class
 */
class ListingArchiveSettings extends ElListingsWidgetBase {
	use ListingStyleTrait;
	/**
	 * Action General Section.
	 */
	use ArchiveGeneralTrait;

	/**
	 * Undocumented function
	 *
	 * @param array $data default data.
	 * @param array $args default arg.
	 */
	public function __construct( $data = [], $args = null ) {
		parent::__construct( $data, $args );
		$this->rtcl_category = 'rtcl-elementor-archive-widgets'; // Category.
	}
	/**
	 * Set field controlls
	 *
	 * @return array
	 */
	public function widget_general_fields(): array {
		$fields = array_merge(
			$this->archive_general_fields(),
			$this->listing_layout_control(),
			$this->listing_content_visibility_fields(),
			// $this->listing_responsive_control()
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
			$this->widget_listing_toolbar_bar(),
			$this->widget_listing_wrapper(),
			$this->listing_promotion_section(),
			$this->widget_style_image_wrapper(),
			$this->widget_style_sec_title(),
			$this->widget_style_sec_meta(),
			$this->widget_style_sec_description(),
			$this->widget_style_sec_price(),
			$this->widget_style_badge_section(),
			$this->widget_style_action_button(),
			$this->widget_style_sec_pagination()
		);
		return apply_filters( 'rtcl_el_archive_listing_widget_style_field', $fields, $this );
	}
	/**
	 * Set Layout controlls
	 *
	 * @return array
	 */
	public function listing_layout_control() {
		$fields = [
			[
				'mode'  => 'section_start',
				'id'    => 'rtcl_layout_general',
				'label' => __( 'Layout', 'rtcl-elementor-builder' ),
			],
			[
				'type'            => Controls_Manager::RAW_HTML,
				'id'              => 'rtcl_el_layout_note',
				'raw'             => sprintf(
					'<h3 class="rtcl-elementor-group-heading">%s</h3>',
					__( 'Default View', 'rtcl-elementor-builder' )
				),
				'content_classes' => 'elementor-panel-heading-title',
			],
			[
				'type'    => 'rtcl-image-selector',
				'id'      => 'rtcl_listings_view',
				'options' => $this->listings_view(),
				'default' => 'list',
			],
			[
				'type'            => Controls_Manager::RAW_HTML,
				'id'              => 'listings_style_note',
				'raw'             => sprintf(
					'<h3 class="rtcl-elementor-group-heading">%s</h3>',
					__( 'List Style', 'rtcl-elementor-builder' )
				),
				'content_classes' => 'elementor-panel-heading-title',
				'conditions'      => [
					'relation' => 'or',
					'terms'    => [
						[
							'terms' => [
								[
									'name'     => 'rtcl_listings_view',
									'operator' => '===',
									'value'    => 'list',
								],
							],
						],
						[
							'terms' => [
								[
									'name'     => 'rtcl_listings_view',
									'operator' => '!==',
									'value'    => 'list',
								],
								[
									'name'     => 'rtcl_archive_view_switcher',
									'operator' => '===',
									'value'    => 'yes',
								],
							],
						],
					],
				],
			],
			[
				'type'       => 'rtcl-image-selector',
				'id'         => 'rtcl_listings_style',
				'options'    => $this->list_style(),
				'default'    => 'style-1',
				'conditions' => [
					'relation' => 'or',
					'terms'    => [
						[
							'terms' => [
								[
									'name'     => 'rtcl_listings_view',
									'operator' => '===',
									'value'    => 'list',
								],
							],
						],
						[
							'terms' => [
								[
									'name'     => 'rtcl_listings_view',
									'operator' => '!==',
									'value'    => 'list',
								],
								[
									'name'     => 'rtcl_archive_view_switcher',
									'operator' => '===',
									'value'    => 'yes',
								],
							],
						],
					],
				],
			],
			[
				'type'            => Controls_Manager::RAW_HTML,
				'id'              => 'listings_grid_style_note',
				'raw'             => sprintf(
					'<h3 class="rtcl-elementor-group-heading">%s</h3>',
					__( 'Grid Style', 'rtcl-elementor-builder' )
				),
				'content_classes' => 'elementor-panel-heading-title',
				'conditions'      => [
					'relation' => 'or',
					'terms'    => [
						[
							'terms' => [
								[
									'name'     => 'rtcl_listings_view',
									'operator' => '===',
									'value'    => 'grid',
								],
							],
						],
						[
							'terms' => [
								[
									'name'     => 'rtcl_listings_view',
									'operator' => '!==',
									'value'    => 'grid',
								],
								[
									'name'     => 'rtcl_archive_view_switcher',
									'operator' => '===',
									'value'    => 'yes',
								],
							],
						],
					],
				],
			],
			[
				'type'       => 'rtcl-image-selector',
				'id'         => 'rtcl_listings_grid_style',
				'options'    => $this->grid_style(),
				'default'    => 'style-1',
				'conditions' => [
					'relation' => 'or',
					'terms'    => [
						[
							'terms' => [
								[
									'name'     => 'rtcl_listings_view',
									'operator' => '===',
									'value'    => 'grid',
								],
							],
						],
						[
							'terms' => [
								[
									'name'     => 'rtcl_listings_view',
									'operator' => '!==',
									'value'    => 'grid',
								],
								[
									'name'     => 'rtcl_archive_view_switcher',
									'operator' => '===',
									'value'    => 'yes',
								],
							],
						],
					],
				],
			],
			[
				'type'       => Controls_Manager::SELECT,
				'mode'       => 'responsive',
				'id'         => 'rtcl_listings_column',
				'label'      => __( 'Grid Column', 'rtcl-elementor-builder' ),
				'options'    => $this->column_number(),
				'default'    => '3',
				'devices'    => [ 'desktop', 'tablet', 'mobile' ],
				'conditions' => [
					'relation' => 'or',
					'terms'    => [
						[
							'terms' => [
								[
									'name'     => 'rtcl_listings_view',
									'operator' => '===',
									'value'    => 'grid',
								],
							],
						],
						[
							'terms' => [
								[
									'name'     => 'rtcl_listings_view',
									'operator' => '!==',
									'value'    => 'grid',
								],
								[
									'name'     => 'rtcl_archive_view_switcher',
									'operator' => '===',
									'value'    => 'yes',
								],
							],
						],
					],
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
	public function widget_listing_toolbar_bar() {
		$fields = [
			[
				'mode'  => 'section_start',
				'id'    => 'rtcl_listing_toolbar',
				'tab'   => Controls_Manager::TAB_STYLE,
				'label' => __( 'Archive Listing Toolbar', 'rtcl-elementor-builder' ),
			],
			[
				'type'       => Controls_Manager::SLIDER,
				'id'         => 'list_toolbar_gap',
				'label'      => esc_html__( 'Toolbar Gap', 'rtcl-elementor-builder' ),
				'size_units' => [ 'px' ],
				'selectors'  => [
					'{{WRAPPER}} .rtcl-listings-wrapper .rtcl-listings-actions' => 'gap: {{SIZE}}{{UNIT}};',
				],
			],
			[
				'type'            => Controls_Manager::RAW_HTML,
				'id'              => 'rtcl_el_archive_toolbar_note',
				'raw'             => sprintf(
					'<h3 class="rtcl-elementor-group-heading">%s</h3>',
					__( 'Result Count', 'rtcl-elementor-builder' )
				),
				'content_classes' => 'elementor-panel-heading-title',
			],
			[
				'mode'     => 'group',
				'type'     => Group_Control_Typography::get_type(),
				'id'       => 'rtcl_count_typo',
				'label'    => __( 'Typography', 'rtcl-elementor-builder' ),
				'selector' => '{{WRAPPER}} .rtcl-listings-actions .rtcl-result-count',
			],
			[
				'type'      => Controls_Manager::COLOR,
				'id'        => 'result_count_color',
				'label'     => __( 'Text Color', 'rtcl-elementor-builder' ),
				'selectors' => [
					'{{WRAPPER}} .rtcl-listings-actions .rtcl-result-count' => 'color: {{VALUE}};',
				],
			],
			[
				'type'            => Controls_Manager::RAW_HTML,
				'id'              => 'rtcl_el_archive_sorting_title',
				'raw'             => sprintf(
					'<h3 class="rtcl-elementor-group-heading">%s</h3>',
					__( 'Sorting', 'rtcl-elementor-builder' )
				),
				'content_classes' => 'elementor-panel-heading-title',
			],
			[
				'type'       => Controls_Manager::SLIDER,
				'id'         => 'select_field_height',
				'label'      => esc_html__( 'Height', 'rtcl-elementor-builder' ),
				'size_units' => [ 'px' ],
				'selectors'  => [
					'{{WRAPPER}} .rtcl-listings-actions .rtcl-ordering select' => 'height: {{SIZE}}{{UNIT}};',
				],
			],
			[
				'mode'       => 'responsive',
				'label'      => __( 'Padding', 'rtcl-elementor-builder' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'id'         => 'select_field_padding',
				'size_units' => [ 'px', 'em', '%' ],
				'selectors'  => [
					'{{WRAPPER}} .rtcl-listings-actions .rtcl-ordering select' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			],
			[
				'mode'       => 'responsive',
				'label'      => __( 'Border Radius', 'rtcl-elementor-builder' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'id'         => 'select_field_radius',
				'size_units' => [ 'px', 'em', '%' ],
				'selectors'  => [
					'{{WRAPPER}} .rtcl-listings-actions .rtcl-ordering select' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			],
			[
				'mode'       => 'responsive',
				'label'      => __( 'Margin', 'rtcl-elementor-builder' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'id'         => 'select_sorting_form_margin',
				'size_units' => [ 'px', 'em', '%' ],
				'selectors'  => [
					'{{WRAPPER}} .rtcl-listings-actions .rtcl-ordering' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			],
			[
				'type'            => Controls_Manager::RAW_HTML,
				'id'              => 'rtcl_el_archive_grid_list_view',
				'raw'             => sprintf(
					'<h3 class="rtcl-elementor-group-heading">%s</h3>',
					__( 'Grid View & List View Button', 'rtcl-elementor-builder' )
				),
				'content_classes' => 'elementor-panel-heading-title',
			],
			[
				'type'       => Controls_Manager::SLIDER,
				'id'         => 'list_grid_view_btn_gap',
				'label'      => esc_html__( 'Button Gap', 'rtcl-elementor-builder' ),
				'size_units' => [ 'px' ],
				'selectors'  => [
					'{{WRAPPER}} .rtcl-listings-actions .rtcl-view-switcher' => 'gap: {{SIZE}}{{UNIT}};',
				],
			],
			[
				'type'       => Controls_Manager::SLIDER,
				'id'         => 'rtcl_el_grid_list_view_icon_height',
				'label'      => esc_html__( 'Height', 'rtcl-elementor-builder' ),
				'size_units' => [ 'px' ],
				'selectors'  => [
					'{{WRAPPER}} .rtcl-listings-actions .rtcl-view-trigger' => 'height: {{SIZE}}{{UNIT}};',
				],
			],
			[
				'type'       => Controls_Manager::SLIDER,
				'id'         => 'rtcl_el_grid_list_view_icon_width',
				'label'      => esc_html__( 'Width', 'rtcl-elementor-builder' ),
				'size_units' => [ 'px' ],
				'selectors'  => [
					'{{WRAPPER}} .rtcl-listings-actions .rtcl-view-trigger' => 'width: {{SIZE}}{{UNIT}};',
				],
			],
			[
				'type'       => Controls_Manager::SLIDER,
				'id'         => 'rtcl_el_grid_list_view_icon_size',
				'label'      => esc_html__( 'Icon Size', 'rtcl-elementor-builder' ),
				'size_units' => [ 'px' ],
				'selectors'  => [
					'{{WRAPPER}} .rtcl-listings-actions .rtcl-view-trigger i' => 'font-size: {{SIZE}}{{UNIT}};',
				],
			],
			[
				'type'      => Controls_Manager::COLOR,
				'id'        => 'result_icon_color',
				'label'     => __( 'Icon Color', 'rtcl-elementor-builder' ),
				'selectors' => [
					'{{WRAPPER}} .rtcl-listings-actions .rtcl-view-trigger i' => 'color: {{VALUE}};',
				],
			],
			[
				'mode'       => 'responsive',
				'label'      => __( 'Border Radius', 'rtcl-elementor-builder' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'id'         => 'icon_border_radius',
				'size_units' => [ 'px', 'em', '%' ],
				'selectors'  => [
					'{{WRAPPER}} .rtcl-listings-actions .rtcl-view-trigger' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			],
			[
				'type'            => Controls_Manager::RAW_HTML,
				'id'              => 'rtcl_el_archive_wrapper_note',
				'raw'             => sprintf(
					'<h3 class="rtcl-elementor-group-heading">%s</h3>',
					__( 'Wrapper', 'rtcl-elementor-builder' )
				),
				'content_classes' => 'elementor-panel-heading-title',
			],
			[
				'mode'       => 'responsive',
				'label'      => __( 'Wrapper Margin', 'rtcl-elementor-builder' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'id'         => 'rtcl_wrapper_margin',
				'size_units' => [ 'px', 'em', '%' ],
				'selectors'  => [
					'{{WRAPPER}} .rtcl-listings-wrapper .rtcl-listings-actions' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			],
			[
				'mode' => 'section_end',
			],
		];
		return $fields;
	}
}
