<?php

/**
 * Plugin Name: Classified Listing – Archive & Single Page Builder Addon
 * Plugin URI: https://radiustheme.com/
 * Description: Provides Gutenberg, Elementor, Divi builder widgets for Classified Listing Archive And Single Page.
 * Author: RadiusTheme
 * Version: 3.0.0
 * Author URI: www.radiustheme.com
 * Text Domain: rtcl-elementor-builder
 * Domain Path: /languages
 * Requires Plugins:  classified-listing, classified-listing-toolkits
 *
 * @package  RTCL_Elementor_Builder
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Defining Constants.
 */
define( 'RTCL_ELB_VERSION', '3.0.0' );
define( 'RTCL_ELB_PLUGIN_ACTIVE_FILE_NAME', __FILE__ );
define( 'RTCL_ELB_PLUGIN_URL', plugins_url( '', __FILE__ ) );
define( 'RTCL_ELB_LANGUAGE_PATH', dirname( plugin_basename( __FILE__ ) ) . '/languages' );
define( 'RTCL_ELB_PATH', plugin_dir_path( RTCL_ELB_PLUGIN_ACTIVE_FILE_NAME ) );

if ( ! defined( 'RTCL_ELEMENTOR_ADDONS_PLUGIN_FILE' ) ) {
	define( 'RTCL_ELEMENTOR_ADDONS_PLUGIN_FILE', __FILE__ );
}
if ( ! defined( 'RTCL_ELEMENTOR_ADDONS_PLUGIN_PATH' ) ) {
	define( 'RTCL_ELEMENTOR_ADDONS_PLUGIN_PATH', plugin_dir_path( RTCL_ELEMENTOR_ADDONS_PLUGIN_FILE ) );
}

if ( file_exists( RTCL_ELB_PATH . '/vendor/autoload.php' ) ) {
	require_once RTCL_ELB_PATH . '/vendor/autoload.php';
}

register_activation_hook( __FILE__, 'activate_rtcl_elb' );

register_deactivation_hook( __FILE__, 'deactivate_rtcl_elb' );

/**
 * Plugin activation action.
 *
 * Plugin activation will not work after "plugins_loaded" hook
 * that's why activation hooks run here.
 */
function activate_rtcl_elb() {
	\RtclElb\Helpers\Installer::activate();
}

/**
 * Plugin deactivation action.
 *
 * Plugin deactivation will not work after "plugins_loaded" hook
 * that's why deactivation hooks run here.
 */
function deactivate_rtcl_elb() {
	\RtclElb\Helpers\Installer::deactivate();
}

/**
 * App init.
 */

/**
 * Returns RtclElb.
 *
 * @return RtclElb
 */
function rtclElb() {
	return RtclElb\RtclElb::getInstance();
}

rtclElb()->init();
