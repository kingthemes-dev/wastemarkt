<?php

namespace  RtclElb\DiviModule\Store\StoreBanner;
use Rtcl\Helpers\Functions;
use Rtcl\Traits\Addons\ListingItem;
use RtclElb\Helpers\Fns;

Class StoreBanner extends \ET_Builder_Module {
	use ListingItem;
	public $slug = 'rtcl_listing_store_banner';
	public $vb_support = 'on';
	public $icon_path;
	protected $module_credits
		= [
			'author'     => 'RadiusTheme',
			'author_uri' => 'https://radiustheme.com',
		];
	public function init() {
		$this->name      = esc_html__( 'Store Banner', 'rtcl-elementor-builder' );
		$this->icon_path = plugin_dir_path( __FILE__ ) . 'icon.svg';
		$this->folder_name = 'et_pb_classified_store_single_page_modules';
		$this->settings_modal_toggles = [
			'general'  => [
				'toggles' => [
					'general'    => esc_html__( 'General', 'rtcl-elementor-builder' ),
				],
			],
			'advanced' => [
				'toggles' => [
					'title'       		=> esc_html__( 'Title', 'rtcl-elementor-builder' ),
					'name'       		=> esc_html__( 'Name', 'rtcl-elementor-builder' ),
					'category'       	=> esc_html__( 'Category', 'rtcl-elementor-builder' ),
					'review'       		=> esc_html__( 'Review', 'rtcl-elementor-builder' ),
					'review_count'      => esc_html__( 'Review Count', 'rtcl-elementor-builder' ),
					'logo'       		=> esc_html__( 'Logo', 'rtcl-elementor-builder' ),
				],
			],
		];
	}

	public function get_fields() {
		return [
			'rtcl_show_store_logo'       => [
				'label'          => esc_html__( 'Show Store Logo', 'rtcl-elementor-builder' ),
				'type'        => 'yes_no_button',
				'options'     => [
					'on'  => esc_html__( 'Yes', 'rtcl-elementor-builder' ),
					'off' => esc_html__( 'No', 'rtcl-elementor-builder' ),
				],
				'default'        => 'on',
				'description'    => esc_html__( 'Select Status for display.', 'rtcl-elementor-builder' ),
				'mobile_options' => true,
				'tab_slug'       => 'general',
				'toggle_slug'    => 'general',
			],
			'rtcl_show_store_name'       => [
				'label'          => esc_html__( 'Show Store Name', 'rtcl-elementor-builder' ),
				'type'        => 'yes_no_button',
				'options'     => [
					'on'  => esc_html__( 'Yes', 'rtcl-elementor-builder' ),
					'off' => esc_html__( 'No', 'rtcl-elementor-builder' ),
				],
				'default'        => 'on',
				'description'    => esc_html__( 'Select Addess display.', 'rtcl-elementor-builder' ),
				'mobile_options' => true,
				'tab_slug'       => 'general',
				'toggle_slug'    => 'general',
			],
			'rtcl_show_category'       => [
				'label'          => esc_html__( 'Show Category', 'rtcl-elementor-builder' ),
				'type'        => 'yes_no_button',
				'options'     => [
					'on'  => esc_html__( 'Yes', 'rtcl-elementor-builder' ),
					'off' => esc_html__( 'No', 'rtcl-elementor-builder' ),
				],
				'default'        => 'on',
				'description'    => esc_html__( 'Select Addess display.', 'rtcl-elementor-builder' ),
				'mobile_options' => true,
				'tab_slug'       => 'general',
				'toggle_slug'    => 'general',
			],
			'rtcl_show_rating'       => [
				'label'          => esc_html__( 'Show Rating', 'rtcl-elementor-builder' ),
				'type'        => 'yes_no_button',
				'options'     => [
					'on'  => esc_html__( 'Yes', 'rtcl-elementor-builder' ),
					'off' => esc_html__( 'No', 'rtcl-elementor-builder' ),
				],
				'default'        => 'on',
				'description'    => esc_html__( 'Select Addess display.', 'rtcl-elementor-builder' ),
				'mobile_options' => true,
				'tab_slug'       => 'general',
				'toggle_slug'    => 'general',
			],
			'__get_listing'           => array(
				'type'                => 'computed',
				'computed_callback'   => array( 'RtclElb\DiviModule\Store\StoreBanner\StoreBanner', 'get_listing_title' ),
				'computed_depends_on' => array(
					'rtcl_show_rating',
					'rtcl_show_category',
					'rtcl_show_store_name',
					'rtcl_show_store_logo',
				)
			),
		];
	}

	public static function get_listing_title(  $settings ) {
		$template_style = 'divi/store-single/banner';
		$data = [
			'template'      => $template_style,
			'settings'      => $settings,
			'instance' 		=> $settings,
			'listing'       => rtcl()->factory->get_listing(self::listing_id()),
			'store'       	=> rtclStore()->factory->get_store( Fns::last_store_id() ),
			'template_path' => Fns::get_plugin_template_path(),
		];

		return Functions::get_template_html( $data['template'], $data, '', $data['template_path'] );
	}

	public function get_advanced_fields_config() {

		$advanced_fields                = [];
		$advanced_fields['text']        = [];
		$advanced_fields['text_shadow'] = [];

		$advanced_fields['fonts'] = [
			'title'       => [
				'css'              => array(
					'main' => '%%order_class%% .store-name-logo .store-name h2',
				),
				'important'        => 'all',
				'tab_slug'         => 'advanced',
				'toggle_slug'      => 'title',
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
			'category'       => [
				'css'              => array(
					'main' => '%%order_class%% .store-info .rtcl-store-cat',
				),
				'important'        => 'all',
				'tab_slug'         => 'advanced',
				'toggle_slug'      => 'category',
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
			'review'       => [
				'css'              => array(
					'main' => '.rtcl-page.single-store %%order_class%%  .store-name-logo .reviews-rating',
				),
				'important'        => 'all',
				'tab_slug'         => 'advanced',
				'toggle_slug'      => 'review',
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
			'review_count'       => [
				'css'              => array(
					'main' => '%%order_class%% .store-name-logo .reviews-rating .reviews-rating-count',
				),
				'important'        => 'all',
				'tab_slug'         => 'advanced',
				'toggle_slug'      => 'review_count',
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
		$advanced_fields['form_field'] = [
			'logo'  => array(
				'label'           => __( 'Logo', 'rtcl-elementor-builder' ),
				'css'         => array(
					'main'    => '%%order_class%% .rtcl.store-content-wrap .store-name-logo .store-logo',
					'padding' => '%%order_class%% .rtcl.store-content-wrap .store-name-logo .store-name-logo .store-logo',
					'margin'  => '%%order_class%% .rtcl.store-content-wrap .store-name-logo .store-name-logo .store-logo',
					'important' => ['all'],
				),
				'hide_text_color'  => true,
				'hide_text_size'   => true,
				'hide_use_font'    => true,
				'hide_text_shadow' => true,
				'hide_text_align'  => true,
				'text_color'      => false,
				'font' => false,
				'focus_text_color'      => false,
				'focus_background_color' => false,
				'box_shadow'      => array(
					'css' => array(
						'main' => implode(
							',',
							array(
								'%%order_class%% .rtcl.store-content-wrap .store-name-logo .store-logo',
							)
						),
						'important' => array('all'),
					),
				),
				'border_styles'   => array(
					'logo'       => array(
						'label_prefix' => __( 'Fields', 'rtcl-elementor-builder' ),
						'css'          => array(
							'main' => array(
								'border_styles' => implode(
									',',
									array(
										'%%order_class%% .rtcl.store-content-wrap .store-name-logo .store-logo',
									)
								),
								'border_radii'  => implode(
									',',
									array(
										'%%order_class%% .rtcl.store-content-wrap .store-name-logo .store-logo',
									)
								),
							),
						),
						'defaults'     => array(
							'border_radii'  => 'on|0px|0px|0px|0px',
							'border_styles' => array(
								'width' => '0px',
								'style' => 'solid',
							),
						),
					),
				),
				'margin_padding'  => array(
					'css' => array(
						'main'    => '%%order_class%% .rtcl.store-content-wrap .store-name-logo .store-logo',
						'padding' => '%%order_class%% .rtcl.store-content-wrap .store-name-logo .store-logo',
						'margin'  => '%%order_class%% .rtcl.store-content-wrap .store-name-logo .store-logo',
						'important' => 'all',
					),
				),
				'width'           => array(),
				'toggle_priority' => 55,
			),
		];

		return $advanced_fields;
	}

	public static function listing_id(): int
	{
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

		return self::get_listing_title( $settings );
	}

	protected function render_css( $render_slug ) {
		
	}


}