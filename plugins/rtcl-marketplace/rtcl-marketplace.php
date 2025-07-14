<?php

/**
 * @wordpress-plugin
 * Plugin Name:       Classified Listing - Marketplace
 * Plugin URI:        https://radiustheme.com/demo/wordpress/classifiedpro
 * Description:       Enhance listing functionality and sell listing with WooCommerce.
 * Version:           2.0.1
 * Author:            RadiusTheme
 * Author URI:        https://radiustheme.com
 * Text Domain:       rtcl-marketplace
 * Domain Path:       /languages
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
// Define Constants
if ( ! defined( 'RTCL_MARKETPLACE_VERSION' ) ) {
	define( 'RTCL_MARKETPLACE_VERSION', '2.0.1' );
}
if ( ! defined( 'RTCL_MARKETPLACE_PLUGIN_FILE' ) ) {
	define( 'RTCL_MARKETPLACE_PLUGIN_FILE', __FILE__ );
}
if ( ! defined( 'RTCL_MARKETPLACE_PLUGIN_PATH' ) ) {
	define( 'RTCL_MARKETPLACE_PLUGIN_PATH', plugin_dir_path( RTCL_MARKETPLACE_PLUGIN_FILE ) );
}
// Include Files
require_once 'app/RtclMarketplace.php';