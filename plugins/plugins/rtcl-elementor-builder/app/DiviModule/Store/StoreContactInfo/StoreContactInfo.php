<?php

namespace  RtclElb\DiviModule\Store\StoreContactInfo;
use Rtcl\Helpers\Functions;
use Rtcl\Traits\Addons\ListingItem;
use RtclElb\Helpers\Fns;

Class StoreContactInfo extends \ET_Builder_Module {
	use ListingItem;
	public $slug = 'rtcl_listing_store_contact_info';
	public $vb_support = 'on';
	public $icon_path;
	protected $module_credits
		= [
			'author'     => 'RadiusTheme',
			'author_uri' => 'https://radiustheme.com',
		];
	public function init() {
		$this->name      = esc_html__( 'Store Contact Info', 'rtcl-elementor-builder' );
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
					'title'       => esc_html__( 'Contact', 'rtcl-elementor-builder' ),
					'submit_button'       => esc_html__( 'Submit Button', 'rtcl-elementor-builder' ),
				],
			],
		];
	}

	public function get_fields() {
		return [
			'rtcl_show_store_status'       => [
				'label'          => esc_html__( 'Show Status', 'rtcl-elementor-builder' ),
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
			'rtcl_show_store_address'       => [
				'label'          => esc_html__( 'Show Address', 'rtcl-elementor-builder' ),
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
			'rtcl_show_store_phone'       => [
				'label'          => esc_html__( 'Show Phone', 'rtcl-elementor-builder' ),
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
			'rtcl_show_store_social_media'       => [
				'label'          => esc_html__( 'Show Social Media', 'rtcl-elementor-builder' ),
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
			'rtcl_show_store_email'       => [
				'label'          => esc_html__( 'Show Store Email', 'rtcl-elementor-builder' ),
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
				'computed_callback'   => array( 'RtclElb\DiviModule\Store\StoreContactInfo\StoreContactInfo', 'get_listing_title' ),
				'computed_depends_on' => array(
					'rtcl_show_store_social_media',
					'rtcl_show_store_status',
					'rtcl_show_store_phone',
					'rtcl_show_store_address',
					'rtcl_show_store_email',
				)
			),
		];
	}

	public static function get_listing_title(  $settings ) {
		$template_style = 'divi/store-single/contact-info';
		
		add_action( 'wp_footer', function (){
			$template_style = 'divi/store-single/details-modal';
			$data           = [
				'template'              => $template_style,
				'store'                 => rtclStore()->factory->get_store( Fns::last_store_id() ),
				'default_template_path' => Fns::get_plugin_template_path(),
			];
			$data           = apply_filters( 'rtcl_el_store_contact_details_modal_data', $data );
			Functions::get_template( $data['template'], $data, '', $data['default_template_path'] );
		} );
		
		
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
					'main' => '%%order_class%% .store-information .store-info .store-info-item',
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
		];
		$advanced_fields['button'] = array(
			'submit_button'        => array(
				'label'           => __( 'Submit Button', 'rtcl-elementor-builder' ),
				'css'             => array(
					'main' => '%%order_class%% #store-email-area button.sc-submit',
				),
				'use_alignment'   => false,
				'border_width'    => array(
					'default' => '2px',
				),
				'box_shadow'      => array(
					'css' => array(
						'main' => '%%order_class%% #store-email-area button.sc-submit',
					),
				),
				'margin_padding'  => array(
					'css' => array(
						'important' => 'all',
					),
				),
				'toggle_priority' => 80,
				'toggle_slug'     => 'submit_button',
			),
		);
		$advanced_fields['margin_padding'] = [
			'css'         => [
				'main'      => '%%order_class%% .store-name',
				'important' => 'all',
			],
			'tab_slug'    => 'advanced',
			'toggle_slug' => 'card',
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