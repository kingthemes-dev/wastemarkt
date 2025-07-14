<?php

namespace RadiusTheme\COUPON\Api\V1;

use RadiusTheme\COUPON\Models\Coupon;
use RtclPro\Helpers\Api;
use WP_REST_Request;
use WP_REST_Response;
use WP_REST_Server;

class V1_CouponApi {
	public function register_route() {
		register_rest_route( 'rtcl/v1', 'coupon/apply', [
			[
				'methods'             => WP_REST_Server::CREATABLE,
				'callback'            => [ $this, 'apply_coupon_callback' ],
				'permission_callback' => [ Api::class, 'permission_check' ],
				'args'                => [
					'plan_id'     => [
						'required'          => true,
						'type'              => 'integer',
						'description'       => esc_html__( 'Plane id', 'rtcl-coupon' ),
						'sanitize_callback' => 'absint',
					],
					'coupon_code' => [
						'required'    => true,
						'description' => esc_html__( 'Coupon code type.', 'rtcl-coupon' ),
						'type'        => 'string'
					]
				]
			]
		] );
	}

	public function apply_coupon_callback( WP_REST_Request $request ) {
		Api::is_valid_auth_request();
		$user_id = get_current_user_id();
		if ( ! $user_id ) {
			wp_send_json( [
				'status'        => "error",
				'error'         => 'FORBIDDEN',
				'code'          => '403',
				'message' => esc_html__( "You are not logged in.", 'rtcl-coupon' )
			], 403 );
		}
		$plan_id       = absint( $request->get_param( "plan_id" ) );
		$coupon_code   = sanitize_text_field( wp_unslash( trim( $request->get_param( "coupon_code" ) ) ) );
		$coupon        = new Coupon( $coupon_code, $plan_id );
		$coupon_errors = $coupon->get_errors();
		if ( ! empty( $coupon_errors ) ) {
			$error_key = array_keys( $coupon_errors )[0];

			return new WP_REST_Response(
				[
					'status'  => "error",
					'error'   => $error_key,
					'message' => $coupon_errors[ $error_key ]
				],
				409
			);
		}
		$coupon_data = $coupon->get_coupon_data();

		return rest_ensure_response( $coupon_data );
	}
}