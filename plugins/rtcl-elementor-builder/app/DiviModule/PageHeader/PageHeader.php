<?php

namespace  RtclElb\DiviModule\PageHeader;
use Rtcl\Helpers\Functions;
use RtclElb\Helpers\Fns;

Class PageHeader extends \ET_Builder_Module {

	public $slug = 'rtcl_page_header';
	public $vb_support = 'on';
	public $icon_path;
	protected $module_credits
		= [
			'author'     => 'RadiusTheme',
			'author_uri' => 'https://radiustheme.com',
		];
	public function init() {
		$this->name      = esc_html__( ' Classified Page Header', 'rtcl-elementor-builder' );
		$this->icon_path = plugin_dir_path( __FILE__ ) . 'icon.svg';
		$this->folder_name = 'et_pb_classified_single_page_modules';
		$this->folder_name = 'et_pb_classified_Archive_modules';
		$this->settings_modal_toggles = [
			'general'  => [
				'toggles' => [
					'general'    => esc_html__( 'General', 'rtcl-elementor-builder' ),
					'visibility' => esc_html__( 'Visibility', 'rtcl-elementor-builder' ),
				],
			],
			'advanced' => [
				'toggles' => [
					'title'       => esc_html__( 'Title', 'rtcl-elementor-builder' ),
					'breadcrumb' => esc_html__( 'Breadcrumb', 'rtcl-elementor-builder' ),
					'link_color' => esc_html__( 'Breadcrumb Link', 'rtcl-elementor-builder' ),
				],
			],
		];
	}

	public function get_fields() {
		return [
			'rtcl_show_page_title'        => [
				'label'       => esc_html__( 'Show Title', 'rtcl-elementor-builder' ),
				'type'        => 'yes_no_button',
				'options'     => [
					'on'  => esc_html__( 'Yes', 'rtcl-elementor-builder' ),
					'off' => esc_html__( 'No', 'rtcl-elementor-builder' ),
				],
				'default'     => 'on',
				'description' => __( 'Show / Hide Titles.', 'rtcl-elementor-builder' ),
				'tab_slug'    => 'general',
				'toggle_slug' => 'visibility',
			],
			'rtcl_breadcrumb_position'       => [
				'label'          => esc_html__( 'Breadcrumb Position', 'rtcl-elementor-builder' ),
				'type'           => 'select',
				'options'        => [
					'style-1' => __( 'Bottom', 'rtcl-elementor-builder' ),
					'style-2' => __( 'Top', 'rtcl-elementor-builder' ),
					'style-3' => __( 'Right', 'rtcl-elementor-builder' ),
				],
				'default'        => 'style-1',
				'description'    => esc_html__( 'Select Breadcrumb Position to display Breadcrumb.', 'rtcl-elementor-builder' ),
				'mobile_options' => true,
				'tab_slug'       => 'general',
				'toggle_slug'    => 'general',
			],
			'header_size'       => [
				'type'           => 'select',
				'label'     => __( 'Title HTML Tag', 'rtcl-elementor-builder' ),
				'options'   => [
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
				'default'        => 'h1',
				'description'    => esc_html__( 'Select Breadcrumb Position to display Breadcrumb.', 'rtcl-elementor-builder' ),
				'mobile_options' => true,
				'tab_slug'       => 'general',
				'toggle_slug'    => 'general',
			],
			// computed.
			'__get_hedaer'           => array(
				'type'                => 'computed',
				'computed_callback'   => array( PageHeader::class, 'get_content' ),
				'computed_depends_on' => array(
					'rtcl_show_page_title',
					'rtcl_breadcrumb_position',
					'header_size',
					'rtcl_show_breadcrumb'
				)
			),
			// visibility
			'rtcl_show_breadcrumb'        => [
				'label'       => esc_html__( 'Show Breadcrumb', 'rtcl-elementor-builder' ),
				'type'        => 'yes_no_button',
				'options'     => [
					'on'  => esc_html__( 'Yes', 'rtcl-elementor-builder' ),
					'off' => esc_html__( 'No', 'rtcl-elementor-builder' ),
				],
				'default'     => 'on',
				'description' => __( 'Show / Hide listing image or icon.', 'rtcl-elementor-builder' ),
				'tab_slug'    => 'general',
				'toggle_slug' => 'visibility',
			],
			'rtcl_title_color'       => [
				'label'       => esc_html__( 'Name Color', 'rtcl-elementor-builder' ),
				'description' => esc_html__( 'Here you can define a custom color for category name.', 'rtcl-elementor-builder' ),
				'type'        => 'color-alpha',
				'tab_slug'    => 'advanced',
				'toggle_slug' => 'title',
				'hover'       => 'tabs',
			],
			'rtcl_desc_color'        => [
				'label'       => esc_html__( 'Description Color', 'rtcl-elementor-builder' ),
				'description' => esc_html__( 'Here you can define a custom color for category description.', 'rtcl-elementor-builder' ),
				'type'        => 'color-alpha',
				'tab_slug'    => 'advanced',
				'toggle_slug' => 'description'
			],
		];
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
			'breadcrumb' => [
				'css'              => array(
					'main' => '%%order_class%% .rtcl-breadcrumb',
				),
				'important'        => 'all',
				'tab_slug'         => 'advanced',
				'toggle_slug'      => 'breadcrumb',
				'line_height'      => array(
					'range_settings' => array(
						'min'  => '1',
						'max'  => '3',
						'step' => '.1',
					),
					'default'        => '1.6em',
				),
				'font_size'        => array(
					'default' => '16px',
				),
				'font'             => [
					'default' => '|400|||||||',
				],
			],
			'link_color' => [
				'css'              => array(
					'main' => '%%order_class%% .rtcl-breadcrumb a',
				),
				'important'        => 'all',
				'tab_slug'         => 'advanced',
				'toggle_slug'      => 'link_color',
				'line_height'      => array(
					'range_settings' => array(
						'min'  => '1',
						'max'  => '3',
						'step' => '.1',
					),
					'default'        => '1.6em',
				),
				'font_size'        => array(
					'default' => '16px',
				),
				'font'             => [
					'default' => '|400|||||||',
				],
			]
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

	public static function get_content( $settings ) {

		$template_style = 'divi/page-header/listing-page-header';
		$data = [
			'template'      => $template_style,
			'settings'      => $settings,
			'template_path' => Fns::get_plugin_template_path(),
		];

		return Functions::get_template_html( $data['template'], $data, '', $data['template_path'] );
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
		$wrapper 		   = '%%order_class%% .el-single-addon';
		$title_color       = $this->props['rtcl_title_color'];
		$title_hover_color = $this->get_hover_value('rtcl_title_color');
		$description_color = $this->props['rtcl_desc_color'];
		$title_font_weight = explode( '|', $this->props['title_font'] )[1];
		// Title Color
		if (!empty($title_color)) {
			\ET_Builder_Element::set_style(
				$render_slug,
				[
					'selector'    => $wrapper." .rtcl-listing-header .rtcl-listings-header-title",
					'declaration' => sprintf('color: %1$s !important;', $title_color),
				]
			);
		}
		if ( ! empty( $title_hover_color ) ) {
			\ET_Builder_Element::set_style(
				$render_slug,
				[
					'selector'    => "$wrapper .rtcl-listing-header span.rtcl-listings-header-title:hover",
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
		// Description Color (Breadcrumb)
		if (!empty($description_color)) {
			\ET_Builder_Element::set_style(
				$render_slug,
				[
					'selector'    => "$wrapper .breadcrumb-section .rtcl-breadcrumb",
					'declaration' => sprintf('color: %1$s !important;', $description_color),
				]
			);
		}
	}


}