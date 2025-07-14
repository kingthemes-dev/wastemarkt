<?php

namespace  RtclElb\DiviModule\ListingImage;
use Rtcl\Helpers\Functions;
use Rtcl\Traits\Addons\ListingItem;
use RtclElb\Helpers\Fns;
use RtclPro\Controllers\ScriptController;

Class ListingImage extends \ET_Builder_Module {
	use ListingItem;
	public $slug = 'rtcl_listing_image';
	public $vb_support = 'on';
	public $icon_path;
	protected $module_credits
		= [
			'author'     => 'RadiusTheme',
			'author_uri' => 'https://radiustheme.com',
		];
	public function init() {
		$this->name      = esc_html__( 'Listing Image', 'rtcl-elementor-builder' );
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
					'zoom'       => esc_html__( 'Zoom', 'rtcl-elementor-builder' ),
					'arrow'       => esc_html__( 'Carousel Arrow', 'rtcl-elementor-builder' ),
				],
			],
		];
	}

	public function get_fields() {
		$fields =  [
			// computed.
			'__listing_image'           => array(
				'type'                => 'computed',
				'computed_callback'   => array('RtclElb\DiviModule\ListingImage\ListingImage', 'get_content' ),
				'computed_depends_on' => array(
					'rtcl_enable_feature_image',
					'rtcl_enable_gallery_image',
					'rtcl_enable_slider',
					'rtcl_enable_thumb_slider',
					'rtcl_show_video',
					'rtcl_show_arrow',
					'rtcl_show_lightbox_icon',
					'rtcl_enable_zoom',
					'rtcl_show_badge',
					'rtcl_thumb_image',
				)
			),
			// visibility
			'rtcl_enable_feature_image'        => [
				'label'       => __( 'Enable Feature Image', 'rtcl-elementor-builder' ),
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
			'rtcl_enable_gallery_image'        => [
				'label'       => esc_html__( 'Enable Gallery Image', 'rtcl-elementor-builder' ),
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
			'rtcl_enable_slider'        => [
				'label'       => esc_html__( 'Enable slider', 'rtcl-elementor-builder' ),
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
			'rtcl_enable_thumb_slider'        => [
				'label'       => esc_html__( 'Show Thumbnail', 'rtcl-elementor-builder' ),
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
			'rtcl_show_video'        => [
				'label'       => esc_html__( 'Show Video', 'rtcl-elementor-builder' ),
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
			'rtcl_show_arrow'        => [
				'label'       => esc_html__( 'Show Arrow', 'rtcl-elementor-builder' ),
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
			'rtcl_show_lightbox_icon'        => [
				'label'       => esc_html__( 'Show Lightbox Icon', 'rtcl-elementor-builder' ),
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
			'rtcl_enable_zoom'        => [
				'label'       => esc_html__( 'Enable Image Zoom', 'rtcl-elementor-builder' ),
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
			'rtcl_show_badge'        => [
				'label'       => esc_html__( 'Show Badge', 'rtcl-elementor-builder' ),
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
			'rtcl_thumb_image'        => [
				'label'       		=> esc_html__( 'Show Image Size', 'rtcl-elementor-builder' ),
				'type'             => 'select',
				'options'          => self::get_image_sizes_select(),
				'default'          => 'rtcl-thumbnail',
				'description' => __( 'Show / Hide new Label.', 'rtcl-elementor-builder' ),
				'tab_slug'    => 'general',
				'toggle_slug' => 'general',
			],
			
			'rtcl_zoom_icon_color'       => [
				'label'       => esc_html__( 'Icon Color', 'rtcl-elementor-builder' ),
				'description' => esc_html__( 'Here you can define a custom color for category name.', 'rtcl-elementor-builder' ),
				'type'        => 'color-alpha',
				'tab_slug'    => 'advanced',
				'toggle_slug' => 'zoom',
				'hover'       => 'tabs',
			],
            'rtcl_zoom_icon_bg_color'       => [
				'label'       => esc_html__( 'Icon Color', 'rtcl-elementor-builder' ),
				'description' => esc_html__( 'Here you can define a custom color for category name.', 'rtcl-elementor-builder' ),
				'type'        => 'color-alpha',
				'tab_slug'    => 'advanced',
				'toggle_slug' => 'zoom',
				'hover'       => 'tabs',
			],
			
			'rtcl_arrow_icon_color'       => [
				'label'       => esc_html__( 'Icon Color', 'rtcl-elementor-builder' ),
				'description' => esc_html__( 'Here you can define a custom color for category name.', 'rtcl-elementor-builder' ),
				'type'        => 'color-alpha',
				'tab_slug'    => 'advanced',
				'toggle_slug' => 'arrow',
				'hover'       => 'tabs',
			],
            'rtcl_arrow_icon_bg_color'       => [
				'label'       => esc_html__( 'Icon Color', 'rtcl-elementor-builder' ),
				'description' => esc_html__( 'Here you can define a custom color for category name.', 'rtcl-elementor-builder' ),
				'type'        => 'color-alpha',
				'tab_slug'    => 'advanced',
				'toggle_slug' => 'arrow',
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

	public static function get_image_sizes_select() {

		global $_wp_additional_image_sizes;

		$intermediate_image_sizes = get_intermediate_image_sizes();

		$image_sizes = array();
		foreach ( $intermediate_image_sizes as $size ) {
			if ( isset( $_wp_additional_image_sizes[ $size ] ) ) {
				$image_sizes[ $size ] = array(
					'width'  => $_wp_additional_image_sizes[ $size ]['width'],
					'height' => $_wp_additional_image_sizes[ $size ]['height']
				);
			} else {
				$image_sizes[ $size ] = array(
					'width'  => intval( get_option( "{$size}_size_w" ) ),
					'height' => intval( get_option( "{$size}_size_h" ) )
				);
			}
		}

		$sizes_arr = [];
		foreach ( $image_sizes as $key => $value ) {
			$sizes_arr[ $key ] = ucwords( strtolower( preg_replace( '/[-_]/', ' ', $key ) ) ) . " - {$value['width']} x {$value['height']}";
		}

		$sizes_arr['full'] = __( 'Full Size', 'rtcl-divi-addons' );

		return $sizes_arr;
	}
	public static function get_the_gallery($settings) {
		$data     = [
			'images' => [],
			'videos' => [],
		];
		
		if (!Functions::is_gallery_disabled()) {
			$listing = rtcl()->factory->get_listing(self::listing_id());
			$video_urls = [];
			if ($settings['rtcl_show_video'] && !Functions::is_video_urls_disabled() && !apply_filters('rtcl_disable_gallery_video', Functions::is_video_gallery_disabled())) {
				$video_urls = get_post_meta($listing->get_id(), '_rtcl_video_urls', true);
				$video_urls = !empty($video_urls) && is_array($video_urls) ? $video_urls : [];
			}
			$data['images'] = $listing->get_images();
			$data['videos'] = $video_urls;
		}
		return $data;
	}
	public static function get_content( $settings ) {
		
		if ( class_exists('RtclPro') && $settings['rtcl_show_lightbox_icon'] ) {
			wp_enqueue_style( 'photoswipe-default-skin' );
			add_action( 'wp_footer', [ ScriptController::class, 'photoswipe_placeholder' ] );
		}
		$settings['rtcl_enable_feature_image'] = $settings['rtcl_enable_feature_image'] === 'on' ? true : false;
		$settings['rtcl_enable_gallery_image'] = $settings['rtcl_enable_gallery_image'] === 'on' ? true : false;
		$settings['rtcl_enable_slider']       = $settings['rtcl_enable_slider'] === 'on' ? true : false;
		$settings['rtcl_enable_thumb_slider'] = $settings['rtcl_enable_thumb_slider'] === 'on' ? true : false;
		$settings['rtcl_show_video']          = $settings['rtcl_show_video'] === 'on' ? true : false;
		$settings['rtcl_show_lightbox_icon'] = $settings['rtcl_show_lightbox_icon'] === 'on' ? true : false;
		$settings['rtcl_thumb_image']        = $settings['rtcl_thumb_image'] === 'on' ? true : false;
		$settings['rtcl_enable_zoom']        = $settings['rtcl_enable_zoom'] === 'on' ? true : false;
		$settings['rtcl_show_badge']         = $settings['rtcl_show_badge'] === 'on' ? true : false;
		$settings['rtcl_show_arrow']         = $settings['rtcl_show_arrow'] === 'on' ? true : false;
		
		$template_style = 'divi/listing-image/image';
		$data = [
			'template'      => $template_style,
			'settings'      => $settings,
			'instance'      => $settings,
			'listing'       => rtcl()->factory->get_listing(self::listing_id()),
			'template_path' => Fns::get_plugin_template_path(),
		];
		$data           = array_merge(
			$data,
			self::get_the_gallery($settings)
		);
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