<?php
/**
 * @wordpress-plugin
 * Plugin Name:       Classified Listing - FAQ
 * Plugin URI:        https://wordpress.org/plugins/classified-listing/
 * Description:       FAQ section for Classified Listing.
 * Version:           1.0.0
 * Author:            RadiusTheme
 * Author URI:        https://radiustheme.com
 * Text Domain:       rtcl-faq
 * Domain Path:       /languages
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
// Define Constants.
if ( ! defined( 'RTCL_FAQ_VERSION' ) ) {
	define( 'RTCL_FAQ_VERSION', '1.0.0' );
	define( 'RTCL_FAQ_PLUGIN_FILE', __FILE__ );
	define( 'RTCL_FAQ_PLUGIN_PATH', plugin_dir_path( RTCL_FAQ_PLUGIN_FILE ) );
	define( 'RTCL_FAQ_URL', plugins_url( '', RTCL_FAQ_PLUGIN_FILE ) );
	define( 'RTCL_FAQ_SLUG', basename( dirname( RTCL_FAQ_PLUGIN_FILE ) ) );
	define( 'RTCL_FAQ_PLUGIN_DIRNAME', dirname( plugin_basename( RTCL_FAQ_PLUGIN_FILE ) ) );
	define( 'RTCL_FAQ_PLUGIN_BASENAME', plugin_basename( RTCL_FAQ_PLUGIN_FILE ) );
}

// Include Files.
require_once 'app/RtclFaq.php';
