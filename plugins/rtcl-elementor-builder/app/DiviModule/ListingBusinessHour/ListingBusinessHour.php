<?php

namespace  RtclElb\DiviModule\ListingBusinessHour;
use Rtcl\Helpers\Functions;
use Rtcl\Traits\Addons\ListingItem;
use RtclElb\Helpers\Fns;

Class ListingBusinessHour extends \ET_Builder_Module {
	use ListingItem;
	public $slug = 'rtcl_listing_business_hours';
	public $vb_support = 'on';
	public $icon_path;
	protected $module_credits
		= [
			'author'     => 'RadiusTheme',
			'author_uri' => 'https://radiustheme.com',
		];
	public function init() {
		$this->name      = esc_html__( 'Listing Business Hour', 'rtcl-elementor-builder' );
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
					'general'       => esc_html__( 'General', 'rtcl-elementor-builder' ),
					'open'       => esc_html__( 'Open Status', 'rtcl-elementor-builder' ),
					'close'       => esc_html__( 'Close Status', 'rtcl-elementor-builder' ),
					'table'       => esc_html__( 'Table', 'rtcl-elementor-builder' ),
				],
			],
		];
	}

	public function get_fields() {
		$fields =  [
			'date_formate'       => [
				'label'          =>  __('Date Format', 'rtcl-elementor-builder'),
				'type'        => 'select',
				'options' => [
					'12' => esc_html__( '12 Hours', 'rtcl-elementor-builder' ),
					'24' => esc_html__( '24 Hours', 'rtcl-elementor-builder' ),
				],
				'default'        => '12',
				'description'    => esc_html__( 'Select Date Format.', 'rtcl-elementor-builder' ),
				'mobile_options' => true,
				'tab_slug'       => 'general',
				'toggle_slug'    => 'general',
			],
			'rtcl_show_open_status' => [
				'label'      		=> esc_html__('Show Open Status', 'rtcl-elementor-builder'),
				'type'        => 'yes_no_button',
				'options'     => [
					'on'  => esc_html__( 'Yes', 'rtcl-elementor-builder' ),
					'off' => esc_html__( 'No', 'rtcl-elementor-builder' ),
				],
				'option_category' 	=> 'basic_option',
				'default'         	=> 'on',
				'tab_slug'    		=> 'general',
				'toggle_slug' 		=> 'general',
			],
			'open_status_text'        => [
				'label'       => esc_html__('Open Status Text', 'rtcl-elementor-builder'),
				'type'            	=> 'text',
				'option_category' 	=> 'basic_option',
				'default'         	=> '',
				'description' => __( 'Show / Hide new badge.', 'rtcl-elementor-builder' ),
				'tab_slug'    => 'general',
				'toggle_slug' => 'general',
			],
			'close_status_text'        => [
				'label'       => __('Close Status Text', 'rtcl-elementor-builder'),
				'type'            	=> 'text',
				'option_category' 	=> 'basic_option',
				'default'         	=> '',
				'description' => __( 'Show / Hide new badge.', 'rtcl-elementor-builder' ),
				'tab_slug'    => 'general',
				'toggle_slug' => 'general',
			],
			// computed.
			'__listing_business_hour'           => array(
				'type'                => 'computed',
				'computed_callback'   => array('RtclElb\DiviModule\ListingBusinessHour\ListingBusinessHour', 'get_content' ),
				'computed_depends_on' => array(
					'close_status_text',
					'open_status_text',
					'rtcl_show_open_status',
					'date_formate',
				)
			),
			// visibility
//			'rtcl_table_td_color'       => [
//				'label'       => esc_html__( 'Data Color', 'rtcl-elementor-builder' ),
//				'description' => esc_html__( 'Here you can define a custom color for category name.', 'rtcl-elementor-builder' ),
//				'type'        => 'color-alpha',
//				'tab_slug'    => 'advanced',
//				'toggle_slug' => 'table',
//				'hover'       => 'tabs',
//			],
//			'rtcl_social_color'       => [
//				'label'       => esc_html__( 'Social Color', 'rtcl-elementor-builder' ),
//				'description' => esc_html__( 'Here you can define a custom color for category name.', 'rtcl-elementor-builder' ),
//				'type'        => 'color-alpha',
//				'tab_slug'    => 'advanced',
//				'toggle_slug' => 'social',
//				'hover'       => 'tabs',
//			],
//			'rtcl_social_bg_color'       => [
//				'label'       => esc_html__( 'Social Background Color', 'rtcl-elementor-builder' ),
//				'description' => esc_html__( 'Here you can define a custom color for category name.', 'rtcl-elementor-builder' ),
//				'type'        => 'color-alpha',
//				'tab_slug'    => 'advanced',
//				'toggle_slug' => 'social',
//				'hover'       => 'tabs',
//			]
		];
		return $fields;
	}

	public function get_advanced_fields_config() {

		$advanced_fields                = [];
		$advanced_fields['text']        = [];
		$advanced_fields['text_shadow'] = [];

		$advanced_fields['fonts'] = [
			'open'       => [
				'css'              => array(
					'main' => '%%order_class%% .el-single-addon.business-hours .rtclbh-status.rtclbh-status-open',
				),
				'important'        => 'all',
				'tab_slug'         => 'advanced',
				'toggle_slug'      => 'open',
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
			'close'       => [
				'css'              => array(
					'main' => '%%order_class%% .el-single-addon.business-hours .rtclbh-status.rtclbh-status-closed',
				),
				'important'        => 'all',
				'tab_slug'         => 'advanced',
				'toggle_slug'      => 'close',
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
			'table'		=> [
				'css'              => array(
					'main' => '%%order_class%% .el-single-addon.business-hours .rtclbh-block .rtclbh th',
				),
				'label'            => esc_html__( 'Table', 'rtcl-elementor-builder' ),
				'important'        => 'all',
				'tab_slug'         => 'advanced',
				'toggle_slug'      => 'table',
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
			'table_data'		=> [
				'css'              => array(
					'main' => '%%order_class%% .el-single-addon.business-hours .rtclbh-block .rtclbh .rtclbh-info',
				),
				'label'            => esc_html__( 'Table Data', 'rtcl-elementor-builder' ),
				'important'        => 'all',
				'tab_slug'         => 'advanced',
				'toggle_slug'      => 'table',
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
			]
		];
		$advanced_fields['form_field'] = [
			'table_col' =>[
				'label'                => esc_html__( 'Table Cell', 'rtcl-elementor-builder' ),
				'text' => false,
				'margin_padding' => false,
				'text_color'             => false,
				'background_color'             => false,
				'focus_background_color' => false,
				'focus_text_color'       => false,
				'border_styles'          => array(
					'table_col' => array(
						'label_prefix'      => __( 'Table Cell', 'rtcl-elementor-builder' ),
						'css'               => array(
							'main'      => array(
								'border_radii'  => "%%order_class%%  .el-single-addon.business-hours .rtclbh",
								'border_styles' => "%%order_class%%   .el-single-addon.business-hours .rtclbh,
										%%order_class%%   .el-single-addon.business-hours table td,
										%%order_class%%   .el-single-addon.business-hours table th,
										%%order_class%%   .el-single-addon.business-hours table tr",
							),
							'important' => array( 'border-color' ),
						),
						'use_focus_borders' => false,
					),
				),
			]
		];
		$advanced_fields['margin_padding'] = [
				'use_margin'   => true,
				'use_padding'  => true,
				'css'          => array(
					'main'      => "%%order_class%% .el-single-addon.business-hours .rtclbh-block",
					'important' => 'all',
				),
				'label_prefix' => __( 'Business Hour', 'rtcl-elementor-builder' ),
				'tab_slug'     => 'advanced',
				'toggle_slug'  => 'general',
		];
		$advanced_fields['border'] = [
			'css'          => array(
				'main' => array(
					'border_radii'  => "%%order_class%%  .el-single-addon.business-hours .rtclbh-block",
					'border_styles' => "%%order_class%%   .el-single-addon.business-hours .rtclbh-block",
				),
				'important' => 'all',
			),
			'label_prefix' => __( 'Table', 'rtcl-elementor-builder' ),
			'tab_slug'     => 'advanced',
			'toggle_slug'  => 'table',
		];

		return $advanced_fields;
	}

	public static function get_content( $settings ) {
		add_filter( 'rtcl_time_format', function ($formate) use ( $settings ) {
			if ( '24' === $settings['date_formate'] ) {
				$formate = 'H:i';
			}
			return $formate;
		} );
		add_filter( 'rtcl_business_hours_display_options', function ( $options ) use ( $settings ) {
			if ( $settings['open_status_text'] ) {
				$options['open_status_text'] = $settings['open_status_text'];
			}
			if ( $settings['close_status_text'] ) {
				$options['close_status_text'] = $settings['close_status_text'];
			}
			$rtcl_show_open_status = !empty( $settings['rtcl_show_open_status'] ) && $settings['rtcl_show_open_status'] === 'on' ?true : false;
			$options['show_open_status'] = $rtcl_show_open_status;
			return $options;
		} );
		
		$template_style = 'divi/listing-business-hour/business-hours';
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
		$wrapper = '%%order_class%% .el-single-addon.business-hours ';
		
		// ✅ Badge Styles (color + bg-color)
		$badge_styles = [
			[
				'class' => 'rtclbh-info',
				'text_color' => 'rtcl_table_td_color',
			],
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