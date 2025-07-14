<?php

namespace RtclClaimListing\Api\V1;

use RtclClaimListing\Helpers\Functions as ClaimFunctions;
use RtclPro\Helpers\Api;
use WP_Error;
use WP_REST_Request;
use WP_REST_Server;

class V1_ClaimListingsApi {
    public function __construct() {
        register_rest_route( 'rtcl/v1', 'claim-listing', [
            [
                'methods'             => WP_REST_Server::CREATABLE,
                'callback'            => [ $this, 'save_claim_listing' ],
                'permission_callback' => [ Api::class, 'permission_check' ],
                'args'                => [
                    'listing_id' => [
                        'required'    => true,
                        'type'        => 'string',
                        'description' => 'name field is the sender name.',
                    ],
                    'name'       => [
                        'required'    => true,
                        'type'        => 'string',
                        'description' => 'name field is the sender name.',
                    ],
                    'email'      => [
                        'required'    => true,
                        'type'        => 'string',
                        'description' => 'email field is the sender email.',
                    ],
                    'phone'      => [
                        'required'    => true,
                        'type'        => 'string',
                        'description' => 'Phone field is the sender email.',
                    ],
                    'message'    => [
                        'required'    => true,
                        'type'        => 'string',
                        'description' => 'Message is the message details',
                    ],
                    'document'   => [
                        'type'        => 'file',
                        'description' => 'Document file is required field and only pdf file is allowed.',
                    ]
                ]
            ]
        ] );
    }

    public function save_claim_listing( WP_REST_Request $request ) {
        Api::is_valid_auth_request();
        $user_id = get_current_user_id();
        $name = $request->get_param( "name" );
        $email = $request->get_param( "email" );
        $listing_id = $request->get_param( "listing_id" );
        $listing = rtcl()->factory->get_listing( $listing_id );
        if ( !$listing ) {
            return new WP_Error( 'rtcl_claim_listing_not_found', esc_html__( 'Listing not found.', 'rtcl-claim-listing' ), [ 'status' => 404 ] );
        }
        $listing_owner_id = $listing->get_owner_id();
        $name = !empty( $name ) ? sanitize_text_field( $name ) : '';
        $email = !empty( $email ) ? sanitize_email( $email ) : '';
        $phone = $request->has_param( "phone" ) ? sanitize_text_field( $request->get_param( "phone" ) ) : '';
        $message = $request->has_param( "message" ) ? sanitize_textarea_field( $request->get_param( "message" ) ) : '';
        $details = [
            'name'    => $name,
            'email'   => $email,
            'phone'   => $phone,
            'message' => $message
        ];
        if ( ClaimFunctions::is_enable_attachment_field() ) {
            $files = $request->get_file_params();
            $document = !empty( $files['document'] ) ? $files['document'] : null;
            if ( $document ) {
                $max_size = 5 * 1024 * 1024; // 5MB
                $file_ext = strtolower( pathinfo( $document['name'], PATHINFO_EXTENSION ) );
                $file_size = $document['size'];

                if ( $file_ext !== 'pdf' ) {
                    return new WP_Error(
                        'invalid_file_type',
                        esc_html__( 'Invalid file type. Only PDF files are allowed.', 'rtcl-claim-listing' ),
                        [ 'status' => 400 ]
                    );
                }

                if ( $file_size > $max_size ) {
                    return new WP_Error(
                        'file_too_large',
                        esc_html__( 'File size exceeds the 5MB limit.', 'rtcl-claim-listing' ),
                        [ 'status' => 400 ]
                    );
                }
            }
            $attachment_id = ClaimFunctions::upload_claim_attachment( $document );
            if ( $attachment_id ) {
                $details['attachment_id'] = $attachment_id;
            }
        }

        global $wpdb;
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
        if (! $wpdb->insert_id ) {
            return new WP_Error( 'rtcl_claim_listing_error', esc_html__( 'Error while creating claim', 'rtcl-claim-listing' ), [ 'status' => 404 ] );
        }

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

        return rest_ensure_response( [
            'data'    => $email,
            'message' => apply_filters( 'rtcl_claim_success_message', esc_html__( "Thank you for submitting claim request!", "rtcl-claim-listing" ) )
        ] );
    }
}