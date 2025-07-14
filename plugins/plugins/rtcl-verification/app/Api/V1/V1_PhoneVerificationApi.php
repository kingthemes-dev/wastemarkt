<?php

namespace RtclVerification\Api\V1;

use RtclPro\Helpers\Api;
use RtclVerification\Helpers\Functions;
use RtclVerification\Services\GeezSMSGateway;
use RtclVerification\Services\TwilioGateway;
use WP_REST_Request;
use WP_REST_Response;
use WP_REST_Server;

class V1_PhoneVerificationApi {
	public function register_route() {
		register_rest_route( 'rtcl/v1', 'verification/send-otp', [
			[
				'methods'             => WP_REST_Server::CREATABLE,
				'callback'            => [ $this, 'send_otp_callback' ],
				'permission_callback' => [ Api::class, 'permission_check' ],
				'args'                => [
					'phone'   => [
						'required'          => true,
						'description'       => 'Phone number.',
						'type'              => 'string',
						'sanitize_callback' => 'sanitize_text_field',
						'validate_callback' => 'rest_validate_request_arg',
					],
					'gateway' => [
						'required'    => true,
						'description' => 'Sms gateway type.',
						'type'        => 'string',
						'enum'        => [ 'twilio', 'firebase', 'geezsms' ]
					]
				]
			]
		] );
		register_rest_route( 'rtcl/v1', 'verification/verify-otp', [
			[
				'methods'             => WP_REST_Server::CREATABLE,
				'callback'            => [ $this, 'verify_otp_callback' ],
				'permission_callback' => [ Api::class, 'permission_check' ],
				'args'                => [
					'phone' => [
						'required'          => true,
						'description'       => 'Phone number.',
						'type'              => 'string',
						'sanitize_callback' => 'sanitize_text_field',
						'validate_callback' => 'rest_validate_request_arg',
					],
					'code'  => [
						'required'    => true,
						'description' => 'Otp code',
						'type'        => 'string',
						'minLength'   => 6,
						'maxLength'   => 6,
					]
				]
			]
		] );
		register_rest_route( 'rtcl/v1', 'verification/store-firebase-verified-otp', [
			[
				'methods'             => WP_REST_Server::CREATABLE,
				'callback'            => [ $this, 'firebase_verified_otp_callback' ],
				'permission_callback' => [ Api::class, 'permission_check' ],
				'args'                => [
					'phone' => [
						'required'          => true,
						'description'       => 'Phone number.',
						'type'              => 'string',
						'sanitize_callback' => 'sanitize_text_field',
					],
					'uid'   => [
						'required'    => true,
						'description' => 'Firebase message send uid code',
						'type'        => 'string',
					],
					'code'  => [
						'required'    => true,
						'description' => 'Otp code',
						'type'        => 'string',
						'minLength'   => 6,
						'maxLength'   => 6,
					]
				]
			]
		] );
	}

	public function firebase_verified_otp_callback( WP_REST_Request $request ) {
		Api::check_is_auth_user_request();
		$phone    = _sanitize_text_fields( $request->get_param( "phone" ) );
		$code     = $request->get_param( 'code' );
		$uid      = _sanitize_text_fields( $request->get_param( 'uid' ) );
		$response = Functions::save_firebase_otp( $phone, $code, $uid );
		if ( empty( $response['status'] ) ) {
			return new WP_REST_Response(
				[
					'status'  => "error",
					'error'   => 'FORBIDDEN',
					'message' => __( 'Error while saving the verified otp for firebase.', 'rtcl-verification' )
				],
				409
			);
		}
		$user_id = get_current_user_id();
		if ( $user_id ) {
			update_user_meta( $user_id, '_rtcl_phone', $phone );
			Functions::add_user_verified_phone( $user_id );
		}

		return rest_ensure_response( [
			'status'  => "success",
			'message' => __( 'OTP code saved for firebase.', 'rtcl-verification' )
		] );
	}

	public function send_otp_callback( WP_REST_Request $request ) {
		Api::check_is_auth_user_request();
		$phone   = $request->get_param( "phone" );
		$gateway = $request->get_param( "gateway" );
		if ( Functions::number_is_used( $phone ) ) {
			return new WP_REST_Response(
				[
					'status'  => "error",
					'error'   => 'FORBIDDEN',
					'message' => sprintf( __( 'This %s number is already used.', 'rtcl-verification' ), $phone )
				],
				409
			);
		}
		$sendOtp      = false;
		$errorMessage = '';
		if ( 'twilio' === $gateway ) {
			$twilio   = new TwilioGateway( $phone );
			$response = $twilio->send_otp();
			if ( ! empty( $response['status'] ) && true === $response['status'] ) {
				$sendOtp = true;
			} else {
				$errorMessage = ! empty( $response['msg'] ) ? $response['msg'] : __( 'Fetch error to send twilio OTP', 'rtcl-verification' );
			}
		} elseif ( 'geezsms' === $gateway ) {
			$geez_sms = new GeezSMSGateway( $phone );
			$response = $geez_sms->send_otp();
			if ( ! empty( $response['status'] ) && true === $response['status'] ) {
				$sendOtp = true;
			} else {
				$errorMessage = ! empty( $response['msg'] ) ? $response['msg'] : __( 'Fetch error to send twilio OTP', 'rtcl-verification' );
			}
		}elseif ( 'firebase' === $gateway ) {
			$sendOtp = true;
		} else {
			$errorMessage = __( 'Sms gateway not defined.', 'rtcl-verification' );
		}
		if ( ! $sendOtp ) {
			return new WP_REST_Response(
				[
					'status'  => "error",
					'error'   => 'FORBIDDEN',
					'message' => $errorMessage
				],
				400
			);
		}

		return rest_ensure_response( [
			'status'  => "success",
			'message' => sprintf( __( 'OTP code is sent to %s.', 'rtcl-verification' ), $phone )
		] );
	}

	public function verify_otp_callback( WP_REST_Request $request ) {
		Api::check_is_auth_user_request();
		$phone    = $request->get_param( "phone" );
		$otpCode  = $request->get_param( "code" );
		$response = Functions::verify_otp( $phone, $otpCode );

		return rest_ensure_response( $response );
	}
}