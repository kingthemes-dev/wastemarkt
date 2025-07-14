<?php

namespace  RtclElb\DiviModule\ListingMap;
use Rtcl\Helpers\Functions;
use Rtcl\Traits\Addons\ListingItem;
use RtclElb\Helpers\Fns;

Class ListingMap extends \ET_Builder_Module {
	use ListingItem;
	public $slug = 'rtcl_listing_map';
	public $vb_support = 'on';
	public $icon_path;
	protected $module_credits
		= [
			'author'     => 'RadiusTheme',
			'author_uri' => 'https://radiustheme.com',
		];
	public function init() {
		$this->name      = esc_html__( 'Listing Map', 'rtcl-elementor-builder' );
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
//					'general'       => esc_html__( 'General', 'rtcl-elementor-builder' ),
				],
			],
		];
	}

	public function get_fields() {
		$fields =  [
			'rtcl_font_icon'      => array(
				'label'           => esc_html__( 'Show Label', 'et_builder' ),
				'type'        => 'yes_no_button',
				'options'     => [
					'on'  => esc_html__( 'Yes', 'rtcl-elementor-builder' ),
					'off' => esc_html__( 'No', 'rtcl-elementor-builder' ),
				],
				'option_category' => 'basic_option',
				'default'         => 'on',
				'toggle_slug'     => 'main_content',
				'description'     => esc_html__( 'Choose an icon to display with your blurb.', 'et_builder' ),
				'mobile_options'  => true,
				'hover'           => 'tabs',
			),
			// computed.
			'__listing_map'           => array(
				'type'                => 'computed',
				'computed_callback'   => array('RtclElb\DiviModule\ListingMap\ListingMap', 'get_content' ),
				'computed_depends_on' => array(
					'rtcl_font_icon',
				)
			),
		];
		return $fields;
	}

	public function get_advanced_fields_config() {

		$advanced_fields                = [];
		$advanced_fields['text']        = [];
		$advanced_fields['text_shadow'] = [];

		$advanced_fields['fonts'] = [
			'general'       => [
				'css'              => array(
					'main' => '%%order_class%% .rtcl-listing-badge-wrap .rtcl-badge-new,
					 		%%order_class%% .rtcl-listing-badge-wrap .rtcl-badge-featured,
					 		%%order_class%% .rtcl-listing-badge-wrap .rtcl-badge-popular,
					 		%%order_class%% .rtcl-listing-badge-wrap .rtcl-badge-_top,
					 		%%order_class%% .rtcl-listing-badge-wrap .rtcl-badge-_bump_up',
				),
				'important'        => 'all',
				'hide_text_color'  => true,
				'hide_text_shadow' => true,
				'hide_text_align'  => true,
				'tab_slug'         => 'advanced',
				'toggle_slug'      => 'general',
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
		$advanced_fields['margin_padding'] = [
			'css'         => [
				'main'      => '%%order_class%% .rtcl-listing-badge-wrap .rtcl-badge-new,
					 		%%order_class%% .rtcl-listing-badge-wrap .rtcl-badge-featured,
					 		%%order_class%% .rtcl-listing-badge-wrap .rtcl-badge-popular,
					 		%%order_class%% .rtcl-listing-badge-wrap .rtcl-badge-_top,
					 		%%order_class%% .rtcl-listing-badge-wrap .rtcl-badge-_bump_up',
				'important' => 'all',
			],
			'tab_slug'    => 'advanced',
			'toggle_slug' => 'general',
		];

		return $advanced_fields;
	}

	public static function get_content( $settings ) {
		$template_style = 'divi/listing-map/map';
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
//		$this->render_css( $render_slug );
		return self::get_content( $settings );
	}
	



}