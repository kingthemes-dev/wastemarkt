<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

use Rtcl\Helpers\Functions as RtclFunctions;

if ( ! class_exists( 'RtclSellerVerificationRestApi' ) ) {
	final class RtclSellerVerificationRestApi {
		protected static $instance = null;


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
			$user_id                      = ! empty( $user_data['id'] ) ? absint( $user_data['id'] ) : 0;
			$user_data['seller_verified'] = rtcl_sv_check_verified_user( $user_id );

			return $user_data;
		}

		/**
		 * @param array $config
		 *
		 * @return array
		 */
		public static function add_verification_config( $config ) {
			$config['seller_verification'] = [
				'badge_color' => RtclFunctions::get_option_item( 'rtcl_misc_settings', 'sv_label_color', '#008000' ),
			];

			return $config;
		}

		public function register_rest_api() {
			require_once "v1/RtclRestApiSellerVerificationV1.php";
			( new RtclRestApiSellerVerificationV1() )->register_route();
		}


		public static function getInstance() {
			if ( null === self::$instance ) {
				self::$instance = new self();
			}

			return self::$instance;
		}
	}

	RtclSellerVerificationRestApi::getInstance();
}
