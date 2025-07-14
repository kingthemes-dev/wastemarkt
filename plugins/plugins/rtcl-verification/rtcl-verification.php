<?php
/**
 * @wordpress-plugin
 * Plugin Name:     Classified Listing - Mobile Number Verification
 * Plugin URI:      https://www.radiustheme.com/downloads/classified-listing-mobile-no-verification/
 * Description:     Mobile Number Verification addon for Classified Listing
 * Version:         1.3.1
 * Author:          RadiusTheme
 * Author URI:      https://radiustheme.com
 * Text Domain:     rtcl-verification
 * Domain Path:     /languages
 */

defined( 'ABSPATH' ) || die( 'Keep Silent' );

if ( ! defined( 'RTCL_VERIFICATION_VERSION' ) ) {
	define('RTCL_VERIFICATION_VERSION', '1.3.1');
}
if ( ! defined( 'RTCL_VERIFICATION_PLUGIN_FILE' ) ) {
	define('RTCL_VERIFICATION_PLUGIN_FILE', __FILE__);
}
if ( ! defined( 'RTCL_VERIFICATION_PATH' ) ) {
	define( 'RTCL_VERIFICATION_PATH', plugin_dir_path( RTCL_VERIFICATION_PLUGIN_FILE ) );
}

require_once 'app/RtclVerification.php';