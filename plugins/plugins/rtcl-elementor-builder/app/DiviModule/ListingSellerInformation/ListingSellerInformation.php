<?php

namespace  RtclElb\DiviModule\ListingSellerInformation;
use Rtcl\Controllers\Hooks\TemplateHooks;
use Rtcl\Helpers\Functions;
use Rtcl\Traits\Addons\ListingItem;
use RtclElb\Helpers\Fns;
use RtclPro\Controllers\Hooks\TemplateHooks as TemplateHooksPro;

Class ListingSellerInformation extends \ET_Builder_Module {
	use ListingItem;
	public $slug = 'rtcl_listing_seller_information';
	public $vb_support = 'on';
	public $icon_path;
	protected $module_credits
		= [
			'author'     => 'RadiusTheme',
			'author_uri' => 'https://radiustheme.com',
		];
	public function init() {
		$this->name      = esc_html__( 'Listing Selling Information', 'rtcl-elementor-builder' );
		$this->icon_path = plugin_dir_path( __FILE__ ) . 'icon.svg';
		$this->folder_name = 'et_pb_classified_single_page_modules';
		$this->settings_modal_toggles = [
			'general'  => [
				'toggles' => [
					'general'    => esc_html__( 'General', 'rtcl-elementor-builder' ),
				],
			],
			'advanced' => [
				'toggles' => [
					'user'       => esc_html__( 'User Information', 'rtcl-elementor-builder' ),
					'location'       => esc_html__( 'Location', 'rtcl-elementor-builder' ),
					'contact' => esc_html__( 'Contact', 'rtcl-elementor-builder' ),
					'chat' => esc_html__( 'Chat', 'rtcl-elementor-builder' ),
					'website' => esc_html__( 'Website', 'rtcl-elementor-builder' ),
					'online_status' => esc_html__( 'Online Status', 'rtcl-elementor-builder' ),
					'offline_status' => esc_html__( 'Online Status', 'rtcl-elementor-builder' ),
				],
			],
		];
	}

	public function get_fields() {
		$fields =  [
			'rtcl_show_author'       => [
				'label'          =>  __('Show Author', 'rtcl-elementor-builder'),
				'type'        => 'yes_no_button',
				'options'     => [
					'on'  => esc_html__( 'Yes', 'rtcl-elementor-builder' ),
					'off' => esc_html__( 'No', 'rtcl-elementor-builder' ),
				],
				'default'        => 'on',
				'description'    => esc_html__( 'Select list style.', 'rtcl-elementor-builder' ),
				'mobile_options' => true,
				'tab_slug'       => 'general',
				'toggle_slug'    => 'general',
			],
			'rtcl_show_author_image' => [
				'label'      		=> esc_html__('Show Author Image', 'rtcl-elementor-builder'),
				'type'           => 'yes_no_button',
				'options'     => [
					'on'  => esc_html__( 'Yes', 'rtcl-elementor-builder' ),
					'off' => esc_html__( 'No', 'rtcl-elementor-builder' ),
				],
				'default'     => 'on',
				'option_category' 	=> 'basic_option',
				'tab_slug'    		=> 'general',
				'toggle_slug' 		=> 'general',
				'show_if'    => [
					'rtcl_show_comment_list' => 'on',
				],
			],
			'rtcl_show_location'        => [
				'label'       => esc_html__('Show Location', 'rtcl-elementor-builder'),
				'type'        => 'yes_no_button',
				'options'     => [
					'on'  => esc_html__( 'Yes', 'rtcl-elementor-builder' ),
					'off' => esc_html__( 'No', 'rtcl-elementor-builder' ),
				],
				'default'     => 'on',
				'description' => __( 'Show / Hide new badge.', 'rtcl-elementor-builder' ),
				'tab_slug'    => 'general',
				'toggle_slug' => 'general',
			],
			'rtcl_show_contact'        => [
				'label'       => __('Show Contact Number', 'rtcl-elementor-builder'),
				'type'        => 'yes_no_button',
				'options'     => [
					'on'  => esc_html__( 'Yes', 'rtcl-elementor-builder' ),
					'off' => esc_html__( 'No', 'rtcl-elementor-builder' ),
				],
				'default'     => 'on',
				'description' => __( 'Show / Hide new badge.', 'rtcl-elementor-builder' ),
				'tab_slug'    => 'general',
				'toggle_slug' => 'general',
			],
			'rtcl_show_contact_form'        => [
				'label'       => __('Show Contact Form', 'rtcl-elementor-builder'),
				'type'        => 'yes_no_button',
				'options'     => [
					'on'  => esc_html__( 'Yes', 'rtcl-elementor-builder' ),
					'off' => esc_html__( 'No', 'rtcl-elementor-builder' ),
				],
				'default'     => 'on',
				'description' => __( 'Show / Hide new badge.', 'rtcl-elementor-builder' ),
				'tab_slug'    => 'general',
				'toggle_slug' => 'general',
			],
			'rtcl_contact_btn_text'        => [
				'label'       => __('Contact Button Text', 'rtcl-elementor-builder'),
				'type'        => 'text',
				'default'     => '',
				'description' => __( 'Show / Hide new badge.', 'rtcl-elementor-builder' ),
				'show_if'    => [
					'rtcl_show_contact_form' => 'on',
				],
				'tab_slug'    => 'general',
				'toggle_slug' => 'general',
			],
			'rtcl_show_seller_website'        => [
				'label'       => __('Show Seller Website', 'rtcl-elementor-builder'),
				'type'        => 'yes_no_button',
				'options'     => [
					'on'  => esc_html__( 'Yes', 'rtcl-elementor-builder' ),
					'off' => esc_html__( 'No', 'rtcl-elementor-builder' ),
				],
				'default'     => 'on',
				'description' => __( 'Show / Hide new badge.', 'rtcl-elementor-builder' ),
				'tab_slug'    => 'general',
				'toggle_slug' => 'general',
			],
			'rtcl_show_seller_website_text'        => [
				'label'       => __('Website Button Text', 'rtcl-elementor-builder'),
				'type'        => 'text',
				'default'     => '',
				'description' => __( 'Show / Hide new badge.', 'rtcl-elementor-builder' ),
				'tab_slug'    => 'general',
				'toggle_slug' => 'general',
				'show_if'    => [
					'rtcl_show_seller_website' => 'on',
				],
			],
			// computed.
			'__listing_selling_information'           => array(
				'type'                => 'computed',
				'computed_callback'   => array('RtclElb\DiviModule\ListingSellerInformation\ListingSellerInformation', 'get_content' ),
				'computed_depends_on' => array(
					'rtcl_show_author',
					'rtcl_show_author_image',
					'rtcl_show_contact',
					'rtcl_show_location',
					'rtcl_show_contact_form',
					'rtcl_offline_status_text',
					'rtcl_online_status_text',
					'rtcl_chat_btn_text',
					'rtcl_add_user_online_status',
					'rtcl_add_chat_link',
					'rtcl_show_seller_website_text',
					'rtcl_show_seller_website',
					'rtcl_contact_btn_text',
				)
			),
			// visibility
			'rtcl_price_color'       => [
				'label'       => esc_html__( 'Price Color', 'rtcl-elementor-builder' ),
				'description' => esc_html__( 'Here you can define a custom color for category name.', 'rtcl-elementor-builder' ),
				'type'        => 'color-alpha',
				'tab_slug'    => 'advanced',
				'toggle_slug' => 'price',
				'hover'       => 'tabs',
			]
		];
		
		
		if ( rtcl()->has_pro() ) {
			$pro_fileds = [
				'rtcl_add_chat_link'        => [
					'label'       => __('Show Chat Button', 'rtcl-elementor-builder'),
					'type'        => 'yes_no_button',
					'options'     => [
						'on'  => esc_html__( 'Yes', 'rtcl-elementor-builder' ),
						'off' => esc_html__( 'No', 'rtcl-elementor-builder' ),
					],
					'default'     => 'on',
					'description' => __( 'Show / Hide new badge.', 'rtcl-elementor-builder' ),
					'tab_slug'    => 'general',
					'toggle_slug' => 'general',
				],
				'rtcl_chat_btn_text'        => [
					'label'       => __('Chat Button Text', 'rtcl-elementor-builder'),
					'type'        => 'text',
					'default'     => '',
					'description' => __( 'Show / Hide new badge.', 'rtcl-elementor-builder' ),
					'tab_slug'    => 'general',
					'toggle_slug' => 'general',
				],
				'rtcl_add_user_online_status'        => [
					'label'       => __('Show Online Status', 'rtcl-elementor-builder'),
					'type'        => 'yes_no_button',
					'options'     => [
						'on'  => esc_html__( 'Yes', 'rtcl-elementor-builder' ),
						'off' => esc_html__( 'No', 'rtcl-elementor-builder' ),
					],
					'default'     => 'on',
					'description' => __( 'Show / Hide new badge.', 'rtcl-elementor-builder' ),
					'tab_slug'    => 'general',
					'toggle_slug' => 'general',
				],
				'rtcl_offline_status_text'        => [
					'label'       => __('Offline Status Text', 'rtcl-elementor-builder'),
					'type'        => 'text',
					'default'     => '',
					'description' => __( 'Show / Hide new badge.', 'rtcl-elementor-builder' ),
					'tab_slug'    => 'general',
					'toggle_slug' => 'general',
					'show_if'    => [
						'rtcl_add_user_online_status' => 'on',
					],
				],
				'rtcl_online_status_text'        => [
					'label'       => __('Online Status Text', 'rtcl-elementor-builder'),
					'type'        => 'text',
					'default'     => '',
					'description' => __( 'Show / Hide new badge.', 'rtcl-elementor-builder' ),
					'tab_slug'    => 'general',
					'toggle_slug' => 'general',
					'show_if'    => [
						'rtcl_add_user_online_status' => 'on',
					],
				],
			];
			$fields = array_merge($fields, $pro_fileds);
		}
		return $fields;
	}

	public function get_advanced_fields_config() {

		$advanced_fields                = [];
		$advanced_fields['text']        = [];
		$advanced_fields['text_shadow'] = [];

		$advanced_fields['fonts'] = [
			'user'       => [
				'css'              => array(
					'main' => '%%order_class%% .rtcl-listing-user-info .listing-author .author-name',
				),
				'important'        => 'all',
//				'hide_text_color'  => true,
//				'hide_text_shadow' => true,
//				'hide_text_align'  => true,
				'tab_slug'         => 'advanced',
				'toggle_slug'      => 'user',
				'line_height'      => array(
					'range_settings' => array(
						'min'  => '1',
						'max'  => '3',
						'step' => '.1',
					),
					'default'        => '1.2em',
				),
				'font_size'        => array(
					'default' => '18px',
				),
				'font'             => [
					'default' => '|700|||||||',
				],
			],
			'location'       => [
				'css'              => array(
					'main' => '%%order_class%% .el-single-addon.seller-information .list-group-item:not(.reveal-phone) .media',
				),
				'important'        => 'all',
				'tab_slug'         => 'advanced',
				'toggle_slug'      => 'location',
				'line_height'      => array(
					'range_settings' => array(
						'min'  => '1',
						'max'  => '3',
						'step' => '.1',
					),
					'default'        => '1.2em',
				),
				'font_size'        => array(
					'default' => '18px',
				),
				'font'             => [
					'default' => '|700|||||||',
				],
			],
			'contact'       => [
				'css'              => array(
					'main' => '%%order_class%% .el-single-addon.seller-information .list-group-item.reveal-phone .media , %%order_class%% .el-single-addon.seller-information .list-group-item.reveal-phone .media .media-body .revealed-phone-number',
				),
				'important'        => 'all',
				'tab_slug'         => 'advanced',
				'toggle_slug'      => 'contact',
				'line_height'      => array(
					'range_settings' => array(
						'min'  => '1',
						'max'  => '3',
						'step' => '.1',
					),
					'default'        => '1.2em',
				),
				'font_size'        => array(
					'default' => '18px',
				),
				'font'             => [
					'default' => '|700|||||||',
				],
			],
			'chat'       => [
				'css'              => array(
					'main' => '%%order_class%% .el-single-addon.seller-information .rtcl-contact-seller.list-group-item a',
				),
				'important'        => 'all',
				'tab_slug'         => 'advanced',
				'toggle_slug'      => 'chat',
				'line_height'      => array(
					'range_settings' => array(
						'min'  => '1',
						'max'  => '3',
						'step' => '.1',
					),
					'default'        => '1.2em',
				),
				'font_size'        => array(
					'default' => '18px',
				),
				'font'             => [
					'default' => '|700|||||||',
				],
			],
			'online_status'       => [
				'css'              => array(
					'main' => '%%order_class%% .el-single-addon.seller-information .list-group-item.rtcl-user-status.online > span',
				),
				'important'        => 'all',
				'tab_slug'         => 'advanced',
				'toggle_slug'      => 'online_status',
				'line_height'      => array(
					'range_settings' => array(
						'min'  => '1',
						'max'  => '3',
						'step' => '.1',
					),
					'default'        => '1.2em',
				),
				'font_size'        => array(
					'default' => '18px',
				),
				'font'             => [
					'default' => '|700|||||||',
				],
			],
			'offline_status'       => [
				'css'              => array(
					'main' => '%%order_class%% .el-single-addon.seller-information .list-group-item.rtcl-user-status.offline > span',
				),
				'important'        => 'all',
				'tab_slug'         => 'advanced',
				'toggle_slug'      => 'offline_status',
				'line_height'      => array(
					'range_settings' => array(
						'min'  => '1',
						'max'  => '3',
						'step' => '.1',
					),
					'default'        => '1.2em',
				),
				'font_size'        => array(
					'default' => '18px',
				),
				'font'             => [
					'default' => '|700|||||||',
				],
			],
		];
		$advanced_fields['button'] = array(
			'website'        => array(
				'label'           => __( 'Submit Button', 'rtcl-elementor-builder' ),
				'css'             => array(
					'main' => '%%order_class%% .el-single-addon.seller-information .rtcl-website.list-group-item a',
				),
				'use_alignment'   => false,
				'use_icon'       => false,
				'border_width'    => array(
					'default' => '2px',
				),
				'box_shadow'      => array(
					'css' => array(
						'main' => '%%order_class%% .el-single-addon.seller-information .rtcl-website.list-group-item'
					),
				),
				'margin_padding'  => array(
					'css' => array(
						'important' => 'all',
					),
				),
				'toggle_priority' => 80,
				'toggle_slug'     => 'website',
			),
		);
		

		return $advanced_fields;
	}

	public static function get_content( $settings ) {
		add_filter( 'rtcl_is_chat_link_available', '__return_true' );
		$helper = new ListingSellerInformationHelper($settings);
		/* === Contact Form === */
		remove_action( 'rtcl_listing_seller_information', [ TemplateHooks::class, 'seller_email' ], 30 );
		if ( $settings['rtcl_show_contact_form'] === 'on' ) {
			add_action( 'rtcl_listing_seller_information', [ $helper, 'seller_contact_email' ], 30 );
		}

		/* === Chat Form === */
		if ( class_exists( TemplateHooksPro::class ) ) {
			remove_action( 'rtcl_listing_seller_information', [ TemplateHooksPro::class, 'add_chat_link' ], 40 );
			if ( $settings['rtcl_add_chat_link'] == 'on' ) {
				add_action( 'rtcl_listing_seller_information', [ $helper, 'add_chat_link' ], 40 );
			}
		}

		/* === Seller Website === */
		remove_action( 'rtcl_listing_seller_information', [ TemplateHooks::class, 'seller_website' ], 50 );
		if ( $settings['rtcl_show_seller_website'] === 'on' ) {
			add_action( 'rtcl_listing_seller_information', [$helper, 'seller_website' ], 50 );
		}

		/* === User Online Status === */
		if ( class_exists( TemplateHooksPro::class ) ) { // ✅ Works correctly
			remove_action( 'rtcl_listing_seller_information', [ TemplateHooksPro::class, 'add_user_online_status' ], 50 );
			if ( $settings['rtcl_add_user_online_status'] === 'on' ) {
				add_action( 'rtcl_listing_seller_information', [ $helper, 'listing_user_online_status' ], 50 );
			}
		}
		$template_style = 'divi/listing-seller-information/seller-information';
		$data = [
			'template'      => $template_style,
			'settings'      => $settings,
			'instance'      => $settings,
			'listing'       => rtcl()->factory->get_listing(self::listing_id()),
			'template_path' => Fns::get_plugin_template_path(),
		];

		return Functions::get_template_html( $data['template'], $data, '', $data['template_path'] );
	}
	public static function listing_id(): int {
		$_id = self::get_prepared_listing_id();
		return absint($_id);
	}

	/**
	 * Widget result.
	 *
	 * @param [array] $data array of query.
	 *
	 * @return array
	 */

	public function render( $unprocessed_props, $content, $render_slug ) {
		$settings = $this->props;
		$this->render_css( $render_slug );
		return self::get_content( $settings );
	}

	protected function render_css( $render_slug ) {
		$wrapper = '%%order_class%% .el-single-addon.item-price ';
		
		// ✅ Badge Styles (color + bg-color)
		$badge_styles = [
			[
				'class' => 'rtcl-price',
				'text_color' => 'rtcl_price_color',
				'bg_color'   => 'rtcl_price_bg_color',
			],
			[
				'class' => 'price-unit',
				'text_color' => 'rtcl_unit_color',
			],
			[
				'class' => 'price-type',
				'text_color' => 'rtcl_price_type_color',
			],
			[
				'class' => 'divider-after-price',
				'text_color' => 'rtcl_divider_color',
			]
		];

		foreach ( $badge_styles as $badge ) {
			$selector = "$wrapper .{$badge['class']}";
			$text_color = $this->props[ $badge['text_color'] ] ?? '';
			$bg_color   = $this->props[ $badge['bg_color'] ?? '' ] ?? '';

			if ( ! empty( $text_color ) ) {
				\ET_Builder_Element::set_style(
					$render_slug,
					[
						'selector'    => $selector,
						'declaration' => sprintf( 'color: %1$s !important;', $text_color ),
					]
				);
			}

			if ( ! empty( $bg_color ) ) {
				\ET_Builder_Element::set_style(
					$render_slug,
					[
						'selector'    => $selector,
						'declaration' => sprintf( 'background-color: %1$s !important;', $bg_color ),
					]
				);
			}
		}
	}



}