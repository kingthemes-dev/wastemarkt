<?php

namespace RtclVerification\Helpers;

use Rtcl\Helpers\Functions as RtclFunctions;
use RtclVerification\Hooks\ActionHooks;
use RtclVerification\Services\GeezSMSGateway;
use RtclVerification\Services\TwilioGateway;

class Functions {

	function init() {
		rtcl()->countries->get_countries();
	}

	public static function insert_otp_to_db( $phone, $code, $sid ) {
		global $wpdb;

		$second      = RtclFunctions::get_option_item( 'rtcl_misc_settings', 'verification_expired_time', 100 );
		$current     = current_time( 'mysql' );
		$expiredTime = date( 'Y-m-d H:i:s', strtotime( $current ) + $second );

		$phone_verification_table_name = $wpdb->prefix . "rtcl_phone_verification";
		$data                          = [
			'phone'      => $phone,
			'code'       => $code,
			'ref_id'     => $sid,
			'expired_at' => $expiredTime
		];

		$wpdb->insert( $phone_verification_table_name, $data );

		return $wpdb->insert_id;
	}

	public static function update_otp_code( $phone, $code, $sid ) {
		global $wpdb;

		$phone_verification_table_name = $wpdb->prefix . "rtcl_phone_verification";

		$second      = RtclFunctions::get_option_item( 'rtcl_misc_settings', 'verification_expired_time', 100 );
		$current     = current_time( 'mysql' );
		$expiredTime = date( 'Y-m-d H:i:s', strtotime( $current ) + $second );

		$data = [
			'code'       => $code,
			'ref_id'     => $sid,
			'expired_at' => $expiredTime,
			'updated_at' => $current
		];

		$where = [
			'phone' => $phone
		];

		return $wpdb->update( $phone_verification_table_name, $data, $where );
	}

	public static function generate_otp() {
		return wp_rand( 100000, 999999 );
	}

	public static function send_otp( $to, $data = [] ) {
		$gateway = RtclFunctions::get_option_item( 'rtcl_misc_settings', 'verification_gateway', 'firebase' );

		$response = [
			'status' => false,
			'msg'    => esc_html__( 'Error message', 'rtcl-verification' )
		];

		$used = self::number_is_used( $to );

		if ( $used ) {
			$response['msg'] = sprintf( __( 'This %s number is already used.', 'rtcl-verification' ), $to );
		} else {
			if ( 'twilio' === $gateway ) {
				$twilio   = new TwilioGateway( $to, $data );
				$response = $twilio->send_otp();
			} else if ( 'geezsms' === $gateway ) {
				$geez_sms = new GeezSMSGateway( $to, $data );
				$response = $geez_sms->send_otp();
			} else if ( 'firebase' === $gateway ) {
				$response = [
					'status' => true,
					'msg'    => sprintf( __( 'OTP sent to %s', 'rtcl-verification' ), $to )
				];
			}
		}

		return $response;
	}

	public static function number_is_verified( $to ) {
		global $wpdb;

		$phone_table_name = $wpdb->prefix . "rtcl_phone";
		$user_id          = get_current_user_id();

		$results = $wpdb->get_results( $wpdb->prepare( "SELECT user_id FROM {$phone_table_name} WHERE phone = %s AND user_id = %d", [
			$to,
			$user_id
		] ) );

		return is_array( $results ) && count( $results );

	}

	public static function number_is_used( $to ) {
		global $wpdb;

		$phone_table_name = $wpdb->prefix . "rtcl_phone";

		$results = $wpdb->get_results( $wpdb->prepare( "SELECT user_id FROM {$phone_table_name} WHERE phone = %s", $to ) );

		return ( is_array( $results ) && count( $results ) ) ? count( $results ) : 0;
	}

	public static function number_exist_in_verification( $to ) {
		global $wpdb;

		$phone_verification_table_name = $wpdb->prefix . "rtcl_phone_verification";

		$results = $wpdb->get_results( $wpdb->prepare( "SELECT id FROM {$phone_verification_table_name} WHERE phone = %s", $to ) );

		return ( is_array( $results ) && count( $results ) ) ? count( $results ) : 0;
	}

	public static function verify_otp( $phone_no, $otp_code ) {
		global $wpdb;

		$status  = false;
		$message = esc_html__( 'Error message!', 'rtcl-verification' );

		$phone_verification_table_name = $wpdb->prefix . "rtcl_phone_verification";

		$results = $wpdb->get_results( $wpdb->prepare( "SELECT expired_at FROM {$phone_verification_table_name} WHERE phone = %s AND code = %d", [
			$phone_no,
			$otp_code
		] ) );

		$now = $expired_time = strtotime( current_time( 'mysql' ) );
		if ( is_array( $results ) && ! empty( $results ) ) {
			$row          = end( $results );
			$expired_time = strtotime( $row->expired_at );

			if ( $expired_time > $now ) {
				// Update expired time 3 hours
				$data  = [
					'expired_at' => date( 'Y-m-d H:i:s', $now + ( 3600 * 3 ) ),
					'verified'   => 1,
				];
				$where = [
					'phone' => $phone_no,
					'code'  => $otp_code
				];

				$updated = $wpdb->update( $phone_verification_table_name, $data, $where );
				// If Existing user verify from edit-account
				$user_id = get_current_user_id();
				if ( $updated && $user_id ) {
					update_user_meta( $user_id, '_rtcl_phone', $phone_no );
					Functions::add_user_verified_phone( $user_id );
				}

				if ( $updated ) {
					$status  = true;
					$message = esc_html__( 'Verified the number', 'rtcl-verification' );
				} else {
					$message = esc_html__( 'Something wrong!', 'rtcl-verification' );
				}
			} else {
				$message = esc_html__( 'Expired OTP Code', 'rtcl-verification' );
			}
		} else {
			$message = esc_html__( 'Invalid OTP Code', 'rtcl-verification' );
		}

		return [
			'status' => $status,
			'msg'    => $message
		];

	}

	public static function save_firebase_otp( $phone_no, $otp_code, $uid ) {
		global $wpdb;

		$updated = '';
		$status  = false;
		$message = esc_html__( 'Error message!', 'rtcl-verification' );

		$phone_verification_table_name = $wpdb->prefix . "rtcl_phone_verification";

		$current     = current_time( 'mysql' );
		$expiredTime = date( 'Y-m-d H:i:s', strtotime( $current ) + ( 3600 * 3 ) );

		$existRow = Functions::number_exist_in_verification( $phone_no );

		if ( $existRow ) {

			$data = [
				'code'       => $otp_code,
				'ref_id'     => $uid,
				'expired_at' => $expiredTime,
				'updated_at' => $current,
				'verified'   => 1,
			];

			$where = [
				'phone' => $phone_no
			];

			$updated = $wpdb->update( $phone_verification_table_name, $data, $where );

		} else {

			$data = [
				'phone'      => $phone_no,
				'code'       => $otp_code,
				'ref_id'     => $uid,
				'expired_at' => $expiredTime,
				'verified'   => 1,
			];

			$wpdb->insert( $phone_verification_table_name, $data );
		}

		if ( $updated || $wpdb->insert_id ) {
			$status  = true;
			$message = esc_html__( 'Verified the number', 'rtcl-verification' );
		}

		return [
			'status' => $status,
			'msg'    => $message
		];

	}

	public static function add_user_verified_phone( $user_id ) {
		$phone = get_user_meta( $user_id, '_rtcl_phone', true );
		if ( $phone ) {
			global $wpdb;
			$deleted = $wpdb->delete( $wpdb->prefix . "rtcl_phone_verification",
				[
					'phone'    => $phone,
					'verified' => 1
				]
			);
			if ( $deleted ) {
				$phoneTableName   = $wpdb->prefix . "rtcl_phone";
				$checkPhoneExists = $wpdb->get_var( $wpdb->prepare( "SELECT ID FROM {$phoneTableName} WHERE phone = %s", $phone ) );
				if ( ! $checkPhoneExists ) {
					$checkUserExists = $wpdb->get_var( $wpdb->prepare( "SELECT ID FROM {$phoneTableName} WHERE user_id = %d", $user_id ) );

					if ( ! $checkUserExists ) {
						$checkIfPrimaryExists = $wpdb->get_var( $wpdb->prepare( "SELECT ID FROM $phoneTableName WHERE user_id =  %d AND type = %s", $user_id, 'primary' ) );
						$wpdb->insert( $phoneTableName, [
							'phone'   => $phone,
							'user_id' => $user_id,
							'type'    => $checkIfPrimaryExists ? '' : 'primary'
						] );
					} else {
						$wpdb->update(
							$phoneTableName,
							[
								'phone'       => $phone,
								'verified_at' => current_time( 'mysql' ),
								'updated_at'  => current_time( 'mysql' ),
							],
							[
								'id' => $checkUserExists
							]
						);
					}
				}
			}
		}
	}

	public static function sms_gateway_options() {
		$options = apply_filters( 'rtcl_verification_gateway_options', array(
			'firebase'  => esc_html__( 'Google Firebase', 'rtcl-verification' ),
			'twilio'    => esc_html__( 'Twilio', 'rtcl-verification' ),
			'geezsms'   => esc_html__( 'GeezSMS', 'rtcl-verification' ),
		) );

		return $options;
	}
}