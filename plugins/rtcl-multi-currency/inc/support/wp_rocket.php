<?php
defined( 'ABSPATH' ) or die();

if ( defined( 'WPROCKETHELPERS_CACHE_DYNAMIC_COOKIE' ) ) {
	return;
}
define( 'WMC_CACHE_DYNAMIC_COOKIE', 'wmc_current_currency' );

if ( ! class_exists( 'WOOMULTI_CURRENCY_Plugin_WP_Rocket' ) ) {

	class WOOMULTI_CURRENCY_Plugin_WP_Rocket {
		public function __construct() {

			// Add cookie ID to cookkies for dynamic caches.
			add_filter( 'rocket_cache_dynamic_cookies', array( $this, 'cache_dynamic_cookie' ) );
			add_filter( 'rocket_cache_mandatory_cookies', array( $this, 'cache_dynamic_cookie' ) );

			// Remove .htaccess-based rewrites, since we need to detect the cookie,
			// which happens in inc/front/process.php.
			add_filter( 'rocket_htaccess_mod_rewrite', '__return_false' );
			register_activation_hook( WOOMULTI_CURRENCY_FILE, array( $this, 'activate' ) );
			register_deactivation_hook( WOOMULTI_CURRENCY_FILE, array( $this, 'deactivate' ) );
		}

		/**
		 * @param $cookies
		 *
		 * @return array
		 */
		public function cache_dynamic_cookie( $cookies ) {
			$cookies[] = 'wmc_current_currency';
			$cookies[] = 'wmc_current_currency_old';
			$cookies[] = 'wmc_ip_info';
			$cookies[] = 'wmc_ip_add';

			return $cookies;
		}


		/**
		 * Updates .htaccess, regenerates WP Rocket config file.
		 *
		 */
		public function flush_wp_rocket() {

			if ( ! function_exists( 'flush_rocket_htaccess' ) || ! function_exists( 'rocket_generate_config_file' ) ) {
				return;
			}

			// Update WP Rocket .htaccess rules.
			flush_rocket_htaccess();

			// Regenerate WP Rocket config file.
			rocket_generate_config_file();
		}

		/**
		 * Add customizations, updates .htaccess, regenerates config file.
		 *
		 */
		public function activate() {
			// Add customizations upon activation.
			add_filter( 'rocket_htaccess_mod_rewrite', '__return_false' );
			add_filter( 'rocket_cache_dynamic_cookies', array( $this, 'cache_dynamic_cookie' ) );
//			add_filter( 'rocket_cache_mandatory_cookies', array( $this, 'cache_dynamic_cookie' ) );

			// Flush .htaccess rules, and regenerate WP Rocket config file.
			$this->flush_wp_rocket();
		}

		/**
		 * Removes customizations, updates .htaccess, regenerates config file.
		 *
		 */
		public function deactivate() {

			// Remove customizations upon deactivation.
			remove_filter( 'rocket_htaccess_mod_rewrite', '__return_false' );
			remove_filter( 'rocket_cache_dynamic_cookies', array( $this, 'cache_dynamic_cookie' ) );
//			remove_filter( 'rocket_cache_mandatory_cookies', array( $this, 'cache_dynamic_cookie' ) );

			// Flush .htaccess rules, and regenerate WP Rocket config file.
			$this->flush_wp_rocket();
		}
	}
}
