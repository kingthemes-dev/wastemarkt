<?php

/**
 * @wordpress-plugin
 * Plugin Name:       Classified Listing - Job Manager
 * Plugin URI:        https://radiustheme.com/demo/wordpress/classifiedpro
 * Description:       Enhance listing functionality and sell listing with WooCommerce.
 * Version:           1.0.4
 * Author:            RadiusTheme
 * Author URI:        https://radiustheme.com
 * Text Domain:       rtcl-job-manager
 * Domain Path:       /languages
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
// Define Constants
define( 'RTCL_JOB_MANAGER_VERSION', '1.0.4' );
define( 'RTCL_JOB_MANAGER_PLUGIN_FILE', __FILE__ );
define( 'RTCL_JOB_MANAGER_PATH', plugin_dir_path( RTCL_JOB_MANAGER_PLUGIN_FILE ) );
define( 'RTCL_JOB_MANAGER_URL', plugins_url( '', RTCL_JOB_MANAGER_PLUGIN_FILE ) );
define( 'RTCL_JOB_MANAGER_SLUG', basename( dirname( RTCL_JOB_MANAGER_PLUGIN_FILE ) ) );
define( 'RTCL_JOB_MANAGER_PLUGIN_DIRNAME', dirname( plugin_basename( RTCL_JOB_MANAGER_PLUGIN_FILE ) ) );
define( 'RTCL_JOB_MANAGER_PLUGIN_BASENAME', plugin_basename( RTCL_JOB_MANAGER_PLUGIN_FILE ) );

// Include Files
require_once 'app/App.php';