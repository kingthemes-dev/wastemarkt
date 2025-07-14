<?php

use Rtcl\Helpers\Functions;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class RtclMcStaticScripts {
	protected static $instance = null;


	final public static function instance() {
		if ( null === self::$instance ) {
			self::$instance = new self();
		}

		return self::$instance;
	}

	private function __construct() {
		add_action( 'wp_enqueue_scripts', [ $this, 'currency_scripts' ] );
		add_action( 'admin_enqueue_scripts', [ $this, 'currency_scripts' ] );
	}

	/**
	 *
	 * @return void
	 */
	public function currency_scripts() {

		global $wp;
		global $pagenow, $post_type;

		// validate page
		if ( ( is_admin() && rtcl()->post_type === $post_type && in_array( $pagenow, [
					'post.php',
					'post-new.php',
					'edit.php'
				] ) ) || Functions::is_listing_form_page() || ( Functions::is_account_page() && ( isset( $wp->query_vars['edit-account'] ) || isset( $wp->query_vars['rtcl_edit_account'] ) ) ) ) {
			wp_enqueue_script( 'rtclmc-static', RTCLMC_ASSETS_URL . 'js/rtclmc-static.min.js', [
				'jquery',
				'select2'
			], RTCLMC_VERSION );
		}

	}
}

RtclMcStaticScripts::instance();