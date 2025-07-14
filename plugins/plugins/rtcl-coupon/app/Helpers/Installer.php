<?php
/**
 * Install class.
 *
 * @package RadiusTheme\COUPON
 */

namespace RadiusTheme\COUPON\Helpers;

/**
 * Install class.
 */
class Installer {
	/**
	 * Activation actions.
	 *
	 * @return void
	 */
	public static function activate() {
		self::create_tables();
		\flush_rewrite_rules();
	}
	/**
	 * Cleare table Function.
	 *
	 * @return void
	 */
	public static function create_tables() {
		global $wpdb;
		if ( ! function_exists( 'dbDelta' ) ) {
			require_once ABSPATH . 'wp-admin/includes/upgrade.php';
		}
		$coupon_table = $wpdb->prefix . 'rtcl_coupon';
		$collate      = '';
		if ( $wpdb->has_cap( 'collation' ) ) {
			$collate = $wpdb->get_charset_collate();
		}
		$coupon_table_sql = "CREATE TABLE IF NOT EXISTS `{$coupon_table}` (	
					`id` int(11) NOT NULL AUTO_INCREMENT,
					`coupon_id` int(11) NOT NULL,
					`discount_type` text NOT NULL,
					`discount_amount` float NOT NULL,
					`expire_date` int(15) DEFAULT NULL,
					`pricing_type` text DEFAULT NULL,
					`include_pricing` text DEFAULT NULL,
					`exclude_pricing` text DEFAULT NULL,
					`usage_limit` text DEFAULT NULL,
					`per_user_limit` text DEFAULT NULL,
					`usage_count` int(11) DEFAULT 0,
					`meta` text DEFAULT NULL,
					PRIMARY KEY (`id`)
				) $collate";

		dbDelta( $coupon_table_sql );

		$coupon_lookup_table = $wpdb->prefix . 'rtcl_order_coupon_lookup';

		$coupon_lookup_sql = "CREATE TABLE IF NOT EXISTS `{$coupon_lookup_table}` (	
			`id` int(11) NOT NULL AUTO_INCREMENT,
			`order_id` int(11) NOT NULL,
			`coupon_id` int(11) NOT NULL,
			`user_id` int(11) NOT NULL,
			`coupon_summary` text NOT NULL,
			PRIMARY KEY (`id`)
		) $collate";

		dbDelta( $coupon_lookup_sql );

	}

	/**
	 * Deactivation actions.
	 *
	 * @return void
	 */
	public static function deactivate() {
		\flush_rewrite_rules();
	}
}
