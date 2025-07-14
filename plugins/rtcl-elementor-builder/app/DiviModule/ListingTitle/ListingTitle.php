<?php

namespace  RtclElb\DiviModule\ListingTitle;
use Rtcl\Helpers\Functions;
use Rtcl\Traits\Addons\ListingItem;
use RtclElb\Helpers\Fns;

Class ListingTitle extends \ET_Builder_Module {
	use ListingItem;
	public $slug = 'rtcl_listing_title';
	public $vb_support = 'on';
	public $icon_path;
	protected $module_credits
		= [
			'author'     => 'RadiusTheme',
			'author_uri' => 'https://radiustheme.com',
		];
	public function init() {
		$this->name      = esc_html__( 'Listing Title', 'rtcl-elementor-builder' );
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
					'title'       => esc_html__( 'Title', 'rtcl-elementor-builder' ),
				],
			],
		];
	}

	public function get_fields() {
		return [
			'rtcl_header_size'       => [
				'label'          => esc_html__( 'HTML Tag ', 'rtcl-elementor-builder' ),
				'type'           => 'select',
				'options'        => [
					'h1'   => 'H1',
					'h2'   => 'H2',
					'h3'   => 'H3',
					'h4'   => 'H4',
					'h5'   => 'H5',
					'h6'   => 'H6',
					'div'  => 'div',
					'span' => 'span',
					'p'    => 'p',
				],
				'default'        => 'h2',
				'description'    => esc_html__( 'Select HTML tag for display.', 'rtcl-elementor-builder' ),
				'mobile_options' => true,
				'tab_slug'       => 'general',
				'toggle_slug'    => 'general',
			],
			'rtcl_title_color'       => [
				'label'       => esc_html__( 'Name Color', 'rtcl-elementor-builder' ),
				'description' => esc_html__( 'Here you can define a custom color for category name.', 'rtcl-elementor-builder' ),
				'type'        => 'color-alpha',
				'tab_slug'    => 'advanced',
				'toggle_slug' => 'title',
				'hover'       => 'tabs',
			],
			'__get_listing'           => array(
				'type'                => 'computed',
				'computed_callback'   => array( 'RtclElb\DiviModule\ListingTitle\ListingTitle', 'get_listing_title' ),
				'computed_depends_on' => array(
					'rtcl_header_size'
				)
			),
		];
	}
	
	public static function get_listing_title(  $settings ) {
		$template_style = 'divi/listing-title/listing-title';
		$data = [
			'template'      => $template_style,
			'settings'      => $settings,
			'listing'       => rtcl()->factory->get_listing(self::listing_id()),
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
					'main' => '%%order_class%% .rtcl-listings-header-title',
				),
				'important'        => 'all',
				'hide_text_color'  => true,
				'hide_text_shadow' => true,
				'hide_text_align'  => true,
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
				'main'      => '%%order_class%% .rtcl-breadcrumb',
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
		$wrapper 		   = '%%order_class%% .el-single-addon.listing-title';
		$title_color       = $this->props['rtcl_title_color'];
		$title_hover_color = $this->get_hover_value('rtcl_title_color');
		$title_font_weight = explode( '|', $this->props['title_font'] )[1];
		// Title Color
		if (!empty($title_color)) {
			\ET_Builder_Element::set_style(
				$render_slug,
				[
					'selector'    => $wrapper."  .rtcl-listings-header-title.page-title",
					'declaration' => sprintf('color: %1$s !important;', $title_color),
				]
			);
		}
		if ( ! empty( $title_hover_color ) ) {
			\ET_Builder_Element::set_style(
				$render_slug,
				[
					'selector'    => "$wrapper  .rtcl-listings-header-title.page-title:hover",
					'declaration' => sprintf( 'color: %1$s;', $title_hover_color ),
				]
			);
		}
		if ( ! empty( $title_font_weight ) ) {
			\ET_Builder_Element::set_style(
				$render_slug,
				array(
					'selector'    => '.et-db .et-l %%order_class%% .rtcl-listings-header-title',
					'declaration' => sprintf( 'font-weight: %1$s;', $title_font_weight ),
				)
			);
		}
	}


}