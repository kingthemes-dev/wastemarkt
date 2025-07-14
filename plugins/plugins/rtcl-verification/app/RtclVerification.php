<?php

require_once RTCL_VERIFICATION_PATH . 'vendor/autoload.php';

use Rtcl\Helpers\Functions as RtclFunctions;
use RtclVerification\Api\RestApi;
use RtclVerification\Helpers\Installer;
use RtclVerification\Hooks\AdminSettingsHook;
use RtclVerification\Models\Dependencies;
use RtclVerification\Hooks\ActionHooks;
use RtclVerification\Hooks\FilterHooks;
use RtclVerification\Services\FirebaseGateway;

final class RtclVerification {

	/**
	 * Verification the singleton object.
	 */
	private static $singleton = false;

	/**
	 * Create an inaccessible constructor.
	 */
	private function __construct() {
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
			add_action( 'wp_enqueue_scripts', [ $this, 'front_end_script' ] );
		}
	}

	private function define_constants() {
		if ( ! defined( 'RTCL_VERIFICATION_URL' ) ) {
			define( 'RTCL_VERIFICATION_URL', plugins_url( '', RTCL_VERIFICATION_PLUGIN_FILE ) );
		}
		if ( ! defined( 'RTCL_VERIFICATION_SLUG' ) ) {
			define( 'RTCL_VERIFICATION_SLUG', basename( dirname( RTCL_VERIFICATION_PLUGIN_FILE ) ) );
		}
		if ( ! defined( 'RTCL_VERIFICATION_PLUGIN_DIRNAME' ) ) {
			define( 'RTCL_VERIFICATION_PLUGIN_DIRNAME', dirname( plugin_basename( RTCL_VERIFICATION_PLUGIN_FILE ) ) );
		}
		if ( ! defined( 'RTCL_VERIFICATION_PLUGIN_BASENAME' ) ) {
			define( 'RTCL_VERIFICATION_PLUGIN_BASENAME', plugin_basename( RTCL_VERIFICATION_PLUGIN_FILE ) );
		}
	}

	public function load_language() {
		load_plugin_textdomain( 'rtcl-verification', false, trailingslashit( RTCL_VERIFICATION_PLUGIN_DIRNAME ) . 'languages' );
	}

	private function hooks() {
		$dependence = Dependencies::getInstance();
		if ( $dependence->check() ) {
			FilterHooks::init();
			ActionHooks::init();
			if ( is_admin() ) {
				AdminSettingsHook::init();
			}
			if ( rtcl()->has_pro() ) {
				new RestApi();
			}
			do_action( 'rtcl_verification_loaded', $this );
		}
	}

	public function front_end_script() {
		$gateway    = RtclFunctions::get_option_item( 'rtcl_misc_settings', 'verification_gateway', 'firebase' );
		$second     = RtclFunctions::get_option_item( 'rtcl_misc_settings', 'verification_expired_time', 100 );
		$disable_ad = RtclFunctions::get_option_item( 'rtcl_misc_settings', 'verification_post_restriction', false );

		$version = ( defined( 'WP_DEBUG' ) && WP_DEBUG ) ? time() : RTCL_VERIFICATION_VERSION;

		$scriptObj = [
			'gateway'        => $gateway,
			'phone_readonly' => 'yes' == $disable_ad ? false : true,
			'expireTime'     => $second,
			'resendText'     => __( 'Resend OTP', 'rtcl-verification' )
		];

		if ( 'firebase' === $gateway ) {
			$scriptObj['firebase']        = FirebaseGateway::getSettings();
			$scriptObj['enable_firebase'] = RtclFunctions::is_account_page() || RtclFunctions::is_listing_form_page();
		}

		wp_register_script( 'rtcl-verification', RTCL_VERIFICATION_URL . '/assets/js/verification.js', [
			'rtcl-common',
			'rtcl-validator',
			'rtcl-public'
		], $version, true );

		wp_register_script( 'rtcl-verification-admin', RTCL_VERIFICATION_URL . '/assets/js/verification-admin.js', [
			'rtcl-common',
			'rtcl-validator',
			'rtcl-public'
		], $version, true );

		wp_register_style( 'rtcl-verification', RTCL_VERIFICATION_URL . '/assets/css/verification.css', [
			'rtcl-public'
		], $version );

		if ( RtclFunctions::is_account_page() || RtclFunctions::is_listing_form_page() ) {
			wp_enqueue_style( 'rtcl-verification' );

			if ( ( RtclFunctions::is_account_page() || RtclFunctions::is_listing_form_page() ) && 'firebase' === $gateway ) {
				//wp_enqueue_script( 'firebase', RTCL_VERIFICATION_URL . '/assets/js/firebase.js' );
				wp_enqueue_script( 'firebase', 'https://www.gstatic.com/firebasejs/8.3.1/firebase.js' );
			}
		}

		if ( ( RtclFunctions::is_account_page() || RtclFunctions::is_listing_form_page() ) && ! is_user_logged_in() ) {
			wp_localize_script( 'rtcl-verification', 'rtcl_verify', $scriptObj );
			wp_enqueue_script( 'rtcl-verification' );
		}

		if ( ( RtclFunctions::is_account_page() || RtclFunctions::is_listing_form_page() ) && ! is_admin() && is_user_logged_in() ) {
			wp_localize_script( 'rtcl-verification-admin', 'rtcl_verify', $scriptObj );
			wp_enqueue_script( 'rtcl-verification-admin' );
		}
	}

}

/**
 * @return RtclVerification
 */
function rtclVerification() {
	return RtclVerification::getInstance();
}

rtclVerification();

register_activation_hook( RTCL_VERIFICATION_PLUGIN_FILE, [ Installer::class, 'activate' ] );
register_deactivation_hook( RTCL_VERIFICATION_PLUGIN_FILE, [ Installer::class, 'deactivate' ] );