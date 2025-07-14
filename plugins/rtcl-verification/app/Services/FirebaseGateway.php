<?php

namespace RtclVerification\Services;


use Rtcl\Helpers\Functions as RtclFunctions;

class FirebaseGateway {
	public static function getSettings() {
		$settings = RtclFunctions::get_option( 'rtcl_misc_settings' );

		return [
			'apiKey'            => isset( $settings['firebase_api_key'] ) ? $settings['firebase_api_key'] : '',
			'appId'             => isset( $settings['firebase_app_id'] ) ? $settings['firebase_app_id'] : '',
			'projectId'         => isset( $settings['firebase_project_id'] ) ? $settings['firebase_project_id'] : '',
			'authDomain'        => isset( $settings['firebase_project_id'] ) ? $settings['firebase_project_id'] . '.firebaseapp.com' : '',
			'storageBucket'     => isset( $settings['firebase_project_id'] ) ? $settings['firebase_project_id'] . '.appspot.com' : '',
			'messagingSenderId' => isset( $settings['firebase_sender_id'] ) ? $settings['firebase_sender_id'] : '',
			'measurementId'     => isset( $settings['firebase_measurement_id'] ) ? $settings['firebase_measurement_id'] : ''
		];
	}
}