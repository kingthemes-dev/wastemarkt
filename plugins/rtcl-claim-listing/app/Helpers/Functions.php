<?php

namespace RtclClaimListing\Helpers;

use Rtcl\Controllers\Hooks\Filters;

class Functions {

	public static function get_max_file_upload_size() {
		$max_size = absint( apply_filters( 'rtcl_claim_document_max_file_upload_size', 5 ) );

		return $max_size * ( 1024 * 1024 );
	}

	public static function claim_listing_status() {
		return [
			'pending'   => __( 'Pending', 'rtcl-claim-listing' ),
			'approved'  => __( 'Approved', 'rtcl-claim-listing' ),
			//'completed' => __( 'Completed', 'rtcl-claim-listing' ),
			'cancelled' => __( 'Cancelled', 'rtcl-claim-listing' ),
		];
	}

	public static function transfer_listing_ownership( $listing_id, $claimer_id, $owner_id = 0 ) {

		$message = __( 'Something wrong to change listing ownership!', 'rtcl-claim-listing' );

		// check listing exist or not
		if ( ! get_post_status( $listing_id ) ) {
			$message = __( 'The listing does not exist!', 'rtcl-claim-listing' );
		}

		// check user exist or not
		$user = get_userdata( $claimer_id );
		if ( $user === false ) {
			$message = __( 'User id for claimer does not exist!', 'rtcl-claim-listing' );
		}

		// update post author
		$arg = array(
			'ID'          => $listing_id,
			'post_author' => $claimer_id,
		);

		if ( ! is_wp_error( wp_update_post( $arg ) ) ) {
			$message = __( 'Updated listing ownership!', 'rtcl-claim-listing' );
			// add meta for claim badge
			update_post_meta( $listing_id, 'rtcl_claimed_listing', 'yes' );
			// send email to claimer
			rtcl()->mailer()->emails['Claim_Approved_Email']->trigger( $listing_id, [ 'claimer_id' => $claimer_id ] );
		}

		return $message;

	}

	public static function count_rows( $query ) {
		global $wpdb;

		$results = $wpdb->get_results( $query );

		if ( ! is_wp_error( $results ) ) {
			return count( $results );
		}

		return 0;
	}

	public static function get_claim_listing_options() {
		$options = get_transient( 'rtcl_claim_options' );

		if ( $options === false ) {
			$options = get_option( 'rtcl_claim_settings' );
			set_transient( 'rtcl_claim_options', $options, 3600 );
		}

		return $options;
	}

	public static function get_claim_action_title() {
		$options = self::get_claim_listing_options();

		return $options['claimTitle'] ?? __( 'Claim this listing', 'rtcl-claim-listing' );
	}

	public static function claim_listing_enable() {
		$options = self::get_claim_listing_options();

		return ! empty( $options['claimEnable'] ) ?? true;
	}

	public static function is_enable_claim_badge() {
		$options = self::get_claim_listing_options();

		return ! empty( $options['badgeEnable'] ) ?? true;
	}

	public static function is_enable_attachment_field() {
		$options = self::get_claim_listing_options();

		return ! empty( $options['enableAttachment'] ) ?? true;
	}

	public static function get_claim_popup_title() {
		$options = self::get_claim_listing_options();

		return $options['popupTitle'] ?? __( 'Claim Listing', 'rtcl-claim-listing' );
	}

	public static function get_claim_button_text() {
		$options = self::get_claim_listing_options();

		return $options['buttonText'] ?? __( 'Submit', 'rtcl-claim-listing' );
	}

    public static function upload_claim_attachment( $file = null ) {
        if ( !function_exists( 'wp_handle_upload' ) ) {
            require_once( ABSPATH . 'wp-admin/includes/file.php' );
        }
        $attach_id = 0;
        $file = $file ?? $_FILES['document'];
        Filters::beforeUpload();
        $status = wp_handle_upload( $file, [
            'test_form' => false
        ] );
        Filters::afterUpload();
        if ( $status && !isset( $status['error'] ) ) {
            // $filename should be the path to a file in the upload directory.
            $filename = $status['file'];

            // Check the type of tile. We'll use this as the 'post_mime_type'.
            $filetype = wp_check_filetype( basename( $filename ) );

            // Get the path to the upload directory.
            $wp_upload_dir = wp_upload_dir();

            // Prepare an array of post data for the attachment.
            $attachment = [
                'guid'           => $wp_upload_dir['url'] . '/' . basename( $filename ),
                'post_mime_type' => $filetype['type'],
                'post_title'     => preg_replace( '/\.[^.]+$/', '', basename( $filename ) ),
                'post_content'   => '',
                'post_status'    => 'inherit'
            ];

            // Insert the attachment.
            $attachment_id = wp_insert_attachment( $attachment, $filename );
            if ( !is_wp_error( $attachment_id ) ) {
                $attach_id = $attachment_id;
                do_action( 'rtcl_claim_user_document_uploaded' );
            }
        }

        return $attach_id;

    }

}