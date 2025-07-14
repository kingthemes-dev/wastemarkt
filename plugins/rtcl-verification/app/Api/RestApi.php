<?php

namespace RtclVerification\Api;

use Rtcl\Helpers\Functions as RtclFunctions;
use RtclVerification\Api\V1\V1_PhoneVerificationApi;
use RtclVerification\Helpers\Functions;

class RestApi {
	public function __construct() {
		add_action( 'rest_api_init', [ &$this, 'register_rest_api' ] );
		add_filter( 'rtcl_rest_api_config_data', [ &$this, 'add_verification_config' ] );
		add_filter( 'rtcl_rest_api_user_data', [ &$this, 'add_user_data' ] );
	}

	/**
	 * @param array $user_data
	 *
	 * @return array
	 */
	public static function add_user_data( $user_data ) {
		$user_data['phone_verified'] = Functions::number_is_verified( $user_data['phone'] );

		return $user_data;
	}

	/**
	 * @param array $config
	 *
	 * @return array
	 */
	public static function add_verification_config( $config ) {
		$config['verification'] = [
			'gateway'          => RtclFunctions::get_option_item( 'rtcl_misc_settings', 'verification_gateway', 'firebase' ),
			'expired_time'     => RtclFunctions::get_option_item( 'rtcl_misc_settings', 'verification_expired_time', 100 ),
			'post_restriction' => RtclFunctions::get_option_item( 'rtcl_misc_settings', 'verification_post_restriction', false, 'checkbox' ),
		];

		$defaultCountry  = RtclFunctions::get_option_item( 'rtcl_misc_settings', 'verification_default_country' );
		$specificCountry = RtclFunctions::get_option_item( 'rtcl_misc_settings', 'verification_country_list', [], 'multiselect' );

		if ( is_array( $specificCountry ) && ! empty( $specificCountry ) ) {
			$config['verification']['country_list'] = $specificCountry;
		}

		if ( ! empty( $defaultCountry ) ) {
			if ( ! empty( $specificCountry ) && ! in_array( $defaultCountry, $specificCountry ) ) {
				$defaultCountry = $specificCountry[0];
			}

			$config['verification']['default_country'] = $defaultCountry;
		}

		return $config;
	}

	public function register_rest_api() {
		( new V1_PhoneVerificationApi() )->register_route();
	}

}
