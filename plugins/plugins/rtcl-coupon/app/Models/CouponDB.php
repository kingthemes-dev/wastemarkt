<?php
/**
 * Main initialization class.
 *
 * @package RadiusTheme\COUPON
 */

namespace RadiusTheme\COUPON\Models;

/**
 * Main initialization Models Coupon.
 */
class CouponDB {
	/**
	 * Coupon table name
	 *
	 * @var object
	 */
	public $db;
	/**
	 * Coupon table name
	 *
	 * @var string
	 */
	public $coupon_table;
	/**
	 * Coupon table name
	 *
	 * @var string
	 */
	public $lookup_table;
	/**
	 * Class Construct
	 */
	public function __construct() {
		global $wpdb;
		$this->db           = $wpdb;
		$this->coupon_table = $wpdb->prefix . 'rtcl_coupon';
		$this->lookup_table = $wpdb->prefix . 'rtcl_order_coupon_lookup';
	}
	/**
	 * Delete Coupon.
	 *
	 * @param intger $post_id Post id.
	 * @return void
	 */
	public function delete_coupon( $post_id ) {
		$query2 = $this->db->prepare( "DELETE FROM {$this->coupon_table} WHERE `coupon_id` = %d", $post_id );
		$this->db->query( $query2 );
	}
	/**
	 * Delete Coupon.
	 *
	 * @param intger $post_id Post id.
	 * @return void
	 */
	public function delete_lookup( $post_id ) {
		$query2 = $this->db->prepare( "DELETE FROM {$this->lookup_table} WHERE `order_id` = %d", $post_id );
		$this->db->query( $query2 );
	}
}
