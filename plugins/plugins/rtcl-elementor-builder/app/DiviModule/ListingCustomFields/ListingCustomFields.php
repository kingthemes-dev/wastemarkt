<?php

namespace  RtclElb\DiviModule\ListingCustomFields;
use Rtcl\Helpers\Functions;
use Rtcl\Traits\Addons\ListingItem;
use RtclElb\Helpers\Fns;

Class ListingCustomFields extends \ET_Builder_Module {
	use ListingItem;
	public $slug = 'rtcl_listing_custom_fields';
	public $vb_support = 'on';
	public $icon_path;
	protected $module_credits
		= [
			'author'     => 'RadiusTheme',
			'author_uri' => 'https://radiustheme.com',
		];
	public function init() {
		$this->name      = esc_html__( 'Listing Custom Fields', 'rtcl-elementor-builder' );
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
//					'item'       => esc_html__( 'Item', 'rtcl-elementor-builder' ),
					'label'       => esc_html__( 'Label', 'rtcl-elementor-builder' ),
					'data'       => esc_html__( 'Data', 'rtcl-elementor-builder' ),
				],
			],
		];
	}

	public function get_fields() {
		$fields =  [
			// computed.
			'__listing_custom_fields'           => array(
				'type'                => 'computed',
				'computed_callback'   => array('RtclElb\DiviModule\ListingCustomFields\ListingCustomFields', 'get_content' ),
				'computed_depends_on' => array(
					'rtcl_dispaly_style',
					'custom_field_group_list',
					'rtcl_show_new_line',
					'rtcl_show_list_item_separator',
					'list-item-column-gap',
					'list-item-column-gap-separator',
					'list-item-row-gap',
				)
			),
			// visibility
			'rtcl_dispaly_style'        => [
				'label'       => esc_html__( 'Show Types', 'rtcl-elementor-builder' ),
				'type'        => 'select',
				'options' => [
					'style-1' => __( 'Style 1', 'rtcl-elementor-builder' ),
					'style-2' => __( 'Style 2', 'rtcl-elementor-builder' ),
				],
				'default' => 'style-1',
				'description' => __( 'Show / Hide new Label.', 'rtcl-elementor-builder' ),
				'tab_slug'    => 'general',
				'toggle_slug' => 'general',
			],
			'rtcl_show_new_line'        => [
				'label'       => esc_html__( 'Label and Value in New Line', 'rtcl-elementor-builder' ),
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
			'rtcl_show_list_item_separator'        => [
				'label'       => esc_html__( 'List item separator?', 'rtcl-elementor-builder' ),
				'type'        => 'yes_no_button',
				'options'     => [
					'on'  => esc_html__( 'Yes', 'rtcl-elementor-builder' ),
					'off' => esc_html__( 'No', 'rtcl-elementor-builder' ),
				],
				'default'     => 'on',
				'description' => __( 'Show / Hide new Label.', 'rtcl-elementor-builder' ),
				'tab_slug'    => 'advanced',
				'toggle_slug' => 'item',
				'hover'       => 'tabs',
			],
			'list-item-column-gap'       => [
				'label'       => esc_html__( 'List Item Column Gap', 'rtcl-elementor-builder' ),
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
				'toggle_slug' => 'item',
				'hover'       => 'tabs',
				'show_if'     => [
					'rtcl_show_list_item_separator' => 'off',
				],
			],
			'list-item-column-gap-separator'       => [
				'label'       => esc_html__( 'List Item Column Gap', 'rtcl-elementor-builder' ),
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
				'toggle_slug' => 'item',
				'hover'       => 'tabs',
				'show_if'     => [
					'rtcl_show_list_item_separator' => 'on',
				],
			],
			'column-gap'       => [
				'label'       => esc_html__( 'Column Gap', 'rtcl-elementor-builder' ),
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
				'toggle_slug' => 'item',
				'hover'       => 'tabs',
			],
			'list-item-row-gap'       => [
				'label'       => esc_html__( 'List Item Row Gap', 'rtcl-elementor-builder' ),
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
				'toggle_slug' => 'item',
				'hover'       => 'tabs',
			]
		];
		
		if(!Functions::isEnableFb()){
			$fields['custom_field_group_list']= [
				'label'       => esc_html__( 'Custom Field Group\'s', 'rtcl-divi-addons' ),
				'type'        => 'multiple_checkboxes',
				'options'     => ListingCustomFieldsHelper::custom_field_group_list(),
				'tab_slug'    => 'general',
				'toggle_slug' => 'general',
			];
		}
		
		return $fields;
	}

	public function get_advanced_fields_config() {

		$advanced_fields                = [];
		$advanced_fields['text']        = [];
		$advanced_fields['text_shadow'] = [];

		$advanced_fields['fonts'] = [
			'label'       => [
				'css'              => array(
					'main' => "%%order_class%%  .el-single-addon .cfp-label"
				),
				'important'        => 'all',
				'tab_slug'         => 'advanced',
				'toggle_slug'      => 'label',
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
			'data'       => [
				'css'              => array(
					'main' => "%%order_class%%  .el-single-addon .cfp-value"
				),
				'important'        => 'all',
				'tab_slug'         => 'advanced',
				'toggle_slug'      => 'data',
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
		$template_style = 'divi/listing-custom-fields/custom-fields';
		$settings['rtcl_dispaly_style'] = $settings['rtcl_dispaly_style'] === 'on' ? true : false;
		$settings['rtcl_show_new_line'] = $settings['rtcl_show_new_line'] === 'on' ? 'label-new-line' : '';
		$settings['rtcl_show_list_item_separator'] = $settings['rtcl_show_list_item_separator'] === 'on' ? 'item-separator' : false;
		add_filter( 'rtcl_listing_get_custom_field_group_ids', [ ListingCustomFieldsHelper::class, 'custom_field_group_ids' ], 10, 1 );
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
		$wrapper = '%%order_class%% .el-single-addon';
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