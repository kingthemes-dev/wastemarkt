<?php

namespace  RtclElb\DiviModule\Store\StoreSlogan;
use Rtcl\Helpers\Functions;
use Rtcl\Traits\Addons\ListingItem;
use RtclElb\Helpers\Fns;

Class StoreSlogan extends \ET_Builder_Module {
	use ListingItem;
	public $slug = 'rtcl_listing_store_slogan';
	public $vb_support = 'on';
	public $icon_path;
	protected $module_credits
		= [
			'author'     => 'RadiusTheme',
			'author_uri' => 'https://radiustheme.com',
		];
	public function init() {
		$this->name      = esc_html__( 'Store Slogan', 'rtcl-elementor-builder' );
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
					'title'       => esc_html__( 'Title', 'rtcl-elementor-builder' ),
				],
			],
		];
	}

	public function get_fields() {
		return [
			'__get_listing'           => array(
				'type'                => 'computed',
				'computed_callback'   => array( 'RtclElb\DiviModule\Store\StoreSlogan\StoreSlogan', 'get_listing_title' ),
				'computed_depends_on' => [
					'__get_listing',
				],
			),
		];
	}
	
	public static function get_listing_title(  $settings ) {
		$template_style = 'divi/store-single/slogan';
		$data = [
			'template'      => $template_style,
			'settings'      => $settings,
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
					'main' => '%%order_class%%.rtcl_listing_store_slogan .store-details > h3',
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