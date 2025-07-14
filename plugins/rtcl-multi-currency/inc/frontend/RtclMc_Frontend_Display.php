<?php

use Rtcl\Helpers\Functions;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class RtclMc_Frontend_Display {
	protected static $instance = null;
	protected $settings;
	/**
	 * @var string
	 */
	private $suffix;


	/**
	 * @param bool $new
	 *
	 * @return RtclMc_Frontend_Display
	 */
	public static function instance( $new = false ) {
		// If the single instance hasn't been set, set it now.
		if ( $new || null === self::$instance ) {
			self::$instance = new self;
		}

		return self::$instance;
	}

	private function __construct() {
		$this->suffix   = ( defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ) || ( defined( 'WP_DEBUG' ) && WP_DEBUG ) ? '' : '.min';
		$this->settings = RtclMc_Data::instance();

		/*Add order information*/
		add_action( 'wp_footer', [ $this, 'show_action' ] );


		if ( $this->settings->get_enable() ) {
			add_action( 'wp_enqueue_scripts', [ $this, 'front_end_script' ] );
			add_action( 'wp_enqueue_scripts', [ $this, 'switch_currency_by_js_script' ], 999999 );
		}

	}


	/**
	 * Public
	 */
	public function switch_currency_by_js_script() {
		if ( $this->settings->enable_switch_currency_by_js() ) {
			wp_enqueue_script( 'rtclmc-switcher', RTCLMC_ASSETS_URL . "rtclmc-switcher$this->suffix.js", [ 'jquery' ], RTCLMC_VERSION );
			$params = [
				'use_session'        => $this->settings->use_session(),
				'do_not_reload_page' => $this->settings->get_param( 'do_not_reload_page' ),
				'ajaxUrl'            => admin_url( 'admin-ajax.php' ),
				'posts_submit'       => count( $_POST ),
			];
			wp_localize_script( 'rtclmc-switcher', 'rtclmcSParams', $params );
		}
	}

	/**
	 * Show Currency converter
	 */
	public function show_action() {
		if ( ! $this->enable() ) {
			return;
		}

		$language = '';
		if ( is_plugin_active( 'sitepress-multilingual-cms/sitepress.php' ) ) {
			$default_lang     = apply_filters( 'wpml_default_language', null );
			$current_language = apply_filters( 'wpml_current_language', null );

			if ( $current_language && $current_language !== $default_lang ) {
				$language = $current_language;
			}
		} else if ( class_exists( 'Polylang' ) ) {
			$default_lang     = pll_default_language( 'slug' );
			$current_language = pll_current_language( 'slug' );
			if ( $current_language && $current_language !== $default_lang ) {
				$language = $current_language;
			}
		}
		wp_enqueue_style( 'rtclmc-flags', RTCLMC_ASSETS_URL . "css/flags-64$this->suffix.css", [], RTCLMC_VERSION );

		$enable_collapse     = $this->settings->enable_collapse();
		$mb_disable_collapse = $this->settings->disable_collapse();
		$classes             = [
			'rtclmc-' . $this->settings->get_design_position(),/*Position left or right*/
			'style-1'
		];
		switch ( $this->settings->get_sidebar_style() ) {
			case 1:
				$classes[] = 'rtclmc-currency-symbol';
				break;
			case 2:
				$classes[] = 'rtclmc-currency-flag';
				break;
			case 3:
				$classes[] = 'rtclmc-currency-flag rtclmc-currency-code';
				break;
			case 4:
				$classes[] = 'rtclmc-currency-flag rtclmc-currency-symbol';
				break;
		}
		if ( $enable_collapse ) {
			$classes[] = 'rtclmc-collapse';
		}

		if ( $mb_disable_collapse ) {
			$classes[] = 'rtclmc-mobile-no-collapse';
		}
		Functions::get_template(
			'multi-currency/display',
			[ 'settings' => $this->settings, 'language' => $language, 'classes' => $classes ],
			'',
			RTCLMC_PATH . 'templates/'
		);
	}

	/**
	 * Public
	 */
	public function front_end_script() {
		wp_enqueue_style( 'rtclmc', RTCLMC_ASSETS_URL . "css/rtclmc$this->suffix.css", [], RTCLMC_VERSION );
		if ( is_rtl() ) {
			//wp_enqueue_style('rtclmc-rtl', RTCLMC_ASSETS_URL . 'css/frontend-rtl.css', [], RTCLMC_VERSION);
		}

		/*Custom CSS*/
		$text_color       = $this->settings->get_text_color();
		$background_color = $this->settings->get_background_color();
		$main_color       = $this->settings->get_main_color();
		$links            = $this->settings->get_links();
		$currency_qty     = count( $links ) - 1;

		$custom = '.rtclmc .rtclmc-list-currencies .rtclmc-currency.rtclmc-active,.rtclmc .rtclmc-list-currencies .rtclmc-currency:hover,.rtclmc.rtclmc-price-switcher a:hover {background: ' . $main_color . ' !important;}
		.rtclmc .rtclmc-list-currencies .rtclmc-currency,.rtclmc .rtclmc-title, .rtclmc.rtclmc-price-switcher a {background: ' . $background_color . ' !important;}
		.rtclmc .rtclmc-title, .rtclmc .rtclmc-list-currencies .rtclmc-currency span,.rtclmc .rtclmc-list-currencies .rtclmc-currency a,.rtclmc.rtclmc-price-switcher a {color: ' . $text_color . ' !important;}';

		$custom .= ".rtclmc.rtclmc-shortcode.vertical-currency-symbols-circle .rtclmc-currency-wrapper:hover .rtclmc-sub-currency {animation: height_slide {$currency_qty}00ms;}";
		$custom .= "@keyframes height_slide {0% {height: 0;} 100% {height: {$currency_qty}00%;} }";
		wp_add_inline_style( 'rtclmc', $custom );

		wp_enqueue_script( 'rtclmc', RTCLMC_ASSETS_URL . 'js/rtclmc.js', [ 'jquery' ], RTCLMC_VERSION );

		wp_localize_script( 'rtclmc', 'rtclmcParams', [
			'ajaxUrl' => admin_url( 'admin-ajax.php' )
		] );

	}


	/**
	 * Check design enable
	 *
	 * @return bool
	 *
	 */
	protected function enable() {
		$enable = $this->settings->get_enable_design();
		if ( ! $enable ) {
			return false;
		}

		return true;
	}

}

RtclMc_Frontend_Display::instance();