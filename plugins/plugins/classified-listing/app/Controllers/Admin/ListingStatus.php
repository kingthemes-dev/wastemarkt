<?php

namespace Rtcl\Controllers\Admin;

use Rtcl\Helpers\Functions;
use WP_Post;

/**
 * Class ListingStatus
 *
 * @package Rtcl\Controllers\Admin
 */
class ListingStatus {
	static function init() {
		add_action( 'transition_post_status', [ __CLASS__, 'transition_post_status' ], 10, 3 );
		add_action( 'transition_post_status', [ __CLASS__, 'add_expired_date_at_publish_status' ], 99, 3 );
	}

	public static function transition_post_status( $new_status, $old_status, $post ) {

		if ( rtcl()->post_type !== $post->post_type ) {
			return;
		}
		$publish_count = absint( get_post_meta( $post->ID, '_rtcl_publish_count', true ) );
		if ( 'publish' == $new_status ) {
			update_post_meta( $post->ID, '_rtcl_publish_count', $publish_count + 1 );
		}
		// Check if we are transitioning from pending to publish
		if ( 'pending' == $old_status && 'publish' == $new_status ) {

			try {
				Functions::apply_payment_pricing( $post->ID );
			} catch ( \Exception $e ) {
				$log = rtcl()->logger();
				$log->info( 'Pricing apply error', [ 'post_id', $post->ID, 'error' => $e->getMessage() ] );
			}

		}

		// This hook is deprecated, use rtcl_transition_listing_status instead
		do_action_deprecated( 'rtcl_listing_status_' . $old_status . '_to_' . $new_status, [ 'new_status' => $new_status, 'old_status' => $old_status, 'post' => $post ], '4.1.2', 'rtcl_transition_listing_status' );
		// New hook for developers
		do_action( 'rtcl_transition_listing_status', $new_status, $old_status, $post );
	}

	/**
	 * @param          $new_status
	 * @param          $old_status
	 * @param WP_Post $post
	 */
	public static function add_expired_date_at_publish_status( $new_status, $old_status, $post ) {
		if ( rtcl()->post_type !== $post->post_type ) {
			return;
		}

		if ( 'publish' == $new_status && !get_post_meta( $post->ID, 'never_expires', true ) && !get_post_meta( $post->ID, 'expiry_date', true ) ) {
			Functions::add_default_expiry_date( $post->ID );
		}
	}
}