<?php

namespace  RtclElb\DiviModule\ListingRelated;
use Rtcl\Controllers\Hooks\AppliedBothEndHooks;
use Rtcl\Helpers\Functions;
use Rtcl\Traits\Addons\ListingItem;
use RtclElb\DiviModule\ListingSellerInformation\ListingSellerInformationHelper;
use RtclElb\Helpers\Fns;

Class ListingRelated extends \ET_Builder_Module {
	use ListingItem;
	public $slug = 'rtcl_listing_related';
	public $vb_support = 'on';
	public $icon_path;
	protected $module_credits
		= [
			'author'     => 'RadiusTheme',
			'author_uri' => 'https://radiustheme.com',
		];
	public function init() {
		$this->name      = esc_html__( 'Related Listing', 'rtcl-elementor-builder' );
		$this->icon_path = plugin_dir_path( __FILE__ ) . 'icon.svg';
		$this->folder_name = 'et_pb_classified_single_page_modules';
		$this->settings_modal_toggles = [
			'general'  => [
				'toggles' => [
					'general'    => esc_html__( 'General', 'rtcl-elementor-builder' ),
					'visibility'    => esc_html__( 'Visibility', 'rtcl-elementor-builder' ),
					'slider'    => esc_html__( 'Slider Option', 'rtcl-elementor-builder' ),
				],
			],
			'advanced' => [
				'toggles' => [
					'title' => esc_html__( 'Title', 'rtcl-divi-addons' ),
					'price' => esc_html__( 'Price', 'rtcl-divi-addons' ),
					'meta'  => esc_html__( 'Meta', 'rtcl-divi-addons' ),
				],
			],
		];
	}

	public function get_fields() {
		$fields =  [
			'rtcl_listings_per_page'       => [
				'label'          =>  __('Listing Per Page', 'rtcl-elementor-builder'),
				'type'        => 'number',
				'default'        => '10',
				'mobile_options' => true,
				'tab_slug'       => 'general',
				'toggle_slug'    => 'general',
			],
			'rtcl_listings_filter' => [
				'label'      		=> esc_html__('Listing Criteria', 'rtcl-elementor-builder'),
				'type'           => 'select',
				'options'     => [
					'category'     => esc_html__( 'Same Category', 'rtcl-elementor-builder' ),
					'location'     => esc_html__( 'Same Location', 'rtcl-elementor-builder' ),
					'listing_type' => esc_html__( 'Same Type', 'rtcl-elementor-builder' ),
					'author'       => esc_html__( 'Same Author', 'rtcl-elementor-builder' ),
				],
				'option_category' 	=> 'basic_option',
				'default'         	=> 'category',
				'tab_slug'    		=> 'general',
				'toggle_slug' 		=> 'general',
			],
			'rtcl_listings_grid_style'        => [
				'label'       => esc_html__('Style', 'rtcl-elementor-builder'),
				'type'        => 'select',
				'options'     => [
					'style-1' => esc_html__( 'Style 1', 'rtcl-elementor-builder' ),
					'style-2' => esc_html__( 'Style 2', 'rtcl-elementor-builder' ),
					'style-3' => esc_html__( 'Style 3', 'rtcl-elementor-builder' ),
					'style-4' => esc_html__( 'Style 4', 'rtcl-elementor-builder' ),
					'style-5' => esc_html__( 'Style 5', 'rtcl-elementor-builder' ),
				],
				'default'     => 'style-1',
				'description' => __( 'Show / Hide new badge.', 'rtcl-elementor-builder' ),
				'tab_slug'    => 'general',
				'toggle_slug' => 'general',
			],
			'rtcl_listings_column'        => [
				'label'       => esc_html__('Column', 'rtcl-elementor-builder'),
				'type'        => 'select',
				'options'     => [
					'1' => esc_html__( 'Column 1', 'rtcl-elementor-builder' ),
					'2' => esc_html__( 'Column 2', 'rtcl-elementor-builder' ),
					'3' => esc_html__( 'Column 3', 'rtcl-elementor-builder' ),
					'4' => esc_html__( 'Column 4', 'rtcl-elementor-builder' ),
					'5' => esc_html__( 'Column 5', 'rtcl-elementor-builder' ),
					'6' => esc_html__( 'Column 6', 'rtcl-elementor-builder' ),
					'7' => esc_html__( 'Column 7', 'rtcl-elementor-builder' ),
					'8' => esc_html__( 'Column 8', 'rtcl-elementor-builder' ),
				],
				'default'     => '3',
				'description' => __( 'Show / Hide new badge.', 'rtcl-elementor-builder' ),
				'tab_slug'    => 'general',
				'toggle_slug' => 'general',
			],
			'rtcl_thumb_image_size'          => [
				'label'            => esc_html__( 'Image Resolution', 'rtcl-elementor-builder' ),
				'type'             => 'select',
				'options'          => Fns::get_image_sizes_select(),
				'default'          => 'rtcl-thumbnail',
				'computed_affects' => [
					'__html',
				],
				'tab_slug'         => 'general',
				'toggle_slug'      => 'general',
			],
			'rtcl_enable_slider'        => [
				'label'       => __('Enable Slider', 'rtcl-elementor-builder'),
				'type'        => 'yes_no_button',
				'options'     => [
					'on'  => esc_html__( 'Yes', 'rtcl-elementor-builder' ),
					'off' => esc_html__( 'No', 'rtcl-elementor-builder' ),
				],
				'default'     => 'on',
				'tab_slug'    => 'general',
				'toggle_slug' => 'general',
			],
			
			//visibility
			'rtcl_show_image'        => [
				'label'       => __('Show Image', 'rtcl-elementor-builder'),
				'type'        => 'yes_no_button',
				'options'     => [
					'on'  => esc_html__( 'Yes', 'rtcl-elementor-builder' ),
					'off' => esc_html__( 'No', 'rtcl-elementor-builder' ),
				],
				'default'     => 'on',
				'description' => __( 'Show / Hide new badge.', 'rtcl-elementor-builder' ),
				'tab_slug'    => 'general',
				'toggle_slug' => 'visibility',
			],
			'rtcl_show_title'        		=> [
				'label'       => __('Show Title', 'rtcl-elementor-builder'),
				'type'        => 'yes_no_button',
				'options'     => [
					'on'  => esc_html__( 'Yes', 'rtcl-elementor-builder' ),
					'off' => esc_html__( 'No', 'rtcl-elementor-builder' ),
				],
				'default'     => 'on',
				'description' => __( 'Show / Hide new badge.', 'rtcl-elementor-builder' ),
				'tab_slug'    => 'general',
				'toggle_slug' => 'visibility',
			],
			'rtcl_show_description'        	=> [
				'label'       => __('Show Description', 'rtcl-elementor-builder'),
				'type'        => 'yes_no_button',
				'options'     => [
					'on'  => esc_html__( 'Yes', 'rtcl-elementor-builder' ),
					'off' => esc_html__( 'No', 'rtcl-elementor-builder' ),
				],
				'default'     => 'off',
				'description' => __( 'Show / Hide new badge.', 'rtcl-elementor-builder' ),
				'tab_slug'    => 'general',
				'toggle_slug' => 'visibility',
			],
			'rtcl_content_limit'        	=> [
				'label'       => __('Show Description', 'rtcl-elementor-builder'),
				'type'        => 'text',
				'default'     => '20',
				'description' => __( 'Show / Hide new badge.', 'rtcl-elementor-builder' ),
				'tab_slug'    => 'general',
				'toggle_slug' => 'visibility',
			],
			'rtcl_show_labels'        		=> [
				'label'       => __('Show Label', 'rtcl-elementor-builder'),
				'type'        => 'yes_no_button',
				'options'     => [
					'on'  => esc_html__( 'Yes', 'rtcl-elementor-builder' ),
					'off' => esc_html__( 'No', 'rtcl-elementor-builder' ),
				],
				'default'     => 'on',
				'description' => __( 'Show / Hide new badge.', 'rtcl-elementor-builder' ),
				'tab_slug'    => 'general',
				'toggle_slug' => 'visibility',
			],
			'rtcl_show_details_button'      => [
				'label'       => __('Show Details', 'rtcl-elementor-builder'),
				'type'        => 'yes_no_button',
				'options'     => [
					'on'  => esc_html__( 'Yes', 'rtcl-elementor-builder' ),
					'off' => esc_html__( 'No', 'rtcl-elementor-builder' ),
				],
				'default'     => 'on',
				'description' => __( 'Show / Hide new badge.', 'rtcl-elementor-builder' ),
				'tab_slug'    => 'general',
				'toggle_slug' => 'visibility',
			],
			'rtcl_show_date'        		=> [
				'label'       => __('Show Date', 'rtcl-elementor-builder'),
				'type'        => 'yes_no_button',
				'options'     => [
					'on'  => esc_html__( 'Yes', 'rtcl-elementor-builder' ),
					'off' => esc_html__( 'No', 'rtcl-elementor-builder' ),
				],
				'default'     => 'on',
				'description' => __( 'Show / Hide new badge.', 'rtcl-elementor-builder' ),
				'tab_slug'    => 'general',
				'toggle_slug' => 'visibility',
			],
			'rtcl_show_location'        	=> [
				'label'       => __('Show Location', 'rtcl-elementor-builder'),
				'type'        => 'yes_no_button',
				'options'     => [
					'on'  => esc_html__( 'Yes', 'rtcl-elementor-builder' ),
					'off' => esc_html__( 'No', 'rtcl-elementor-builder' ),
				],
				'default'     => 'on',
				'description' => __( 'Show / Hide new badge.', 'rtcl-elementor-builder' ),
				'tab_slug'    => 'general',
				'toggle_slug' => 'visibility',
			],
			'rtcl_show_category'        	=> [
				'label'       => __('Show Category', 'rtcl-elementor-builder'),
				'type'        => 'yes_no_button',
				'options'     => [
					'on'  => esc_html__( 'Yes', 'rtcl-elementor-builder' ),
					'off' => esc_html__( 'No', 'rtcl-elementor-builder' ),
				],
				'default'     => 'on',
				'description' => __( 'Show / Hide new badge.', 'rtcl-elementor-builder' ),
				'tab_slug'    => 'general',
				'toggle_slug' => 'visibility',
			],
			'rtcl_show_price'       		=> [
				'label'       => __('Show price', 'rtcl-elementor-builder'),
				'type'        => 'yes_no_button',
				'options'     => [
					'on'  => esc_html__( 'Yes', 'rtcl-elementor-builder' ),
					'off' => esc_html__( 'No', 'rtcl-elementor-builder' ),
				],
				'default'     => 'on',
				'description' => __( 'Show / Hide new badge.', 'rtcl-elementor-builder' ),
				'tab_slug'    => 'general',
				'toggle_slug' => 'visibility',
			],
			'rtcl_show_price_unit'        	=> [
				'label'       => __('Show price unit', 'rtcl-elementor-builder'),
				'type'        => 'yes_no_button',
				'options'     => [
					'on'  => esc_html__( 'Yes', 'rtcl-elementor-builder' ),
					'off' => esc_html__( 'No', 'rtcl-elementor-builder' ),
				],
				'default'     => 'on',
				'description' => __( 'Show / Hide new badge.', 'rtcl-elementor-builder' ),
				'tab_slug'    => 'general',
				'toggle_slug' => 'visibility',
			],
			'rtcl_show_price_type'        	=> [
				'label'       => __('Show price type', 'rtcl-elementor-builder'),
				'type'        => 'yes_no_button',
				'options'     => [
					'on'  => esc_html__( 'Yes', 'rtcl-elementor-builder' ),
					'off' => esc_html__( 'No', 'rtcl-elementor-builder' ),
				],
				'default'     => 'on',
				'description' => __( 'Show / Hide new badge.', 'rtcl-elementor-builder' ),
				'tab_slug'    => 'general',
				'toggle_slug' => 'visibility',
			],
			'rtcl_show_user'        		=> [
				'label'       => __('Show user', 'rtcl-elementor-builder'),
				'type'        => 'yes_no_button',
				'options'     => [
					'on'  => esc_html__( 'Yes', 'rtcl-elementor-builder' ),
					'off' => esc_html__( 'No', 'rtcl-elementor-builder' ),
				],
				'default'     => 'off',
				'description' => __( 'Show / Hide new badge.', 'rtcl-elementor-builder' ),
				'tab_slug'    => 'general',
				'toggle_slug' => 'visibility',
			],
			'rtcl_show_views'        		=> [
				'label'       => __('Show Views', 'rtcl-elementor-builder'),
				'type'        => 'yes_no_button',
				'options'     => [
					'on'  => esc_html__( 'Yes', 'rtcl-elementor-builder' ),
					'off' => esc_html__( 'No', 'rtcl-elementor-builder' ),
				],
				'default'     => 'on',
				'description' => __( 'Show / Hide new badge.', 'rtcl-elementor-builder' ),
				'tab_slug'    => 'general',
				'toggle_slug' => 'visibility',
			],
			'rtcl_show_types'        		=> [
				'label'       => __('Show Type', 'rtcl-elementor-builder'),
				'type'        => 'yes_no_button',
				'options'     => [
					'on'  => esc_html__( 'Yes', 'rtcl-elementor-builder' ),
					'off' => esc_html__( 'No', 'rtcl-elementor-builder' ),
				],
				'default'     => 'on',
				'description' => __( 'Show / Hide new badge.', 'rtcl-elementor-builder' ),
				'tab_slug'    => 'general',
				'toggle_slug' => 'visibility',
			],
			'rtcl_show_phone'        		=> [
				'label'       => __('Show Phone', 'rtcl-elementor-builder'),
				'type'        => 'yes_no_button',
				'options'     => [
					'on'  => esc_html__( 'Yes', 'rtcl-elementor-builder' ),
					'off' => esc_html__( 'No', 'rtcl-elementor-builder' ),
				],
				'default'     => 'on',
				'description' => __( 'Show / Hide new badge.', 'rtcl-elementor-builder' ),
				'tab_slug'    => 'general',
				'toggle_slug' => 'visibility',
				'show_if'    => [
					'rtcl_listings_grid_style' => 'style-3',
				],
			],
			'rtcl_show_favourites'        	=> [
				'label'       => __('Show Favorites', 'rtcl-elementor-builder'),
				'type'        => 'yes_no_button',
				'options'     => [
					'on'  => esc_html__( 'Yes', 'rtcl-elementor-builder' ),
					'off' => esc_html__( 'No', 'rtcl-elementor-builder' ),
				],
				'default'     => 'on',
				'description' => __( 'Show / Hide new badge.', 'rtcl-elementor-builder' ),
				'tab_slug'    => 'general',
				'toggle_slug' => 'visibility',
			],
			'rtcl_show_custom_fields'        	=> [
				'label'       => __('Show Custom Fields', 'rtcl-elementor-builder'),
				'type'        => 'yes_no_button',
				'options'     => [
					'on'  => esc_html__( 'Yes', 'rtcl-elementor-builder' ),
					'off' => esc_html__( 'No', 'rtcl-elementor-builder' ),
				],
				'default'     => 'on',
				'description' => __( 'Show / Hide new badge.', 'rtcl-elementor-builder' ),
				'tab_slug'    => 'general',
				'toggle_slug' => 'visibility',
			],
			'rtcl_action_button_layout'     => [
				'label'       => __('Show Favorites', 'rtcl-elementor-builder'),
				'type'        => 'select',
				'options'    => array(
					'vertical'   => __( 'Vertical View', 'classified-listing' ),
					'horizontal' => __( 'Horizontal View', 'classified-listing' ),
				),
				'default'     => 'horizontal',
				'description' => __( 'Show / Hide new badge.', 'rtcl-elementor-builder' ),
				'tab_slug'    => 'general',
				'toggle_slug' => 'visibility',
				'show_if'    => [
					'rtcl_listings_grid_style' => array( 'style-1', 'style-2', 'style-4' ),
				],
			],
			'rtcl_auto_height'        	=> [
				'label'       => __('Auto Height', 'rtcl-elementor-builder'),
				'type'        => 'yes_no_button',
				'options'     => [
					'on'  => esc_html__( 'Yes', 'rtcl-elementor-builder' ),
					'off' => esc_html__( 'No', 'rtcl-elementor-builder' ),
				],
				'default'     => 'off',
				'description' => __( 'Show / Hide new badge.', 'rtcl-elementor-builder' ),
				'tab_slug'    => 'general',
				'toggle_slug' => 'slider',
			],
			'slider_loop'        	=> [
				'label'       => __('Loop', 'rtcl-elementor-builder'),
				'type'        => 'yes_no_button',
				'options'     => [
					'on'  => esc_html__( 'Yes', 'rtcl-elementor-builder' ),
					'off' => esc_html__( 'No', 'rtcl-elementor-builder' ),
				],
				'default'     => 'off',
				'description' => __( 'Show / Hide new badge.', 'rtcl-elementor-builder' ),
				'tab_slug'    => 'general',
				'toggle_slug' => 'slider',
			],
			'slider_autoplay'        	=> [
				'label'       => __('Autoplay', 'rtcl-elementor-builder'),
				'type'        => 'yes_no_button',
				'options'     => [
					'on'  => esc_html__( 'Yes', 'rtcl-elementor-builder' ),
					'off' => esc_html__( 'No', 'rtcl-elementor-builder' ),
				],
				'default'     => 'on',
				'description' => __( 'Show / Hide new badge.', 'rtcl-elementor-builder' ),
				'tab_slug'    => 'general',
				'toggle_slug' => 'slider',
			],
			'slider_stop_on_hover'        	=> [
				'label'       => __('Stop On hover', 'rtcl-elementor-builder'),
				'type'        => 'yes_no_button',
				'options'     => [
					'on'  => esc_html__( 'Yes', 'rtcl-elementor-builder' ),
					'off' => esc_html__( 'No', 'rtcl-elementor-builder' ),
				],
				'default'     => 'on',
				'description' => __( 'Show / Hide new badge.', 'rtcl-elementor-builder' ),
				'tab_slug'    => 'general',
				'toggle_slug' => 'slider',
			],
			'slider_delay'        	=> [
				'label'       => __('Stop On hover', 'rtcl-elementor-builder'),
				'type'        => 'select',
				'options'     => [
					'7000' => __('7 Seconds', 'classified-listing'),
					'6000' => __('6 Seconds', 'classified-listing'),
					'5000' => __('5 Seconds', 'classified-listing'),
					'4000' => __('4 Seconds', 'classified-listing'),
					'3000' => __('3 Seconds', 'classified-listing'),
					'2000' => __('2 Seconds', 'classified-listing'),
					'1000' => __('1 Second', 'classified-listing'),
				],
				'default'     => '5000',
				'description' => __( 'Show / Hide new badge.', 'rtcl-elementor-builder' ),
				'tab_slug'    => 'general',
				'toggle_slug' => 'slider',
			],
			'slider_autoplay_speed'        	=> [
				'label'       => __('Slider Speed', 'rtcl-elementor-builder'),
				'type'        => 'text',
				'default'     => '2000',
				'description' => __( 'Show / Hide new badge.', 'rtcl-elementor-builder' ),
				'tab_slug'    => 'general',
				'toggle_slug' => 'slider',
			],
			'slider_space_between'        	=> [
				'label'       => __('Space Between', 'rtcl-elementor-builder'),
				'type'        => 'text',
				'default'     => '20',
				'description' => __( 'Show / Hide new badge.', 'rtcl-elementor-builder' ),
				'tab_slug'    => 'general',
				'toggle_slug' => 'slider',
			],
			'slider_nav'        	=> [
				'label'       => __('Arrow Navigation', 'rtcl-elementor-builder'),
				'type'        => 'yes_no_button',
				'options'     => [
					'on'  => esc_html__( 'Yes', 'rtcl-elementor-builder' ),
					'off' => esc_html__( 'No', 'rtcl-elementor-builder' ),
				],
				'default'     => 'on',
				'description' => __( 'Show / Hide new badge.', 'rtcl-elementor-builder' ),
				'tab_slug'    => 'general',
				'toggle_slug' => 'slider',
				
			],
			'rtcl_button_arrow_style'        	=> [
				'label'       => __('Arrow navigation Style', 'rtcl-elementor-builder'),
				'type'        => 'select',
				'options'   => [
					'style-1' => esc_html__('Center', 'classified-listing'),
					'style-2' => esc_html__('Left Top', 'classified-listing'),
					'style-3' => esc_html__('Right Top', 'classified-listing'),
				],
				'default'     => 'style-1',
				'description' => __( 'Show / Hide new badge.', 'rtcl-elementor-builder' ),
				'tab_slug'    => 'general',
				'toggle_slug' => 'slider',
				'show_if'    => [
					'rtcl_enable_slider' => 'on',
				],
			],
			'slider_dots'        	=> [
				'label'       => __('Dot Navigation', 'rtcl-elementor-builder'),
				'type'        => 'yes_no_button',
				'options'     => [
					'on'  => esc_html__( 'Yes', 'rtcl-elementor-builder' ),
					'off' => esc_html__( 'No', 'rtcl-elementor-builder' ),
				],
				'default'     => 'on',
				'description' => __( 'Show / Hide new badge.', 'rtcl-elementor-builder' ),
				'tab_slug'    => 'general',
				'toggle_slug' => 'slider',
			],
			'rtcl_button_dot_style'        	=> [
				'label'       => __('Dot navigation Style', 'rtcl-elementor-builder'),
				'type'        => 'select',
				'options'   => [
					'style-1' => esc_html__('Style 1', 'classified-listing'),
					'style-2' => esc_html__('Style 2', 'classified-listing'),
					'style-3' => esc_html__('Style 3', 'classified-listing'),
					'style-4' => esc_html__('Style 4', 'classified-listing'),
				],
				'default'   => 'style-3',
				'description' => __( 'Show / Hide new badge.', 'rtcl-elementor-builder' ),
				'tab_slug'    => 'general',
				'toggle_slug' => 'slider',
				'show_if'    => [
					'slider_dots' => 'on',
				],
			],
			'rtcl_icon_color'       => [
				'label'       => esc_html__( 'Icon Color', 'rtcl-elementor-builder' ),
				'description' => esc_html__( 'Here you can define a custom color for category name.', 'rtcl-elementor-builder' ),
				'type'        => 'color-alpha',
				'tab_slug'    => 'advanced',
				'toggle_slug' => 'meta',
				'hover'       => 'tabs',
			],
			// slider option
			
			
			// computed.
			'__listing_related'           => array(
				'type'                => 'computed',
				'computed_callback'   => array('RtclElb\DiviModule\ListingRelated\ListingRelated', 'get_content' ),
				'computed_depends_on' => array(
					'rtcl_auto_height',
					'slider_loop',
					'slider_autoplay',
					'slider_stop_on_hover',
					'slider_delay',
					'slider_autoplay_speed',
					'slider_space_between',
					'slider_nav',
					'rtcl_button_arrow_style',
					'rtcl_button_dot_style',
					'slider_dots',
					'rtcl_show_custom_fields',
					'rtcl_show_quick_view',
					'rtcl_show_compare',
					'rtcl_thumb_image_size',
					'rtcl_listings_grid_style',
					'rtcl_enable_slider',
					'rtcl_listings_column',
					'rtcl_listings_per_page',
					'rtcl_listings_filter',
					'rtcl_show_image',
					'rtcl_show_title',
					'rtcl_show_description',
					'rtcl_content_limit',
					'rtcl_show_labels',
					'rtcl_show_details_button',
					'rtcl_show_date',
					'rtcl_show_category',
					'rtcl_show_location',
					'rtcl_show_price',
					'rtcl_show_price_unit',
					'rtcl_show_price_type',
					'rtcl_show_user',
					'rtcl_action_button_layout',
					'rtcl_show_favourites',
					'rtcl_show_phone',
					'rtcl_show_types',
					'rtcl_show_views',
				)
			),
		];

		if(function_exists( 'rtclSellerVerification' )){
			$fields['rtcl_verified_user_base'] = [
				'label'       => __('Show Seller Verification', 'rtcl-elementor-builder'),
				'type'        => 'yes_no_button',
				'options'     => [
					'on'  => esc_html__( 'Yes', 'rtcl-elementor-builder' ),
					'off' => esc_html__( 'No', 'rtcl-elementor-builder' ),
				],
				'default'     => 'on',
				'description' => __( 'Show / Hide new badge.', 'rtcl-elementor-builder' ),
				'tab_slug'    => 'general',
				'toggle_slug' => 'visibility',
			];
		}

		if ( rtcl()->has_pro() ) {
			$fields['rtcl_show_quick_view']       	= [
				'label'       => __('Show Quick View', 'rtcl-elementor-builder'),
				'type'        => 'yes_no_button',
				'options'     => [
					'on'  => esc_html__( 'Yes', 'rtcl-elementor-builder' ),
					'off' => esc_html__( 'No', 'rtcl-elementor-builder' ),
				],
				'default'     => 'on',
				'description' => __( 'Show / Hide new badge.', 'rtcl-elementor-builder' ),
				'tab_slug'    => 'general',
				'toggle_slug' => 'visibility',
			];
			$fields['rtcl_show_compare']   	= [
				'label'       => __('Show Compare', 'rtcl-elementor-builder'),
				'type'        => 'yes_no_button',
				'options'     => [
					'on'  => esc_html__( 'Yes', 'rtcl-elementor-builder' ),
					'off' => esc_html__( 'No', 'rtcl-elementor-builder' ),
				],
				'default'     => 'on',
				'description' => __( 'Show / Hide new badge.', 'rtcl-elementor-builder' ),
				'tab_slug'    => 'general',
				'toggle_slug' => 'visibility',
			];
		}
		return $fields;
	}

	public function get_advanced_fields_config() {

		$advanced_fields                = [];
		$advanced_fields['text']        = [];
		$advanced_fields['text_shadow'] = [];

		$advanced_fields['fonts'] = [
			'title' => [
				'css'              => array(
					'main' => '%%order_class%% .rtcl-listings-wrapper .rtcl-listing-title a',
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
			'meta'  => [
				'css'              => array(
					'main' => '%%order_class%% .rtcl-listings-wrapper .rtcl-listing-meta-data li',
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
					'default' => '16px',
				),
				'font'             => [
					'default' => '|400|||||||',
				],
			],
			'price' => [
				'css'              => array(
					'main' => '%%order_class%% .rtcl-listings-wrapper .rtcl-listings .rtcl-price .rtcl-price-amount.amount',
				),
				'important'        => 'all',
				'tab_slug'         => 'advanced',
				'toggle_slug'      => 'price',
				'line_height'      => array(
					'range_settings' => array(
						'min'  => '1',
						'max'  => '3',
						'step' => '.1',
					),
					'default'        => '1.3em',
				),
				'font_size'        => array(
					'default' => '20px',
				),
				'font'             => [
					'default' => '|600|||||||',
				],
			]
		];
		

		return $advanced_fields;
	}



	public static function get_content( $settings ) {
		$listing  = rtcl()->factory->get_listing(self::listing_id());
		$helper = new ListingRelatedHelper($settings, $listing);
		$settings['slider_options'] = [
			'loop'              => $settings['slider_loop'] === 'on' ? true : false,
			'autoplay'          => $settings['slider_autoplay'] === 'on' ? [ 'delay' => (int) $settings['slider_delay'] ] : false,
			'speed'             => (int) $settings['slider_autoplay_speed'],
			'spaceBetween'      => (int) $settings['slider_space_between'],
			'navigation'        => $settings['slider_nav'] === 'on' ? [ 'nextEl' => '.swiper-button-next', 'prevEl' => '.swiper-button-prev' ] : false,
			'pagination'        => $settings['slider_dots'] === 'on' ? [ 'el' => '.swiper-pagination', 'clickable' => true ] : false,
			'autoHeight'        => $settings['rtcl_auto_height'] === 'on' ? true : false,
			'pauseOnMouseEnter' => $settings['slider_stop_on_hover'] === 'on' ? true : false,
		];
		 
		add_filter('excerpt_length', [$helper, 'excerpt_limit']);
		if (empty($settings['rtcl_show_price_unit']) || $settings['rtcl_show_price_unit'] === 'off' ) {
			remove_filter('rtcl_price_meta_html', [AppliedBothEndHooks::class, 'add_price_unit_to_price'], 10, 3);
		}
		if (empty($settings['rtcl_show_price_type']) || $settings['rtcl_show_price_type'] === 'off' ) {
			remove_filter('rtcl_price_meta_html', [AppliedBothEndHooks::class, 'add_price_type_to_price'], 20, 3);
		}
		add_filter('rtcl_loop_item_listable_fields', [$helper, 'listable_fields_arg'], 10, 1);

		add_filter('rtcl_related_listing_query_arg', [$helper, 'related_listing_query_arg']);
		add_filter('rtcl_related_listings_data', [$helper, 'related_listings_data'], 10, 2);

		// Capture the output of the related listings.
		ob_start();
		$listing->the_related_listings();
		$content = ob_get_clean();

		if (empty($settings['rtcl_show_price_unit']) || $settings['rtcl_show_price_unit'] === 'off') {
			add_filter('rtcl_price_meta_html', [AppliedBothEndHooks::class, 'add_price_unit_to_price'], 10, 3);
		}
		if (empty($settings['rtcl_show_price_type']) || $settings['rtcl_show_price_type'] === 'off') {
			add_filter('rtcl_price_meta_html', [AppliedBothEndHooks::class, 'add_price_type_to_price'], 20, 3);
		}

		// Return the captured content.
		return $content;
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
		$wrapper = '%%order_class%% .rtin-content-area ';
		
		// ✅ Badge Styles (color + bg-color)
		$badge_styles = [
			[
				'class' => 'rtcl-listing-meta-data li i',
				'text_color' => 'rtcl_icon_color',
			],
		];

		foreach ( $badge_styles as $badge ) {
			$selector = "$wrapper .{$badge['class']}";
			$text_color = $this->props[ $badge['text_color'] ] ?? '';

			if ( ! empty( $text_color ) ) {
				\ET_Builder_Element::set_style(
					$render_slug,
					[
						'selector'    => $selector,
						'declaration' => sprintf( 'color: %1$s !important;', $text_color ),
					]
				);
			}
		}
	}



}