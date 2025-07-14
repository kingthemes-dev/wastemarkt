<?php

namespace RtclMarketplace\Hooks;

use RtclMarketplace\Helpers\Functions;

class AjaxHooks {

	public static function init() {
		add_action( 'wp_ajax_handle_ajax_file_upload', [ __CLASS__, 'handle_ajax_file_upload' ] );
		add_action( 'wp_ajax_nopriv_handle_ajax_file_upload', [ __CLASS__, 'handle_ajax_file_upload' ] );
		add_action( 'wp_ajax_rtcl_marketplace_payout_request', [ __CLASS__, 'payout_request' ] );
		add_action( 'wp_ajax_rtcl_marketplace_update_payout_status', [ __CLASS__, 'update_payout_status' ] );
		add_action( 'wp_ajax_rtcl_marketplace_add_order_note', [ __CLASS__, 'add_order_note' ] );
	}

	public static function add_order_note() {
		if ( ! wp_verify_nonce( $_POST[ rtcl()->nonceId ] ?? '', rtcl()->nonceText ) ) {
			wp_send_json(
				[
					'success' => false,
					'message' => esc_html__( 'Session expired!!', 'rtcl-marketplace' ),
				]
			);
		}

		$user_id = isset( $_POST['user_id'] ) ? absint( $_POST['user_id'] ) : 0;

		if ( ! $user_id ) {
			wp_send_json(
				[
					'success' => false,
					'message' => esc_html__( 'Unauthorized access!!', 'rtcl-marketplace' ),
				]
			);
		}

		$post_id   = isset( $_POST['post_id'] ) ? absint( $_POST['post_id'] ) : '';
		$note      = isset( $_POST['note'] ) ? sanitize_textarea_field( wp_unslash( $_POST['note'] ) ) : '';
		$note_type = isset( $_POST['note_type'] ) ? sanitize_text_field( wp_unslash( $_POST['note_type'] ) ) : '';

		$is_customer_note = ( $note_type === 'customer' ) ? 1 : 0;

		if ( $post_id > 0 ) {
			$order      = wc_get_order( $post_id );
			$comment_id = $order->add_order_note( $note, $is_customer_note, true );
			if ( $comment_id ) {
				if ( ! $is_customer_note ) {
					rtcl()->mailer()->emails['OrderNoteEmail']->trigger( $order->get_id(),
						[ 'order' => $order, 'seller_id' => $user_id, 'customer_note' => $note ] );
				}
				wp_send_json(
					[
						'success' => true,
						'message' => esc_html__( 'Added note successfully!', 'rtcl-marketplace' ),
					]
				);
			}
		}

		die();
	}

	public static function update_payout_status() {
		if ( ! wp_verify_nonce( $_POST[ rtcl()->nonceId ] ?? '', rtcl()->nonceText ) ) {
			wp_send_json(
				[
					'success' => false,
					'message' => esc_html__( 'Session expired!!', 'rtcl-marketplace' ),
				]
			);
		}

		$payout_id = isset( $_POST['payout_id'] ) ? absint( $_POST['payout_id'] ) : 0;
		$status    = isset( $_POST['status'] ) ? sanitize_text_field( $_POST['status'] ) : 'hold';

		if ( ! $payout_id ) {
			wp_send_json(
				[
					'success' => false,
					'message' => esc_html__( 'Access denied!!', 'rtcl-marketplace' ),
				]
			);
		}

		$response = Functions::update_withdraw_status( $payout_id, $status );

		if ( Functions::is_enable_payout_paid_email() && 'paid' === $status && $response['success'] ) {
			rtcl()->mailer()->emails['PayoutPaidEmail']->trigger( $payout_id );
		}

		wp_send_json( $response );

	}

	public static function payout_request() {

		if ( ! wp_verify_nonce( $_POST[ rtcl()->nonceId ] ?? '', rtcl()->nonceText ) ) {
			wp_send_json(
				[
					'success' => false,
					'message' => esc_html__( 'Session expired!!', 'rtcl-marketplace' ),
				]
			);
		}

		$user_id = isset( $_POST['user_id'] ) ? absint( $_POST['user_id'] ) : 0;

		if ( ! $user_id ) {
			wp_send_json(
				[
					'success' => false,
					'message' => esc_html__( 'Unauthorized access!!', 'rtcl-marketplace' ),
				]
			);
		}

		$available_balance = Functions::get_available_balance( $user_id );
		$minimum_payout    = Functions::get_minimum_payout();

		if ( $available_balance < $minimum_payout || ! $available_balance ) {
			wp_send_json(
				[
					'success' => false,
					'message' => esc_html__( 'You have less amount!!', 'rtcl-marketplace' ),
				]
			);
		}

		$payout_method = Functions::get_current_selected_payout_method( $user_id );

		if ( empty( $payout_method ) || ! is_array( $payout_method ) ) {
			wp_send_json(
				[
					'success' => false,
					'message' => esc_html__( 'Please, set payout method!!', 'rtcl-marketplace' ),
				]
			);
		}

		if ( ! isset( $payout_method['method'] ) || ! isset( $payout_method['details'] ) ) {
			wp_send_json(
				[
					'success' => false,
					'message' => esc_html__( 'Please, set payout information properly.', 'rtcl-marketplace' ),
				]
			);
		}

		$insert_id = Functions::add_withdraw_request( $user_id, $available_balance, $payout_method );

		$success = false;
		$message = esc_html__( 'Error to insert data!!', 'rtcl-marketplace' );

		if ( ! is_wp_error( $insert_id ) ) {
			$success = true;
			$message = esc_html__( 'Added withdraw request successfully.', 'rtcl-marketplace' );

			if ( Functions::is_enable_payout_request_email() ) {
				rtcl()->mailer()->emails['PayoutRequestEmail']->trigger( $insert_id, [ 'seller_id' => $user_id, 'amount' => $available_balance ] );
			}
		}

		wp_send_json(
			[
				'success' => $success,
				'message' => $message,
			]
		);


	}


	/**
	 * @return void
	 */
	public static function handle_ajax_file_upload() {

		check_ajax_referer( 'file_upload_nonce', 'nonce' );

		if ( ! function_exists( 'wp_handle_upload' ) ) {
			require_once ABSPATH . 'wp-admin/includes/file.php';
		}

		$file_data = base64_decode( $_POST['file'] );
		$filename  = sanitize_file_name( $_POST['filename'] );
		$filetype  = sanitize_mime_type( $_POST['filetype'] );

		$upload_dir       = wp_upload_dir();
		$unique_file_name = wp_unique_filename( $upload_dir['path'], $filename );
		$upload_path      = $upload_dir['path'] . '/' . $unique_file_name;

		if ( file_put_contents( $upload_path, $file_data ) ) {
			$attachment = array(
				'guid'           => $upload_dir['url'] . '/' . basename( $unique_file_name ),
				'post_mime_type' => $filetype,
				'post_title'     => preg_replace( '/\.[^.]+$/', '', basename( $unique_file_name ) ),
				'post_content'   => '',
				'post_status'    => 'inherit',
			);

			$attach_id = wp_insert_attachment( $attachment, $upload_path );
			require_once ABSPATH . 'wp-admin/includes/image.php';
			$attach_data = wp_generate_attachment_metadata( $attach_id, $upload_path );
			wp_update_attachment_metadata( $attach_id, $attach_data );

			wp_send_json_success( array( 'url' => wp_get_attachment_url( $attach_id ) ) );
		} else {
			wp_send_json_error( array( 'error' => 'File upload failed.' ) );
		}
	}
}
