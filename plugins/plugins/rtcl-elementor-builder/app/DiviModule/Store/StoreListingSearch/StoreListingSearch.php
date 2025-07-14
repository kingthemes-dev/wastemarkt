<?php

namespace  RtclElb\DiviModule\Store\StoreListingSearch;
use Rtcl\Helpers\Functions;
use Rtcl\Traits\Addons\ListingItem;
use Rtcl\Widgets\AjaxFilter;
use RtclElb\Helpers\Fns;
use RtclStore\Widgets\Search;

Class StoreListingSearch extends \ET_Builder_Module {
	use ListingItem;
	public $slug = 'rtcl_listing_store_search';
	public $vb_support = 'on';
	public $icon_path;
	protected $module_credits
		= [
			'author'     => 'RadiusTheme',
			'author_uri' => 'https://radiustheme.com',
		];
	public function init() {
		$this->name      = esc_html__( 'Store Search', 'rtcl-elementor-builder' );
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
					'title'       => esc_html__( 'Description', 'rtcl-elementor-builder' ),
				],
			],
		];
	}

	public function get_fields() {
		$filters = Functions::get_option( 'rtcl_filter_settings' );
		$filters = ! empty( $filters ) && is_array( $filters ) ? array_map( function ( $filter ) {
			return $filter['name'];
		},
			$filters ) : [];
		return [
			'title'       => [
				'label'       => esc_html__( 'Search Title', 'rtcl-elementor-builder' ),
				'description' => esc_html__( 'Here you can define a custom color for category name.', 'rtcl-elementor-builder' ),
				'type'        => 'text',
				'default'     => esc_html__( 'Search Store', 'rtcl-elementor-builder' ),
				'tab_slug'       => 'general',
				'toggle_slug'    => 'general',
			],
			'style'       => [
				'label'          => esc_html__( 'Style', 'rtcl-elementor-builder' ),
				'type'        		=> 'select',
				'options'     		=> [
					'inline'  => esc_html__( 'Inline', 'rtcl-elementor-builder' ),
					'vertical' => esc_html__( 'Vertical', 'rtcl-elementor-builder' ),
				],
				'default'        => 'vertical',
				'description'    => esc_html__( 'Show or Hide Drop Cap. Default: off', 'rtcl-elementor-builder' ),
				'mobile_options' => true,
				'tab_slug'       => 'general',
				'toggle_slug'    => 'general',
			],
			'search_by_keyword'       => [
				'label'       => esc_html__( 'Search By', 'rtcl-elementor-builder' ),
				'description' => esc_html__( 'Here you can define a custom color for category name.', 'rtcl-elementor-builder' ),
				'type'        => 'yes_no_button',
				'options'     => [
					'on' => esc_html__( 'Yes', 'rtcl-elementor-builder' ),
					'off' => esc_html__( 'NO', 'rtcl-elementor-builder' ),
				],
				'default'     => 'on',
				'tab_slug'       => 'general',
				'toggle_slug'    => 'general',
			],
			'search_by_category'       => [
				'label'       => esc_html__( 'Search By', 'rtcl-elementor-builder' ),
				'description' => esc_html__( 'Here you can define a custom color for category name.', 'rtcl-elementor-builder' ),
				'type'        => 'yes_no_button',
				'options'     => [
					'on' => esc_html__( 'Yes', 'rtcl-elementor-builder' ),
					'off' => esc_html__( 'NO', 'rtcl-elementor-builder' ),
				],
				'default'     => 'on',
				'tab_slug'       => 'general',
				'toggle_slug'    => 'general',
			],
			'__get_listing_store_search'           => array(
				'type'                => 'computed',
				'computed_callback'   => array( 'RtclElb\DiviModule\Store\StoreListingSearch\StoreListingSearch', 'get_listing_filter_data' ),
				'computed_depends_on' => array(
					'search_by_category',
					'search_by_keyword',
					'style',
					'title',
				)
			),
		];
	}

	public function get_advanced_fields_config() {

		$advanced_fields                = [];
		$advanced_fields['text']        = [];
		$advanced_fields['text_shadow'] = [];

		$advanced_fields['fonts'] = [
			'title'       => [
				'css'              => array(
					'main' => '%%order_class%% .rtcl-listing-description p',
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
				'main'      => '%%order_class%% .rtcl-listing-description',
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


	public static function get_listing_filter_data( $settings ) {
		ob_start();
		$instance                = $settings;
		$data             = [
			'id'                          => wp_rand(),
			'style'                       => ! empty( $instance['style'] ) && $instance['style'] === 'inline' ? 'inline' : 'vertical',
			'can_search_by_keyword'      =>  $instance['search_by_keyword'] === 'on' ? 1 : 0,
			'can_search_by_category'      =>  $instance['search_by_category'] === 'on' ? 1 : 0,
			'selected_category'           => false,
		];
		$data['template'] = "widgets/search/{$data['style']}";
		$data             = apply_filters( 'rtcl_widget_store_search_values', $data, [], $instance, new Search() );
		echo '<div id="rtcl-widget-store-search" class="widget rtcl rtcl-widget-store-search">';

		if ( ! empty( $instance['title'] ) ) {
			echo '<h5>' .apply_filters( 'widget_title', $instance['title'] ) .'</h5>';
		}
		 Functions::get_template( $data['template'], $data, '', rtclStore()->get_plugin_template_path() );
		echo '</div>';
		$output = ob_get_clean();
		return $output;

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
		return self::get_listing_filter_data( $settings );
	}

	protected function render_css( $render_slug ) {
		$wrapper 		   = '%%order_class%%';
		$title_color       = $this->props['rtcl_title_color'];
		$title_hover_color = $this->get_hover_value('rtcl_title_color');
		$title_font_weight = explode( '|', $this->props['title_font'] )[1];
		// Title Color
		if (!empty($title_color)) {
			\ET_Builder_Element::set_style(
				$render_slug,
				[
					'selector'    => $wrapper."  .rtcl-listing-description p",
					'declaration' => sprintf('color: %1$s !important;', $title_color),
				]
			);
		}
		if ( ! empty( $title_hover_color ) ) {
			\ET_Builder_Element::set_style(
				$render_slug,
				[
					'selector'    => "$wrapper  .rtcl-listing-description p:hover",
					'declaration' => sprintf( 'color: %1$s;', $title_hover_color ),
				]
			);
		}
		if ( ! empty( $title_font_weight ) ) {
			\ET_Builder_Element::set_style(
				$render_slug,
				array(
					'selector'    => '.et-db .et-l %%order_class%% .rtcl-listing-description p',
					'declaration' => sprintf( 'font-weight: %1$s;', $title_font_weight ),
				)
			);
		}
	}
}