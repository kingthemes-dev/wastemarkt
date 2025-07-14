<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

use Rtcl\Controllers\Hooks\Filters;
use RtclPro\Helpers\Api;

if ( ! class_exists( 'RtclRestApiSellerVerificationV1' ) ) {
	class RtclRestApiSellerVerificationV1 {

		public function register_route() {
			register_rest_route( 'rtcl/v1', 'my/documents', [
				[
					'methods'             => WP_REST_Server::READABLE,
					'callback'            => [ $this, 'get_my_documents_callback' ],
					'permission_callback' => [ Api::class, 'permission_check' ]
				]
			] );
			register_rest_route( 'rtcl/v1', 'my/documents/photo_id', [
				[
					'methods'             => WP_REST_Server::CREATABLE,
					'callback'            => [ $this, 'upload_document_photo_id_callback' ],
					'permission_callback' => [ Api::class, 'permission_check' ],
					'args'                => [
						'image' => [
							'type'              => 'file',
							'validate_callback' => function ( $value, $request, $param ) {
								$files = $request->get_file_params();
								if ( empty( $files['image'] ) ) {
									return new WP_Error( 'rest_invalid_param', esc_html__( 'parameter image file field is required.', 'rtcl-seller-verification' ), [ 'status' => 400 ] );
								}

								return true;
							},
							'description'       => 'Image file is required field.',
						]
					]
				]
			] );

			register_rest_route( 'rtcl/v1', 'my/documents/other', [
				[
					'methods'             => WP_REST_Server::CREATABLE,
					'callback'            => [ $this, 'upload_other_document_callback' ],
					'permission_callback' => [ Api::class, 'permission_check' ],
					'args'                => [
						'other' => [
							'type'              => 'file',
							'validate_callback' => function ( $value, $request, $param ) {
								$files = $request->get_file_params();
								if ( empty( $files['other'] ) ) {
									return new WP_Error( 'rest_invalid_param', esc_html__( 'parameter other file field is required.', 'rtcl-seller-verification' ), [ 'status' => 400 ] );
								}

								return true;
							},
							'description'       => 'Others file is required field.',
						]
					]
				]
			] );
		}

		public function get_my_documents_callback( WP_REST_Request $request ) {
			Api::is_valid_auth_request();
			$user_id = get_current_user_id();
			if ( ! $user_id ) {
				$response = [
					'status'        => "error",
					'error'         => 'FORBIDDEN',
					'code'          => '403',
					'error_message' => "You are not logged in."
				];
				wp_send_json( $response, 403 );
			}

			return rest_ensure_response( [
				'photo_id'       => rtcl_seller_verification_get_photo_id_url( $user_id ),
				'other_document' => rtcl_seller_verification_get_document_file_url( $user_id )
			] );
		}

		public function upload_document_photo_id_callback( WP_REST_Request $request ) {
			Api::is_valid_auth_request();
			$user_id = get_current_user_id();
			if ( ! $user_id ) {
				$response = [
					'status'        => "error",
					'error'         => 'FORBIDDEN',
					'code'          => '403',
					'error_message' => "You are not logged in."
				];
				wp_send_json( $response, 403 );
			}
			$files = $request->get_file_params();
			if ( empty( $files['image'] ) ) {
				$response = [
					'status'        => "error",
					'error'         => 'BADREQUEST',
					'code'          => '400',
					'error_message' => "Image file field is required."
				];
				wp_send_json( $response, 400 );
			}

			require_once( ABSPATH . 'wp-admin/includes/file.php' );
			require_once( ABSPATH . 'wp-admin/includes/image.php' );
			Filters::beforeUpload();
			$status = wp_handle_upload( $files['image'], [ 'test_form' => false ] );
			Filters::afterUpload();
			if ( $status && isset( $status['error'] ) ) {
				$response = [
					'status'        => "error",
					'error'         => 'BADREQUEST',
					'code'          => '400',
					'error_message' => $status['error']
				];
				wp_send_json( $response, 400 );
			}
			$filename = $status['file'];
			// Check the type of tile. We'll use this as the 'post_mime_type'.
			$fileType = wp_check_filetype( basename( $filename ) );

			// Get the path to the upload directory.
			$wp_upload_dir = wp_upload_dir();

			// Prepare an array of post data for the attachment.
			$attachment = [
				'guid'           => $wp_upload_dir['url'] . '/' . basename( $filename ),
				'post_mime_type' => $fileType['type'],
				'post_title'     => preg_replace( '/\.[^.]+$/', '', basename( $filename ) ),
				'post_content'   => '',
				'post_status'    => 'inherit'
			];

			// Insert the attachment.
			$attach_id = wp_insert_attachment( $attachment, $filename );
			if ( is_wp_error( $attach_id ) ) {
				$response = [
					'status'        => "error",
					'error'         => 'BADREQUEST',
					'code'          => '400',
					'error_message' => $attach_id->get_error_message()
				];
				wp_send_json( $response, 400 );
			}

			if ( $existing_pid = absint( get_user_meta( $user_id, 'photo_id', true ) ) ) {
				wp_delete_attachment( $existing_pid, true );
			}
			update_user_meta( $user_id, 'photo_id', $attach_id );
			wp_update_attachment_metadata( $attach_id, wp_generate_attachment_metadata( $attach_id, $filename ) );
			$photo_id_url = wp_get_attachment_image_url( $attach_id, 'full' );

			return rest_ensure_response( $photo_id_url );
		}

		public function upload_other_document_callback( WP_REST_Request $request ) {
			Api::is_valid_auth_request();
			$user_id = get_current_user_id();
			if ( ! $user_id ) {
				$response = [
					'status'        => "error",
					'error'         => 'FORBIDDEN',
					'code'          => '403',
					'error_message' => "You are not logged in."
				];
				wp_send_json( $response, 403 );
			}
			$files = $request->get_file_params();
			if ( empty( $files['other'] ) ) {
				$response = [
					'status'        => "error",
					'error'         => 'BADREQUEST',
					'code'          => '400',
					'error_message' => "Others file field is required."
				];
				wp_send_json( $response, 400 );
			}

			require_once( ABSPATH . 'wp-admin/includes/file.php' );
			require_once( ABSPATH . 'wp-admin/includes/image.php' );
			Filters::beforeUpload();
			$status = wp_handle_upload( $files['other'], [ 'test_form' => false ] );
			Filters::afterUpload();
			if ( $status && isset( $status['error'] ) ) {
				$response = [
					'status'        => "error",
					'error'         => 'BADREQUEST',
					'code'          => '400',
					'error_message' => $status['error']
				];
				wp_send_json( $response, 400 );
			}
			$filename = $status['file'];
			// Check the type of tile. We'll use this as the 'post_mime_type'.
			$fileType = wp_check_filetype( basename( $filename ) );

			// Get the path to the upload directory.
			$wp_upload_dir = wp_upload_dir();

			// Prepare an array of post data for the attachment.
			$attachment = [
				'guid'           => $wp_upload_dir['url'] . '/' . basename( $filename ),
				'post_mime_type' => $fileType['type'],
				'post_title'     => preg_replace( '/\.[^.]+$/', '', basename( $filename ) ),
				'post_content'   => '',
				'post_status'    => 'inherit'
			];

			// Insert the attachment.
			$attach_id = wp_insert_attachment( $attachment, $filename );
			if ( is_wp_error( $attach_id ) ) {
				$response = [
					'status'        => "error",
					'error'         => 'BADREQUEST',
					'code'          => '400',
					'error_message' => $attach_id->get_error_message()
				];
				wp_send_json( $response, 400 );
			}

			if ( $existing_oid = absint( get_user_meta( $user_id, 'other_document_id', true ) ) ) {
				wp_delete_attachment( $existing_oid, true );
			}
			update_user_meta( $user_id, 'other_document_id', $attach_id );
			wp_update_attachment_metadata( $attach_id, wp_generate_attachment_metadata( $attach_id, $filename ) );
			$other_id_url = wp_get_attachment_url( $attach_id );

			return rest_ensure_response( $other_id_url );
		}

	}
}
