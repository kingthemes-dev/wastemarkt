<?php

namespace RtclClaimListing\Hooks;

use Rtcl\Helpers\Functions;
use RtclClaimListing\Helpers\Functions as ClaimFunctions;

class AjaxHooks {

    public static function init() {
        add_action( 'wp_ajax_rtcl_claim_form_submit', [ __CLASS__, 'save_form_data' ] );
    }



    public static function save_form_data() {
        global $wpdb;
        $msg = '';
        $success = false;

        if ( apply_filters( 'rtcl_booking_form_remove_nonce', true ) || Functions::verify_nonce() ) {
            $listing_id = isset( $_POST['listing_id'] ) ? absint( $_POST['listing_id'] ) : 0;
            $user_id = isset( $_POST['user_id'] ) ? absint( $_POST['user_id'] ) : '';
            $listing_owner_id = rtcl()->factory->get_listing( $listing_id )->get_owner_id();
            $name = isset( $_POST['name'] ) ? sanitize_text_field( $_POST['name'] ) : '';
            $email = isset( $_POST['email'] ) ? sanitize_email( $_POST['email'] ) : '';
            $phone = isset( $_POST['phone'] ) ? sanitize_text_field( $_POST['phone'] ) : '';
            $message = isset( $_POST['message'] ) ? sanitize_textarea_field( $_POST['message'] ) : '';

            $details = [
                'name'    => $name,
                'email'   => $email,
                'phone'   => $phone,
                'message' => $message
            ];

            $attachment_id = 0;
            if ( isset( $_FILES['document'] ) ) {
                $attachment_id = ClaimFunctions::upload_claim_attachment($_FILES['document']);
            }

            if ( $attachment_id ) {
                $details['attachment_id'] = $attachment_id;
            }

            $claim_table = $wpdb->prefix . "rtcl_claims";

            $data = [
                'title'         => rtcl()->factory->get_listing( $listing_id )->get_the_title(),
                'listing_id'    => $listing_id,
                'user_id'       => $user_id,
                'prev_owner_id' => $listing_owner_id,
                'info'          => serialize( $details ),
                'created_at'    => current_time( 'mysql' ),
                'updated_at'    => current_time( 'mysql' ),
                'status'        => 'pending',
            ];

            $wpdb->insert( $claim_table, $data );

            if ( $wpdb->insert_id ) {
                Functions::add_notice( apply_filters( 'rtcl_claim_success_message',
                    esc_html__( "Thank you for submitting claim request!", "rtcl-claim-listing" ), $_REQUEST ) );
                $success = true;
                // send email to admin
                rtcl()->mailer()->emails['Claim_Request_Email']->trigger(
                    $listing_id,
                    [
                        'name'    => $name,
                        'email'   => $email,
                        'phone'   => $phone,
                        'message' => $message
                    ]
                );
            }
        } else {
            Functions::add_notice( apply_filters( 'rtcl_claim_session_error_message', esc_html__( "Session Error !!", "rtcl-claim-listing" ), $_REQUEST ),
                'error' );
        }

        $msg = Functions::get_notices( 'error' );
        if ( $success ) {
            $msg = Functions::get_notices( 'success' );
        }
        Functions::clear_notices(); // Clear all notice created by checking

        $response = [
            'success' => $success,
            'message' => $msg,
        ];

        wp_send_json( $response );
    }

}