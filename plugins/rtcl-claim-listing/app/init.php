<?php

require_once __DIR__ . './../vendor/autoload.php';

use Rtcl\Helpers\Functions as RtclFunctions;
use RtclClaimListing\Admin\AdminHooks;
use RtclClaimListing\Api\RestApi;
use RtclClaimListing\Helpers\Functions;
use RtclClaimListing\Helpers\Installer;
use RtclClaimListing\Hooks\ActionHooks;
use RtclClaimListing\Hooks\AjaxHooks;
use RtclClaimListing\Hooks\FilterHooks;
use RtclClaimListing\Models\Dependencies;

final class RtclInitClaimListing {

	private $suffix;
	private $version;

	/**
	 * RtclInitClaimListing the singleton object.
	 */
	private static $singleton = false;

	/**
	 * Create an inaccessible constructor.
	 */
	private function __construct() {
		$this->suffix  = ( defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ) ? '' : '.min';
		$this->version = ( defined( 'WP_DEBUG' ) && WP_DEBUG ) ? time() : RTCL_CLAIM_LISTING_VERSION;

		$this->load_scripts();
		$this->init();
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
	 * Classified Listing Constructor.
	 */
	protected function init() {
		$this->define_constants();
		$this->load_language();
		$this->hooks();
	}

	private function load_scripts() {
		$dependence = Dependencies::getInstance();
		if ( $dependence->check() ) {
			add_action( 'wp_enqueue_scripts', [ $this, 'front_end_script' ], 30 );
			add_action( 'admin_enqueue_scripts', [ $this, 'load_admin_script' ] );
		}
	}

	private function define_constants() {
		if ( ! defined( 'RTCL_CLAIM_LISTING_PATH' ) ) {
			define( 'RTCL_CLAIM_LISTING_PATH', plugin_dir_path( RTCL_CLAIM_LISTING_PLUGIN_FILE ) );
		}
		if ( ! defined( 'RTCL_CLAIM_LISTING_URL' ) ) {
			define( 'RTCL_CLAIM_LISTING_URL', plugins_url( '', RTCL_CLAIM_LISTING_PLUGIN_FILE ) );
		}
		if ( ! defined( 'RTCL_CLAIM_LISTING_SLUG' ) ) {
			define( 'RTCL_CLAIM_LISTING_SLUG', basename( dirname( RTCL_CLAIM_LISTING_PLUGIN_FILE ) ) );
		}
		if ( ! defined( 'RTCL_CLAIM_LISTING_PLUGIN_DIRNAME' ) ) {
			define( 'RTCL_CLAIM_LISTING_PLUGIN_DIRNAME', dirname( plugin_basename( RTCL_CLAIM_LISTING_PLUGIN_FILE ) ) );
		}
		if ( ! defined( 'RTCL_CLAIM_LISTING_PLUGIN_BASENAME' ) ) {
			define( 'RTCL_CLAIM_LISTING_PLUGIN_BASENAME', plugin_basename( RTCL_CLAIM_LISTING_PLUGIN_FILE ) );
		}
	}

	public function load_language() {
		load_plugin_textdomain( 'rtcl-claim-listing', false, trailingslashit( RTCL_CLAIM_LISTING_PLUGIN_DIRNAME ) . 'languages' );
	}

	private function hooks() {
		$dependence = Dependencies::getInstance();
		if ( $dependence->check() ) {
			FilterHooks::init();
			ActionHooks::init();
			AjaxHooks::init();
			if ( is_admin() ) {
				AdminHooks::init();
			}
			( new RestApi() )->init();
			do_action( 'rtcl_claim_listing_loaded', $this );
		}
	}

	public function front_end_script() {
		wp_register_script( 'rtcl-claim-listing', RTCL_CLAIM_LISTING_URL . "/assets/js/claim-listing{$this->suffix}.js", [
			'rtcl-common',
			'rtcl-validator',
			'rtcl-public'
		], $this->version, true );

		wp_register_style( 'rtcl-claim-listing', RTCL_CLAIM_LISTING_URL . '/assets/css/claim-listing.css', [
			'rtcl-public'
		], $this->version );

		$max_file_size = Functions::get_max_file_upload_size();

		$localize_data = apply_filters( 'rtcl_claim_listing_localize_options', [
			'ajax_url'        => admin_url( "admin-ajax.php" ),
			rtcl()->nonceId   => wp_create_nonce( rtcl()->nonceText ),
			'max_file_size'   => $max_file_size,
			'user_id'         => is_user_logged_in() ? get_current_user_id() : 0,
			'view_text'       => esc_html__( 'Download', 'rtcl-claim-listing' ),
			'remove_text'     => esc_html__( 'Remove', 'rtcl-claim-listing' ),
			'confirm_text'    => esc_html__( "Are you sure to delete?", 'rtcl-claim-listing' ),
			'error_extension' => esc_html__( 'File extension not supported.', 'rtcl-claim-listing' ),
			"error_file_size" => sprintf( esc_html__( "File size is more then %s.", 'rtcl-claim-listing' ), RtclFunctions::formatBytes( $max_file_size ) ),
		] );

		wp_localize_script( 'rtcl-claim-listing', 'rtcl_claim', $localize_data );

		if ( RtclFunctions::is_listing() ) {
			wp_enqueue_style( 'rtcl-claim-listing' );
			wp_enqueue_script( 'rtcl-claim-listing' );
		}
	}

	public function load_admin_script() {

		wp_enqueue_style( 'rtcl-claim-listing-admin', RTCL_CLAIM_LISTING_URL . '/assets/css/claim-listing-admin.css', [
			'rtcl-admin'
		], $this->version );

		wp_register_script( 'rtcl-claim-listing-admin', RTCL_CLAIM_LISTING_URL . "/assets/js/claim-listing-admin{$this->suffix}.js", [
			'jquery'
		], $this->version, true );

		$localize_data = apply_filters( 'rtcl_claim_listing_admin_localize_options', [
			'nonce'        => wp_create_nonce( 'wp_rest' ),
			'rest_root'    => esc_url_raw( rest_url() ),
			'claim_status' => Functions::claim_listing_status()
		] );

		wp_localize_script( 'rtcl-claim-listing-admin', 'rtcl_claim', $localize_data );

		wp_enqueue_script( 'rtcl-claim-listing-admin' );
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
		return untrailingslashit( plugin_dir_path( RTCL_CLAIM_LISTING_PLUGIN_FILE ) );
	}

}

/**
 * @return RtclInitClaimListing
 */
function rtclClaimListing() {
	return RtclInitClaimListing::getInstance();
}

rtclClaimListing();

register_activation_hook( RTCL_CLAIM_LISTING_PLUGIN_FILE, [ Installer::class, 'activate' ] );
register_deactivation_hook( RTCL_CLAIM_LISTING_PLUGIN_FILE, [ Installer::class, 'deactivate' ] );