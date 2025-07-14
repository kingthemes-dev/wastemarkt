<?php
namespace  RtclElb\DiviModule\ArchiveListing;

use Rtcl\Controllers\Hooks\AppliedBothEndHooks;
use Rtcl\Controllers\Hooks\TemplateHooks as RtclTemplateHooks;
use Rtcl\Helpers\Functions;
use Rtcl\Traits\Addons\TopQueryTrait;
use RtclElb\Helpers\Fns;
use RtclElb\Widgets\WidgetQuery\ListingArchiveQuery;
use RtclPro\Controllers\Hooks\TemplateHooks as RtclProTemplateHooks;

Class ArchiveListing extends \ET_Builder_Module {
	use TopQueryTrait;
	public $slug = 'rtcl_archive_listing';
	public $vb_support = 'on';
    
	public $icon_path;
	protected $module_credits
		= [
			'author'     => 'RadiusTheme',
			'author_uri' => 'https://radiustheme.com',
		];
	public function init() {
		$this->name      = esc_html__( ' Classified Archive Listing', 'rtcl-elementor-builder' );
		$this->icon_path = plugin_dir_path( __FILE__ ) . 'icon.svg';
        $this->folder_name = 'et_pb_classified_Archive_modules';
		$this->settings_modal_toggles = [
			'general'  => [
				'toggles' => [
					'general'    => esc_html__( 'General', 'rtcl-elementor-builder' ),
					'layout'     => esc_html__( 'Layout', 'rtcl-elementor-builder' ),
					'visibility' => esc_html__( 'Visibility', 'rtcl-elementor-builder' ),
				],
			],
			'advanced' => [
				'toggles' => [
					'title'       => esc_html__( 'Title', 'rtcl-elementor-builder' ),
					'meta' => esc_html__( 'Meta', 'rtcl-elementor-builder' ),
					'price' => esc_html__( 'Price', 'rtcl-elementor-builder' ),
					'price_unit' => esc_html__( 'Price Unit', 'rtcl-elementor-builder' ),
					'category' => esc_html__( 'Category', 'rtcl-elementor-builder' ),
				],
			],
		];
	}

	public function get_fields() {
		return [
			'rtcl_listings_view'       => [
				'label'          => esc_html__( 'Default view ', 'rtcl-elementor-builder' ),
				'type'           => 'select',
				'options'        => [
					'list' => __( 'List View', 'rtcl-elementor-builder' ),
					'grid' => __( 'Grid View', 'rtcl-elementor-builder' ),
				],
				'default'        => 'list',
				'description'    => esc_html__( 'Select Breadcrumb Position to display Breadcrumb.', 'rtcl-elementor-builder' ),
				'mobile_options' => true,
				'tab_slug'       => 'general',
				'toggle_slug'    => 'general',
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
				'description'    => esc_html__( 'Select list style.', 'rtcl-elementor-builder' ),
				'mobile_options' => true,
				'tab_slug'       => 'general',
				'toggle_slug'    => 'general',
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
				'description'    => esc_html__( 'Select list style.', 'rtcl-elementor-builder' ),
				'mobile_options' => true,
				'tab_slug'       => 'general',
				'toggle_slug'    => 'general',
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
				'toggle_slug'    => 'general',
			],
			'rtcl_listings_column_tablet'       => [
				'label'          => esc_html__( 'Tab Grid Column', 'rtcl-elementor-builder' ),
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
				'default'        => '2',
				'description'    => esc_html__( 'Select column number to display listing.', 'rtcl-elementor-builder' ),
				'mobile_options' => true,
				'tab_slug'       => 'general',
				'toggle_slug'    => 'general',
			],
			'rtcl_listings_column_mobile'       => [
				'label'          => esc_html__( 'Mobile Grid Column', 'rtcl-elementor-builder' ),
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
				'default'        => '1',
				'description'    => esc_html__( 'Select column number to display listing.', 'rtcl-elementor-builder' ),
				'mobile_options' => true,
				'tab_slug'       => 'general',
				'toggle_slug'    => 'general',
			],
			'rtcl_archive_result_count'        => [
				'label'       => esc_html__( 'Result count', 'rtcl-elementor-builder' ),
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
			'rtcl_archive_catalog_ordering'        => [
				'label'       => esc_html__( 'Catalog Ordering', 'rtcl-elementor-builder' ),
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
			'rtcl_archive_view_switcher'        => [
				'label'       => esc_html__( 'View Switcher', 'rtcl-elementor-builder' ),
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
				'show_if'     => [
					'rtcl_show_favourites' => 'on',
				],
			],
			'rtcl_show_details_button'      => [
				'label'       => __('Show Details Button', 'rtcl-elementor-builder'),
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
			// computed.
			'__archive_listing'           => array(
				'type'                => 'computed',
				'computed_callback'   => array( 'RtclElb\DiviModule\ArchiveListing\ArchiveListing', 'get_content' ),
				'computed_depends_on' => array(
					'rtcl_show_details_button',
					'rtcl_listings_column_tablet',
					'rtcl_listings_column_mobile',
					'rtcl_content_limit',
					'rtcl_action_button_layout',
					'rtcl_listings_view',
					'rtcl_listings_style',
					'rtcl_listings_grid_style',
					'rtcl_listings_column',
					'rtcl_archive_result_count',
					'rtcl_archive_catalog_ordering',
					'rtcl_archive_view_switcher',
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
			],
			'price_unit' => [
				'css'              => array(
					'main' => '%%order_class%% .rtcl-listings-wrapper .rtcl-listings .item-price .rtcl-price .rtcl-price-unit-label ',
				),
				'important'        => 'all',
				'tab_slug'         => 'advanced',
				'toggle_slug'      => 'price_unit',
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
			],
			'category' => [
				'css'              => array(
					'main' => '%%order_class%% .rtcl-listings-wrapper .rtcl-listings .rtin-content-area .item-content  .category a ',
				),
				'important'        => 'all',
				'tab_slug'         => 'advanced',
				'toggle_slug'      => 'category',
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
			],
		];

		return $advanced_fields;
	}
	public static function listable_fields_arg( $args ) {
		unset( $args['meta_query'] );
		return $args;
	}
	public static function get_content($settings)
	{
		$show_price_unit = $settings['rtcl_show_price_unit'] ?? true;
		$show_price_type = $settings['rtcl_show_price_type'] ?? true;

		$settings['rtcl_show_details_button'] = $settings['rtcl_show_details_button'] === 'on' ? true : false;
		$settings['rtcl_action_button_layout'] = $settings['rtcl_action_button_layout'] === 'on' ? true : false;
		$settings['rtcl_archive_result_count'] = $settings['rtcl_archive_result_count'] === 'on' ? true : false;
		$settings['rtcl_archive_catalog_ordering'] = $settings['rtcl_archive_catalog_ordering'] === 'on' ? true : false;
		$settings['rtcl_archive_view_switcher'] = $settings['rtcl_archive_view_switcher'] === 'on' ? true : false;
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

		add_filter( 'excerpt_more', '__return_empty_string' );
		remove_action( 'rtcl_listing_badges', [ RtclTemplateHooks::class, 'listing_featured_badge' ], 20 );
		if(!$settings['rtcl_listing_pagination']){
			remove_action( 'rtcl_after_listing_loop', [ RtclTemplateHooks::class, 'pagination' ], 10 );
		}
		if ( empty( $settings['rtcl_archive_result_count'] ) ) {
			remove_action( 'rtcl_listing_loop_action', [ RtclTemplateHooks::class, 'result_count' ], 10 );
		}
		if ( empty( $settings['rtcl_archive_catalog_ordering'] ) ) {
			remove_action( 'rtcl_listing_loop_action', [ RtclTemplateHooks::class, 'catalog_ordering' ], 20 );
		}
		if ( empty( $settings['rtcl_archive_view_switcher'] ) ) {
			remove_action( 'rtcl_listing_loop_action', [ RtclProTemplateHooks::class, 'view_switcher' ], 30 );
		}
		add_filter( 'rtcl_loop_item_listable_fields', [ 'RtclElb\DiviModule\ArchiveListing\ArchiveListing', 'listable_fields_arg' ], 10, 1 );

		$rr = new ArchiveListingHelper();
		$widget_results = $rr->widget_results();
		$the_query = $widget_results['loop_obj'] ?? null;
		$instance = new self();
		$top_query_data = $instance->top_listing_query_prepared();
		$top_query = $top_query_data['top_query'] ?? [];

		$view = ! empty( $settings['rtcl_listings_view'] ) ? $settings['rtcl_listings_view'] : 'list';
		if ( isset( $_GET['view'] ) ) {
			$query_view = sanitize_key( $_GET['view'] );
			if ( in_array( $query_view, [ 'list', 'grid' ], true ) ) {
				$view = $query_view;
			}
		}
		add_filter( 'rtcl_archive_default_view', function ( $default_view ) use ( $view ) {
			return $view;
		}, 10, 1 );

		$style = $view === 'list'
			? ($settings['rtcl_listings_style'] ?? 'style-1')
			: ($settings['rtcl_listings_grid_style'] ?? 'style-1');

		$template_style = 'divi/listing-archive/listing-archive';
		$data = [
			'template'              => $template_style,
			'view'                  => $view,
			'style'                 => $style,
			'instance'              => $settings,
			'the_query'             => $the_query,
			'top_query'             => $top_query,
			'default_template_path' => Fns::get_plugin_template_path(),
		];

		$data = apply_filters( 'rtcl_el_listing_archive_data', $data );

		if ( ! $show_price_unit ) {
			remove_filter( 'rtcl_price_meta_html', [ AppliedBothEndHooks::class, 'add_price_unit_to_price' ], 10 );
		}
		if ( ! $show_price_type ) {
			remove_filter( 'rtcl_price_meta_html', [ AppliedBothEndHooks::class, 'add_price_type_to_price' ], 20 );
		}

		// 🧠 Buffer the output
		ob_start();
		Functions::get_template( $data['template'], $data, '', $data['default_template_path'] );
		$output = ob_get_clean();

		if ( ! $show_price_unit ) {
			add_filter( 'rtcl_price_meta_html', [ AppliedBothEndHooks::class, 'add_price_unit_to_price' ], 10, 3 );
		}
		if ( ! $show_price_type ) {
			add_filter( 'rtcl_price_meta_html', [ AppliedBothEndHooks::class, 'add_price_type_to_price' ], 20, 3 );
		}

		return $output;
	}

	public function render( $unprocessed_props, $content, $render_slug )
	{
		$settings = $this->props;
		$this->render_css( $render_slug );
		return self::get_content($settings);
	}

	public function render_css()
	{
		
	}
}