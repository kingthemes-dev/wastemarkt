<?php
/**
 * Main Elementor ListingSellerInfoSettings Class
 *
 * ListingSellerInfoSettings main class
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
use Elementor\Group_Control_Border;
use Elementor\Group_Control_Background;
use Elementor\Group_Control_Typography;
use RtclElb\Abstracts\ElementorSingleListingBase;

/**
 * ListingSellerInfoSettings class
 */
class ListingSellerInfoSettings extends ElementorSingleListingBase {
	/**
	 * Set style controlls
	 */
	public function widget_general_fields(): array {
		return $this->general_fields();
	}

	/**
	 * Set style controlls
	 */
	public function widget_style_fields(): array {
		return array_merge(
			$this->user_info_style(),
			$this->location_number_style(),
			$this->seller_message_style(),
			$this->seller_chat(),
			$this->seller_website(),
			$this->user_online_status()
		);
	}

	/**
	 * Set style controlls
	 *
	 * @return array
	 */
	public function general_fields() {
		$fields = [
			[
				'mode'  => 'section_start',
				'id'    => 'general',
				'label' => __( 'General', 'rtcl-elementor-builder' ),
			],
			[
				'type'        => Controls_Manager::SWITCHER,
				'id'          => 'rtcl_show_author',
				'label'       => __( 'Show Author', 'rtcl-elementor-builder' ),
				'label_on'    => __( 'Show', 'rtcl-elementor-builder' ),
				'label_off'   => __( 'Hide', 'rtcl-elementor-builder' ),
				'default'     => 'yes',
				'description' => __( 'Switch to Show Author', 'rtcl-elementor-builder' ),
			],
			[
				'type'        => Controls_Manager::SWITCHER,
				'id'          => 'rtcl_show_author_image',
				'label'       => __( 'Show Author Image', 'rtcl-elementor-builder' ),
				'label_on'    => __( 'Show', 'rtcl-elementor-builder' ),
				'label_off'   => __( 'Hide', 'rtcl-elementor-builder' ),
				'default'     => 'yes',
				'condition'   => [ 'rtcl_show_author' => [ 'yes' ] ],
				'description' => __( 'Switch to Show Author Image', 'rtcl-elementor-builder' ),
			],
			[
				'type'        => Controls_Manager::SWITCHER,
				'id'          => 'rtcl_show_location',
				'label'       => __( 'Show Location', 'rtcl-elementor-builder' ),
				'label_on'    => __( 'Show', 'rtcl-elementor-builder' ),
				'label_off'   => __( 'Hide', 'rtcl-elementor-builder' ),
				'default'     => 'yes',
				'description' => __( 'Switch to Show Location', 'rtcl-elementor-builder' ),
			],
			[
				'type'        => Controls_Manager::SWITCHER,
				'id'          => 'rtcl_show_contact',
				'label'       => __( 'Show Contact Number', 'rtcl-elementor-builder' ),
				'label_on'    => __( 'Show', 'rtcl-elementor-builder' ),
				'label_off'   => __( 'Hide', 'rtcl-elementor-builder' ),
				'default'     => 'yes',
				'description' => __( 'Switch to Show Contact Number', 'rtcl-elementor-builder' ),
			],
			[
				'type'        => Controls_Manager::SWITCHER,
				'id'          => 'rtcl_show_contact_form',
				'label'       => __( 'Show Contact Form', 'rtcl-elementor-builder' ),
				'label_on'    => __( 'Show', 'rtcl-elementor-builder' ),
				'label_off'   => __( 'Hide', 'rtcl-elementor-builder' ),
				'default'     => 'yes',
				'description' => __( 'Switch to Show Contact Form', 'rtcl-elementor-builder' ),
			],
			[
				'type'      => Controls_Manager::TEXT,
				'id'        => 'rtcl_contact_btn_text',
				'label'     => __('Contact Button Text', 'rtcl-elementor-builder'),
				'condition' => ['rtcl_show_contact_form' => 'yes'],
			],
			[
				'type'        => Controls_Manager::SWITCHER,
				'id'          => 'rtcl_show_seller_website',
				'label'       => __( 'Show Seller Website', 'rtcl-elementor-builder' ),
				'label_on'    => __( 'Show', 'rtcl-elementor-builder' ),
				'label_off'   => __( 'Hide', 'rtcl-elementor-builder' ),
				'default'     => 'yes',
				'description' => __( 'Switch to Show Seller Website', 'rtcl-elementor-builder' ),
			],
			[
				'type'      => Controls_Manager::TEXT,
				'id'        => 'rtcl_show_seller_website_text',
				'label'     => __('Website Button Text', 'rtcl-elementor-builder'),
				'condition' => ['rtcl_show_seller_website' => 'yes'],
			],
			[
				'type'      => Controls_Manager::RAW_HTML,
				'id'        => 'rtcl_pro_functionality',
				'separator' => 'before',
				'raw'       => sprintf(
					'<h3 class="rtcl-elementor-group-heading">%s</h3>',
					__( 'Support for Classified Listing Pro Plugin Features.', 'rtcl-elementor-builder' )
				),
			],
			[
				'type'        => Controls_Manager::SWITCHER,
				'id'          => 'rtcl_add_chat_link',
				'label'       => __( 'Show Chat Button', 'rtcl-elementor-builder' ),
				'label_on'    => __( 'Show', 'rtcl-elementor-builder' ),
				'label_off'   => __( 'Hide', 'rtcl-elementor-builder' ),
				'default'     => 'yes',
				'description' => __( 'Note: Classified Listing Pro Plugin Required and must be enable chat from the Classified Listing -> Chat -> Chat checked', 'rtcl-elementor-builder' ),
			],
			[
				'type'      => Controls_Manager::TEXT,
				'id'        => 'rtcl_chat_btn_text',
				'label'     => __('Chat Button Text', 'rtcl-elementor-builder'),
				'condition' => ['rtcl_add_chat_link' => ['yes']],
			],
			[
				'type'        => Controls_Manager::SWITCHER,
				'id'          => 'rtcl_add_user_online_status',
				'label'       => __( 'Show Online Status', 'rtcl-elementor-builder' ),
				'label_on'    => __( 'Show', 'rtcl-elementor-builder' ),
				'label_off'   => __( 'Hide', 'rtcl-elementor-builder' ),
				'default'     => 'yes',
				'description' => __( 'Switch to Show Online Status. Note: Classified Listing Pro Plugin Required', 'rtcl-elementor-builder' ),
			],
			[
				'type'      => Controls_Manager::TEXT,
				'id'        => 'rtcl_offline_status_text',
				'label'     => __('Offline Status Text', 'rtcl-elementor-builder'),
				'condition' => ['rtcl_add_user_online_status' => ['yes']],
			],
			[
				'type'      => Controls_Manager::TEXT,
				'id'        => 'rtcl_online_status_text',
				'label'     => __('Online Status Text', 'rtcl-elementor-builder'),
				'condition' => ['rtcl_add_user_online_status' => ['yes']],
			],
			[
				'mode' => 'section_end',
			],
		];

		return $fields;
	}

	/**
	 * Set style for author information
	 *
	 * @return array
	 */
	public function user_info_style() {
		$fields = [
			[
				'mode'  => 'section_start',
				'id'    => 'user_info_style',
				'tab'   => Controls_Manager::TAB_STYLE,
				'label' => __( 'User Information', 'rtcl-elementor-builder' ),
			],
			[
				'type'           => Group_Control_Border::get_type(),
				'label'          => __( 'Border', 'rtcl-elementor-builder' ),
				'mode'           => 'group',
				'id'             => 'rtcl_author_box_border',
				'fields_options' => [
					'border' => [
						'default' => 'solid',
					],
					'width'  => [
						'default' => [
							'top'      => '1',
							'right'    => '1',
							'bottom'   => '1',
							'left'     => '1',
							'isLinked' => false,
						],
					],
					'color'  => [
						'default' => 'rgba(0, 0, 0, 0.125)',
					],
				],
				'selector'       => '{{WRAPPER}} .rtcl-listing-user-info .listing-author',
			],
			[
				'mode'       => 'responsive',
				'label'      => __( 'Margin', 'rtcl-elementor-builder' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'id'         => 'rtcl_author_box_margin',
				'size_units' => [ 'px', 'em', '%' ],
				'selectors'  => [
					'{{WRAPPER}} .rtcl-listing-user-info .listing-author' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			],
			[
				'mode'       => 'responsive',
				'label'      => __( 'Padding', 'rtcl-elementor-builder' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'id'         => 'rtcl_author_box_padding',
				'size_units' => [ 'px', 'em', '%' ],
				'selectors'  => [
					'{{WRAPPER}} .rtcl-listing-user-info .listing-author' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			],
			/* == Avatar == */
			[
				'type'      => Controls_Manager::RAW_HTML,
				'id'        => 'rtcl_avatar_heading',
				'separator' => 'before',
				'raw'       => sprintf(
					'<h3 class="rtcl-elementor-group-heading">%s</h3>',
					__( 'Avatar', 'rtcl-elementor-builder' )
				),
			],
			[
				'mode'       => 'responsive',
				'label'      => __( 'Border Radius', 'rtcl-elementor-builder' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'id'         => 'rtcl_avatar_border_radius',
				'size_units' => [ 'px', 'em', '%' ],
				'selectors'  => [
					'{{WRAPPER}} .rtcl-listing-user-info .author-logo-wrapper img' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			],
			/* == Name == */
			[
				'type'      => Controls_Manager::RAW_HTML,
				'id'        => 'rtcl_name_heading',
				'separator' => 'before',
				'raw'       => sprintf(
					'<h3 class="rtcl-elementor-group-heading">%s</h3>',
					__( 'Name', 'rtcl-elementor-builder' )
				),
			],
			[
				'type'      => Controls_Manager::COLOR,
				'id'        => 'rtcl_name_color',
				'label'     => __( 'Color', 'rtcl-elementor-builder' ),
				'selectors' => [
					'{{WRAPPER}} .rtcl-listing-user-info .author-name' => 'color: {{VALUE}};',
				],
			],
			[
				'mode'     => 'group',
				'type'     => Group_Control_Typography::get_type(),
				'id'       => 'rtcl_name_typo',
				'label'    => __( 'Label Typography', 'rtcl-elementor-builder' ),
				'selector' => '{{WRAPPER}} .rtcl-listing-user-info .author-name',
			],
			[
				'mode'       => 'responsive',
				'label'      => __( 'Margin', 'rtcl-elementor-builder' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'id'         => 'rtcl_name_margin',
				'size_units' => [ 'px', 'em', '%' ],
				'selectors'  => [
					'{{WRAPPER}} .rtcl-listing-user-info .author-name' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			],
			[
				'mode' => 'section_end',
			],
		];

		return $fields;
	}
	
	/**
	 * Set style for locations number
	 *
	 * @return array
	 */
	public function location_number_style() {
		$fields = [
			[
				'mode'  => 'section_start',
				'id'    => 'location_number_style',
				'tab'   => Controls_Manager::TAB_STYLE,
				'label' => __( 'Location & Number', 'rtcl-elementor-builder' ),
			],
			[
				'type'           => Group_Control_Border::get_type(),
				'label'          => __( 'Border', 'rtcl-elementor-builder' ),
				'mode'           => 'group',
				'id'             => 'rtcl_listing_location_border',
				'fields_options' => [
					'border' => [
						'default' => 'solid',
					],
					'width'  => [
						'default' => [
							'top'      => '1',
							'right'    => '1',
							'bottom'   => '1',
							'left'     => '1',
							'isLinked' => false,
						],
					],
					'color'  => [
						'default' => 'rgba(0, 0, 0, 0.125)',
					],
				],
				'selector'       => '{{WRAPPER}} .el-single-addon.seller-information .list-group-item',
			],
			[
				'mode'       => 'responsive',
				'label'      => __( 'Margin', 'rtcl-elementor-builder' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'id'         => 'rtcl_location_box_margin',
				'size_units' => [ 'px', 'em', '%' ],
				'selectors'  => [
					'{{WRAPPER}} .el-single-addon.seller-information .list-group-item' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			],
			[
				'mode'       => 'responsive',
				'label'      => __( 'Padding', 'rtcl-elementor-builder' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'id'         => 'rtcl_location_box_padding',
				'size_units' => [ 'px', 'em', '%' ],
				'selectors'  => [
					'{{WRAPPER}} .el-single-addon.seller-information .list-group-item' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			],
			[
				'mode'       => 'responsive',
				'label'      => __( 'Border Radius', 'rtcl-elementor-builder' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'id'         => 'rtcl_location_box_border_radius',
				'size_units' => [ 'px', 'em', '%' ],
				'selectors'  => [
					'{{WRAPPER}} .el-single-addon.seller-information .list-group-item' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			],
			/* == Icon == */
			[
				'type'      => Controls_Manager::RAW_HTML,
				'id'        => 'rtcl_icon_heading',
				'separator' => 'before',
				'raw'       => sprintf(
					'<h3 class="rtcl-elementor-group-heading">%s</h3>',
					__( 'Icon', 'rtcl-elementor-builder' )
				),
			],
			[
				'type'      => Controls_Manager::COLOR,
				'id'        => 'rtcl_location_icon_color',
				'label'     => __( 'Color', 'rtcl-elementor-builder' ),
				'selectors' => [
					'{{WRAPPER}} .el-single-addon.seller-information .list-group-item .rtcl-icon' => 'color: {{VALUE}};',
				],
			],
			[
				'type'       => Controls_Manager::SLIDER,
				'id'         => 'rtcl_location_icon_size',
				'label'      => esc_html__( 'Size', 'rtcl-elementor-builder' ),
				'size_units' => [ 'px' ],
				'selectors'  => [
					'{{WRAPPER}} .el-single-addon.seller-information .list-group-item .rtcl-icon' => 'font-size: {{SIZE}}{{UNIT}};',
				],
			],
			
			/* == Title == */
			[
				'type'      => Controls_Manager::RAW_HTML,
				'id'        => 'rtcl_title_heading',
				'separator' => 'before',
				'raw'       => sprintf(
					'<h3 class="rtcl-elementor-group-heading">%s</h3>',
					__( 'Title', 'rtcl-elementor-builder' )
				),
			],
			[
				'type'      => Controls_Manager::COLOR,
				'id'        => 'title_color',
				'label'     => __( 'Color', 'rtcl-elementor-builder' ),
				'selectors' => [
					'{{WRAPPER}} .el-single-addon.seller-information .list-group-item .media-body span' => 'color: {{VALUE}};',
				],
			],
			[
				'mode'     => 'group',
				'type'     => Group_Control_Typography::get_type(),
				'id'       => 'title_typo',
				'label'    => __( 'Typography', 'rtcl-elementor-builder' ),
				'selector' => '{{WRAPPER}} .el-single-addon.seller-information .list-group-item .media-body span',
			],
			[
				'mode'       => 'responsive',
				'label'      => __( 'Margin', 'rtcl-elementor-builder' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'id'         => 'rtcl_location_title_margin',
				'size_units' => [ 'px', 'em', '%' ],
				'selectors'  => [
					'{{WRAPPER}} .el-single-addon.seller-information .list-group-item .media-body span' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			],
			
			/* == Text == */
			[
				'type'      => Controls_Manager::RAW_HTML,
				'id'        => 'rtcl_text_heading',
				'separator' => 'before',
				'raw'       => sprintf(
					'<h3 class="rtcl-elementor-group-heading">%s</h3>',
					__( 'Text', 'rtcl-elementor-builder' )
				),
			],
			[
				'type'      => Controls_Manager::COLOR,
				'id'        => 'text_color',
				'label'     => __( 'Color', 'rtcl-elementor-builder' ),
				'selectors' => [
					'{{WRAPPER}} .el-single-addon.seller-information .list-group-item .media-body .numbers' => 'color: {{VALUE}};',
					'{{WRAPPER}} .el-single-addon.seller-information .list-group-item .media-body .locations' => 'color: {{VALUE}};',
				],
			],
			[
				'mode'     => 'group',
				'type'     => Group_Control_Typography::get_type(),
				'id'       => 'text_typo',
				'label'    => __( 'Typography', 'rtcl-elementor-builder' ),
				'selector' => '
					{{WRAPPER}} .el-single-addon.seller-information .list-group-item .media-body .numbers,
					{{WRAPPER}} .el-single-addon.seller-information .list-group-item .media-body .locations
				',
			],
			[
				'mode'       => 'responsive',
				'label'      => __( 'Margin', 'rtcl-elementor-builder' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'id'         => 'rtcl_location_text_margin',
				'size_units' => [ 'px', 'em', '%' ],
				'selectors'  => [
					'{{WRAPPER}} .el-single-addon.seller-information .list-group-item .media-body .numbers' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
					'{{WRAPPER}} .el-single-addon.seller-information .list-group-item .media-body .locations' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			],
			[
				'mode' => 'section_end',
			],
		];

		return $fields;
	}
	
	/**
	 * Set style for Seller Message
	 *
	 * @return array
	 */
	public function seller_message_style() {
		$fields = [
			[
				'mode'  => 'section_start',
				'id'    => 'seller_message_style',
				'tab'   => Controls_Manager::TAB_STYLE,
				'label' => esc_html__( 'Seller Message', 'rtcl-elementor-builder' ),
			],
			[
				'mode'      => 'group',
				'label'     => esc_html__('Background', 'rtcl-elementor-builder'),
				'id'        => 'rtcl_seller_message_background',
				'type'      => Group_Control_Background::get_type(),
				'fields_options' => [
					'background' => [
						'label' => esc_html__(' Background', 'rtcl-elementor-builder'),
						'default' => 'classic',
					]
				],
				'selector'  => '{{WRAPPER}} .el-single-addon.seller-information .rtcl-do-email.list-group-item',
			],
			[
				'type'           => Group_Control_Border::get_type(),
				'label'          => esc_html__( 'Border', 'rtcl-elementor-builder' ),
				'mode'           => 'group',
				'id'             => 'rtcl_seller_message_border',
				'fields_options' => [
					'border' => [
						'default' => 'solid',
					],
					'width'  => [
						'default' => [
							'top'      => '1',
							'right'    => '1',
							'bottom'   => '1',
							'left'     => '1',
							'isLinked' => false,
						],
					],
					'color'  => [
						'default' => 'rgba(0, 0, 0, 0.125)',
					],
				],
				'selector'       => '{{WRAPPER}} .el-single-addon.seller-information .rtcl-do-email.list-group-item',
			],
			[
				'mode'       => 'responsive',
				'label'      => esc_html__( 'Margin', 'rtcl-elementor-builder' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'id'         => 'rtcl_seller_message_margin',
				'size_units' => [ 'px', 'em', '%' ],
				'selectors'  => [
					'{{WRAPPER}} .el-single-addon.seller-information .rtcl-do-email.list-group-item' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			],
			[
				'mode'       => 'responsive',
				'label'      => esc_html__( 'Padding', 'rtcl-elementor-builder' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'id'         => 'rtcl_seller_message_padding',
				'size_units' => [ 'px', 'em', '%' ],
				'selectors'  => [
					'{{WRAPPER}} .el-single-addon.seller-information .rtcl-do-email.list-group-item' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			],
			[
				'mode'       => 'responsive',
				'label'      => esc_html__( 'Border Radius', 'rtcl-elementor-builder' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'id'         => 'rtcl_seller_message_border_radius',
				'size_units' => [ 'px', 'em', '%' ],
				'selectors'  => [
					'{{WRAPPER}} .el-single-addon.seller-information .rtcl-do-email.list-group-item' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			],
			/* == Icon == */
			[
				'type'      => Controls_Manager::RAW_HTML,
				'id'        => 'rtcl_seller_message_icon_heading',
				'separator' => 'before',
				'raw'       => sprintf(
					'<h3 class="rtcl-elementor-group-heading">%s</h3>',
					__( 'Icon', 'rtcl-elementor-builder' )
				),
			],
			[
				'type'      => Controls_Manager::COLOR,
				'id'        => 'rtcl_seller_message_icon_color',
				'label'     => esc_html__( 'Color', 'rtcl-elementor-builder' ),
				'selectors' => [
					'{{WRAPPER}} .el-single-addon.seller-information .rtcl-do-email.list-group-item .rtcl-icon' => 'color: {{VALUE}};',
				],
			],
			[
				'type'       => Controls_Manager::SLIDER,
				'id'         => 'rtcl_seller_message_icon_size',
				'label'      => esc_html__( 'Size', 'rtcl-elementor-builder' ),
				'size_units' => [ 'px' ],
				'selectors'  => [
					'{{WRAPPER}} .el-single-addon.seller-information .rtcl-do-email.list-group-item .rtcl-icon' => 'font-size: {{SIZE}}{{UNIT}};',
				],
			],
			/* == Title == */
			[
				'type'      => Controls_Manager::RAW_HTML,
				'id'        => 'rtcl_seller_message_heading',
				'separator' => 'before',
				'raw'       => sprintf(
					'<h3 class="rtcl-elementor-group-heading">%s</h3>',
					__( 'Title', 'rtcl-elementor-builder' )
				),
			],
			[
				'type'      => Controls_Manager::COLOR,
				'id'        => 'seller_message_title_color',
				'label'     => esc_html__( 'Color', 'rtcl-elementor-builder' ),
				'selectors' => [
					'{{WRAPPER}} .el-single-addon.seller-information .rtcl-do-email.list-group-item .media-body span' => 'color: {{VALUE}};',
				],
			],
			[
				'mode'     => 'group',
				'type'     => Group_Control_Typography::get_type(),
				'id'       => 'seller_message_title_typo',
				'label'    => esc_html__( 'Typography', 'rtcl-elementor-builder' ),
				'selector' => '{{WRAPPER}} .el-single-addon.seller-information .rtcl-do-email.list-group-item .media-body span',
			],
			[
				'mode'       => 'responsive',
				'label'      => esc_html__( 'Title Margin', 'rtcl-elementor-builder' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'id'         => 'rtcl_seller_message_title_margin',
				'size_units' => [ 'px', 'em', '%' ],
				'selectors'  => [
					'{{WRAPPER}} .el-single-addon.seller-information .rtcl-do-email.list-group-item .media-body span' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			],

			/* == Input Box == */
			[
				'type'      => Controls_Manager::RAW_HTML,
				'id'        => 'rtcl_seller_message_input_heading',
				'separator' => 'before',
				'raw'       => sprintf(
					'<h3 class="rtcl-elementor-group-heading">%s</h3>',
					__( 'Input Box', 'rtcl-elementor-builder' )
				),
			],
			[
				'type'      => Controls_Manager::COLOR,
				'id'        => 'rtcl_seller_message_input_color',
				'label'     => esc_html__( 'Color', 'rtcl-elementor-builder' ),
				'selectors' => [ '{{WRAPPER}} .rtcl .rtcl-do-email .form-control' => 'color: {{VALUE}};'],
			],
			[
				'mode'     => 'group',
				'type'     => Group_Control_Typography::get_type(),
				'id'       => 'rtcl_seller_message_input_typo',
				'label'    => esc_html__( 'Typography', 'rtcl-elementor-builder' ),
				'selector' => ' {{WRAPPER}} .rtcl .rtcl-do-email .form-control',
			],
			[
				'type'       => Controls_Manager::SLIDER,
				'id'         => 'rtcl_seller_message_input_height',
				'label'      => esc_html__( 'Field Height', 'rtcl-elementor-builder' ),
				'size_units' => [ 'px' ],
				'default'    => [
					'unit' => 'px',
					'size' => 5,
				],
				'selectors'  => [
					'{{WRAPPER}} .rtcl .rtcl-do-email .form-control' => 'height:{{SIZE}}{{UNIT}};',
				],
			],
			[
				'mode'       => 'responsive',
				'label'      => esc_html__( 'Margin', 'rtcl-elementor-builder' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'id'         => 'rtcl_seller_message_input_margin',
				'size_units' => [ 'px', 'em', '%' ],
				'selectors'  => [ '{{WRAPPER}} .rtcl .rtcl-do-email .form-control' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};'],
			],
			[
				'mode'       => 'responsive',
				'label'      => esc_html__( 'Border Radius', 'rtcl-elementor-builder' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'id'         => 'rtcl_seller_message_input_border_radius',
				'size_units' => [ 'px', 'em', '%' ],
				'selectors'  => [ '{{WRAPPER}} .rtcl .rtcl-do-email .form-control' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};'],
			],
			/* == Form Button == */
			[
				'type'      => Controls_Manager::RAW_HTML,
				'id'        => 'rtcl_seller_message_form_button_heading',
				'separator' => 'before',
				'raw'       => sprintf(
					'<h3 class="rtcl-elementor-group-heading">%s</h3>',
					__( 'Form Button', 'rtcl-elementor-builder' )
				),
			],
			[
				'mode'     => 'group',
				'type'     => Group_Control_Typography::get_type(),
				'id'       => 'rtcl_seller_message_form_button_typo',
				'label'    => esc_html__( 'Typography', 'rtcl-elementor-builder' ),
				'selector' => '{{WRAPPER}} .el-single-addon.seller-information .rtcl-do-email.list-group-item .btn,{{WRAPPER}} .el-single-addon.seller-information .rtcl-do-email.list-group-item button',
			],
			[
				'type'      => Controls_Manager::COLOR,
				'id'        => 'rtcl_seller_message_form_button_color',
				'label'     => esc_html__( 'Text Color', 'rtcl-elementor-builder' ),
				'selectors' => [
					'{{WRAPPER}} .el-single-addon.seller-information .rtcl-do-email.list-group-item .btn,{{WRAPPER}} .el-single-addon.seller-information .rtcl-do-email.list-group-item button' => 'color: {{VALUE}};',
				],
			],
			[
				'type'      => Controls_Manager::COLOR,
				'id'        => 'rtcl_seller_message_form_button_color_hover',
				'label'     => esc_html__( 'Hover Text Color', 'rtcl-elementor-builder' ),
				'selectors' => [
					'{{WRAPPER}} .el-single-addon.seller-information .rtcl-do-email.list-group-item .btn:hover,{{WRAPPER}} .el-single-addon.seller-information .rtcl-do-email.list-group-item button:hover' => 'color: {{VALUE}};',
				],
			],
			[
				'type'      => Controls_Manager::COLOR,
				'id'        => 'rtcl_seller_message_form_button_bg_color',
				'label'     => esc_html__( 'Background', 'rtcl-elementor-builder' ),
				'selectors' => [
					'{{WRAPPER}} .el-single-addon.seller-information .rtcl-do-email.list-group-item .btn,{{WRAPPER}} .el-single-addon.seller-information .rtcl-do-email.list-group-item button' => 'background: {{VALUE}};',
				],
			],
			[
				'type'      => Controls_Manager::COLOR,
				'id'        => 'rtcl_seller_message_form_button_bg_hover_color',
				'label'     => esc_html__( 'Hover Background', 'rtcl-elementor-builder' ),
				'selectors' => [
					'{{WRAPPER}} .el-single-addon.seller-information .rtcl-do-email.list-group-item .btn:hover,{{WRAPPER}} .el-single-addon.seller-information .rtcl-do-email.list-group-item button:hover' => 'background: {{VALUE}};',
				],
			],
			[
				'mode'       => 'responsive',
				'label'      => esc_html__( 'Button Padding', 'rtcl-elementor-builder' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'id'         => 'rtcl_seller_message_form_button_padding',
				'size_units' => [ 'px', 'em', '%' ],
				'selectors'  => [
					'{{WRAPPER}} .el-single-addon.seller-information .rtcl-do-email.list-group-item .btn,{{WRAPPER}} .el-single-addon.seller-information .rtcl-do-email.list-group-item button' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			],
			[
				'mode' => 'section_end',
			],
		];

		return $fields;
	}

	/**
	 * Set style for Seller Chat
	 *
	 * @return array
	 */
	public function seller_chat() {
		$fields = [
			[
				'mode'  => 'section_start',
				'id'    => 'seller_chat_style',
				'tab'   => Controls_Manager::TAB_STYLE,
				'label' => __( 'Chat', 'rtcl-elementor-builder' ),
			],
			[
				'mode'      => 'group',
				'label'     => __('Background', 'rtcl-elementor-builder'),
				'id'        => 'rtcl_seller_chat_background',
				'type'      => Group_Control_Background::get_type(),
				'fields_options' => [
					'background' => [
						'label' => esc_html__(' Background', 'rtcl-elementor-builder'),
						'default' => 'classic',
					]
				],
				'selector'  => '{{WRAPPER}} .el-single-addon.seller-information .rtcl-contact-seller.list-group-item',
			],
			[
				'type'           => Group_Control_Border::get_type(),
				'label'          => __( 'Border', 'rtcl-elementor-builder' ),
				'mode'           => 'group',
				'id'             => 'rtcl_seller_chat_border',
				'fields_options' => [
					'border' => [
						'default' => 'solid',
					],
					'width'  => [
						'default' => [
							'top'      => '1',
							'right'    => '1',
							'bottom'   => '1',
							'left'     => '1',
							'isLinked' => false,
						],
					],
					'color'  => [
						'default' => 'rgba(0, 0, 0, 0.125)',
					],
				],
				'selector'       => '{{WRAPPER}} .el-single-addon.seller-information .rtcl-contact-seller.list-group-item',
			],
			[
				'mode'       => 'responsive',
				'label'      => __( 'Margin', 'rtcl-elementor-builder' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'id'         => 'rtcl_seller_chat_box_margin',
				'size_units' => [ 'px', 'em', '%' ],
				'selectors'  => [
					'{{WRAPPER}} .el-single-addon.seller-information .rtcl-contact-seller.list-group-item' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			],
			[
				'mode'       => 'responsive',
				'label'      => __( 'Padding', 'rtcl-elementor-builder' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'id'         => 'rtcl_seller_chat_box_padding',
				'size_units' => [ 'px', 'em', '%' ],
				'selectors'  => [
					'{{WRAPPER}} .el-single-addon.seller-information .rtcl-contact-seller.list-group-item' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			],
			[
				'mode'       => 'responsive',
				'label'      => __( 'Border Radius', 'rtcl-elementor-builder' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'id'         => 'rtcl_seller_chat_box_border_radius',
				'size_units' => [ 'px', 'em', '%' ],
				'selectors'  => [
					'{{WRAPPER}} .el-single-addon.seller-information .rtcl-contact-seller.list-group-item' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			],
			/* == Icon == */
			[
				'type'      => Controls_Manager::RAW_HTML,
				'id'        => 'seller_chat_icon_heading',
				'separator' => 'before',
				'raw'       => sprintf(
					'<h3 class="rtcl-elementor-group-heading">%s</h3>',
					__( 'Icon', 'rtcl-elementor-builder' )
				),
			],
			[
				'type'      => Controls_Manager::COLOR,
				'id'        => 'rtcl_seller_chat_icon_color',
				'label'     => __( 'Color', 'rtcl-elementor-builder' ),
				'selectors' => [
					'{{WRAPPER}} .el-single-addon.seller-information .rtcl-contact-seller.list-group-item .rtcl-icon' => 'color: {{VALUE}};',
				],
			],
			[
				'type'       => Controls_Manager::SLIDER,
				'id'         => 'rtcl_seller_chat_icon_size',
				'label'      => esc_html__( 'Size', 'rtcl-elementor-builder' ),
				'size_units' => [ 'px' ],
				'selectors'  => [
					'{{WRAPPER}} .el-single-addon.seller-information .rtcl-contact-seller.list-group-item .rtcl-icon' => 'font-size: {{SIZE}}{{UNIT}};',
				],
			],
			/* == Title == */
			[
				'type'      => Controls_Manager::RAW_HTML,
				'id'        => 'rtcl_seller_chat_title_heading',
				'separator' => 'before',
				'raw'       => sprintf(
					'<h3 class="rtcl-elementor-group-heading">%s</h3>',
					__( 'Title', 'rtcl-elementor-builder' )
				),
			],
			[
				'type'      => Controls_Manager::COLOR,
				'id'        => 'rtcl_seller_chat_title_color',
				'label'     => __( 'Color', 'rtcl-elementor-builder' ),
				'selectors' => [
					'{{WRAPPER}} .el-single-addon.seller-information .rtcl-contact-seller.list-group-item .rtcl-contact-seller' => 'color: {{VALUE}};',
				],
			],
			[
				'mode'     => 'group',
				'type'     => Group_Control_Typography::get_type(),
				'id'       => 'rtcl_seller_chat_title_typo',
				'label'    => __( 'Typography', 'rtcl-elementor-builder' ),
				'selector' => '{{WRAPPER}} .el-single-addon.seller-information .rtcl-contact-seller.list-group-item .rtcl-contact-seller',
			],
			[
				'mode'       => 'responsive',
				'label'      => __( 'Margin', 'rtcl-elementor-builder' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'id'         => 'rtcl_seller_chat_title_margin',
				'size_units' => [ 'px', 'em', '%' ],
				'selectors'  => [
					'{{WRAPPER}} .el-single-addon.seller-information .rtcl-contact-seller.list-group-item .rtcl-contact-seller' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			],
			[
				'mode' => 'section_end',
			],
		];

		return $fields;
	}
	
	/**
	 * Set style for Seller Chat
	 *
	 * @return array
	 */
	private function seller_website() {
		$fields = [
			[
				'mode'  => 'section_start',
				'id'    => 'seller_website_style',
				'tab'   => Controls_Manager::TAB_STYLE,
				'label' => __( 'Website', 'rtcl-elementor-builder' ),
			],
			[
				'type'           => Group_Control_Border::get_type(),
				'label'          => __( 'Border', 'rtcl-elementor-builder' ),
				'mode'           => 'group',
				'id'             => 'rtcl_seller_website_border',
				'fields_options' => [
					'border' => [
						'default' => 'solid',
					],
					'width'  => [
						'default' => [
							'top'      => '1',
							'right'    => '1',
							'bottom'   => '1',
							'left'     => '1',
							'isLinked' => false,
						],
					],
					'color'  => [
						'default' => 'rgba(0, 0, 0, 0.125)',
					],
				],
				'selector'       => '{{WRAPPER}} .el-single-addon.seller-information .list-group-item.rtcl-website',
			],
			[
				'mode'       => 'responsive',
				'label'      => __( 'Margin', 'rtcl-elementor-builder' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'id'         => 'rtcl_seller_website_box_margin',
				'size_units' => [ 'px', 'em', '%' ],
				'selectors'  => [
					'{{WRAPPER}} .el-single-addon.seller-information .list-group-item.rtcl-website' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			],
			[
				'mode'       => 'responsive',
				'label'      => __( 'Padding', 'rtcl-elementor-builder' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'id'         => 'rtcl_seller_website_box_padding',
				'size_units' => [ 'px', 'em', '%' ],
				'selectors'  => [
					'{{WRAPPER}} .el-single-addon.seller-information .list-group-item.rtcl-website' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			],
			[
				'mode'       => 'responsive',
				'label'      => __( 'Border Radius', 'rtcl-elementor-builder' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'id'         => 'rtcl_seller_website_box_border_radius',
				'size_units' => [ 'px', 'em', '%' ],
				'selectors'  => [
					'{{WRAPPER}} .el-single-addon.seller-information .list-group-item.rtcl-website' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			],
			
			/* == Button == */
			[
				'type'      => Controls_Manager::RAW_HTML,
				'id'        => 'seller_website_btn_heading',
				'separator' => 'before',
				'raw'       => sprintf(
					'<h3 class="rtcl-elementor-group-heading">%s</h3>',
					__( 'Button', 'rtcl-elementor-builder' )
				),
			],
			[
				'type'      => Controls_Manager::COLOR,
				'id'        => 'rtcl_seller_website_btn_color',
				'label'     => __( 'Color', 'rtcl-elementor-builder' ),
				'selectors' => [
					'{{WRAPPER}} .el-single-addon.seller-information .list-group-item .rtcl-website-link.btn' => 'color: {{VALUE}};',
				],
			],
			[
				'type'      => Controls_Manager::COLOR,
				'id'        => 'rtcl_seller_website_btn_hover_color',
				'label'     => __( 'Hover Color', 'rtcl-elementor-builder' ),
				'selectors' => [
					'{{WRAPPER}} .el-single-addon.seller-information .list-group-item .rtcl-website-link.btn:hover' => 'color: {{VALUE}};',
				],
			],
			[
				'mode'      => 'group',
				'label'     => __('Background', 'rtcl-elementor-builder'),
				'id'        => 'rtcl_seller_btn_background',
				'type'      => Group_Control_Background::get_type(),
				'fields_options' => [
					'background' => [
						'label' => esc_html__(' Background', 'rtcl-elementor-builder'),
						'default' => 'classic',
					]
				],
				'selector'  => '{{WRAPPER}} .el-single-addon.seller-information .list-group-item .rtcl-website-link.btn',
			],
			[
				'mode'      => 'group',
				'label'     => __('Background', 'rtcl-elementor-builder'),
				'id'        => 'rtcl_seller_btn_hover_background',
				'type'      => Group_Control_Background::get_type(),
				'fields_options' => [
					'background' => [
						'label' => esc_html__('Hover Background', 'rtcl-elementor-builder'),
						'default' => 'classic',
					]
				],
				'selector'  => '{{WRAPPER}} .el-single-addon.seller-information .list-group-item .rtcl-website-link.btn:hover',
			],
			
			[
				'type'           => Group_Control_Border::get_type(),
				'label'          => __( 'Border', 'rtcl-elementor-builder' ),
				'mode'           => 'group',
				'id'             => 'rtcl_seller_website_btn_border',
				'fields_options' => [
					'border' => [
						'default' => 'solid',
					],
					'width'  => [
						'default' => [
							'top'      => '1',
							'right'    => '1',
							'bottom'   => '1',
							'left'     => '1',
							'isLinked' => false,
						],
					],
					'color'  => [
						'default' => 'rgba(0, 0, 0, 0.125)',
					],
				],
				'selector'       => '{{WRAPPER}} .el-single-addon.seller-information .list-group-item .rtcl-website-link.btn',
			],
			
			[
				'mode'       => 'responsive',
				'label'      => __( 'Margin', 'rtcl-elementor-builder' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'id'         => 'rtcl_seller_website_btn_margin',
				'size_units' => [ 'px', 'em', '%' ],
				'selectors'  => [
					'{{WRAPPER}} .el-single-addon.seller-information .list-group-item .rtcl-website-link.btn' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			],
			[
				'mode'       => 'responsive',
				'label'      => __( 'Padding', 'rtcl-elementor-builder' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'id'         => 'rtcl_seller_website_btn_padding',
				'size_units' => [ 'px', 'em', '%' ],
				'selectors'  => [
					'{{WRAPPER}} .el-single-addon.seller-information .list-group-item .rtcl-website-link.btn' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			],
			[
				'mode'       => 'responsive',
				'label'      => __( 'Border Radius', 'rtcl-elementor-builder' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'id'         => 'rtcl_seller_website_btn_border_radius',
				'size_units' => [ 'px', 'em', '%' ],
				'selectors'  => [
					'{{WRAPPER}} .el-single-addon.seller-information .list-group-item .rtcl-website-link.btn' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			],
			
			/* == Icon == */
//			[
//				'type'      => Controls_Manager::RAW_HTML,
//				'id'        => 'seller_website_icon_heading',
//				'separator' => 'before',
//				'raw'       => sprintf(
//					'<h3 class="rtcl-elementor-group-heading">%s</h3>',
//					__( 'Icon', 'rtcl-elementor-builder' )
//				),
//			],
//			[
//				'type'      => Controls_Manager::COLOR,
//				'id'        => 'rtcl_seller_website_icon_color',
//				'label'     => __( 'Color', 'rtcl-elementor-builder' ),
//				'selectors' => [
//					'{{WRAPPER}} .el-single-addon.seller-information .rtcl-contact-seller.list-group-item .rtcl-icon' => 'color: {{VALUE}};',
//				],
//			],
//			[
//				'type'       => Controls_Manager::SLIDER,
//				'id'         => 'rtcl_seller_website_icon_size',
//				'label'      => esc_html__( 'Size', 'rtcl-elementor-builder' ),
//				'size_units' => [ 'px' ],
//				'selectors'  => [
//					'{{WRAPPER}} .el-single-addon.seller-information .rtcl-contact-seller.list-group-item .rtcl-icon' => 'font-size: {{SIZE}}{{UNIT}};',
//				],
//			],
			/* == Title == */
//			[
//				'type'      => Controls_Manager::RAW_HTML,
//				'id'        => 'rtcl_seller_website_title_heading',
//				'separator' => 'before',
//				'raw'       => sprintf(
//					'<h3 class="rtcl-elementor-group-heading">%s</h3>',
//					__( 'Title', 'rtcl-elementor-builder' )
//				),
//			],
//			[
//				'type'      => Controls_Manager::COLOR,
//				'id'        => 'rtcl_seller_website_title_color',
//				'label'     => __( 'Color', 'rtcl-elementor-builder' ),
//				'selectors' => [
//					'{{WRAPPER}} .el-single-addon.seller-information .rtcl-contact-seller.list-group-item .rtcl-contact-seller' => 'color: {{VALUE}};',
//				],
//			],
//			[
//				'mode'     => 'group',
//				'type'     => Group_Control_Typography::get_type(),
//				'id'       => 'rtcl_seller_website_title_typo',
//				'label'    => __( 'Typography', 'rtcl-elementor-builder' ),
//				'selector' => '{{WRAPPER}} .el-single-addon.seller-information .rtcl-contact-seller.list-group-item .rtcl-contact-seller',
//			],
//			[
//				'mode'       => 'responsive',
//				'label'      => __( 'Margin', 'rtcl-elementor-builder' ),
//				'type'       => Controls_Manager::DIMENSIONS,
//				'id'         => 'rtcl_seller_website_title_margin',
//				'size_units' => [ 'px', 'em', '%' ],
//				'selectors'  => [
//					'{{WRAPPER}} .el-single-addon.seller-information .rtcl-contact-seller.list-group-item .rtcl-contact-seller' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
//				],
//			],
			[
				'mode' => 'section_end',
			],
		];

		return $fields;
	}
	
	/**
	 * Set style for User Online Status
	 *
	 * @return array
	 */
	public function user_online_status() {
		$fields = [
			[
				'mode'  => 'section_start',
				'id'    => 'user_online_status_style',
				'tab'   => Controls_Manager::TAB_STYLE,
				'label' => __( 'User Online Status', 'rtcl-elementor-builder' ),
			],
			[
				'type'           => Group_Control_Border::get_type(),
				'label'          => __( 'Border', 'rtcl-elementor-builder' ),
				'mode'           => 'group',
				'id'             => 'rtcl_user_online_status_border',
				'fields_options' => [
					'border' => [
						'default' => 'solid',
					],
					'width'  => [
						'default' => [
							'top'      => '1',
							'right'    => '1',
							'bottom'   => '1',
							'left'     => '1',
							'isLinked' => false,
						],
					],
					'color'  => [
						'default' => 'rgba(0, 0, 0, 0.125)',
					],
				],
				'selector'       => '{{WRAPPER}} .el-single-addon.seller-information .list-group-item.rtcl-user-status',
			],
			[
				'mode'       => 'responsive',
				'label'      => __( 'Margin', 'rtcl-elementor-builder' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'id'         => 'rtcl_user_online_status_box_margin',
				'size_units' => [ 'px', 'em', '%' ],
				'selectors'  => [
					'{{WRAPPER}} .el-single-addon.seller-information .list-group-item.rtcl-user-status' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			],
			[
				'mode'       => 'responsive',
				'label'      => __( 'Padding', 'rtcl-elementor-builder' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'id'         => 'rtcl_user_online_status_box_padding',
				'size_units' => [ 'px', 'em', '%' ],
				'selectors'  => [
					'{{WRAPPER}} .el-single-addon.seller-information .list-group-item.rtcl-user-status' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			],
			[
				'mode'       => 'responsive',
				'label'      => __( 'Border Radius', 'rtcl-elementor-builder' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'id'         => 'rtcl_user_online_status_box_border_radius',
				'size_units' => [ 'px', 'em', '%' ],
				'selectors'  => [
					'{{WRAPPER}} .el-single-addon.seller-information .list-group-item.rtcl-user-status' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			],
			
			/* == Icon == */
			[
				'type'      => Controls_Manager::RAW_HTML,
				'id'        => 'user_online_status_icon_heading',
				'separator' => 'before',
				'raw'       => sprintf(
					'<h3 class="rtcl-elementor-group-heading">%s</h3>',
					__( 'Icon', 'rtcl-elementor-builder' )
				),
			],
			[
				'type'      => Controls_Manager::COLOR,
				'id'        => 'rtcl_user_online_status_icon_color',
				'label'     => __( 'Color', 'rtcl-elementor-builder' ),
				'selectors' => [
					'{{WRAPPER}} .el-single-addon .rtcl-listing-user-info .rtcl-user-status.online > span::before' => 'background-color: {{VALUE}};',
				],
			],
			[
				'type'       => Controls_Manager::SLIDER,
				'id'         => 'rtcl_user_online_status_icon_size',
				'label'      => esc_html__( 'Size', 'rtcl-elementor-builder' ),
				'size_units' => [ 'px' ],
				'selectors'  => [
					'{{WRAPPER}} .el-single-addon .rtcl-listing-user-info .rtcl-user-status.online > span::before' => 'width: {{SIZE}}{{UNIT}}; height: {{SIZE}}{{UNIT}};',
				],
			],
			
			/* == Title == */
			[
				'type'      => Controls_Manager::RAW_HTML,
				'id'        => 'rtcl_user_online_status_title_heading',
				'separator' => 'before',
				'raw'       => sprintf(
					'<h3 class="rtcl-elementor-group-heading">%s</h3>',
					__( 'Title', 'rtcl-elementor-builder' )
				),
			],
			[
				'type'      => Controls_Manager::COLOR,
				'id'        => 'rtcl_user_online_status_title_color',
				'label'     => __( 'Color', 'rtcl-elementor-builder' ),
				'selectors' => [
					'{{WRAPPER}} .el-single-addon.seller-information .list-group-item.rtcl-user-status span' => 'color: {{VALUE}};',
				],
			],
			[
				'mode'     => 'group',
				'type'     => Group_Control_Typography::get_type(),
				'id'       => 'rtcl_user_online_status_title_typo',
				'label'    => __( 'Typography', 'rtcl-elementor-builder' ),
				'selector' => '{{WRAPPER}} .el-single-addon.seller-information .list-group-item.rtcl-user-status span',
			],
			[
				'mode'       => 'responsive',
				'label'      => __( 'Margin', 'rtcl-elementor-builder' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'id'         => 'rtcl_user_online_status_title_margin',
				'size_units' => [ 'px', 'em', '%' ],
				'selectors'  => [
					'{{WRAPPER}} .el-single-addon.seller-information .list-group-item.rtcl-user-status span' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			],
			[
				'mode' => 'section_end',
			],
		];

		return $fields;
	}
}
