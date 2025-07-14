<?php

namespace  RtclElb\DiviModule\ListingMeta;
use Rtcl\Helpers\Functions;
use Rtcl\Traits\Addons\ListingItem;
use RtclElb\Helpers\Fns;

Class ListingMeta extends \ET_Builder_Module {
	use ListingItem;
	public $slug = 'rtcl_listing_meta';
	public $vb_support = 'on';
	public $icon_path;
	protected $module_credits
		= [
			'author'     => 'RadiusTheme',
			'author_uri' => 'https://radiustheme.com',
		];
	public function init() {
		$this->name      = esc_html__( 'Listing Meta', 'rtcl-elementor-builder' );
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
					'meta'       => esc_html__( 'Meta', 'rtcl-elementor-builder' ),
				],
			],
		];
	}

	public function get_fields() {
		$fields =  [
			// computed.
			'__listing_meta'           => array(
				'type'                => 'computed',
				'computed_callback'   => array('RtclElb\DiviModule\ListingMeta\ListingMeta', 'get_content' ),
				'computed_depends_on' => array(
					'rtcl_show_types',
					'rtcl_show_date',
					'rtcl_show_user',
					'rtcl_show_category',
					'rtcl_show_location',
					'rtcl_show_views',
					'rtcl_meta_display',
					'rtcl_title_color',
				)
			),
			// visibility
			'rtcl_show_types'        => [
				'label'       => esc_html__( 'Show Types', 'rtcl-elementor-builder' ),
				'type'        => 'yes_no_button',
				'options'     => [
					'on'  => esc_html__( 'Yes', 'rtcl-elementor-builder' ),
					'off' => esc_html__( 'No', 'rtcl-elementor-builder' ),
				],
				'default'     => 'on',
				'description' => __( 'Show / Hide new Label.', 'rtcl-elementor-builder' ),
				'tab_slug'    => 'general',
				'toggle_slug' => 'general',
			],
			'rtcl_show_date'        => [
				'label'       => esc_html__( 'Show Date', 'rtcl-elementor-builder' ),
				'type'        => 'yes_no_button',
				'options'     => [
					'on'  => esc_html__( 'Yes', 'rtcl-elementor-builder' ),
					'off' => esc_html__( 'No', 'rtcl-elementor-builder' ),
				],
				'default'     => 'on',
				'description' => __( 'Show / Hide new Label.', 'rtcl-elementor-builder' ),
				'tab_slug'    => 'general',
				'toggle_slug' => 'general',
			],
			'rtcl_show_user'        => [
				'label'       => esc_html__( 'Show User', 'rtcl-elementor-builder' ),
				'type'        => 'yes_no_button',
				'options'     => [
					'on'  => esc_html__( 'Yes', 'rtcl-elementor-builder' ),
					'off' => esc_html__( 'No', 'rtcl-elementor-builder' ),
				],
				'default'     => 'on',
				'description' => __( 'Show / Hide new Label.', 'rtcl-elementor-builder' ),
				'tab_slug'    => 'general',
				'toggle_slug' => 'general',
			],
			'rtcl_show_category'        => [
				'label'       => esc_html__( 'Show Category', 'rtcl-elementor-builder' ),
				'type'        => 'yes_no_button',
				'options'     => [
					'on'  => esc_html__( 'Yes', 'rtcl-elementor-builder' ),
					'off' => esc_html__( 'No', 'rtcl-elementor-builder' ),
				],
				'default'     => 'on',
				'description' => __( 'Show / Hide new Label.', 'rtcl-elementor-builder' ),
				'tab_slug'    => 'general',
				'toggle_slug' => 'general',
			],
			'rtcl_show_location'        => [
				'label'       => esc_html__( 'Show Location', 'rtcl-elementor-builder' ),
				'type'        => 'yes_no_button',
				'options'     => [
					'on'  => esc_html__( 'Yes', 'rtcl-elementor-builder' ),
					'off' => esc_html__( 'No', 'rtcl-elementor-builder' ),
				],
				'default'     => 'on',
				'description' => __( 'Show / Hide new Label.', 'rtcl-elementor-builder' ),
				'tab_slug'    => 'general',
				'toggle_slug' => 'general',
			],
			'rtcl_show_views'        => [
				'label'       => esc_html__( 'Show View', 'rtcl-elementor-builder' ),
				'type'        => 'yes_no_button',
				'options'     => [
					'on'  => esc_html__( 'Yes', 'rtcl-elementor-builder' ),
					'off' => esc_html__( 'No', 'rtcl-elementor-builder' ),
				],
				'default'     => 'on',
				'description' => __( 'Show / Hide new Label.', 'rtcl-elementor-builder' ),
				'tab_slug'    => 'general',
				'toggle_slug' => 'general',
			],
			'rtcl_meta_display'        => [
				'label'       => esc_html__( 'Display', 'rtcl-elementor-builder' ),
				'type'        => 'select',
				'options'     => [
					'inline'  => esc_html__( 'Inline', 'rtcl-elementor-builder' ),
					'block' => esc_html__( 'Block', 'rtcl-elementor-builder' ),
				],
				'default'     => 'inline',
				'description' => __( 'Show / Hide new Label.', 'rtcl-elementor-builder' ),
				'tab_slug'    => 'general',
				'toggle_slug' => 'general',
			],
			
			'rtcl_icon_color'       => [
				'label'       => esc_html__( 'Icon Color', 'rtcl-elementor-builder' ),
				'description' => esc_html__( 'Here you can define a custom color for category name.', 'rtcl-elementor-builder' ),
				'type'        => 'color-alpha',
				'tab_slug'    => 'advanced',
				'toggle_slug' => 'meta',
				'hover'       => 'tabs',
			],
			'rtcl_meta_gap'       => [
				'label'       => esc_html__( 'Gap Between Meta Items', 'rtcl-elementor-builder' ),
				'description' => esc_html__( 'Here you can define a custom color for category name.', 'rtcl-elementor-builder' ),
				'type'           => 'range',
				'range_settings' => array(
					'step'      => 1,
					'min'       => 1,
					'max'       => 100,
					'min_limit' => 1,
					'max_limit' => 100,
				),
				'default'       => 10,
				'tab_slug'    => 'advanced',
				'toggle_slug' => 'meta',
				'hover'       => 'tabs',
			],
			'rtcl_meta_gap_icon'       => [
				'label'       => esc_html__( 'Gap Between Meta Icon with content', 'rtcl-elementor-builder' ),
				'description' => esc_html__( 'Here you can define a custom color for category name.', 'rtcl-elementor-builder' ),
				'type'           => 'range',
				'range_settings' => array(
					'step'      => 1,
					'min'       => 1,
					'max'       => 100,
					'min_limit' => 1,
					'max_limit' => 100,
				),
				'default'       => 5,
				'tab_slug'    => 'advanced',
				'toggle_slug' => 'meta',
				'hover'       => 'tabs',
			]
		];
		return $fields;
	}

	public function get_advanced_fields_config() {

		$advanced_fields                = [];
		$advanced_fields['text']        = [];
		$advanced_fields['text_shadow'] = [];

		$advanced_fields['fonts'] = [
			'meta'       => [
				'css'              => array(
					'main' => "%%order_class%%  .single-listing-meta-wrap .rtcl-listing-meta-data li"
				),
				'important'        => 'all',
				'tab_slug'         => 'advanced',
				'toggle_slug'      => 'meta',
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
				'main'      => "%%order_class%% .single-listing-meta-wrap .rtcl-listing-meta-data",
				'important' => 'all',
			],
			'tab_slug'    => 'advanced',
			'toggle_slug' => 'general',
		];

		return $advanced_fields;
	}

	public static function get_content( $settings ) {
		$template_style = 'divi/listing-meta/meta';
		$settings['rtcl_show_types'] = $settings['rtcl_show_types'] === 'on' ? true : false;
		$settings['rtcl_show_date'] = $settings['rtcl_show_date'] === 'on' ? true : false;
		$settings['rtcl_show_user'] = $settings['rtcl_show_user'] === 'on' ? true : false;
		$settings['rtcl_show_category'] = $settings['rtcl_show_category'] === 'on' ? true : false;
		$settings['rtcl_show_location'] = $settings['rtcl_show_location'] === 'on' ? true : false;
		$settings['rtcl_show_views'] = $settings['rtcl_show_views'] === 'on' ? true : false;
		$settings['rtcl_meta_display'] = $settings['rtcl_meta_display'] === 'inline' ? 'inline' : 'block';
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
		$wrapper = '%%order_class%% .el-single-addon.single-listing-meta-wrap';
		// ✅ Badge Styles (color + bg-color)
		$badge_styles = [
			[
				'class' => 'rtcl-listing-meta-data li i',
				'text_color' => 'rtcl_icon_color',
			],
			[
				'class' => 'rtcl-listing-meta-data',
				'gap' => 'rtcl_meta_gap',
			],
			[
				'class' => 'rtcl-listing-meta-data li',
				'icon_gap' => 'rtcl_meta_gap_icon',
			]
		];

		foreach ( $badge_styles as $badge ) {
			$selector 	= "$wrapper .{$badge['class']}";
			$text_color = $this->props[ $badge['text_color'] ?? '' ] ?? '';
			$bg_color   = $this->props[ $badge['bg_color'] ?? '' ] ?? '';
			$gap   		= $this->props[ $badge['gap'] ?? '' ] ?? '';
			$icon_gap   = $this->props[ $badge['icon_gap'] ?? '' ] ?? '';

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
			if ( ! empty( $gap ) ) {
				\ET_Builder_Element::set_style(
					$render_slug,
					[
						'selector'    => $selector,
						'declaration' => sprintf( 'gap: %1$s px !important;', $gap ),
					]
				);
			}
			if ( ! empty( $icon_gap ) ) {
				\ET_Builder_Element::set_style(
					$render_slug,
					[
						'selector'    => $selector,
						'declaration' => sprintf( 'display:inline-flex; gap: %1$spx !important;', $icon_gap ),
					]
				);
			}
		}
	}



}