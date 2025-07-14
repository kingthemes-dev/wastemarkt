<?php

namespace  RtclElb\DiviModule\ListingBadge;
use Rtcl\Helpers\Functions;
use Rtcl\Traits\Addons\ListingItem;
use RtclElb\Helpers\Fns;

Class ListingBadge extends \ET_Builder_Module {
	use ListingItem;
	public $slug = 'rtcl_listing_badge';
	public $vb_support = 'on';
	public $icon_path;
	protected $module_credits
		= [
			'author'     => 'RadiusTheme',
			'author_uri' => 'https://radiustheme.com',
		];
	public function init() {
		$this->name      = esc_html__( 'Listing Badge', 'rtcl-elementor-builder' );
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
					'title'       => esc_html__( 'New Badge', 'rtcl-elementor-builder' ),
					'top' => esc_html__( 'Top Badge', 'rtcl-elementor-builder' ),
					'feature' => esc_html__( 'Feature Badge', 'rtcl-elementor-builder' ),
					'popular' => esc_html__( 'Popular Badge', 'rtcl-elementor-builder' ),
					'bump_up' => esc_html__( 'Bump Up Badge', 'rtcl-elementor-builder' ),
				],
			],
		];
	}

	public function get_fields() {
		$fields =  [
			'rtcl_hide_new'        => [
				'label'       => esc_html__( 'Show New?', 'rtcl-elementor-builder' ),
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
			'rtcl_hide_featured'        => [
				'label'       => esc_html__( 'Show Featured', 'rtcl-elementor-builder' ),
				'type'        => 'yes_no_button',
				'options'     => [
					'on'  => esc_html__( 'Yes', 'rtcl-elementor-builder' ),
					'off' => esc_html__( 'No', 'rtcl-elementor-builder' ),
				],
				'default'     => 'on',
				'description' => __( 'Show / Hide Featured.', 'rtcl-elementor-builder' ),
				'tab_slug'    => 'general',
				'toggle_slug' => 'general',
			],
			// computed.
			'__listing_badge'           => array(
				'type'                => 'computed',
				'computed_callback'   => array('RtclElb\DiviModule\ListingBadge\ListingBadge', 'get_content' ),
				'computed_depends_on' => array(
					'rtcl_hide_new',
					'rtcl_hide_featured',
					'rtcl_hide_popular',
					'rtcl_hide_top',
					'rtcl_hide_bump_up',
				)
			),
			// visibility
			'rtcl_title_color'       => [
				'label'       => esc_html__( 'Text Color', 'rtcl-elementor-builder' ),
				'description' => esc_html__( 'Here you can define a custom color for category name.', 'rtcl-elementor-builder' ),
				'type'        => 'color-alpha',
				'tab_slug'    => 'advanced',
				'toggle_slug' => 'title',
				'hover'       => 'tabs',
			],
			'rtcl_title_bg_color'       => [
				'label'       => esc_html__( 'Bakground Color', 'rtcl-elementor-builder' ),
				'description' => esc_html__( 'Here you can define a custom color for category name.', 'rtcl-elementor-builder' ),
				'type'        => 'color-alpha',
				'tab_slug'    => 'advanced',
				'toggle_slug' => 'title',
				'hover'       => 'tabs',
			],
			'rtcl_top_color'       => [
				'label'       => esc_html__( 'Top Color', 'rtcl-elementor-builder' ),
				'description' => esc_html__( 'Here you can define a custom color for category name.', 'rtcl-elementor-builder' ),
				'type'        => 'color-alpha',
				'tab_slug'    => 'advanced',
				'toggle_slug' => 'top',
				'hover'       => 'tabs',
			],
			'rtcl_top_bg_color'       => [
				'label'       => esc_html__( 'Background Color', 'rtcl-elementor-builder' ),
				'description' => esc_html__( 'Here you can define a custom color for category name.', 'rtcl-elementor-builder' ),
				'type'        => 'color-alpha',
				'tab_slug'    => 'advanced',
				'toggle_slug' => 'top',
				'hover'       => 'tabs',
			],
			'rtcl_bump_up_color'       => [
				'label'       => esc_html__( 'Bump Up Color', 'rtcl-elementor-builder' ),
				'description' => esc_html__( 'Here you can define a custom color for category name.', 'rtcl-elementor-builder' ),
				'type'        => 'color-alpha',
				'tab_slug'    => 'advanced',
				'toggle_slug' => 'bump_up',
				'hover'       => 'tabs',
			],
			'rtcl_bump_up_bg_color'       => [
				'label'       => esc_html__( 'Background Color', 'rtcl-elementor-builder' ),
				'description' => esc_html__( 'Here you can define a custom color for category name.', 'rtcl-elementor-builder' ),
				'type'        => 'color-alpha',
				'tab_slug'    => 'advanced',
				'toggle_slug' => 'bump_up',
				'hover'       => 'tabs',
			],
			
			'rtcl_feature_color'       => [
				'label'       => esc_html__( 'Feature Color', 'rtcl-elementor-builder' ),
				'description' => esc_html__( 'Here you can define a custom color for category name.', 'rtcl-elementor-builder' ),
				'type'        => 'color-alpha',
				'tab_slug'    => 'advanced',
				'toggle_slug' => 'feature',
				'hover'       => 'tabs',
			],
			'rtcl_feature_bg_color'       => [
				'label'       => esc_html__( 'Background Color', 'rtcl-elementor-builder' ),
				'description' => esc_html__( 'Here you can define a custom color for category name.', 'rtcl-elementor-builder' ),
				'type'        => 'color-alpha',
				'tab_slug'    => 'advanced',
				'toggle_slug' => 'feature',
				'hover'       => 'tabs',
			],
			
			'rtcl_popular_color'        => [
				'label'       => esc_html__( 'Popular Color', 'rtcl-elementor-builder' ),
				'description' => esc_html__( 'Here you can define a custom color for category description.', 'rtcl-elementor-builder' ),
				'type'        => 'color-alpha',
				'tab_slug'    => 'advanced',
				'toggle_slug' => 'popular',
				'hover'       => 'tabs',
			],
			'rtcl_popular_bg_color'        => [
				'label'       => esc_html__( 'Background Color', 'rtcl-elementor-builder' ),
				'description' => esc_html__( 'Here you can define a custom color for category description.', 'rtcl-elementor-builder' ),
				'type'        => 'color-alpha',
				'tab_slug'    => 'advanced',
				'toggle_slug' => 'popular',
				'hover'       => 'tabs',
			],
		];
		if(rtcl()->has_pro()){
			$fields['rtcl_hide_popular'] = [
				'label'       => esc_html__( 'Show Popular?', 'rtcl-elementor-builder' ),
				'type'        => 'yes_no_button',
				'options'     => [
					'on'  => esc_html__( 'Yes', 'rtcl-elementor-builder' ),
					'off' => esc_html__( 'No', 'rtcl-elementor-builder' ),
				],
				'default'     => 'on',
				'description' => __( 'Show / Hide Popular.', 'rtcl-elementor-builder' ),
				'tab_slug'    => 'general',
				'toggle_slug' => 'general',
			];
			$fields['rtcl_hide_top'] = [
				'label'       => esc_html__( 'Show Top', 'rtcl-elementor-builder' ),
				'type'        => 'yes_no_button',
				'options'     => [
					'on'  => esc_html__( 'Yes', 'rtcl-elementor-builder' ),
					'off' => esc_html__( 'No', 'rtcl-elementor-builder' ),
				],
				'default'     => 'on',
				'description' => __( 'Show / Hide Top.', 'rtcl-elementor-builder' ),
				'tab_slug'    => 'general',
				'toggle_slug' => 'general',
			];
			$fields['rtcl_hide_bump_up'] = [
					'label'       => esc_html__( 'Show Bump Up', 'rtcl-elementor-builder' ),
				'type'        => 'yes_no_button',
				'options'     => [
					'on'  => esc_html__( 'Yes', 'rtcl-elementor-builder' ),
					'off' => esc_html__( 'No', 'rtcl-elementor-builder' ),
				],
				'default'     => 'on',
				'description' => __( 'Show / Hide Bump Up.', 'rtcl-elementor-builder' ),
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
		$template_style = 'divi/listing-badge/badge';
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
				'class' => 'rtcl-badge-new',
				'text_color' => 'rtcl_title_color',
				'bg_color'   => 'rtcl_title_bg_color',
			],
			[
				'class' => 'rtcl-badge-_top',
				'text_color' => 'rtcl_top_color',
				'bg_color'   => 'rtcl_top_bg_color',
			],
			[
				'class' => 'rtcl-badge-_bump_up',
				'text_color' => 'rtcl_bump_up_color',
				'bg_color'   => 'rtcl_bump_up_bg_color',
			],
			[
				'class' => 'rtcl-badge-feature',
				'text_color' => 'rtcl_feature_color',
				'bg_color'   => 'rtcl_feature_bg_color',
			],
			[
				'class' => 'rtcl-badge-popular',
				'text_color' => 'rtcl_popular_color',
				'bg_color'   => 'rtcl_popular_bg_color',
			],
		];

		foreach ( $badge_styles as $badge ) {
			$selector = "$wrapper .rtcl-listing-badge-wrap .{$badge['class']}";
			$text_color = $this->props[ $badge['text_color'] ] ?? '';
			$bg_color   = $this->props[ $badge['bg_color'] ] ?? '';

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