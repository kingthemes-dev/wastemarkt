<?php

namespace RtclVerification\Services;

use Rtcl\Helpers\Functions as RtclFunctions;
use RtclVerification\Helpers\Functions;

class GeezSMSGateway extends SmsGateway {

	protected $id = 'geezsms';

	/**
	 * @return array
	 */
	public function send_otp() {
		$auth_token    = RtclFunctions::get_option_item( 'rtcl_misc_settings', 'geezsms_token', false );

		if ( empty( $auth_token ) ) {
			return [
				'status' => false,
				'msg'    => __( 'Missing configuration for GeezSMS. Please contact with Administrator.', 'rtcl-verification' ),
			];
		}

		$message = apply_filters( 'rtcl_verification_geezsms_message', sprintf( 'Your OTP Code from %s is %d', RtclFunctions::get_blogname(), $this->otp_code ), $this->otp_code );

		try {

			$base_url       = 'https://api.geezsms.com/api/v1/sms/send';
			$request_url    = add_query_arg( 'token', $auth_token, $base_url );

			if ( ! empty( $this->to ) ) {
				$request_url = add_query_arg( array(
					'phone' => $this->to,
					'msg'   => $message
				), $request_url );
			}

			$response = wp_remote_get( $request_url );
			$body     = json_decode( wp_remote_retrieve_body( $response ), true );

			// Development purpose temporary object

			/*$body = (object) [
				'error' => false
			];*/

			if ( ! $body['error'] ) {

				$existRow = Functions::number_exist_in_verification( $this->to );

				if ( $existRow ) {
					$updated = Functions::update_otp_code( $this->to, $this->otp_code, null );
					if ( $updated ) {
						return [
							'status' => true,
							'msg'    => sprintf( __( 'OTP resent to %s', 'rtcl-verification' ), $this->to )
						];
					}
				} else {
					$inserted = Functions::insert_otp_to_db( $this->to, $this->otp_code, null );
					if ( $inserted ) {
						return [
							'status' => true,
							'msg'    => sprintf( __( 'OTP sent to %s', 'rtcl-verification' ), $this->to )
						];
					}
				}

			} else {
				return [
					'status' => false,
					'msg'    => __( 'OTP not send, have error in response!', 'rtcl-verification' )
				];
			}

		} catch ( \Exception $e ) {
			return [
				'status' => false,
				'msg'    => $e->getMessage()
			];
		}
	}

}