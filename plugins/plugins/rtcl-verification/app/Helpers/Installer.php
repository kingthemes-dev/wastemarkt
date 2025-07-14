<?php

namespace RtclVerification\Helpers;

class Installer {
	public static function activate() {

		if ( ! is_blog_installed() ) {
			return;
		}

		// Check if we are not already running this routine.
		if ( 'yes' === get_transient( 'rtcl_verification_installing' ) ) {
			return;
		}

		// If we made it till here nothing is running yet, lets set the transient now.
		set_transient( 'rtcl_verification_installing', 'yes', MINUTE_IN_SECONDS * 10 );

		self::create_tables();

		delete_transient( 'rtcl_verification_installing' );

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
		$phone_table_name              = $wpdb->prefix . "rtcl_phone";
		$phone_verification_table_name = $wpdb->prefix . "rtcl_phone_verification";
		$table_schema                  = [];

		if ( $wpdb->get_var( $wpdb->prepare( "SHOW TABLES LIKE %s", $phone_table_name ) ) !== $phone_table_name ) {
			$table_schema[] = "CREATE TABLE $phone_table_name (
                          id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
                          user_id int(10) UNSIGNED DEFAULT NULL,
                          phone varchar(191) NOT NULL,
                          type	varchar(191) NOT NULL,
                          verified_at timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
                          created_at timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
                          updated_at timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
                          PRIMARY KEY (id)
                        ) $collate;";
		}

		if ( $wpdb->get_var( $wpdb->prepare( "SHOW TABLES LIKE %s", $phone_verification_table_name ) ) !== $phone_verification_table_name ) {
			$table_schema[] = "CREATE TABLE $phone_verification_table_name (
                      id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
                      phone varchar(191) NOT NULL,
                      code varchar(191) NOT NULL,
                      ref_id varchar(100),
                      attempted int(10) UNSIGNED DEFAULT 0,
                      expired_at timestamp,
                      created_at timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
                      updated_at timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
                      verified int(1) NOT NULL DEFAULT 0,
                      PRIMARY KEY (id)
                      ) $collate;";
		}

		return $table_schema;
	}

	public static function deactivate() {

	}
}