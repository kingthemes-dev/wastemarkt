<?php

namespace RtclClaimListing\Admin;

use Rtcl\Helpers\Functions;

class AdminHooks {

	public static function init() {
		add_action( 'admin_menu', [ __CLASS__, 'register_menu' ] );
		add_action( 'in_admin_header', [ __CLASS__, 'remove_all_notices' ], 9999 );
		add_filter( 'rtcl_licenses', [ __CLASS__, 'license' ], 20 );
	}

	public static function register_menu() {
		add_menu_page(
			esc_html__( 'Classified Listing - Claim Listings', 'rtcl-claim-listing' ),
			esc_html__( 'Claim Listings', 'rtcl-claim-listing' ),
			'manage_rtcl_options',
			'rtcl-claim-listings',
			[ __CLASS__, 'all_claim_listings' ],
			RTCL_CLAIM_LISTING_URL . '/assets/img/icon-20x20.png',
			6
		);
	}

	public static function all_claim_listings() {
		require_once trailingslashit(RTCL_CLAIM_LISTING_PATH) . 'views/html-admin-claim-listings.php';
	}

	public static function remove_all_notices() {
		$screen = get_current_screen();
		if ( isset( $screen->base ) && ( 'claim-listings_page_rtcl-claim-listing-settings' == $screen->base || 'toplevel_page_rtcl-claim-listings' == $screen->base ) ) {
			remove_all_actions( 'admin_notices' );
			remove_all_actions( 'all_admin_notices' );
		}
	}

	public static function license( $licenses ) {
		$licenses[] = [
			'plugin_file' => RTCL_CLAIM_LISTING_PLUGIN_FILE,
			'api_data'    => [
				'key_name'    => 'claim_license_key',
				'status_name' => 'claim_license_status',
				'action_name' => 'rtcl_manage_claim_licensing',
				'product_id'  => 210774,
				'version'     => RTCL_CLAIM_LISTING_VERSION,
			],
			'settings'    => [
				'title' => esc_html__( 'Claim Listing license key', 'rtcl-claim-listing' ),
			],
		];

		return $licenses;
	}

}