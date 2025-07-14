<?php

namespace  RtclElb\DiviModule\ListingPrice;
use Rtcl\Helpers\Functions;
use Rtcl\Traits\Addons\ListingItem;
use RtclElb\Helpers\Fns;

Class ListingPrice extends \ET_Builder_Module {
	use ListingItem;
	public $slug = 'rtcl_listing_price';
	public $vb_support = 'on';
	public $icon_path;
	protected $module_credits
		= [
			'author'     => 'RadiusTheme',
			'author_uri' => 'https://radiustheme.com',
		];
	public function init() {
		$this->name      = esc_html__( 'Listing Price', 'rtcl-elementor-builder' );
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
					'price'       => esc_html__( 'Price', 'rtcl-elementor-builder' ),
					'divider'       => esc_html__( 'Divider After Price', 'rtcl-elementor-builder' ),
					'unit' => esc_html__( 'Price Unit', 'rtcl-elementor-builder' ),
					'price_type' => esc_html__( 'Price Type', 'rtcl-elementor-builder' ),
				],
			],
		];
	}

	public function get_fields() {
		$fields =  [
			'rtcl_price_style'       => [
				'label'          =>  __('Select Price Style', 'rtcl-elementor-builder'),
				'type'           => 'select',
				'options'        => [
					'style-1' => __( 'Style 1', 'rtcl-elementor-builder' ),
					'style-2' => __( 'Style 2', 'rtcl-elementor-builder' ),
				],
				'default'        => 'style-1',
				'description'    => esc_html__( 'Select list style.', 'rtcl-elementor-builder' ),
				'mobile_options' => true,
				'tab_slug'       => 'general',
				'toggle_slug'    => 'general',
			],
			'rtcl_divider_price_after' => [
				'label'      		=> esc_html__('Divider After Price', 'rtcl-elementor-builder'),
				'type'            	=> 'text',
				'option_category' 	=> 'basic_option',
				'default'         	=> '/',
				'tab_slug'    		=> 'general',
				'toggle_slug' 		=> 'general',
			],
			'rtcl_show_price_unit'        => [
				'label'       => esc_html__('Show Price Unit', 'rtcl-elementor-builder'),
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
			'rtcl_show_price_type'        => [
				'label'       => __('Show Price Type', 'rtcl-elementor-builder'),
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
			// computed.
			'__listing_price'           => array(
				'type'                => 'computed',
				'computed_callback'   => array('RtclElb\DiviModule\ListingPrice\ListingPrice', 'get_content' ),
				'computed_depends_on' => array(
					'rtcl_show_price_type',
					'rtcl_show_price_unit',
					'rtcl_divider_price_after',
					'rtcl_price_style',
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
			],
			'rtcl_price_bg_color'       => [
				'label'       => esc_html__( 'Price Background Color', 'rtcl-elementor-builder' ),
				'description' => esc_html__( 'Here you can define a custom color for category name.', 'rtcl-elementor-builder' ),
				'type'        => 'color-alpha',
				'tab_slug'    => 'advanced',
				'toggle_slug' => 'price',
				'hover'       => 'tabs',
				'show_if'     => [
					'rtcl_price_style' => 'style-2',
				],
			],
			'rtcl_divider_color'       => [
				'label'       => esc_html__( 'Text Color', 'rtcl-elementor-builder' ),
				'description' => esc_html__( 'Here you can define a custom color for category name.', 'rtcl-elementor-builder' ),
				'type'        => 'color-alpha',
				'tab_slug'    => 'advanced',
				'toggle_slug' => 'divider',
				'hover'       => 'tabs',
			],
			'rtcl_unit_color'       => [
				'label'       => esc_html__( 'Text Color', 'rtcl-elementor-builder' ),
				'description' => esc_html__( 'Here you can define a custom color for category name.', 'rtcl-elementor-builder' ),
				'type'        => 'color-alpha',
				'tab_slug'    => 'advanced',
				'toggle_slug' => 'unit',
				'hover'       => 'tabs',
			],
			'rtcl_price_type_color'       => [
				'label'       => esc_html__( 'Text Color', 'rtcl-elementor-builder' ),
				'description' => esc_html__( 'Here you can define a custom color for category name.', 'rtcl-elementor-builder' ),
				'type'        => 'color-alpha',
				'tab_slug'    => 'advanced',
				'toggle_slug' => 'price_type',
				'hover'       => 'tabs',
			],
		];
		return $fields;
	}

	public function get_advanced_fields_config() {

		$advanced_fields                = [];
		$advanced_fields['text']        = [];
		$advanced_fields['text_shadow'] = [];

		$advanced_fields['fonts'] = [
			'price'       => [
				'css'              => array(
					'main' => '%%order_class%% .el-single-addon.item-price .rtcl-price',
				),
				'important'        => 'all',
				'hide_text_color'  => true,
				'hide_text_shadow' => true,
				'hide_text_align'  => true,
				'tab_slug'         => 'advanced',
				'toggle_slug'      => 'price',
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
			'divider'       => [
				'css'              => array(
					'main' => '%%order_class%% .el-single-addon.item-price .divider-after-price',
				),
				'important'        => 'all',
				'hide_text_color'  => true,
				'hide_text_shadow' => true,
				'hide_text_align'  => true,
				'tab_slug'         => 'advanced',
				'toggle_slug'      => 'divider',
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
			'unit'       => [
				'css'              => array(
					'main' => '%%order_class%% .el-single-addon.item-price .price-unit',
				),
				'important'        => 'all',
				'hide_text_color'  => true,
				'hide_text_shadow' => true,
				'hide_text_align'  => true,
				'tab_slug'         => 'advanced',
				'toggle_slug'      => 'unit',
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
			'price_type'       => [
				'css'              => array(
					'main' => '%%order_class%% .el-single-addon.item-price .price-type',
				),
				'important'        => 'all',
				'hide_text_color'  => true,
				'hide_text_shadow' => true,
				'hide_text_align'  => true,
				'tab_slug'         => 'advanced',
				'toggle_slug'      => 'price_type',
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
		

		return $advanced_fields;
	}

	public static function get_content( $settings ) {
		if ( $settings['rtcl_show_price_type'] ) {
			add_filter( 'rtcl_price_meta_html', function () use ( $settings ) {
				$listing = rtcl()->factory->get_listing(self::listing_id());
				$price_units_type = '';
				if ( $settings['rtcl_divider_price_after'] ) {
					$price_units_type = '<span class="divider-after-price">' . $settings['rtcl_divider_price_after'] . '</span>';
				}
				if ( $settings['rtcl_show_price_unit'] && $settings['rtcl_show_price_unit'] === 'on' ) {
					$price_units_type .= '<span class="price-unit">' . $listing->get_price_unit() . '</span>';
				}
				if ( $settings['rtcl_show_price_type'] && $settings['rtcl_show_price_type'] === 'on' ) {
					$price_units_type .= '<span class="price-type">'.$listing->get_price_type().'</span>';
				}
				return $price_units_type;
			}, 20 );
		}
		$template_style = 'divi/listing-price/price';
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