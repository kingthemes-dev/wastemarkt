<?php
namespace  RtclElb\DiviModule\Store\StoreListing;

use Rtcl\Controllers\Hooks\AppliedBothEndHooks;
use Rtcl\Controllers\Hooks\TemplateHooks;
use Rtcl\Helpers\Functions;
use Rtcl\Helpers\Pagination;
use Rtcl\Resources\Options;
use Rtcl\Traits\Addons\TopQueryTrait;
use RtclElb\Helpers\Fns;
use RtclPro\Controllers\Hooks\TemplateHooks as TemplateHooksPro;

Class StoreListing extends \ET_Builder_Module {
	use TopQueryTrait;
	public $slug = 'rtcl_store_listing';
	public $vb_support = 'on';
    
	public $icon_path;
	protected $module_credits
		= [
			'author'     => 'RadiusTheme',
			'author_uri' => 'https://radiustheme.com',
		];
	public function init() {
		$this->name      = esc_html__( 'Store Listing', 'rtcl-elementor-builder' );
		$this->icon_path = plugin_dir_path( __FILE__ ) . 'icon.svg';
		$this->folder_name = 'et_pb_classified_store_single_page_modules';
		$this->settings_modal_toggles = [
			'general'  => [
				'toggles' => [
					'layout'     => esc_html__( 'Layout', 'rtcl-elementor-builder' ),
					'general'    => esc_html__( 'General', 'rtcl-elementor-builder' ),
					'visibility' => esc_html__( 'Visibility', 'rtcl-elementor-builder' ),
				],
			],
			'advanced' => [
				'toggles' => [
					'title'       => esc_html__( 'Title', 'rtcl-elementor-builder' ),
					'meta' => esc_html__( 'Meta', 'rtcl-elementor-builder' ),
					'price' => esc_html__( 'Price', 'rtcl-elementor-builder' ),
					'button' => esc_html__( 'Details Button', 'rtcl-elementor-builder' ),
				],
			],
		];
	}

	public function get_fields() {
		$category_dropdown = Fns::get_listing_taxonomy( 'parent' );
		$location_dropdown = Fns::get_listing_taxonomy( 'parent', rtcl()->location );
		$listing_order_by  = Fns::get_order_options();
		return [
			'rtcl_listings_view'       => [
				'label'          => esc_html__( 'Layout view ', 'rtcl-elementor-builder' ),
				'type'           => 'select',
				'options'        => [
					'list' => __( 'List View', 'rtcl-elementor-builder' ),
					'grid' => __( 'Grid View', 'rtcl-elementor-builder' ),
				],
				'default'        => 'list',
				'description'    => esc_html__( 'Select Breadcrumb Position to display Breadcrumb.', 'rtcl-elementor-builder' ),
				'mobile_options' => true,
				'tab_slug'       => 'general',
				'toggle_slug'    => 'layout',
			],
			'rtcl_listings_style'       => [
				'label'          => esc_html__( 'List Style ', 'rtcl-elementor-builder' ),
				'type'           => 'select',
				'options'        => [
					'style-1' => __( 'Style 1', 'rtcl-elementor-builder' ),
					'style-2' => __( 'Style 2', 'rtcl-elementor-builder' ),
					'style-3' => __( 'Style 3', 'rtcl-elementor-builder' ),
					'style-4' => __( 'Style 4', 'rtcl-elementor-builder' ),
					'style-5' => __( 'Style 5', 'rtcl-elementor-builder' ),
				],
				'default'        => 'style-1',
				'show_if'     => [
					'rtcl_listings_view' => 'list',
				],
				'description'    => esc_html__( 'Select list style.', 'rtcl-elementor-builder' ),
				'mobile_options' => true,
				'tab_slug'       => 'general',
				'toggle_slug'    => 'layout',
			],
			'rtcl_listings_grid_style'       => [
				'label'          => esc_html__( 'Grid Style ', 'rtcl-elementor-builder' ),
				'type'           => 'select',
				'options'        => [
					'style-1' => __( 'Style 1', 'rtcl-elementor-builder' ),
					'style-2' => __( 'Style 2', 'rtcl-elementor-builder' ),
					'style-3' => __( 'Style 3', 'rtcl-elementor-builder' ),
					'style-4' => __( 'Style 4', 'rtcl-elementor-builder' ),
					'style-5' => __( 'Style 5', 'rtcl-elementor-builder' ),
				],
				'default'        => 'style-1',
				'show_if'     => [
					'rtcl_listings_view' => 'grid',
				],
				'description'    => esc_html__( 'Select list style.', 'rtcl-elementor-builder' ),
				'mobile_options' => true,
				'tab_slug'       => 'general',
				'toggle_slug'    => 'layout',
			],
			'rtcl_listings_column'       => [
				'label'          => esc_html__( 'Grid Column', 'rtcl-elementor-builder' ),
				'type'           => 'select',
				'options'        => [
					'8' => __( 'Column 8', 'rtcl-elementor-builder' ),
					'7' => __( 'Column 7', 'rtcl-elementor-builder' ),
					'6' => __( 'Column 6', 'rtcl-elementor-builder' ),
					'5' => __( 'Column 5', 'rtcl-elementor-builder' ),
					'4' => __( 'Column 4', 'rtcl-elementor-builder' ),
					'3' => __( 'Column 3', 'rtcl-elementor-builder' ),
					'2' => __( 'Column 2', 'rtcl-elementor-builder' ),
					'1' => __( 'Column 1', 'rtcl-elementor-builder' ),
				],
				'show_if'     => [
					'rtcl_listings_view' => 'grid',
				],
				'default'        => '3',
				'description'    => esc_html__( 'Select column number to display listing.', 'rtcl-elementor-builder' ),
				'mobile_options' => true,
				'tab_slug'       => 'general',
				'toggle_slug'    => 'layout',
			],
			'rtcl_listings_promotions'       => [
				'label'          => esc_html__( 'Promotions', 'rtcl-elementor-builder' ),
				'type'           => 'multiple_checkboxes',
				'options'        => Options::get_listing_promotions(),
				'description'    => esc_html__( 'Select list style.', 'rtcl-elementor-builder' ),
				'mobile_options' => true,
				'tab_slug'       => 'general',
				'toggle_slug'    => 'general',
			],
			'rtcl_listings_promotions_not_in'       => [
				'label'          => esc_html__( 'Promotions Exclude', 'rtcl-elementor-builder' ),
				'type'           => 'multiple_checkboxes',
				'options'        => Options::get_listing_promotions(),
				'description'    => esc_html__( 'Select list style.', 'rtcl-elementor-builder' ),
				'mobile_options' => true,
				'tab_slug'       => 'general',
				'toggle_slug'    => 'general',
			],
			'rtcl_listing_types'       => [
				'label'          => esc_html__( 'Listing Type', 'rtcl-elementor-builder' ),
				'type'           => 'multiple_checkboxes',
				'options' => array_merge(
					[
						'all' => 'All',
					],
					Functions::get_listing_types(), // OR Options::get_default_listing_types().
				),
				'description'    => esc_html__( 'Select list style.', 'rtcl-elementor-builder' ),
				'mobile_options' => true,
				'tab_slug'       => 'general',
				'toggle_slug'    => 'general',
			],
			'rtcl_listings_by_categories'       => [
				'label'          => esc_html__( 'Listing Categories', 'rtcl-elementor-builder' ),
				'type'           => 'multiple_checkboxes',
				'options'        => $category_dropdown,
				'description'    => esc_html__( 'Select list style.', 'rtcl-elementor-builder' ),
				'mobile_options' => true,
				'tab_slug'       => 'general',
				'toggle_slug'    => 'general',
			],
			'rtcl_listings_categories_include_children'       => [
				'label'          => esc_html__( 'Include Children Categories', 'rtcl-elementor-builder' ),
				'type'        => 'yes_no_button',
				'options'     => [
					'on'  => esc_html__( 'Yes', 'rtcl-elementor-builder' ),
					'off' => esc_html__( 'No', 'rtcl-elementor-builder' ),
				],
				'default'     => 'off',
				'description'    => esc_html__( 'Select list style.', 'rtcl-elementor-builder' ),
				'mobile_options' => true,
				'tab_slug'       => 'general',
				'toggle_slug'    => 'general',
				
			],
			'rtcl_locations'       => [
				'label'          => esc_html__( 'Locations', 'rtcl-elementor-builder' ),
				'type'           => 'multiple_checkboxes',
				'options'        => $location_dropdown,
				'description'    => esc_html__( 'Select list style.', 'rtcl-elementor-builder' ),
				'mobile_options' => true,
				'tab_slug'       => 'general',
				'toggle_slug'    => 'general',
			],
			'rtcl_listings_location_include_children'       => [
				'label'          => esc_html__( 'Include Inner Location', 'rtcl-elementor-builder' ),
				'type'        => 'yes_no_button',
				'options'     => [
					'on'  => esc_html__( 'Yes', 'rtcl-elementor-builder' ),
					'off' => esc_html__( 'No', 'rtcl-elementor-builder' ),
				],
				'default'     => 'off',
				'description'    => esc_html__( 'Select list style.', 'rtcl-elementor-builder' ),
				'mobile_options' => true,
				'tab_slug'       => 'general',
				'toggle_slug'    => 'general',
			],
			'rtcl_listing_per_page'       => [
				'label'          => esc_html__( 'Listing per page', 'rtcl-elementor-builder' ),
				'type'        => 'text',
				'default'     => '12',
				'description'    => esc_html__( 'Select list style.', 'rtcl-elementor-builder' ),
				'mobile_options' => true,
				'tab_slug'       => 'general',
				'toggle_slug'    => 'general',
			],
			'rtcl_orderby'       => [
				'label'          => esc_html__( 'Order by', 'rtcl-elementor-builder' ),
				'type'           => 'select',
				'options'        => $listing_order_by,
				'default'        => 'date',
				'description'    => esc_html__( 'Select list style.', 'rtcl-elementor-builder' ),
				'mobile_options' => true,
				'tab_slug'       => 'general',
				'toggle_slug'    => 'general',
			],
			'rtcl_order'       => [
				'label'          => esc_html__( 'Sort by', 'rtcl-elementor-builder' ),
				'type'           => 'select',
				'options'     => [
					'asc'  => __( 'Ascending', 'rtcl-divi-addons' ),
					'desc' => __( 'Descending', 'rtcl-divi-addons' ),
				],
				'default'     => 'desc',
				'description'    => esc_html__( 'Select list style.', 'rtcl-elementor-builder' ),
				'mobile_options' => true,
				'tab_slug'       => 'general',
				'toggle_slug'    => 'general',
			],
			'rtcl_no_listing_text'       => [
				'label'          => esc_html__( 'No Listing Text', 'rtcl-elementor-builder' ),
				'type'           => 'textarea',
				'default'        => 'No Listing Found',
				'description'    => esc_html__( 'Select list style.', 'rtcl-elementor-builder' ),
				'mobile_options' => true,
				'tab_slug'       => 'general',
				'toggle_slug'    => 'general',
			],
			
			'rtcl_listing_pagination'        => [
				'label'       => esc_html__( 'Pagination', 'rtcl-elementor-builder' ),
				'type'        => 'yes_no_button',
				'options'     => [
					'on'  => esc_html__( 'Yes', 'rtcl-elementor-builder' ),
					'off' => esc_html__( 'No', 'rtcl-elementor-builder' ),
				],
				'default'     => 'on',
				'description' => __( 'Show or Hide Listing Title. Default: On', 'rtcl-elementor-builder' ),
				'tab_slug'    => 'general',
				'toggle_slug' => 'visibility',
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
			'rtcl_show_title'        => [
				'label'       => esc_html__( 'Show Title', 'rtcl-elementor-builder' ),
				'type'        => 'yes_no_button',
				'options'     => [
					'on'  => esc_html__( 'Yes', 'rtcl-elementor-builder' ),
					'off' => esc_html__( 'No', 'rtcl-elementor-builder' ),
				],
				'default'     => 'on',
				'description' => __( 'Show or Hide Listing Title. Default: On', 'rtcl-elementor-builder' ),
				'tab_slug'    => 'general',
				'toggle_slug' => 'visibility',
			],
			'rtcl_show_image'       => [
				'label'          => esc_html__( 'Show Image', 'rtcl-elementor-builder' ),
				'type'        => 'yes_no_button',
				'options'     => [
					'on'  => esc_html__( 'Yes', 'rtcl-elementor-builder' ),
					'off' => esc_html__( 'No', 'rtcl-elementor-builder' ),
				],
				'default'     => 'on',
				'description'    => esc_html__( 'Show or Hide Listing Icon/Image. Default: On', 'rtcl-elementor-builder' ),
				'mobile_options' => true,
				'tab_slug'       => 'general',
				'toggle_slug'    => 'visibility',
			],
			'rtcl_show_description'       => [
				'label'          => esc_html__( 'Short Description', 'rtcl-elementor-builder' ),
				'type'        => 'yes_no_button',
				'options'     => [
					'on'  => esc_html__( 'Yes', 'rtcl-elementor-builder' ),
					'off' => esc_html__( 'No', 'rtcl-elementor-builder' ),
				],
				'default'     => 'off',
				'description'    => esc_html__( 'Show or Hide Listing Description. Default: On.', 'rtcl-elementor-builder' ),
				'mobile_options' => true,
				'tab_slug'       => 'general',
				'toggle_slug'    => 'visibility',
			],
			'rtcl_content_limit'        	=> [
				'label'       => __('Short description content limit', 'rtcl-elementor-builder'),
				'type'        => 'text',
				'default'     => '20',
				'description' => __( 'Show / Hide new badge.', 'rtcl-elementor-builder' ),
				'tab_slug'    => 'general',
				'toggle_slug' => 'visibility',
			],
			'rtcl_show_labels'       => [
				'label'          => esc_html__( 'Show Badge', 'rtcl-elementor-builder' ),
				'type'        => 'yes_no_button',
				'options'     => [
					'on'  => esc_html__( 'Yes', 'rtcl-elementor-builder' ),
					'off' => esc_html__( 'No', 'rtcl-elementor-builder' ),
				],
				'default'     => 'on',
				'description'    => esc_html__( 'Show or Hide labels. Default: On', 'rtcl-elementor-builder' ),
				'mobile_options' => true,
				'tab_slug'       => 'general',
				'toggle_slug'    => 'visibility',
			],
			'rtcl_show_date'       => [
				'label'          => esc_html__( 'Show Date', 'rtcl-elementor-builder' ),
				'type'        => 'yes_no_button',
				'options'     => [
					'on'  => esc_html__( 'Yes', 'rtcl-elementor-builder' ),
					'off' => esc_html__( 'No', 'rtcl-elementor-builder' ),
				],
				'default'     => 'off',
				'description'    => esc_html__( 'Show or Hide date. Default: On.', 'rtcl-elementor-builder' ),
				'mobile_options' => true,
				'tab_slug'       => 'general',
				'toggle_slug'    => 'visibility',
			],
			'rtcl_show_location'       => [
				'label'          => esc_html__( 'Show Location', 'rtcl-elementor-builder' ),
				'type'        => 'yes_no_button',
				'options'     => [
					'on'  => esc_html__( 'Yes', 'rtcl-elementor-builder' ),
					'off' => esc_html__( 'No', 'rtcl-elementor-builder' ),
				],
				'default'     => 'on',
				'description'    => esc_html__( 'Show or Hide Location. Default: On', 'rtcl-elementor-builder' ),
				'mobile_options' => true,
				'tab_slug'       => 'general',
				'toggle_slug'    => 'visibility',
			],
			'rtcl_show_category'       => [
				'label'          => esc_html__( 'Show Category', 'rtcl-elementor-builder' ),
				'type'        => 'yes_no_button',
				'options'     => [
					'on'  => esc_html__( 'Yes', 'rtcl-elementor-builder' ),
					'off' => esc_html__( 'No', 'rtcl-elementor-builder' ),
				],
				'default'     => 'on',
				'description'    => esc_html__( 'Show or Hide Category. Default: On', 'rtcl-elementor-builder' ),
				'mobile_options' => true,
				'tab_slug'       => 'general',
				'toggle_slug'    => 'visibility',
			],
			'rtcl_show_price'       => [
				'label'          => esc_html__( 'Show Price', 'rtcl-elementor-builder' ),
				'type'        => 'yes_no_button',
				'options'     => [
					'on'  => esc_html__( 'Yes', 'rtcl-elementor-builder' ),
					'off' => esc_html__( 'No', 'rtcl-elementor-builder' ),
				],
				'default'     => 'on',
				'description'    => esc_html__( 'Show or Hide Price. Default: On', 'rtcl-elementor-builder' ),
				'mobile_options' => true,
				'tab_slug'       => 'general',
				'toggle_slug'    => 'visibility',
			],
			'rtcl_show_price_unit'       => [
				'label'          => esc_html__( 'Show Price Unit', 'rtcl-elementor-builder' ),
				'type'        => 'yes_no_button',
				'options'     => [
					'on'  => esc_html__( 'Yes', 'rtcl-elementor-builder' ),
					'off' => esc_html__( 'No', 'rtcl-elementor-builder' ),
				],
				'default'     => 'on',
				'description'    => esc_html__( 'Show Price Unit', 'rtcl-elementor-builder' ),
				'mobile_options' => true,
				'tab_slug'       => 'general',
				'toggle_slug'    => 'visibility',
			],
			'rtcl_show_price_type'       => [
				'label'          => esc_html__( 'Show Price Type', 'rtcl-elementor-builder' ),
				'type'        => 'yes_no_button',
				'options'     => [
					'on'  => esc_html__( 'Yes', 'rtcl-elementor-builder' ),
					'off' => esc_html__( 'No', 'rtcl-elementor-builder' ),
				],
				'default'     => 'on',
				'description'    => esc_html__( 'Select Show Price Type to display price type.', 'rtcl-elementor-builder' ),
				'mobile_options' => true,
				'tab_slug'       => 'general',
				'toggle_slug'    => 'visibility',
			],
			'rtcl_show_user'       => [
				'label'          => esc_html__( 'Show User', 'rtcl-elementor-builder' ),
				'type'        => 'yes_no_button',
				'options'     => [
					'on'  => esc_html__( 'Yes', 'rtcl-elementor-builder' ),
					'off' => esc_html__( 'No', 'rtcl-elementor-builder' ),
				],
				'default'     => 'on',
				'description'    => esc_html__( 'Show or Hide User/Author Name. Default: On', 'rtcl-elementor-builder' ),
				'mobile_options' => true,
				'tab_slug'       => 'general',
				'toggle_slug'    => 'visibility',
			],
			'rtcl_show_views'       => [
				'label'          => esc_html__( 'Show Views', 'rtcl-elementor-builder' ),
				'type'        => 'yes_no_button',
				'options'     => [
					'on'  => esc_html__( 'Yes', 'rtcl-elementor-builder' ),
					'off' => esc_html__( 'No', 'rtcl-elementor-builder' ),
				],
				'default'     => 'on',
				'description'    => esc_html__( 'Show or Hide Views Count\'s. Default: On', 'rtcl-elementor-builder' ),
				'mobile_options' => true,
				'tab_slug'       => 'general',
				'toggle_slug'    => 'visibility',
			],
			'rtcl_show_types'       => [
				'label'          => esc_html__( 'Show Types', 'rtcl-elementor-builder' ),
				'type'        => 'yes_no_button',
				'options'     => [
					'on'  => esc_html__( 'Yes', 'rtcl-elementor-builder' ),
					'off' => esc_html__( 'No', 'rtcl-elementor-builder' ),
				],
				'default'     => 'on',
				'description'    => esc_html__( 'Show or Hide Types. Default: On', 'rtcl-elementor-builder' ),
				'mobile_options' => true,
				'tab_slug'       => 'general',
				'toggle_slug'    => 'visibility',
			],
			'rtcl_show_phone'       => [
				'label'          => esc_html__( 'Show Phone', 'rtcl-elementor-builder' ),
				'type'        => 'yes_no_button',
				'options'     => [
					'on'  => esc_html__( 'Yes', 'rtcl-elementor-builder' ),
					'off' => esc_html__( 'No', 'rtcl-elementor-builder' ),
				],
				'default'     => 'on',
				'description'    => esc_html__( 'Show or Hide Phone. Default: On', 'rtcl-elementor-builder' ),
				'mobile_options' => true,
				'tab_slug'       => 'general',
				'toggle_slug'    => 'visibility',
			],
			'rtcl_show_custom_fields'       => [
				'label'          => esc_html__( 'Show Custom fields', 'rtcl-elementor-builder' ),
				'type'        => 'yes_no_button',
				'options'     => [
					'on'  => esc_html__( 'Yes', 'rtcl-elementor-builder' ),
					'off' => esc_html__( 'No', 'rtcl-elementor-builder' ),
				],
				'default'     => 'off',
				'description'    => esc_html__( 'Only work for Pro. Default: Off', 'rtcl-elementor-builder' ),
				'mobile_options' => true,
				'tab_slug'       => 'general',
				'toggle_slug'    => 'visibility',
			],
			'rtcl_show_quick_view'       => [
				'label'          => esc_html__( 'Show Quick View', 'rtcl-elementor-builder' ),
				'type'        => 'yes_no_button',
				'options'     => [
					'on'  => esc_html__( 'Yes', 'rtcl-elementor-builder' ),
					'off' => esc_html__( 'No', 'rtcl-elementor-builder' ),
				],
				'default'     => 'on',
				'description'    => esc_html__( 'Only work for Pro. Default: Off', 'rtcl-elementor-builder' ),
				'mobile_options' => true,
				'tab_slug'       => 'general',
				'toggle_slug'    => 'visibility',
			],
			'rtcl_show_compare'       => [
				'label'          => esc_html__( 'Show Compare', 'rtcl-elementor-builder' ),
				'type'        => 'yes_no_button',
				'options'     => [
					'on'  => esc_html__( 'Yes', 'rtcl-elementor-builder' ),
					'off' => esc_html__( 'No', 'rtcl-elementor-builder' ),
				],
				'default'     => 'on',
				'description'    => esc_html__( 'Only work for Pro. Default: Off', 'rtcl-elementor-builder' ),
				'mobile_options' => true,
				'tab_slug'       => 'general',
				'toggle_slug'    => 'visibility',
			],
			'rtcl_show_favourites'       => [
				'label'          => esc_html__( 'Show Favourites', 'rtcl-elementor-builder' ),
				'type'        => 'yes_no_button',
				'options'     => [
					'on'  => esc_html__( 'Yes', 'rtcl-elementor-builder' ),
					'off' => esc_html__( 'No', 'rtcl-elementor-builder' ),
				],
				'default'     => 'on',
				'description'    => esc_html__( 'Show Favourites', 'rtcl-elementor-builder' ),
				'mobile_options' => true,
				'tab_slug'       => 'general',
				'toggle_slug'    => 'visibility',
			],
			'rtcl_action_button_layout'       => [
				'label'          => esc_html__( 'Show Favourites View', 'rtcl-elementor-builder' ),
				'type'        => 'select',
				'options'     => [
					'vertical'  => esc_html__( 'Vertical View', 'rtcl-elementor-builder' ),
					'horizontal' => esc_html__( 'Horizontal View', 'rtcl-elementor-builder' ),
				],
				'default'     => 'horizontal',
				'description'    => esc_html__( 'Show Favourites', 'rtcl-elementor-builder' ),
				'mobile_options' => true,
				'tab_slug'       => 'general',
				'toggle_slug'    => 'visibility',
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
			'rtcl_icon_color'       => [
				'label'       => esc_html__( 'Icon Color', 'rtcl-elementor-builder' ),
				'description' => esc_html__( 'Here you can define a custom color for category name.', 'rtcl-elementor-builder' ),
				'type'        => 'color-alpha',
				'tab_slug'    => 'advanced',
				'toggle_slug' => 'meta',
				'hover'       => 'tabs',
			],
			'rtcl_price_bg_color'       => [
				'label'       => esc_html__( 'Background Color', 'rtcl-elementor-builder' ),
				'description' => esc_html__( 'Here you can define a custom color for category name.', 'rtcl-elementor-builder' ),
				'type'        => 'color-alpha',
				'tab_slug'    => 'advanced',
				'toggle_slug' => 'price',
				'hover'       => 'tabs',
				'show_if'     => [
					'rtcl_show_price' => 'on',
					'rtcl_listings_grid_style' => 'style-5',
				],
			],
			// computed.
			'__archive_listing' => array(
				'type'                => 'computed',
				'computed_callback'   => array( 'RtclElb\DiviModule\Store\StoreListing\StoreListing', 'get_content' ),
				'computed_depends_on' => array(
					'rtcl_listings_column',
					'rtcl_show_details_button',
					'rtcl_content_limit',
					'rtcl_action_button_layout',
					'rtcl_listings_view',
					'rtcl_listings_style',
					'rtcl_listings_grid_style',
					'rtcl_listing_pagination',
					'rtcl_thumb_image_size',
					'rtcl_show_title',
					'rtcl_show_image',
					'rtcl_show_description',
					'rtcl_show_labels',
					'rtcl_show_date',
					'rtcl_show_location',
					'rtcl_show_category',
					'rtcl_show_price',
					'rtcl_show_price_unit',
					'rtcl_show_price_type',
					'rtcl_show_favourites',
					'rtcl_show_compare',
					'rtcl_show_quick_view',
					'rtcl_show_custom_fields',
					'rtcl_show_phone',
					'rtcl_show_types',
					'rtcl_show_views',
					'rtcl_show_user',
					'rtcl_listings_promotions',
					'rtcl_listings_promotions_not_in',
					'rtcl_listing_types',
					'rtcl_listings_by_categories',
					'rtcl_listings_categories_include_children',
					'rtcl_locations',
					'rtcl_listings_location_include_children',
					'rtcl_listing_per_page',
					'rtcl_orderby',
					'rtcl_order',
					'rtcl_no_listing_text'
				)
			)
		];
	}
	public function get_advanced_fields_config() {

		$advanced_fields                = [];
		$advanced_fields['text']        = [];
		$advanced_fields['text_shadow'] = [];

		$advanced_fields['fonts'] = [
			'title' => [
				'css'              => array(
					'main' => '%%order_class%% .rtcl-listings-wrapper .listing-title.rtcl-listing-title a',
				),
				'important'        => 'all',
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
					'main' => '%%order_class%% .rtcl-listings-wrapper .rtcl-listings .item-price .rtcl-price .rtcl-price-amount',
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

		$advanced_fields['button'] = array(
			'button'        => array(
				'label'           => __( 'Details Button', 'rtcl-elementor-builder' ),
				'css'             => array(
					'main' => '%%order_class%% .rtcl-listings-wrapper .rtin-content-area .rtin-details-button',
				),
				'important' => 'all',
				'use_alignment'   => false,
				'border_width'    => array(
					'default' => '2px',
				),
				'box_shadow'      => array(
					'css' => array(
						'main' => '%%order_class%% ##review-form #respond .form-submit .btn.btn-primary',
					),
				),
				'margin_padding'  => array(
					'css' => array(
						'important' => 'all',
					),
				),
				'toggle_priority' => 80,
				'toggle_slug'     => 'button',
			),
		);

		return $advanced_fields;
	}


	/**
	 * Argument Setings.
	 *
	 * @return array
	 */
	public static function widget_query_args($settings) {

		$categories_list   = isset( $settings['rtcl_listings_by_categories'] ) && ! empty( $settings['rtcl_listings_by_categories'] ) ? $settings['rtcl_listings_by_categories'] : [];
		$location_list     = isset( $settings['rtcl_locations'] ) && ! empty( $settings['rtcl_locations'] ) ? $settings['rtcl_locations'] : [];
		$orderby           = isset( $settings['rtcl_orderby'] ) && ! empty( $settings['rtcl_orderby'] ) ? $settings['rtcl_orderby'] : 'date';
		$order             = isset( $settings['rtcl_order'] ) && ! empty( $settings['rtcl_order'] ) ? $settings['rtcl_order'] : 'desc';
		$listings_per_page = isset( $settings['rtcl_listing_per_page'] ) && ! empty( $settings['rtcl_listing_per_page'] ) ? $settings['rtcl_listing_per_page'] : '5';
		$promotion_in      = isset( $settings['rtcl_listings_promotions'] ) && ! empty( $settings['rtcl_listings_promotions'] ) ? $settings['rtcl_listings_promotions'] : [];
		$promotion_not_in  = isset( $settings['rtcl_listings_promotions_not_in'] ) && ! empty( $settings['rtcl_listings_promotions_not_in'] ) ? $settings['rtcl_listings_promotions_not_in'] : [];

		$categories_children = isset( $settings['rtcl_listings_categories_include_children'] ) && ! empty( $settings['rtcl_listings_categories_include_children'] ) ? true : false;
		$location_children   = isset( $settings['rtcl_listings_location_include_children'] ) && ! empty( $settings['rtcl_listings_location_include_children'] ) ? true : false;
		$listing_type        = isset( $settings['rtcl_listing_types'] ) && ! empty( $settings['rtcl_listing_types'] ) ? $settings['rtcl_listing_types'] : 'all';

		$meta_queries      = [];
		$the_args          = [
			'post_type'      => rtcl()->post_type,
			'posts_per_page' => $listings_per_page,
			'post_status'    => 'publish',
			'tax_query'      => [ // phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_tax_query
				'relation' => 'AND',
			],
		];
		$the_args['paged'] = Pagination::get_page_number();

		if ( ! empty( $order ) && ! empty( $orderby ) ) {

			switch ( $orderby ) {
				case 'price':
					$the_args['meta_key'] = $orderby; // phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_meta_key	
					$the_args['orderby']  = 'meta_value_num';
					$the_args['order']    = $order;
					break;
				case 'views':
					$the_args['meta_key'] = '_views'; // phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_meta_key	
					$the_args['orderby']  = 'meta_value_num';
					$the_args['order']    = $order;
					break;
				case 'rand':
					$the_args['orderby'] = $orderby;
					break;
				default:
					$the_args['orderby'] = $orderby;
					$the_args['order']   = $order;
			}
		}

		if ( ! empty( $categories_list ) ) {
			$the_args['tax_query'][] = [
				'taxonomy'         => rtcl()->category,
				'terms'            => $categories_list,
				'field'            => 'term_id',
				'operator'         => 'IN',
				'include_children' => $categories_children,
			];
		}
		if ( ! empty( $location_list ) ) {
			$the_args['tax_query'][] = [
				'taxonomy'         => rtcl()->location,
				'terms'            => $location_list,
				'field'            => 'term_id',
				'operator'         => 'IN',
				'include_children' => $location_children,
			];
		}
		// Promotions filter

		$promotion_common = array_intersect( $promotion_in, $promotion_not_in );
		$promotion_in     = array_diff( $promotion_in, $promotion_common ); // Unic array


		if ( ! empty( $promotion_in ) && is_array( $promotion_in ) ) {
			$promotions = array_keys( Options::get_listing_promotions() );
			$popular_threshold = (int) Functions::get_option_item( 'rtcl_moderation_settings', 'popular_listing_threshold', 0, 'number' );
			foreach ( $promotion_in as $promotion ) {
				if(  '_views' === $promotion ){
					$meta_queries[] = [
						'key'     => '_views',
						'compare' => '>=',
						'value' => $popular_threshold,
						'type' => 'NUMERIC',
					];
				} else if ( is_string( $promotion ) && in_array( $promotion, $promotions ) ) {
					$meta_queries[] = [
						'key'     => $promotion,
						'compare' => '=',
						'value'   => 1,
					];
				}
			}
		}

		if ( ! empty( $promotion_not_in ) && is_array( $promotion_not_in ) ) {
			$promotions = array_keys( Options::get_listing_promotions() );
			$popular_threshold = (int) Functions::get_option_item( 'rtcl_moderation_settings', 'popular_listing_threshold', 0, 'number' );
			foreach ( $promotion_not_in as $promotion ) {
				if(  '_views' === $promotion ){
					$meta_queries[] = [
						'key'     => '_views',
						'compare' => '<',
						'value' => $popular_threshold,
						'type' => 'NUMERIC',
					];
				} else if ( is_string( $promotion ) && in_array( $promotion, $promotions ) ) {
					$meta_queries[] = [
						'relation' => 'OR',
						[
							'key'     => $promotion,
							'compare' => '!=',
							'value'   => 1,
						],
						[
							'key'     => $promotion,
							'compare' => 'NOT EXISTS',
						],
					];
				}
			}
		}

		// Listing type filter.
		// TODO: Multiple select option needed.
		if ( $listing_type && in_array( $listing_type, array_keys( Functions::get_listing_types() ) ) && ! Functions::is_ad_type_disabled() ) {
			$meta_queries[] = [
				'key'     => 'ad_type',
				'value'   => $listing_type,
				'compare' => '=',
			];
		}

		$count_meta_queries = count( $meta_queries );
		if ( $count_meta_queries ) {
			// phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_meta_query
			$the_args['meta_query'] = ( $count_meta_queries > 1 ) ? array_merge( [ 'relation' => 'AND' ], $meta_queries ) : $meta_queries;
		}

		return $the_args;
	}

	/**
	 * Widget result.
	 *
	 * @return object
	 */
	public static function widget_results($settings) {
		$args     = self::widget_query_args($settings);
		$store = rtclStore()->factory->get_store( Fns::last_store_id() );
		$args['author'] = $store->owner_id();
		add_filter( 'excerpt_length', function ( $length ) use ( $settings ) {
			return ! empty( $settings['rtcl_content_limit'] ) ? $settings['rtcl_content_limit'] : $length;
		} );

		add_filter( 'excerpt_more', '__return_empty_string' );

		// The Query.
		$loop_obj = new \WP_Query( $args );

		return $loop_obj;
	}
	public static function pagination_args($args) {
			$args['base'] = esc_url_raw( add_query_arg( 'listing-page', '%#%' ) );
		return $args;
	}

	public static function get_content( $settings ) {

		ob_start(); // Start output buffering
		$settings['rtcl_show_price_unit'] = $settings['rtcl_show_price_unit'] === 'on' ? true : false;
		$settings['rtcl_show_price_type'] = $settings['rtcl_show_price_type'] === 'on' ? true : false;

		$settings['rtcl_show_details_button'] = $settings['rtcl_show_details_button'] === 'on' ? true : false;
		$settings['rtcl_action_button_layout'] = $settings['rtcl_action_button_layout'] === 'on' ? true : false;
		$settings['rtcl_listing_pagination'] = $settings['rtcl_listing_pagination'] === 'on' ? true : false;
		$settings['rtcl_show_title'] = $settings['rtcl_show_title'] === 'on' ? true : false;
		$settings['rtcl_show_image'] = $settings['rtcl_show_image'] === 'on' ? true : false;
		$settings['rtcl_show_description'] = $settings['rtcl_show_description'] === 'on' ? true : false;
		$settings['rtcl_show_labels'] = $settings['rtcl_show_labels'] === 'on' ? true : false;
		$settings['rtcl_show_date'] = $settings['rtcl_show_date'] === 'on' ? true : false;
		$settings['rtcl_show_location'] = $settings['rtcl_show_location'] === 'on' ? true : false;
		$settings['rtcl_show_category'] = $settings['rtcl_show_category'] === 'on' ? true : false;
		$settings['rtcl_show_price'] = $settings['rtcl_show_price'] === 'on' ? true : false;
		$settings['rtcl_show_price_unit'] = $settings['rtcl_show_price_unit'] === 'on' ? true : false;
		$settings['rtcl_show_price_type'] = $settings['rtcl_show_price_type'] === 'on' ? true : false;
		$settings['rtcl_show_favourites'] = $settings['rtcl_show_favourites'] === 'on' ? true : false;
		$settings['rtcl_show_compare'] = $settings['rtcl_show_compare'] === 'on' ? true : false;
		$settings['rtcl_show_quick_view'] = $settings['rtcl_show_quick_view'] === 'on' ? true : false;
		$settings['rtcl_show_custom_fields'] = $settings['rtcl_show_custom_fields'] === 'on' ? true : false;
		$settings['rtcl_show_phone'] = $settings['rtcl_show_phone'] === 'on' ? true : false;
		$settings['rtcl_show_types'] = $settings['rtcl_show_types'] === 'on' ? true : false;
		$settings['rtcl_show_views'] = $settings['rtcl_show_views'] === 'on' ? true : false;
		$settings['rtcl_show_user'] = $settings['rtcl_show_user'] === 'on' ? true : false;
		$settings['rtcl_listings_categories_include_children'] 	= $settings['rtcl_listings_categories_include_children'] === 'on' ? true : false;
		$settings['rtcl_listings_location_include_children'] 	= $settings['rtcl_listings_location_include_children'] === 'on' ? true : false;
		
		if( $settings['rtcl_listing_pagination'] ) {
			add_filter( 'rtcl_pagination_args', [StoreListing::class, 'pagination_args'],99,1 );
		}
		
		if ( ! $settings['rtcl_show_price_unit'] ) {
			remove_filter( 'rtcl_price_meta_html', [ AppliedBothEndHooks::class, 'add_price_unit_to_price' ], 10, 3 );
		}
		if ( ! $settings['rtcl_show_price_type'] ) {
			remove_filter( 'rtcl_price_meta_html', [ AppliedBothEndHooks::class, 'add_price_type_to_price' ], 20, 3 );
		}

		add_action( 'rtcl_listing_badges', [ TemplateHooks::class, 'listing_featured_badge' ], 20 );

		if ( rtcl()->has_pro() ) {
			add_action( 'rtcl_listing_badges', [ TemplateHooksPro::class, 'listing_popular_badge' ], 30 );
		}

		$the_loops = self::widget_results( $settings );
		$view      = 'list';
		$style     = 'style-1';

		if ( 'list' === $settings['rtcl_listings_view'] ) {
			$style = $settings['rtcl_listings_style'] ? $settings['rtcl_listings_style'] : 'style-1';
		}

		if ( 'grid' === $settings['rtcl_listings_view'] ) {
			$view  = 'grid';
			$style = $settings['rtcl_listings_grid_style'] ? $settings['rtcl_listings_grid_style'] : 'style-1';
		}

		$template_style = 'divi/store-single/listing-ads/' . $view . '/' . $style;

		$data = [
			'template'              => $template_style,
			'view'                  => $view,
			'style'                 => $style,
			'instance'              => $settings,
			'the_loops'             => $the_loops,
			'template_path' 		=> Fns::get_plugin_template_path(),
		];
		if("style-1" !== $settings['rtcl_listings_style']){
			$data['template_path'] = rtclPro()->get_plugin_template_path();
			$data['template'] = 'elementor/listing-ads/' . $view . '/' . $style;;
		}
		if("style-1" !== $settings['rtcl_listings_grid_style']){
			$data['template_path'] = rtclPro()->get_plugin_template_path();
			$data['template'] = 'elementor/listing-ads/' . $view . '/' . $style;;
		}
		if ( $the_loops->found_posts ) {
			Functions::get_template( $data['template'], $data, '', $data['template_path'] );
		} else if ( ! empty( $settings['rtcl_no_listing_text'] ) ) {
			echo '<h3>' . esc_html( $settings['rtcl_no_listing_text'] ) . '</h3>';
		}

		wp_reset_postdata();

		// Re-add removed filters
		if ( ! $settings['rtcl_show_price_unit'] ) {
			add_filter( 'rtcl_price_meta_html', [ AppliedBothEndHooks::class, 'add_price_unit_to_price' ], 10, 3 );
		}
		if ( ! $settings['rtcl_show_price_type'] ) {
			add_filter( 'rtcl_price_meta_html', [ AppliedBothEndHooks::class, 'add_price_type_to_price' ], 20, 3 );
		}

		return ob_get_clean(); // Return the buffered content
	}


	public function render( $unprocessed_props, $content, $render_slug )
	{
		$settings = $this->props;
		$this->render_css( $render_slug );
		return self::get_content($settings);
	}

	protected function render_css( $render_slug ) {
		$wrapper = '%%order_class%% .rtcl-widget-listing-item';

		// ✅ Badge Styles (color + bg-color)
		$badge_styles = [
			[
				'class' => 'rtcl-listing-meta-data li i',
				'text_color' => 'rtcl_icon_color',
			],
			[
				'class' => 'item-price.listing-price',
				'bg_color' => 'rtcl_price_bg_color',
			],
		];

		foreach ( $badge_styles as $badge ) {
			$selector = "$wrapper .{$badge['class']}";
			$text_color = $this->props[ $badge['text_color']?? '' ] ?? '';
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