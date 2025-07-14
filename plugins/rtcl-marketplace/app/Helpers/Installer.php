<?php

namespace RtclMarketplace\Helpers;

class Installer {
	/**
	 * @return void
	 */
	public static function activate() {

		if ( ! is_blog_installed() ) {
			return;
		}

		self::create_tables();

		do_action( 'rtcl_flush_rewrite_rules' );
	}

	/**
	 * @return void
	 */
	public static function deactivate() {
		do_action( 'rtcl_flush_rewrite_rules' );
	}

	private static function create_tables() {
		include_once ABSPATH . 'wp-admin/includes/upgrade.php';

		self::create_seller_orders_table();
		self::create_withdraw_table();
		self::create_payout_method_table();
	}

	private static function create_seller_orders_table() {
		global $wpdb;

		$sql = "CREATE TABLE IF NOT EXISTS `{$wpdb->prefix}rtcl_marketplace_orders` (
                    `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
                    `order_id` bigint(20) DEFAULT NULL,
                    `seller_id` bigint(20) DEFAULT NULL,
                    `order_total` decimal(19,4) DEFAULT NULL,
                    `seller_earning` decimal(19,4) DEFAULT NULL,
                    `admin_earning` decimal(19,4) DEFAULT NULL,
                    `order_status` varchar(30) DEFAULT NULL,
                    PRIMARY KEY (`id`),
                    KEY `order_id` (`order_id`),
                    KEY `seller_id` (`seller_id`)
               ) ENGINE=InnoDB {$wpdb->get_charset_collate()};";

		dbDelta( $sql );
	}

	private static function create_withdraw_table() {
		global $wpdb;

		$sql = "CREATE TABLE IF NOT EXISTS `{$wpdb->prefix}rtcl_marketplace_withdraw` (
                    `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
                    `seller_id` bigint(20) unsigned NOT NULL,
                    `amount` decimal(19,4) NOT NULL,
                    `date` timestamp NOT NULL,
                    `paid_date` timestamp DEFAULT NULL,
                    `status` varchar(30) DEFAULT NULL,
                    `method` varchar(30) NOT NULL,
                    `details` longtext DEFAULT NULL,
                    PRIMARY KEY (id)
               ) ENGINE=InnoDB {$wpdb->get_charset_collate()};";

		dbDelta( $sql );
	}

	private static function create_payout_method_table() {
		global $wpdb;

		$sql = "CREATE TABLE IF NOT EXISTS `{$wpdb->prefix}rtcl_marketplace_payout_method` (
                    `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
                    `seller_id` bigint(20) unsigned NOT NULL,
                    `method` varchar(30) NOT NULL,
                    `details` longtext DEFAULT NULL,
    				`date` timestamp NOT NULL,
                    PRIMARY KEY (id)
               ) ENGINE=InnoDB {$wpdb->get_charset_collate()};";

		dbDelta( $sql );
	}

}