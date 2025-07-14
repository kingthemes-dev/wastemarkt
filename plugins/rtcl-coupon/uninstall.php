<?php
/**
 * Uninstall functionality
 *
 * @package RadiusTheme\COUPON
 * @since    1.0.0
 */

require_once __DIR__ . '/vendor/autoload.php';

$settings = get_option( 'rtcl_tools_settings' );
if ( ! empty( $settings['delete_all_data'] ) && 'yes' === $settings['delete_all_data'] ) {
	// Delete Coupons.
	$args        = [
		'numberposts' => -1,
		'post_type'   => 'rtcl_coupon',
		'fields'      => 'ids',
	];
	// get all posts by this user: posts, pages, attachments, etc..
	$coupon_posts = get_posts( $args );
	if ( empty( $coupon_posts ) ) {
		return;
	}
	foreach ( $coupon_posts as $id ) {
		wp_delete_post( $id, true );
	}
	$coupondb = new RadiusTheme\COUPON\Models\CouponDB();
	$coupondb->db->query( "DROP TABLE IF EXISTS {$coupondb->coupon_table}" );
	$coupondb->db->query( "DROP TABLE IF EXISTS {$coupondb->lookup_table}" );

}

