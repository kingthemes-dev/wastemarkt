<?php
/**
 * Plugin Name: Classified Listing – Coupon
 * Plugin URI: https://www.radiustheme.com/downloads/classified-listing-coupon/
 * Description: Provides RTCL Coupon.
 * Author: RadiusTheme
 * Version: 2.2.1
 * Author URI: www.radiustheme.com
 * Text Domain: rtcl-coupon
 * Domain Path: /languages
 *
 * @package  RadiusTheme\COUPON
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Defining Constants.
 */
define( 'RTCL_COUPON_VERSION', '2.2.1' );
define( 'RTCL_COUPON_FILE_NAME', __FILE__ );
define( 'RTCL_COUPON_URL', plugin_dir_url( __FILE__ ) );
define( 'RTCL_COUPON_DIR_PATH', plugin_dir_path( __FILE__ ) );
define( 'RTCL_COUPON_LANGUAGE_PATH', RTCL_COUPON_DIR_PATH . 'languages' );

require_once __DIR__ . '/vendor/autoload.php';

register_activation_hook( __FILE__, 'activate_rtcl_coupon' );

register_deactivation_hook( __FILE__, 'deactivate_rtcl_coupon' );

/**
 * Plugin activation action.
 *
 * Plugin activation will not work after "plugins_loaded" hook
 * that's why activation hooks run here.
 */
function activate_rtcl_coupon() {
	RadiusTheme\COUPON\Helpers\Installer::activate();
}

/**
 * Plugin deactivation action.
 *
 * Plugin deactivation will not work after "plugins_loaded" hook
 * that's why deactivation hooks run here.
 */
function deactivate_rtcl_coupon() {
	RadiusTheme\COUPON\Helpers\Installer::deactivate();
}

/**
 * Returns Coupon.
 *
 * @return Coupon
 */
function rtcl_coupon() {
	return RadiusTheme\COUPON\RtclCoupon::get_instance();
}
rtcl_coupon();
