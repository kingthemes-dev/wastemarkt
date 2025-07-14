<?php
/**
 * @author  RadiusTheme
 * @since   1.0
 * @version 1.0
 */

// Includes the files needed for the theme updater
if ( !class_exists( 'EDD_Theme_Updater_Admin' ) ) {
	include( dirname( __FILE__ ) . '/theme-updater-admin.php' );
}

add_action( 'after_setup_theme', 'rdtheme_edd_theme_updater', 20 );

function rdtheme_edd_theme_updater(){
	$theme_data = wp_get_theme( get_template() );

	// Config settings
	$config = array(
		'remote_api_url' => 'https://www.radiustheme.com', // Site where EDD is hosted
		'item_id'        => 86677, // ID of item in site where EDD is hosted
		'theme_slug'     => 'classilist', // Theme slug
		'version'        => $theme_data->get( 'Version' ), // The current version of this theme
		'author'         => $theme_data->get( 'Author' ), // The author of this theme
		'download_id'    => '', // Optional, used for generating a license renewal link
		'renew_url'      => '' // Optional, allows for a custom license renewal link
	);

	// Strings
	$strings = array(
		'theme-license'             => __( 'Theme License', 'classilist' ),
		'enter-key'                 => __( 'Enter your theme license key.', 'classilist' ),
		'license-key'               => __( 'License Key', 'classilist' ),
		'license-action'            => __( 'License Action', 'classilist' ),
		'deactivate-license'        => __( 'Deactivate License', 'classilist' ),
		'activate-license'          => __( 'Activate License', 'classilist' ),
		'status-unknown'            => __( 'License status is unknown.', 'classilist' ),
		'renew'                     => __( 'Renew?', 'classilist' ),
		'unlimited'                 => __( 'unlimited', 'classilist' ),
		'license-key-is-active'     => __( 'License key is active.', 'classilist' ),
		'expires%s'                 => __( 'Expires %s.', 'classilist' ),
		'%1$s/%2$-sites'            => __( 'You have %1$s / %2$s sites activated.', 'classilist' ),
		'license-key-expired-%s'    => __( 'License key expired %s.', 'classilist' ),
		'license-key-expired'       => __( 'License key has expired.', 'classilist' ),
		'license-keys-do-not-match' => __( 'License keys do not match.', 'classilist' ),
		'license-is-inactive'       => __( 'License is inactive.', 'classilist' ),
		'license-key-is-disabled'   => __( 'License key is disabled.', 'classilist' ),
		'site-is-inactive'          => __( 'Site is inactive.', 'classilist' ),
		'license-status-unknown'    => __( 'License status is unknown.', 'classilist' ),
		'update-notice'             => __( "Updating this theme will lose any customizations you have made. 'Cancel' to stop, 'OK' to update.", 'classilist' ),
		'update-available'          => __('<strong>%1$s %2$s</strong> is available. <a href="%3$s" class="thickbox" title="%4s">Check out what\'s new</a> or <a href="%5$s"%6$s>update now</a>.', 'classilist' )
	);

	// Loads the updater classes
	$updater = new EDD_Theme_Updater_Admin( $config, $strings );
}