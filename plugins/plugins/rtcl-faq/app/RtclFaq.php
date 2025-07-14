<?php

use RtclFaq\Controller\FAQMeta;
use RtclFaq\Controller\Script;
use RtclFaq\Helpers\Fns;
use RtclFaq\Helpers\Installer;
use RtclFaq\Hooks\FilterHooks;
use RtclFaq\Hooks\TemplateHooks;
use RtclFaq\Models\Dependencies;
use RtclFaq\Traits\SingletonTraits;

require_once RTCL_FAQ_PLUGIN_PATH . 'vendor/autoload.php';

/**
 * RtclFaq class
 */
final class RtclFaq {

	use SingletonTraits;

	/**
	 * Store Dependencies
	 *
	 * @var Dependencies
	 */
	private $dependency;

	/**
	 * Class Constructor
	 */
	private function __construct() {
		$this->dependency = Dependencies::instance();
		$this->init();
	}

	/**
	 * Initialize necessary things
	 */
	protected function init() {
		$this->load_language();
		if ( $this->dependency->check()) {
			FilterHooks::instance();
			FAQMeta::instance();
			TemplateHooks::instance();
			Script::instance();

			do_action( 'rtcl_faq_loaded', $this );
		}
	}

	/**
	 * Load Plugin text-domain
	 *
	 * @return void
	 */
	public function load_language() {
		load_plugin_textdomain( 'rtcl-faq', false, trailingslashit( RTCL_FAQ_PLUGIN_DIRNAME ) . 'languages' );
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
		return untrailingslashit( plugin_dir_path( RTCL_FAQ_PLUGIN_FILE ) );
	}
}

/**
 * Main Init Function
 *
 * @return RtclFaq
 */
function rtcl_faq() {
	return RtclFaq::instance();
}

rtcl_faq();

register_activation_hook( RTCL_FAQ_PLUGIN_FILE, [ Installer::class, 'activate' ] );
register_deactivation_hook( RTCL_FAQ_PLUGIN_FILE, [ Installer::class, 'deactivate' ] );
