<?php

namespace  RtclElb\DiviModule\Store\StoreOpeningHour;
use Rtcl\Helpers\Functions;
use Rtcl\Traits\Addons\ListingItem;
use RtclElb\Helpers\Fns;

Class StoreOpeningHour extends \ET_Builder_Module {
	use ListingItem;
	public $slug = 'rtcl_listing_store_opening_hour';
	public $vb_support = 'on';
	public $icon_path;
	protected $module_credits
		= [
			'author'     => 'RadiusTheme',
			'author_uri' => 'https://radiustheme.com',
		];
	public function init() {
		$this->name      = esc_html__( 'Store Opening Hour', 'rtcl-elementor-builder' );
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
					'title'       => esc_html__( 'Opening Hours', 'rtcl-elementor-builder' ),
					'today'       => esc_html__( 'Today\'s', 'rtcl-elementor-builder' ),
					'offday'       => esc_html__( 'Off Day', 'rtcl-elementor-builder' ),
				],
			],
		];
	}

	public function get_fields() {
		return [
			'__get_listing'           => array(
				'type'                => 'computed',
				'computed_callback'   => array( 'RtclElb\DiviModule\Store\StoreOpeningHour\StoreOpeningHour', 'get_listing_title' ),
				'computed_depends_on' => [
					'__get_listing',
				],
			),
		];
	}
	
	public static function get_listing_title(  $settings ) {
		$template_style = 'divi/store-single/opening';
		$data = [
			'template'      => $template_style,
			'settings'      => $settings,
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
					'main' => '%%order_class%% .store-hours-list .store-hour',
					'important' => 'all',
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
			'today'       => [
				'css'              => array(
					'main' => '%%order_class%% .store-hours-list .store-hour.current-store-hour',
				),
				'important'        => 'all',
				'tab_slug'         => 'advanced',
				'toggle_slug'      => 'today',
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
			'offday'       => [
				'css'              => array(
					'main' => '%%order_class%% .store-hours-list .store-hour .off-day',
				),
				'important'        => 'all',
				'tab_slug'         => 'advanced',
				'toggle_slug'      => 'offday',
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