<?php

namespace RtclClaimListing\Api\V1;

use RtclClaimListing\Helpers\Functions;
use WP_REST_Request;
use WP_REST_Server;

class SettingsApi
{

    public function register_routes() {
        register_rest_route('rtcl-claim/v1', 'listings/settings', [
            [
                'methods' => WP_REST_Server::READABLE,
                'callback' => [$this, 'get_claim_settings'],
                'permission_callback' => [$this, 'permission_check']
            ],
            [
                'methods' => WP_REST_Server::EDITABLE,
                'callback' => [$this, 'update_claim_settings'],
                'permission_callback' => [$this, 'permission_check'],
            ]
        ]);
    }

    public function get_claim_settings() {
        $settings = get_option('rtcl_claim_settings');

        wp_send_json($settings);
    }

    public function update_claim_settings($request) {

        if (!$request->get_param('settings')) {
            $response = [
                'status' => "error",
                'error' => 'BADREQUEST',
                'code' => '400',
                'error_message' => esc_html__('Settings data not found.', "rtcl-claim-listing")
            ];
            wp_send_json($response, 400);
        }

        $success = false;
        $message = esc_html__('Something wrong!', 'rtcl-claim-listing');

        $options = $request->get_param('settings');

        if (is_array($options)) {
            update_option('rtcl_claim_settings', $options);
            delete_transient('rtcl_claim_options');
            $success = true;
            $message = esc_html__('Updated settings successfully', 'rtcl-claim-listing');
        }

        $response = [
            'success' => $success,
            'msg' => $message
        ];

        return rest_ensure_response($response);
    }

    /**
     * @param WP_REST_Request $request
     *
     * @return bool
     */
    public function permission_check(WP_REST_Request $request) {
        return true;

        return current_user_can('manage_options');
    }
}