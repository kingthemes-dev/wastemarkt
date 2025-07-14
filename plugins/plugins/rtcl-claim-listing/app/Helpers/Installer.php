<?php

namespace RtclClaimListing\Helpers;

class Installer {
	public static function activate() {

		if ( ! is_blog_installed() ) {
			return;
		}

		// Check if we are not already running this routine.
		if ( 'yes' === get_transient( 'rtcl_claim_listing_installing' ) ) {
			return;
		}

		// If we made it till here nothing is running yet, lets set the transient now.
		set_transient( 'rtcl_claim_listing_installing', 'yes', MINUTE_IN_SECONDS * 10 );

		self::create_tables();

		delete_transient( 'rtcl_claim_listing_installing' );

		do_action( 'rtcl_flush_rewrite_rules' );
	}

	private static function create_tables() {
		global $wpdb;

		$wpdb->hide_errors();

		require_once ABSPATH . 'wp-admin/includes/upgrade.php';
		dbDelta( self::get_table_schema() );
	}

	/**
	 * @return array
	 */
	static function get_table_schema() {
		global $wpdb;

		$collate = '';

		if ( $wpdb->has_cap( 'collation' ) ) {
			$collate = $wpdb->get_charset_collate();
		}

		$claim_table_name = $wpdb->prefix . "rtcl_claims";
		$table_schema     = array();

		if ( $wpdb->get_var( $wpdb->prepare( "SHOW TABLES LIKE %s", $claim_table_name ) ) !== $claim_table_name ) {
			$table_schema[] = "CREATE TABLE $claim_table_name (
                          id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
                          title text,
                          listing_id int(10) UNSIGNED NOT NULL,
                          user_id int(10) UNSIGNED NOT NULL,
                          prev_owner_id int(10) UNSIGNED,
                          info longtext,
                          created_at timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
                          updated_at timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
                          status varchar(191) NOT NULL,
                          PRIMARY KEY (id)
                        ) $collate;";
		}

		return $table_schema;
	}

	public static function deactivate() {
		do_action( 'rtcl_flush_rewrite_rules' );
	}
}