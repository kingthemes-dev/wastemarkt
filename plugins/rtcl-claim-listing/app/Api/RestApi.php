<?php

namespace RtclClaimListing\Api;

use Rtcl\Models\Listing;
use RtclClaimListing\Api\V1\ClaimListingsApi;
use RtclClaimListing\Api\V1\SettingsApi;
use RtclClaimListing\Api\V1\V1_ClaimListingsApi;
use RtclClaimListing\Helpers\Functions as ClaimFunctions;
use RtclPro;

class RestApi {

    public function init() {
        add_action( 'rest_api_init', [ $this, 'init_rest_routes' ], 99 );
        //add_action( 'init', [ __CLASS__, 'get_single_claim_listing_callback' ], 99 );
        add_filter( 'rtcl_rest_api_config_data', [ __CLASS__, 'add_config' ] );
        if ( ClaimFunctions::claim_listing_enable() ) {
            add_filter( 'rtcl_rest_api_listing_data', [ __CLASS__, 'add_claim_data_to_listing_data' ], 11, 2 );
        }
    }

    public function init_rest_routes() {
        $claim_listings = new ClaimListingsApi();
        $claim_listings->register_routes();
        $settings = new SettingsApi();
        $settings->register_routes();
        if ( class_exists( RtclPro::class ) ) {
            new V1_ClaimListingsApi();
        }
    }


    public static function add_config( $config ) {

        if ( ClaimFunctions::claim_listing_enable() ) {
            $config['claim'] = [
                'badgeEnable'      => ClaimFunctions::is_enable_claim_badge(),
                'enableAttachment' => ClaimFunctions::is_enable_attachment_field(),
                'claimTitle'       => ClaimFunctions::get_claim_action_title(),
                'popupTitle'       => ClaimFunctions::get_claim_popup_title(),
                'buttonText'       => ClaimFunctions::get_claim_button_text(),
                'badge'            => [
                    'label'   => esc_html__( 'Claimed', 'rtcl-claim-listing' ),
                    'listing' => false,
                    'single'  => true,
                    'color'   => [
                        'bg'   => '#00b578',
                        'text' => '#ffffff'
                    ]
                ]
            ];
        }
        return $config;
    }


    public static function add_claim_data_to_listing_data( $data, $listing ) {
        if ( is_a( $listing, Listing::class ) && 'yes' == get_post_meta( $listing->get_id(), 'rtcl_claimed_listing', true ) ) {
            $data['isClaimed'] = true;
        }
        return $data;
    }

    public static function get_single_claim_listing_callback() {

        global $wpdb;

        $claim_listing_id = 1;

        $claims_table = $wpdb->prefix . "rtcl_claims";

        $claim = $wpdb->get_row( $wpdb->prepare( "SELECT * FROM {$claims_table} WHERE id=%d", [ $claim_listing_id ] ) );

        $response = [];

        if ( !empty( $claim ) ) {
            $response = [
                'id'            => $claim->id,
                'listing_id'    => $claim->listing_id,
                'listing_title' => rtcl()->factory->get_listing( $claim->listing_id )->get_the_title(),
                'date'          => $claim->created_at,
                'status'        => $claim->status
            ];
            if ( !empty( $claim->info ) ) {
                $info = maybe_unserialize( $claim->info );
                $response['claim_info'] = $info;
            }
            if ( $claim->user_id ) {
                $owner = get_userdata( $claim->user_id );
                if ( $owner ) {
                    $response['claimer'] = [
                        'id'      => $owner->data->ID,
                        'name'    => $owner->data->display_name,
                        'email'   => $owner->data->user_email,
                        'profile' => get_edit_user_link( $claim->user_id )
                    ];
                }
            }
            if ( $claim->prev_owner_id ) {
                $owner = get_userdata( $claim->prev_owner_id );
                if ( $owner ) {
                    $response['owner'] = [
                        'id'      => $owner->data->ID,
                        'name'    => $owner->data->display_name,
                        'email'   => $owner->data->user_email,
                        'profile' => get_edit_user_link( $claim->prev_owner_id )
                    ];
                }
            }
        }

    }

}