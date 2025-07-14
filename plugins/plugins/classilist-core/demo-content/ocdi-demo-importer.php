<?php
/**
 * @author  RadiusTheme
 * @since   1.2
 * @version 1.15
 */

namespace radiustheme\ClassiList_Core;

use \WPCF7_ContactFormTemplate;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

require_once CLASSILIST_CORE_BASE_DIR . 'demo-content/RtclCategoriesTermUpdater.php';
require_once CLASSILIST_CORE_BASE_DIR . 'demo-content/RtclLocationTermUpdater.php';

class Demo_Importer_OCDI {

	public function __construct() {
		add_filter( 'ocdi/import_files', array( $this, 'demo_config' ) );
		add_filter( 'ocdi/before_content_import', array( $this, 'before_import' ) );
		add_filter( 'ocdi/after_import', array( $this, 'after_import' ) );
		add_action( 'ocdi/before_widgets_import', array( $this, 'remove_default_widgets' ) );
		add_filter( 'ocdi/disable_pt_branding', '__return_true' );
		add_action( 'init', array( $this, 'rewrite_flush_check' ) );
		add_action( 'init', array( $this, 'custom_terms_updater' ) );
	}

	public function demo_config() {
		$demos_array = array(
			'demo1'  => array(
				'title'        => __( 'Home 1', 'classilist-core' ),
				'page'         => __( 'Home 1', 'classilist-core' ),
				'screenshot'   => CLASSILIST_CORE_BASE_URL . 'demo-content/screenshots/01.jpg',
				'preview_link' => 'https://radiustheme.com/demo/wordpress/themes/classilist/',
			),
			'demo2'  => array(
				'title'        => __( 'Home 2', 'classilist-core' ),
				'page'         => __( 'Home 2', 'classilist-core' ),
				'screenshot'   => CLASSILIST_CORE_BASE_URL . 'demo-content/screenshots/02.jpg',
				'preview_link' => 'https://radiustheme.com/demo/wordpress/themes/classilist/home-2/',
			),
			'demo3'  => array(
				'title'        => __( 'Home 3', 'classilist-core' ),
				'page'         => __( 'Home 3', 'classilist-core' ),
				'screenshot'   => CLASSILIST_CORE_BASE_URL . 'demo-content/screenshots/03.png',
				'preview_link' => 'https://radiustheme.com/demo/wordpress/themes/classilist/home-3/',
			),
			'demo4'  => array(
				'title'        => __( 'Home 4', 'classilist-core' ),
				'page'         => __( 'Home 4', 'classilist-core' ),
				'screenshot'   => CLASSILIST_CORE_BASE_URL . 'demo-content/screenshots/04.png',
				'preview_link' => 'https://radiustheme.com/demo/wordpress/themes/classilist/home-4/',
			),
		);

		$config       = array();
		$import_path  = trailingslashit( CLASSILIST_CORE_DEMO_CONTENT ) . 'sample-data/';

		foreach ( $demos_array as $key => $demo ) {
			$config[] = array(
				'import_file_id'               => $key,
				'import_page_name'             => $demo['page'],
				'import_file_name'             => $demo['title'],
				'local_import_file'            => $import_path . 'contents.xml',
				'local_import_widget_file'     => $import_path . 'widgets.wie',
				'local_import_customizer_file' => $import_path . 'customizer.dat',
				'local_import_redux'           => array(
					array(
						'file_path'   => $import_path . 'redux-options.json',
						'option_name' => 'classilist',
					),
					array(
						'file_path'   => $import_path . 'redux-advertisements.json',
						'option_name' => 'classilist_ads',
					),
				),
				'import_preview_image_url'     => $demo['screenshot'],
				'preview_url'                  => $demo['preview_link'],
			);
		}

		return $config;
	}

	public function before_import( $selected_import ) {
		$this->remove_default_rtcl_pages();
		$this->remove_rtcl_builder_default_form();
		$this->import_rtcl_builder_forms();
	}

	public function after_import( $selected_import ){
		$this->assign_menu( $selected_import['import_file_id'] );
		$this->assign_frontpage( $selected_import );
		$this->update_contact_form_sender_email();
		$this->update_permalinks();
		$this->update_rtcl_options();
		$this->update_ajax_filter_options();
		update_option( 'classilist_ocdi_importer_rewrite_flash', true );
	}

	private function assign_menu( $demo ) {
		$primary = get_term_by( 'name', 'Main Menu', 'nav_menu' );

		set_theme_mod( 'nav_menu_locations', array(
			'primary' => $primary->term_id,
		) );
	}

	private function assign_frontpage( $selected_import ) {
		$blog_page  = $this->get_page_by_title( 'Blog' );
		$front_page = $this->get_page_by_title( $selected_import['import_page_name'] );

		update_option( 'show_on_front', 'page' );
		update_option( 'page_on_front', $front_page );
		update_option( 'page_for_posts', $blog_page );
	}

	private function update_contact_form_sender_email() {
		$form1 = $this->get_page_by_title( 'Contact', OBJECT, 'wpcf7_contact_form' );

		$forms = array( $form1 );
		foreach ( $forms as $form ) {
			if ( ! $form ) {
				continue;
			}
			$cf7id = $form->ID;
			$mail = get_post_meta( $cf7id, '_mail', true );
			if ( class_exists( 'WPCF7_ContactFormTemplate' ) ) {
				$pattern        = "/<[^@\s]*@[^@\s]*\.[^@\s]*>/"; // <email@email.com>
				$replacement    = '<' . WPCF7_ContactFormTemplate::from_email() . '>';
				$mail['sender'] = preg_replace( $pattern, $replacement, $mail['sender'] );
			}
			update_post_meta( $cf7id, '_mail', $mail );
		}
	}

	private function update_permalinks() {
		update_option( 'permalink_structure', '/%postname%/' );
	}

	private function update_rtcl_options() {
		$listings     = $this->get_page_by_title( 'All Ads' );
		$listing_form = $this->get_page_by_title( 'Post an Ad' );
		$myaccount    = $this->get_page_by_title( 'My Account' );
		$checkout     = $this->get_page_by_title( 'Checkout' );

		$data = array(
			'rtcl_general_settings'    => array(
				'listings_per_page' => 9,
				'default_view'      => 'list',
				'currency_position' => 'left',
			),
			'rtcl_moderation_settings' => array(
				'listing_duration'       => 0,
				'new_listing_threshold'  => 300,
				'listing_bump_up_label'  => '',
				'display_options'        => array(
					'category',
					'location',
					'date',
					'views',
					'price',
					'new',
					'popular',
					'excerpt'
				),
				'display_options_detail' => array(
					'location',
					'date',
					'user',
					'price',
					'views',
					'top',
					'featured',
					'new',
					'popular'
				),
				'has_comment_form'       => 'yes',
				'enable_review_rating'   => 'yes',
				'enable_update_rating'   => 'yes',
				'has_map'   => 'yes',
			),
			'rtcl_advanced_settings'   => array(
				'permalink'    => 'listing',
				'listings'     => $listings,
				'listing_form' => $listing_form,
				'myaccount'    => $myaccount,
				'checkout'     => $checkout,
			),
			'rtcl_style_settings'      => array(
				'new' => '#e77a1e',
			)
		);

		foreach ( $data as $key => $value ) {
			$defaults = get_option( $key, array() );
			$args     = wp_parse_args( $value, $defaults );
			update_option( $key, $args );
		}
	}

	private function update_ajax_filter_options() {
		$filter_options = array(
			'ajax-filter' => array(
				'name'  => 'Listing filter Widget',
				'items' => [
					[
						'id'         => 'category',
						'title'      => 'Category',
						'type'       => 'What are you looking for...',
						'hide_empty' => '1',
						'show_count' => '1',
					],
					[
						'id'         => 'location',
						'title'      => 'Location',
						'type'       => 'What are you looking for...',
						'hide_empty' => '1',
						'show_count' => '1',
					],
					[
						'id'        => 'radius_filter',
						'title'     => 'Radius Filter',
					],
					[
						'id'        => 'price_range',
						'title'     => 'Price Range',
						'max_price' => '50000',
						'step'      => '5',
					],
					[
						'id'    => 'rating',
						'title' => 'Rating',
					],
				],
			),
		);

		update_option( 'rtcl_filter_settings', $filter_options );
	}

	private function remove_default_rtcl_pages() {
		$listings     = $this->get_page_by_title( 'Listings', 'page' );
		$listing_form = $this->get_page_by_title( 'Listing Form', 'page' );
		$myaccount    = $this->get_page_by_title( 'My Account' );
		$checkout     = $this->get_page_by_title( 'Checkout' );

		if ( $listings ) {
			wp_delete_post( $listings, true );
		}
		if ( $listing_form ) {
			wp_delete_post( $listing_form, true );
		}
		if ( $myaccount ) {
			wp_delete_post( $myaccount, true );
		}
		if ( $checkout ) {
			wp_delete_post( $checkout, true );
		}
	}

	private function import_rtcl_builder_forms() {

		$formFile = trailingslashit( CLASSILIST_CORE_DEMO_CONTENT ) . 'sample-data/rtclform.json';
		$fileExists = file_exists( $formFile );

		if ( $fileExists ) {
			$data  = file_get_contents( $formFile );
			$forms = json_decode( $data, true );

			if ( $forms && is_array( $forms ) ) {
				foreach ( $forms as $formItem ) {
					\Rtcl\Models\Form\Form::query()->insert( $formItem );
				}
			}
		}
	}

	private function remove_rtcl_builder_default_form() {
		global $wpdb;
		$table = $wpdb->prefix . 'rtcl_forms';
		$wpdb->query( "TRUNCATE TABLE $table" );
	}

	public function rewrite_flush_check() {
		if ( get_option( 'classilist_ocdi_importer_rewrite_flash' ) == true ) {
			flush_rewrite_rules();
			delete_option( 'classilist_ocdi_importer_rewrite_flash' );
		}
	}

	private function get_page_by_title( $title, $post_type = 'page' ) {
		$page = get_posts( array(
			'post_type'   => $post_type,
			'name'        => sanitize_title( $title ),
			'post_status' => 'publish',
			'numberposts' => 1
		) );

		if ( $page ) {
			$page = $page[0];
			return $page->ID;
		} else {
			return null;
		}
	}

	public function remove_default_widgets( $selected_import ) {
		delete_option( 'sidebars_widgets' );
	}

	public function custom_terms_updater() {
//		new \RtclCategoriesTermUpdater();
//		new \RtclLocationTermUpdater();
	}
}

new Demo_Importer_OCDI;