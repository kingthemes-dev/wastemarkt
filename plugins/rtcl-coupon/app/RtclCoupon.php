<?php
/**
 * Main initialization class.
 *
 * @package RadiusTheme\COUPON
 */

namespace RadiusTheme\COUPON;

use RadiusTheme\COUPON\Api\RestApi;
use RadiusTheme\COUPON\Traits\SingletonTrait;
use RadiusTheme\COUPON\Controllers\Ajax;
use RadiusTheme\COUPON\Hooks\FilterHooks;
use RadiusTheme\COUPON\Hooks\ActionHooks;
use RadiusTheme\COUPON\Controllers\Scripts;
use RadiusTheme\COUPON\Controllers\UniqueCoupon;
use RadiusTheme\COUPON\Controllers\Admin\AdminNotices;

if ( ! class_exists( Coupon::class ) ) {
	/**
	 * Main initialization class.
	 */
	final class RtclCoupon {
		/**
		 * Singleton Function.
		 */
		use SingletonTrait;

		/**
		 * Main Activity.
		 *
		 * @var string
		 */
		/**
		 * Main Activity.
		 *
		 * @var string
		 */
		public $post_type_coupon = 'rtcl_coupon';

		/**
		 * Class init.
		 *
		 * @return void
		 */
		public function init() {
			add_action( 'plugins_loaded', [ $this, 'on_plugins_loaded' ] );
			add_action( 'init', [ $this, 'init_hooks' ] );
			$dependence = AdminNotices::get_instance();
			if ( $dependence->check() ) {
				Scripts::get_instance();
				Ajax::get_instance();
				UniqueCoupon::get_instance();
				FilterHooks::init();
				ActionHooks::init();
				RestApi::get_instance();
			}
		}

		/**
		 * Init services.
		 *
		 * @return void
		 */
		public function init_hooks() {
			$this->i18n();
		}

		/**
		 * Return plugin dir templates path.
		 *
		 * @return string
		 */
		public function get_plugin_template_path() {
			return untrailingslashit( RTCL_COUPON_DIR_PATH ) . '/templates/';
		}

		/**
		 * Internationalization.
		 *
		 * @return void
		 */
		public function i18n() {
			load_plugin_textdomain( 'rtcl-coupon', false, RTCL_COUPON_LANGUAGE_PATH );
		}

		/**
		 * Actions on Plugins Loaded.
		 *
		 * @return void
		 */
		public function on_plugins_loaded() {
			do_action( 'rtcl_coupon_loaded', $this );
		}

		/**
		 * Assets URL.
		 *
		 * @param string $location file location.
		 *
		 * @return string
		 */
		public function assets_url( $location = '' ) {
			return esc_url( RTCL_COUPON_URL . 'assets/' . $location );
		}

	}

}
