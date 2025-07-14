<?php

use Rtcl\Helpers\Functions;
use Rtcl\Helpers\Functions as RtclFunctions;
use RtclMarketplace\Admin\AdminHooks;
use RtclMarketplace\Helpers\Functions as MarketplaceFunctions;
use RtclMarketplace\Helpers\Installer;
use RtclMarketplace\Helpers\ThemeSupport;
use RtclMarketplace\Hooks\ActionHooks;
use RtclMarketplace\Hooks\AjaxHooks;
use RtclMarketplace\Hooks\FilterHooks;
use RtclMarketplace\Models\Dependencies;

require_once RTCL_MARKETPLACE_PLUGIN_PATH . 'vendor/autoload.php';

/**
 * RtclMarketplace class
 */
final class RtclMarketplace {
	private static string $suffix;
	private static string $version;
	private $dependency;
	private static $singleton = false;

	/**
	 * Create an inaccessible constructor.
	 */
	private function __construct() {
		self::$suffix     = ( defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ) ? '' : '.min';
		self::$version    = ( defined( 'WP_DEBUG' ) && WP_DEBUG ) ? time() : RTCL_MARKETPLACE_VERSION;
		$this->dependency = Dependencies::getInstance();

		$this->init();
		$this->load_scripts();
	}

	/**
	 * Fetch an instance of the class.
	 */
	final public static function getInstance() {
		if ( self::$singleton === false ) {
			self::$singleton = new self();
		}

		return self::$singleton;
	}

	/**
	 * Initialize necessary things
	 */
	protected function init(): void {
		$this->define_constants();
		$this->load_language();
		$this->hooks();
	}

	private function load_scripts(): void {
		if ( $this->dependency->check() ) {
			add_action( 'wp_enqueue_scripts', [ __CLASS__, 'front_end_script' ], 99 );
			add_action( 'admin_enqueue_scripts', [ __CLASS__, 'admin_script' ], 99 );
			add_action( 'admin_enqueue_scripts', [ __CLASS__, 'wc_order_script' ], 99 );

			add_action( 'wp_enqueue_scripts', [ __CLASS__, 'load_common_script' ] );
			add_action( 'admin_enqueue_scripts', [ __CLASS__, 'load_common_script' ] );
		}
	}

	private function define_constants(): void {
		if ( ! defined( 'RTCL_MARKETPLACE_URL' ) ) {
			define( 'RTCL_MARKETPLACE_URL', plugins_url( '', RTCL_MARKETPLACE_PLUGIN_FILE ) );
		}
		if ( ! defined( 'RTCL_MARKETPLACE_SLUG' ) ) {
			define( 'RTCL_MARKETPLACE_SLUG', basename( dirname( RTCL_MARKETPLACE_PLUGIN_FILE ) ) );
		}
		if ( ! defined( 'RTCL_MARKETPLACE_PLUGIN_DIRNAME' ) ) {
			define( 'RTCL_MARKETPLACE_PLUGIN_DIRNAME', dirname( plugin_basename( RTCL_MARKETPLACE_PLUGIN_FILE ) ) );
		}
		if ( ! defined( 'RTCL_MARKETPLACE_PLUGIN_BASENAME' ) ) {
			define( 'RTCL_MARKETPLACE_PLUGIN_BASENAME', plugin_basename( RTCL_MARKETPLACE_PLUGIN_FILE ) );
		}
	}

	public function load_language(): void {
		load_plugin_textdomain( 'rtcl-marketplace', false, trailingslashit( RTCL_MARKETPLACE_PLUGIN_DIRNAME ) . 'languages' );
	}

	private function hooks(): void {
		if ( $this->dependency->check() ) {
			ActionHooks::init();
			FilterHooks::init();
			ThemeSupport::init();

			if ( rtcl()->is_request( 'ajax' ) ) {
				AjaxHooks::init();
			}

			if ( rtcl()->is_request( 'admin' ) ) {
				AdminHooks::init();
			}

			do_action( 'rtcl_marketplace_loaded', $this );
		}
	}

	public static function front_end_script(): void {
		wp_register_style(
			'rtcl-marketplace-frontend',
			RTCL_MARKETPLACE_URL . '/assets/css/frontend.css',
			[
				'rtcl-public',
			],
			self::$version
		);
		wp_enqueue_style( 'rtcl-marketplace-frontend' );
		wp_register_script( 'rtcl-marketplace-frontend', RTCL_MARKETPLACE_URL . '/assets/js/frontend.js', [ 'jquery' ], self::$version );
		if ( Functions::is_account_page() ) {
			wp_enqueue_script( 'rtcl-marketplace-frontend' );
		}
	}

	public static function admin_script(): void {
		if ( ! isset( $_GET['page'] ) ) {
			return;
		}

		if ( 'rtcl-settings' === $_GET['page'] || 'rtcl-marketplace-payouts' === $_GET['page'] || 'rtcl-marketplace-commission' === $_GET['page'] ) {
			wp_enqueue_style( 'rtcl-admin' );
			wp_enqueue_style( 'rtcl-marketplace-admin', RTCL_MARKETPLACE_URL . '/assets/css/admin.css', [ 'rtcl-admin' ], self::$version );
			wp_enqueue_script( 'rtcl-common' );
			wp_enqueue_script( 'rtcl-admin' );
			wp_enqueue_script( 'rtcl-marketplace-admin', RTCL_MARKETPLACE_URL . '/assets/js/admin.js', [ 'rtcl-admin' ], self::$version, true );
		}
	}

	public static function wc_order_script() {
		global $post_type;

		if ( $post_type === 'shop_order' || ( isset( $_GET['page'] ) && 'wc-orders' == $_GET['page'] ) ) {
			wp_enqueue_style( 'rtcl-marketplace-admin', RTCL_MARKETPLACE_URL . '/assets/css/admin.css', [ 'rtcl-admin' ], self::$version );
		}
	}

	/**
	 * Load Admin Assets
	 *
	 * @return void
	 */
	public static function load_common_script(): void {
		global $pagenow, $post_type;
		$is_listing_edit = in_array( $pagenow, [ 'edit.php', 'post.php', 'post-new.php' ] ) && rtcl()->post_type == $post_type;

		if ( Functions::is_listing_form_page() || $is_listing_edit ) {
			wp_enqueue_media();
			wp_enqueue_script( 'media-upload' );
			wp_enqueue_style( 'rtcl-marketplace-common', RTCL_MARKETPLACE_URL . '/assets/css/common.css', '', self::$version );
			wp_enqueue_script( 'jquery-ui-draggable' );
			wp_enqueue_script(
				'rtcl-marketplace-common',
				RTCL_MARKETPLACE_URL . '/assets/js/common.js',
				[ 'media-upload', 'jquery-ui-draggable' ],
				self::$version,
				true
			);
			$max_size = intval( RtclFunctions::get_option_item( 'rtcl_marketplace_settings', 'maximum_download_size', 1024, 'number' ) );
			wp_localize_script(
				'rtcl-marketplace-common',
				'rtclMarketPlace',
				[
					'ajaxurl'       => admin_url( 'admin-ajax.php' ),
					'nonce'         => wp_create_nonce( 'file_upload_nonce' ),
					'allow_format'  => MarketplaceFunctions::get_allow_file_format(),
					'max_file_size' => $max_size,
				]
			);
		}
	}

	/**
	 * @return string
	 */
	public function get_plugin_template_path() {
		return $this->plugin_path() . '/templates/';
	}

	/**
	 * Get the plugin path.
	 *
	 * @return string
	 */
	public function plugin_path() {
		return untrailingslashit( plugin_dir_path( RTCL_MARKETPLACE_PLUGIN_FILE ) );
	}
}

function rtcl_marketplace() {
	return RtclMarketplace::getInstance();
}

rtcl_marketplace();

register_activation_hook( RTCL_MARKETPLACE_PLUGIN_FILE, [ Installer::class, 'activate' ] );
register_deactivation_hook( RTCL_MARKETPLACE_PLUGIN_FILE, [ Installer::class, 'deactivate' ] );
