<?php
/**
 * @wordpress-plugin
 * Plugin Name:     Classified Listing – Seller Verification
 * Plugin URI:      https://www.radiustheme.com/downloads/classified-listing-seller-verification/
 * Description:     Seller Verification addon for Classified Listing Store
 * Version:         1.1.6
 * Author:          RadiusTheme
 * Author URI:      https://radiustheme.com
 * Text Domain:     rtcl-seller-verification
 * Domain Path:     /languages
 */

defined( 'ABSPATH' ) || die( 'Keep Silent' );

// Define RTCL_PLUGIN_FILE.
define( 'RTCL_SELLER_VERSION', '1.1.6' );
define( 'RTCL_SELLER_FILE', __FILE__ );
define( 'RTCL_SELLER_TEMPLATE_DEBUG_MODE', false );
define( 'RTCL_SELLER_URL', plugins_url( '', RTCL_SELLER_FILE ) );

include_once ABSPATH . 'wp-admin/includes/plugin.php';

if ( ! is_plugin_active( 'classified-listing/classified-listing.php' ) ) {
	function rtcl_seller_required_notice() {
		?>
        <div id="message" class="error">
            <p><?php _e( 'Please install and activate Classified Listing to use Seller Verification for Classified listing plugin.', 'rtcl-user-verification' ); ?></p>
        </div>
		<?php
	}

	add_action( 'admin_notices', 'rtcl_seller_required_notice' );

	return;
}

if ( class_exists( 'Rtcl' ) ) {
	require_once "inc/helpers/dependencies.php";
	$dependence = RtclSvDependencies::getInstance();
	if ( $dependence->check() ) {
		require_once 'inc/init.php';
	}
}