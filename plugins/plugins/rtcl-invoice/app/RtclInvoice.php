<?php

use Rtcl\Helpers\Functions;
use RtclInvoice\Admin\AdminSettings;
use RtclInvoice\Models\Dependencies;
use RtclInvoice\Admin\AdminHooks;
use RtclInvoice\Helpers\Installer;
use RtclInvoice\Hooks\ActionHooks;
use RtclInvoice\Hooks\AjaxHooks;
use RtclInvoice\Hooks\FilterHooks;
use Dompdf\Dompdf;
use Dompdf\Options;

require_once __DIR__ . './../vendor/autoload.php';

final class RtclInvoice {

	private $suffix;
	private $version;

	/**
	 * RtclInvoice the singleton object.
	 */
	private static $singleton = false;

	/**
	 * Create an inaccessible constructor.
	 */
	private function __construct() {
		$this->suffix  = ( defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ) ? '' : '.min';
		$this->version = ( defined( 'WP_DEBUG' ) && WP_DEBUG ) ? time() : RTCL_INVOICE_VERSION;

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
			add_action( 'admin_enqueue_scripts', [ $this, 'load_admin_script' ] );
		}
	}

	private function define_constants() {
		if ( ! defined( 'RTCL_INVOICE_PATH' ) ) {
			define( 'RTCL_INVOICE_PATH', plugin_dir_path( RTCL_INVOICE_PLUGIN_FILE ) );
		}
		if ( ! defined( 'RTCL_INVOICE_URL' ) ) {
			define( 'RTCL_INVOICE_URL', plugins_url( '', RTCL_INVOICE_PLUGIN_FILE ) );
		}
		if ( ! defined( 'RTCL_INVOICE_SLUG' ) ) {
			define( 'RTCL_INVOICE_SLUG', basename( dirname( RTCL_INVOICE_PLUGIN_FILE ) ) );
		}
		if ( ! defined( 'RTCL_INVOICE_PLUGIN_DIRNAME' ) ) {
			define( 'RTCL_INVOICE_PLUGIN_DIRNAME', dirname( plugin_basename( RTCL_INVOICE_PLUGIN_FILE ) ) );
		}
		if ( ! defined( 'RTCL_INVOICE_PLUGIN_BASENAME' ) ) {
			define( 'RTCL_INVOICE_PLUGIN_BASENAME', plugin_basename( RTCL_INVOICE_PLUGIN_FILE ) );
		}
	}

	public function load_language() {
		load_plugin_textdomain( 'rtcl-invoices', false, trailingslashit( RTCL_INVOICE_PLUGIN_DIRNAME ) . 'languages' );
	}

	private function hooks() {
		$dependence = Dependencies::getInstance();
		if ( $dependence->check() ) {
			FilterHooks::init();
			ActionHooks::init();
			AjaxHooks::init();
			// dompdf option
			if ( class_exists( '\\Dompdf\\Dompdf' ) ) {
				$options = new Options();
				$options->set( 'defaultFont', 'helvetica' );
				$options->setIsRemoteEnabled( true );
				$dompdf = new Dompdf( $options );
			}
			// admin
			if ( is_admin() ) {
				AdminHooks::init();
				new AdminSettings();
			}
			do_action( 'rtcl_invoice_loaded', $this );
		}
	}

	public function front_end_script() {

		wp_register_script( 'rtcl-invoice', RTCL_INVOICE_URL . "/assets/js/invoice{$this->suffix}.js", [
			'rtcl-common',
			'rtcl-validator',
			'rtcl-public',
		], $this->version, true );

		wp_register_style( 'rtcl-invoice', RTCL_INVOICE_URL . '/assets/css/invoice.css', [
			'rtcl-public'
		], $this->version );

		if ( Functions::is_account_page() ) {
			wp_enqueue_style( 'rtcl-invoice' );
			wp_enqueue_script( 'rtcl-invoice' );
		}
	}

	public function load_admin_script() {

		wp_register_script( 'rtcl-invoice-admin', RTCL_INVOICE_URL . "/assets/js/invoice-admin{$this->suffix}.js", [
			'jquery',
		], $this->version, true );

		wp_register_style( 'rtcl-invoice-admin', RTCL_INVOICE_URL . '/assets/css/invoice-admin.css', [
			'rtcl-admin'
		], $this->version );

		if ( ! empty( $_GET['page'] ) && $_GET['page'] == 'rtcl-invoice' ) {
			wp_enqueue_media();
			wp_enqueue_style( 'rtcl-invoice-admin' );
			wp_enqueue_script( 'rtcl-admin-settings' );
			wp_enqueue_script( 'rtcl-invoice-admin' );
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
		return untrailingslashit( plugin_dir_path( RTCL_INVOICE_PLUGIN_FILE ) );
	}

}

/**
 * @return RtclInvoice
 */
function rtclInvoice() {
	return RtclInvoice::getInstance();
}

rtclInvoice();

register_activation_hook( RTCL_INVOICE_PLUGIN_FILE, [ Installer::class, 'activate' ] );
register_deactivation_hook( RTCL_INVOICE_PLUGIN_FILE, [ Installer::class, 'deactivate' ] );