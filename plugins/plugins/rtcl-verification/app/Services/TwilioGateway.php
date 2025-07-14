<?php

namespace RtclVerification\Services;

use Rtcl\Helpers\Functions as RtclFunctions;
use RtclVerification\Helpers\Functions;
use Twilio\Exceptions\TwilioException;
use Twilio\Rest\Client;

class TwilioGateway extends SmsGateway {

	protected $id = 'twilio';

	/**
	 * @return array
	 */
	public function send_otp() {
		$account_sid   = RtclFunctions::get_option_item( 'rtcl_misc_settings', 'twilio_site_id', false );
		$auth_token    = RtclFunctions::get_option_item( 'rtcl_misc_settings', 'twilio_auth_token', false );
		$twilio_number = RtclFunctions::get_option_item( 'rtcl_misc_settings', 'twilio_phone_from', false );
		if ( empty( $account_sid ) || empty( $auth_token ) || empty( $twilio_number ) ) {
			return [
				'status' => false,
				'msg'    => __( 'Missing configuration for twilio. Please contact with administrator.', 'rtcl-verification' ),
			];
		}

		// Your Account SID and Auth Token from twilio.com/console
		//$account_sid = 'AC8df0ec5c6441e2d0b473a16240c7ae99';
		//$auth_token  = '94548845a1203f8c331d726ddd7e6b48';
		// In production, these should be environment variables. E.g.:
		// $auth_token = $_ENV["TWILIO_AUTH_TOKEN"]

		// A Twilio number you own with SMS capabilities
		//$twilio_number = "+18043957190";

		$message = RtclFunctions::get_blogname() . ' security code ' . $this->otp_code;

		try {
			$client = new Client( $account_sid, $auth_token );

			// Send OTP Code
			$response = $client->messages->create(
				$this->to,
				[
					'from' => $twilio_number,
					'body' => $message
				]
			);

			// Development purpose temporary object

			/*$response = (object) [
				'errorCode' => '',
				'sid'       => wp_generate_password(),
			];*/

			if ( empty( $response->errorCode ) && $response->sid ) {

				$existRow = Functions::number_exist_in_verification( $this->to );

				if ( $existRow ) {
					$updated = Functions::update_otp_code( $this->to, $this->otp_code, $response->sid );
					if ( $updated ) {
						return [
							'status' => true,
							'msg'    => sprintf( __( 'OTP resent to %s', 'rtcl-verification' ), $this->to )
						];
					}
				} else {
					$inserted = Functions::insert_otp_to_db( $this->to, $this->otp_code, $response->sid );
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
					'msg'    => $response->error_message
				];
			}

		} catch ( TwilioException $e ) {
			return [
				'status' => false,
				'msg'    => $e->getMessage()
			];
		}
	}

}