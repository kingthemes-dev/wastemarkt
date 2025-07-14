<?php
/**
 * Main initialization class.
 *
 * @package RadiusTheme\COUPON
 */

namespace RadiusTheme\COUPON\Controllers;

/**
 * Metaboxes class.
 */
class Metaboxes {

	/**
	 * Register Post types
	 *
	 * @return void
	 */
	public static function coupon_metabox() {
		wp_enqueue_style( 'rtcl-coupon-admin' );
		wp_enqueue_script( 'rtcl-coupon-admin' );
		require_once RTCL_COUPON_DIR_PATH . 'views/coupon-metabox.php';
	}

}
