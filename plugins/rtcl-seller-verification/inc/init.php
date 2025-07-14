<?php

use Rtcl\Helpers\Functions;

if ( ! class_exists( RtclSellerVerification::class ) ) {
	final class RtclSellerVerification {
		protected static $instance = null;

		private function __construct() {
			add_action( 'init', [ $this, 'load_textdomain' ], 20 );
			add_action( 'wp_enqueue_scripts', [ $this, 'front_end_script' ] );
			add_action( 'wp_enqueue_scripts', [ $this, 'load_scripts' ] );
			add_action( 'admin_enqueue_scripts', [ $this, 'load_admin_scripts' ] );
			$this->includes();
			$this->init();
		}

		public static function getInstance() {
			if ( null === self::$instance ) {
				self::$instance = new self();
			}

			return self::$instance;
		}

		public function load_textdomain() {
			load_plugin_textdomain( 'rtcl-seller-verification', false, dirname( plugin_basename( RTCL_SELLER_FILE ) ) . '/languages' );
		}

		public function includes() {
			require_once "helpers/functions.php";
			require_once "hooks/RtclSellerActionHooks.php";
			require_once "hooks/RtclSellerFilterHooks.php";
			require_once "hooks/RtclSellerAjaxHooks.php";
			require_once "admin/RtclSellerAdminActionHooks.php";
			require_once "emails/RtclSellerDocumentEmail.php";
			if ( class_exists( RtclPro::class ) ) {
				require_once "api/init.php";
			}
		}

		public function init() {
			new RtclSellerActionHooks();
			new RtclSellerFilterHooks();
			new RtclSellerAjaxHooks();

			if ( is_admin() ) {
				new RtclSellerAdminActionHooks();
			}
		}

		public function front_end_script() {
			$version        = ( defined( 'WP_DEBUG' ) && WP_DEBUG ) ? time() : RTCL_SELLER_VERSION;
			$max_image_size = Functions::get_max_upload();
			$max_file_size  = rtcl_seller_verification_get_max_file_upload_size();
			$user_id        = is_user_logged_in() ? get_current_user_id() : 0;
			if ( ! empty( $_GET['user_id'] ) && is_numeric( $_GET['user_id'] ) ) {
				$user_id = $_GET['user_id'];
			}

			wp_register_style( 'rtcl-seller-verification', RTCL_SELLER_URL . '/assets/css/seller-verification.css', [ 'rtcl-public' ], $version );
			wp_register_script( 'rtcl-seller-verification', RTCL_SELLER_URL . '/assets/js/seller-verification.js', [
				'jquery',
				'rtcl-validator'
			], $version, true );

			wp_localize_script( 'rtcl-seller-verification', 'rtcl_seller', apply_filters( 'rtcl_seller_localize', [
				"ajaxurl"               => admin_url( "admin-ajax.php" ),
				rtcl()->nonceId         => wp_create_nonce( rtcl()->nonceText ),
				"user_id"               => $user_id,
				"max_image_size"        => $max_image_size,
				"max_file_size"         => rtcl_seller_verification_get_max_file_upload_size(),
				"image_allowed_type"    => (array) Functions::get_option_item( 'rtcl_misc_settings', 'image_allowed_type', [
					'png',
					'jpeg',
					'jpg'
				] ),
				"confirm_text"          => esc_html__( "Are You sure to delete?", 'rtcl-seller-verification' ),
				"remove_text"           => esc_html__( "Remove", 'rtcl-seller-verification' ),
				"view_text"             => esc_html__( "Download", 'rtcl-seller-verification' ),
				"error_common"          => esc_html__( "Error while upload image", "rtcl-seller-verification" ),
				"error_image_size"      => sprintf( esc_html__( "Image size is more then %s.", "rtcl-seller-verification" ),
					Functions::formatBytes( $max_image_size ) ),
				"error_file_size"       => sprintf( esc_html__( "File size is more then %s.", "rtcl-seller-verification" ),
					Functions::formatBytes( $max_file_size ) ),
				"error_image_extension" => esc_html__( "File extension not supported.", "rtcl-seller-verification" ),
				"server_error"          => esc_html__( "Server Error.", "rtcl-seller-verification" ),
			] ) );

		}

		function load_scripts() {
			global $wp;

			$rtcl_style_opt    = Functions::get_option( "rtcl_style_settings" );
			$verificationColor = ! empty( $rtcl_style_opt['sv_label_color'] ) ? $rtcl_style_opt['sv_label_color'] : '#008000';

			$rootVar = null;
			if ( $verificationColor ) {
				$rootVar .= '--rtcl-badge-verification-color:' . $verificationColor . ';';
			}

			wp_enqueue_style( 'rtcl-seller-verification' );
			if ( Functions::is_account_page() && isset( $wp->query_vars['my-documents'] ) ) {
				wp_enqueue_script( 'rtcl-seller-verification' );
			}
			if ( $rootVar ) {
				$rootVar = ':root{' . $rootVar . '}';
				wp_add_inline_style( 'rtcl-seller-verification', $rootVar );
			}
		}

		function load_admin_scripts() {
			global $pagenow;

			$version        = ( defined( 'WP_DEBUG' ) && WP_DEBUG ) ? time() : RTCL_SELLER_VERSION;
			$max_image_size = Functions::get_max_upload();
			$max_file_size  = rtcl_seller_verification_get_max_file_upload_size();
			$user_id        = is_user_logged_in() ? get_current_user_id() : 0;
			if ( ! empty( $_GET['user_id'] ) && is_numeric( $_GET['user_id'] ) ) {
				$user_id = $_GET['user_id'];
			}

			if ( $pagenow && ( 'profile.php' === $pagenow || 'user-edit.php' === $pagenow ) ) {
				wp_enqueue_style( 'rtcl-public', rtcl()->get_assets_uri( "css/rtcl-public.min.css" ), '' );
				wp_enqueue_style( 'rtcl-seller-verification-admin', RTCL_SELLER_URL . '/assets/css/seller-verification-admin.css', [ 'rtcl-public' ],
					$version );
				wp_enqueue_script( 'rtcl-seller-verification', RTCL_SELLER_URL . '/assets/js/seller-verification.js', [ 'jquery' ], $version, true );

				wp_localize_script( 'rtcl-seller-verification', 'rtcl_seller', apply_filters( 'rtcl_seller_localize', [
					"ajaxurl"               => admin_url( "admin-ajax.php" ),
					rtcl()->nonceId         => wp_create_nonce( rtcl()->nonceText ),
					"user_id"               => $user_id,
					"max_image_size"        => $max_image_size,
					"max_file_size"         => rtcl_seller_verification_get_max_file_upload_size(),
					"image_allowed_type"    => (array) Functions::get_option_item( 'rtcl_misc_settings', 'image_allowed_type', [
						'png',
						'jpeg',
						'jpg'
					] ),
					"confirm_text"          => esc_html__( "Are You sure to delete?", 'rtcl-seller-verification' ),
					"remove_text"           => esc_html__( "Remove", 'rtcl-seller-verification' ),
					"view_text"             => esc_html__( "Download", 'rtcl-seller-verification' ),
					"error_common"          => esc_html__( "Error while upload image", "rtcl-seller-verification" ),
					"error_image_size"      => sprintf( esc_html__( "Image size is more then %s.", "rtcl-seller-verification" ),
						Functions::formatBytes( $max_image_size ) ),
					"error_file_size"       => sprintf( esc_html__( "File size is more then %s.", "rtcl-seller-verification" ),
						Functions::formatBytes( $max_file_size ) ),
					"error_image_extension" => esc_html__( "File extension not supported.", "rtcl-seller-verification" ),
					"server_error"          => esc_html__( "Server Error.", "rtcl-seller-verification" ),
				] ) );
			}
		}

		/**
		 * @return string
		 */
		public function get_plugin_template_path() {
			return $this->plugin_path() . '/templates/';
		}

		/**
		 * Get the plugin path.
		 *
		 * @return string
		 */
		public function plugin_path() {
			return untrailingslashit( plugin_dir_path( RTCL_SELLER_FILE ) );
		}

	}

	function rtclSellerVerification() {
		return RtclSellerVerification::getInstance();
	}

	add_action( 'plugins_loaded', 'rtclSellerVerification', 30 );
}