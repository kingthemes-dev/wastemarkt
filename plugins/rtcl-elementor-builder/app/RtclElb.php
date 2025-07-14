<?php

/**
 * Main initialization class.
 *
 * @package  RTCL_Elementor_Builder
 */

namespace RtclElb;

use RtclElb\Traits\Singleton;
use RtclElb\Helpers\Fns;
use RtclElb\Controllers\Ajax\ELProAjax;
use RtclElb\Controllers\Builder\TemplateBuilder;
use RtclElb\Controllers\Builder\TemplateBuilderFrontend;
use RtclElb\Controllers\Hooks\FilterHooks;
use RtclElb\Controllers\Hooks\BuilderHooks;

use RtclElb\Controllers\{AdminNotices,
	DiviController,
	ElbScripts,
	ElementorController,
	BlockController,
	StyleGenerator,
	FontLoader};

if ( ! class_exists( RtclElb::class ) ) {
	/**
	 * Main initialization class.
	 */
	final class RtclElb {

		use Singleton;

		/**
		 * Plugin path.
		 *
		 * @var string
		 */
		public $plugin_path;

		/**
		 * Class init.
		 *
		 * @return void
		 */
		public function init() {
			$dependence = AdminNotices::getInstance();
			if ( $dependence->check() ) {
				$this->singleton_controllers();
				FilterHooks::init();
				\add_action( 'init', [ $this, 'init_services' ] );
				add_action( 'after_setup_theme', [ $this, 'theme_support' ] );
				TemplateBuilderFrontend::init();
				TemplateBuilder::init();
				ELProAjax::init();
				BlockController::getInstance();
				if ( ! is_admin() ) {
					StyleGenerator::getInstance();
					BuilderHooks::getInstance();
				}
				FontLoader::getInstance();
				add_action( 'plugins_loaded', [ $this, 'on_plugins_loaded' ] );
			}
		}

		/*
		 * Add theme Support
		 * */
		public function theme_support() {
			add_theme_support( 'rtcl' );
		}

		/**
		 * Init services.
		 *
		 * @return void
		 */
		public function init_services() {

			$this->i18n();
		}

		/**
		 * Internationalization.
		 *
		 * @return void
		 */
		public function i18n() {
			load_plugin_textdomain( 'rtcl-elementor-builder', false, RTCL_ELB_LANGUAGE_PATH );
		}

		/**
		 * Init Controllers.
		 *
		 * @return bool
		 */
		public function is_rtcl_active() {
			return class_exists( Rtcl::class );
		}

		/**
		 * Controllers.
		 *
		 * @return void
		 */
		public function singleton_controllers() {
			$controllers = [
				ElbScripts::class,
			];
			if ( did_action( 'elementor/loaded' ) ) {
				$controllers[] = ElementorController::class;
			}
			$theme = wp_get_theme();
			if( defined('ET_BUILDER_PLUGIN_VERSION') || wp_get_theme()->get('Name') === 'Divi') {
				$controllers[] = DiviController::class;
			}
			Fns::instances( $controllers );
		}

		/**
		 * Actions on Plugins Loaded.
		 *
		 * @return void
		 */
		public function on_plugins_loaded() {
			\do_action( 'rtcl_elb_loaded', $this );
		}

		/**
		 * Plugin path.
		 *
		 * @return string
		 */
		public function plugin_path() {
			return untrailingslashit( plugin_dir_path( RTCL_ELB_PLUGIN_ACTIVE_FILE_NAME ) );
		}

		/**
		 * Template path
		 *
		 * @return string
		 */
		public function get_plugin_template_path() {
			return apply_filters( 'rtcl_elb_template_path', $this->plugin_path() . '/templates/elementor' );
		}

		/**
		 * Template path
		 *
		 * @return string
		 */
		public function get_plugin_block_template_path() {
			return apply_filters( 'rtcl_block_template_path', $this->plugin_path() . '/templates/blocks' );
		}


		/**
		 * Assets URL.
		 *
		 * @param string $location file location.
		 *
		 * @return string
		 */
		public function assets_url( $location = '' ) {
			return esc_url( RTCL_ELB_PLUGIN_URL . '/assets/' . $location );
		}
	}
}
