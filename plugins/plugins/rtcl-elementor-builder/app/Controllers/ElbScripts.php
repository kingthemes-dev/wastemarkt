<?php

/**
 * Main Elementor ElbScripts Class
 *
 * The main class that initiates all scripts.
 *
 * @package  RTCL_Elementor_Builder
 * @since    1.0.0
 */

namespace RtclElb\Controllers;

use RtclElb\Traits\Singleton;
use RtclElb\Traits\ELTempleateBuilderTraits;
use RtclElb\Helpers\Fns;
use radiustheme\Classima\Scripts;

/**
 * Main Elementor ElbScripts Class
 */
class ElbScripts {
	/*
     * Template builder related traits.
     */
	use ELTempleateBuilderTraits;
	/**
	 * Singleton Function.
	 */
	use Singleton;
	/**
	 * Suffix string.
	 *
	 * @var string
	 */
	private $suffix;
	/**
	 * Plugin Version.
	 *
	 * @var string
	 */
	private $version;
	/**
	 * Ajax Url
	 *
	 * @var string
	 */
	/**
	 * Initial Function.
	 *
	 * @return void
	 */
	public function init() {
		$this->suffix  = (defined('SCRIPT_DEBUG') && SCRIPT_DEBUG) ? '' : '.min';
		$this->version = (defined('WP_DEBUG') && WP_DEBUG) ? time() : RTCL_ELB_VERSION;
		add_action('admin_enqueue_scripts', [$this, 'register_admin_scripts']);
		add_action('wp_enqueue_scripts', [$this, 'register_frontend_scripts']);

		add_action('enqueue_block_editor_assets', [$this, 'block_editor_scripts']);
		//both frontend and backend enqueue file
		add_action('enqueue_block_assets', [$this, 'block_frontend_backend_assets']);

		add_action('elementor/editor/before_enqueue_scripts', [ $this, 'elementor_editor_styles' ], 90);
	}

	public function block_frontend_backend_assets() {
		//block frontend css
		wp_enqueue_style(
			'rtcl-blocks-frontend-css',
			rtclElb()->assets_url('css/frontend-block.css'),
			array(),
			$this->version
		);

		//for theme style support
		$theme_css_url =  apply_filters('rtcl_block_editor_theme_style_url', get_stylesheet_directory_uri() . '/assets/css/style.css');
		wp_enqueue_style('rtcl-block-theme-style', $theme_css_url);

		wp_enqueue_style('rtcl-public');
		wp_enqueue_style('rtcl-bootstrap');

		if (is_admin()) {

			if (rtcl()->has_pro()) {
				wp_register_style('rtcl-pro-public', rtclPro()->get_assets_uri("css/public".$this->suffix.".css"), ['rtcl-public'], $this->version);
				wp_enqueue_style('rtcl-pro-public');
			}

			//component css
			wp_enqueue_style(
				'rtcl-blocks-component-css',
				rtclElb()->assets_url('blocks/main.css'),
				array(),
				$this->version
			);
		}

		if ('Classima' == wp_get_theme()->get('Name') && is_admin()) {
			$classimaAssets = new Scripts();
			$classimaAssets->register_scripts();
			wp_enqueue_style('classima-listing');
			wp_enqueue_style('bootstrap');
			wp_enqueue_style('classima-style');
		}

		//for map load in block editor
		wp_enqueue_script('rtcl-google-map');
		wp_enqueue_script('rtcl-map');

		$localize_obj = [
			'plugin'  => RTCL_ELB_PLUGIN_URL,
			'rtclURL' => RTCL_URL,
			'rtclPro' => rtcl()->has_pro(),
			'ajaxurl'    => admin_url('admin-ajax.php'),
			'siteUrl'   => site_url(),
			'admin_url'  => admin_url(),
			'rtcl_block_nonce' => wp_create_nonce('rtcl-block-nonce'),
			'builderSinglePage' => self::is_builder_page_single(),
			'builderArchivePage' => self::is_builder_page_archive(),
			'builderStorePage' => self::is_store_page_builder(),
			'builderSingleId' => self::builder_page_id('single'),
			'builderArchiveId' => self::builder_page_id('archive'),
			'builderStoreId' => self::builder_page_id('store-single'),
			'storePluginActive' => defined('RTCL_STORE_VERSION') && rtcl()->has_pro() ?? false,
			'bookingPluginActive' => defined('RTCL_BOOKING_VERSION') ?? false,
			'radiusBlocksActive' => defined('RTRB_VERSION') ?? false,
			'lastPostId' => Fns::rtcl_last_post_id(),
			'lastStoreId' => Fns::last_store_id(),
			'themeName' => wp_get_theme()->get('Name'),
			'apiKey' => Fns::rtcl_get_api_key(),
			'restApiAllow' => Fns::rtcl_get_restapi_allow(),

		];
		wp_localize_script(
			'rtcl-blocks-editor-script',
			'rtclBlockParams',
			apply_filters('rtcl_localize_script', $localize_obj)
		);

		//store css 
		if (defined('RTCL_STORE_VERSION')) {
			wp_register_style('rtcl-store-public', RTCL_STORE_URL . '/assets/css/store-public.css', ['rtcl-public'], $this->version);
			wp_enqueue_style('rtcl-store-public');
			wp_register_style('rtcl-store-builder', rtclElb()->assets_url('css/rtcl-builder-store' . $this->suffix . '.css'), ['rtcl-store-public'], $this->version);
			wp_enqueue_style('rtcl-store-builder');
		}

		//booking css
		if (defined('RTCL_BOOKING_VERSION')) {
			wp_register_style('rtcl-booking', RTCL_BOOKING_URL . '/assets/css/booking.css', [
				'rtcl-public'
			], $this->version);
			wp_enqueue_style('rtcl-booking');
		}
	}

	public function block_editor_scripts() {
		$script_block_asset_path = RTCL_ELB_PATH . '/assets/blocks/main.asset.php';

		$script_block_dependencies = require($script_block_asset_path);

		$blocks_dependencies_thirdparty = array(
			'rtcl-gb-blocks-js'
		);

		$blocks_dependencies_marged = array_merge(
			$script_block_dependencies['dependencies'],
			$blocks_dependencies_thirdparty
		);

		/**
		 * Register all block depecdencies
		 */
		wp_enqueue_script(
			'rtcl-blocks-editor-script',
			rtclElb()->assets_url('blocks/main.js'),
			$blocks_dependencies_marged,
			$script_block_dependencies['version'],
			true
		);
	}

	/**
	 * Admin related script.
	 *
	 * @return void
	 */
	public function register_admin_scripts() {
		wp_register_script('rtcl-elb-admin', rtclElb()->assets_url('js/admin' . $this->suffix . '.js'), ['jquery', 'rtcl-common'], $this->version, true);
		if (!empty($_GET['post_type']) && $_GET['post_type'] == self::$post_type_tb) {
			//add public & admin cl css
			wp_enqueue_style('rtcl-public', rtcl()->get_assets_uri("css/rtcl-public{$this->suffix}.css"), [], $this->version);
			wp_enqueue_style('rtcl-admin', rtcl()->get_assets_uri("css/rtcl-admin.min.css"), ['rtcl-bootstrap'], $this->version);

			wp_localize_script('rtcl-elb-admin', 'rtcl_el_tb', [
				'ajaxurl'              => admin_url('admin-ajax.php'),
				'loading'              => esc_html__('Loading', 'rtcl-elementor-builder'),
				rtcl()->nonceId        => wp_create_nonce(rtcl()->nonceText),
			]);
			wp_enqueue_script('rtcl-elb-admin');
		}

		wp_enqueue_style(
			'rtcl-blocks-admin-css',
			rtclElb()->assets_url('css/admin.css'),
			array(),
			$this->version
		);
	}
	/**
	 * Admin related script.
	 *
	 * @return void
	 */
	public function register_frontend_scripts() {
		wp_register_script('rtcl-block-builder-frontend', rtclElb()->assets_url('js/public' . $this->suffix . '.js'), ['jquery'], $this->version, true);
		wp_enqueue_script('rtcl-block-builder-frontend');
		wp_register_style('rtcl-store-builder', rtclElb()->assets_url('css/rtcl-builder-store' . $this->suffix . '.css'), ['rtcl-store-public'], $this->version);
		wp_enqueue_style('rtcl-el-builder-public', rtclElb()->assets_url('css/public' . $this->suffix . '.css'), [], $this->version);
	}

	public function elementor_editor_styles() {
		wp_enqueue_style('rtcl-el-builder-elementor-editor', rtclElb()->assets_url('css/elementor-editor' . $this->suffix . '.css'), [], $this->version);
		wp_enqueue_script('swiper' );
	}
	
}
