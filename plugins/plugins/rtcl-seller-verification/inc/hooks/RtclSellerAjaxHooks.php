<?php

use Rtcl\Controllers\Hooks\Filters;
use Rtcl\Helpers\Functions;

class RtclSellerAjaxHooks {

	public function __construct() {
		add_action( 'wp_ajax_rtcl_ajax_documents_photo_upload', [ $this, 'rtcl_ajax_documents_photo_upload' ] );
		add_action( 'wp_ajax_rtcl_ajax_documents_photo_delete', [ $this, 'rtcl_ajax_documents_photo_delete' ] );
		add_action( 'wp_ajax_rtcl_ajax_document_file_upload', [ $this, 'rtcl_ajax_document_file_upload' ] );
		add_action( 'wp_ajax_rtcl_ajax_documents_file_delete', [ $this, 'rtcl_ajax_documents_file_delete' ] );
		add_action( 'wp_ajax_rtcl_ajax_documents_file_download', [ $this, 'rtcl_ajax_document_photo_download' ] );
	}

	public static function rtcl_ajax_document_photo_download() {

		if ( ! wp_verify_nonce( $_REQUEST['__rtcl_wpnonce'], rtcl()->nonceText ) ) {
			wp_send_json_error();
		}

		$user_id   = isset( $_REQUEST['user_id'] ) ? absint( $_REQUEST['user_id'] ) : get_current_user_id();
		$attach_id = get_user_meta( $user_id, 'other_document_id', true );
		$src       = wp_get_attachment_url( $attach_id );

		$data = [
			'path'      => $src,
			'file_name' => basename( $src )
		];

		wp_send_json_success( $data );
	}

	public function rtcl_ajax_documents_photo_upload() {
		if ( ! function_exists( 'wp_handle_upload' ) ) {
			require_once( ABSPATH . 'wp-admin/includes/file.php' );
		}
		$msg   = $data = null;
		$error = true;
		if ( isset( $_FILES['banner'] ) ) {
			Filters::beforeUpload();
			$status = wp_handle_upload( $_FILES['banner'], [
				'test_form' => false
			] );
			Filters::afterUpload();
			if ( $status && ! isset( $status['error'] ) ) {
				// $filename should be the path to a file in the upload directory.
				$filename = $status['file'];

				// The ID of the post this attachment is for.
				$user_id = (int) ( isset( $_POST['user_id'] ) ? $_POST['user_id'] : get_current_user_id() );
				// Check the type of tile. We'll use this as the 'post_mime_type'.
				$filetype = wp_check_filetype( basename( $filename ) );

				// Get the path to the upload directory.
				$wp_upload_dir = wp_upload_dir();

				// Prepare an array of post data for the attachment.
				$attachment = [
					'guid'           => $wp_upload_dir['url'] . '/' . basename( $filename ),
					'post_mime_type' => $filetype['type'],
					'post_title'     => preg_replace( '/\.[^.]+$/', '', basename( $filename ) ),
					'post_content'   => '',
					'post_status'    => 'inherit'
				];

				// Insert the attachment.
				$attach_id = wp_insert_attachment( $attachment, $filename, $user_id );
				if ( ! is_wp_error( $attach_id ) ) {
					if ( $existing_banner = get_user_meta( $user_id, 'photo_id', true ) ) {
						wp_delete_attachment( $existing_banner );
					}
					update_user_meta( $user_id, 'photo_id', $attach_id );
					$metaData = wp_get_attachment_metadata( $attach_id );
					wp_update_attachment_metadata( $attach_id, [] );
					$src   = wp_get_attachment_image_src( $attach_id, 'full' );
					$data  = [
						'photo_id' => $attach_id,
						'src'      => $src[0]
					];
					$error = false;
					$msg   = esc_html__( "Successfully updated.", "rtcl-seller-verification" );

					if ( Functions::get_option_item( 'rtcl_email_settings', 'notify_admin', 'seller_photo_id_uploaded', 'multi_checkbox' ) ) {
						rtcl_sv_send_mail_to_admin( $user_id, 'photo' );
					}

					do_action( 'rtcl_sv_user_document_uploaded' );
				}
			} else {
				$msg = $status['error'];
			}
		} else {
			$msg = esc_html__( "Photo ID should be selected", "rtcl-seller-verification" );
		}

		wp_send_json( [
			'message' => $msg,
			'error'   => $error,
			'data'    => $data
		] );

	}

	public function rtcl_ajax_documents_photo_delete() {
		$error   = true;
		$message = null;
		$user_id = (int) ( isset( $_POST['user_id'] ) ? $_POST['user_id'] : get_current_user_id() );

		if ( $user_id ) {
			$photo_id = absint( get_user_meta( $user_id, 'photo_id', true ) );
			if ( $photo_id && wp_delete_attachment( $photo_id ) ) {
				delete_user_meta( $user_id, 'photo_id' );
				$error   = false;
				$message = esc_html__( "Successfully deleted", "rtcl-seller-verification" );
			} else {
				$message = __( "File could not be deleted.", "rtcl-seller-verification" );
			}
		} else {
			$message = __( "No photo found to remove", "rtcl-seller-verification" );
		}

		wp_send_json( [
			'error'   => $error,
			'message' => $message
		] );
	}

	public function rtcl_ajax_document_file_upload() {
		if ( ! function_exists( 'wp_handle_upload' ) ) {
			require_once( ABSPATH . 'wp-admin/includes/file.php' );
		}
		$msg   = $data = null;
		$error = true;

		if ( isset( $_FILES['document'] ) ) {
			Filters::beforeUpload();
			$status = wp_handle_upload( $_FILES['document'], [
				'test_form' => false
			] );
			Filters::afterUpload();
			if ( $status && ! isset( $status['error'] ) ) {
				// $filename should be the path to a file in the upload directory.
				$filename = $status['file'];

				// The ID of the post this attachment is for.
				$user_id = (int) ( isset( $_POST['user_id'] ) ? $_POST['user_id'] : get_current_user_id() );
				// Check the type of tile. We'll use this as the 'post_mime_type'.
				$filetype = wp_check_filetype( basename( $filename ) );

				// Get the path to the upload directory.
				$wp_upload_dir = wp_upload_dir();

				// Prepare an array of post data for the attachment.
				$attachment = [
					'guid'           => $wp_upload_dir['url'] . '/' . basename( $filename ),
					'post_mime_type' => $filetype['type'],
					'post_title'     => preg_replace( '/\.[^.]+$/', '', basename( $filename ) ),
					'post_content'   => '',
					'post_status'    => 'inherit'
				];

				// Insert the attachment.
				$attach_id = wp_insert_attachment( $attachment, $filename, $user_id );
				if ( ! is_wp_error( $attach_id ) ) {
					if ( $existing_file = get_user_meta( $user_id, 'other_document_id', true ) ) {
						wp_delete_attachment( $existing_file );
					}
					update_user_meta( $user_id, 'other_document_id', $attach_id );
					$metaData = wp_get_attachment_metadata( $attach_id );
					wp_update_attachment_metadata( $attach_id, [] );
					$file_url = wp_get_attachment_url( $attach_id );
					$src      = rtcl_sv_get_pdf_download_file();
					$data     = [
						'other_document_id' => $attach_id,
						'src'               => $src,
						'name'              => basename( $file_url )
					];
					$error    = false;
					$msg      = esc_html__( "Successfully updated.", "rtcl-seller-verification" );

					if ( Functions::get_option_item( 'rtcl_email_settings', 'notify_admin', 'seller_business_file_uploaded', 'multi_checkbox' ) ) {
						rtcl_sv_send_mail_to_admin( $user_id, 'business_document' );
					}

					do_action( 'rtcl_sv_user_document_uploaded' );
				}
			} else {
				$msg = $status['error'];
			}
		} else {
			$msg = esc_html__( "Other document should be selected.", "rtcl-seller-verification" );
		}

		wp_send_json( [
			'message' => $msg,
			'error'   => $error,
			'data'    => $data
		] );

	}

	public function rtcl_ajax_documents_file_delete() {
		$error   = true;
		$message = null;
		$user_id = (int) ( isset( $_POST['user_id'] ) ? $_POST['user_id'] : get_current_user_id() );

		if ( $user_id ) {
			$file_id = absint( get_user_meta( $user_id, 'other_document_id', true ) );
			if ( $file_id && wp_delete_attachment( $file_id ) ) {
				delete_user_meta( $user_id, 'other_document_id' );
				$error   = false;
				$message = esc_html__( "Successfully deleted", "rtcl-seller-verification" );
			} else {
				$message = __( "File could not be deleted.", "rtcl-seller-verification" );
			}
		} else {
			$message = __( "No document found to remove", "rtcl-seller-verification" );
		}

		wp_send_json( [
			'error'   => $error,
			'message' => $message
		] );
	}

}