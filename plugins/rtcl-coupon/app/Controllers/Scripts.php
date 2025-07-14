<?php
/**
 * Main Scripts Class
 *
 * The main class that initiates all scripts.
 *
 * @package RadiusTheme\BBoss
 * @since    1.0.0
 */

namespace RadiusTheme\COUPON\Controllers;

use RadiusTheme\COUPON\Traits\SingletonTrait;

/**
 * Main Scripts Class
 */
class Scripts {

	/**
	 * Singleton Function.
	 */
	use SingletonTrait;

	/**
	 * Plugin Version.
	 *
	 * @var string
	 */
	private $version;
	/**
	 * Initial Function.
	 *
	 * @return void
	 */
	public function init() {
		$this->version = ( defined( 'WP_DEBUG' ) && WP_DEBUG ) ? time() : RTCL_COUPON_VERSION;
		add_action( 'admin_enqueue_scripts', [ $this, 'admin_register_all_scripts' ] );
		add_action( 'wp_enqueue_scripts', [ $this, 'frontend_register_all_scripts' ] );
	}
	/**
	 * Admin related script.
	 *
	 * @return void
	 */
	public function admin_register_all_scripts() {
		wp_register_style( 'rtcl-coupon-admin', rtcl_coupon()->assets_url( 'css/admin.min.css' ), [ 'jquery-ui' ], $this->version );
		wp_register_script( 'rtcl-coupon-tultip', rtcl_coupon()->assets_url( 'vendor/jquery.tipTip.min.js' ), [ 'jquery' ], $this->version, true );
		wp_register_script( 'rtcl-coupon-admin', rtcl_coupon()->assets_url( 'js/coupon-admin.min.js' ), [ 'jquery', 'jquery-ui-datepicker', 'rtcl-coupon-tultip', 'select2' ], $this->version, true );
		wp_localize_script(
			'rtcl-coupon-admin',
			'rtcl_coupon',
			[
				'ajaxurl'              => admin_url( 'admin-ajax.php' ),
				'generate_button_text' => esc_html__( 'Generate coupon code', 'rtcl-coupon' ),
				rtcl()->nonceId        => wp_create_nonce( rtcl()->nonceText ),
			]
		);
	}
	/**
	 * Admin related script.
	 *
	 * @return void
	 */
	public function frontend_register_all_scripts() {
		wp_register_style( 'rtcl-coupon', rtcl_coupon()->assets_url( 'css/styles.min.css' ), [], $this->version );
		wp_register_script( 'rtcl-coupon', rtcl_coupon()->assets_url( 'js/coupon.min.js' ), [ 'jquery' ], $this->version, true );
		wp_localize_script(
			'rtcl-coupon',
			'rtcl_coupon',
			[
				'ajaxurl'       => admin_url( 'admin-ajax.php' ),
				rtcl()->nonceId => wp_create_nonce( rtcl()->nonceText ),
			]
		);
	}
}
