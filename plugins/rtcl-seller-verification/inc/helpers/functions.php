<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

function rtcl_seller_verification_get_photo_id( $user_id = 0 ) {
	$user_id = empty( $user_id ) ? get_current_user_id() : $user_id;

	return apply_filters( 'rtcl_seller_verification_get_photo_id', get_user_meta( $user_id, 'photo_id', true ) );
}

function rtcl_seller_verification_the_photo( $user_id = 0, $size = 'full' ) {
	echo apply_filters( 'rtcl_seller_the_photo', rtcl_seller_verification_get_the_photo( $user_id, $size ) );
}

function rtcl_seller_verification_get_photo_id_url( $user_id = 0, $size = 'full' ) {
	$photo_id = rtcl_seller_verification_get_photo_id( $user_id );

	if ( $photo_id ) {
		return wp_get_attachment_image_url( $photo_id, $size );
	}

	return '';
}

function rtcl_seller_verification_get_the_photo( $user_id = 0, $size = 'full', $attr = [] ) {
	$user_id  = empty( $user_id ) ? get_current_user_id() : $user_id;
	$photo_id = get_user_meta( $user_id, 'photo_id', true );

	$bannerImage = null;

	if ( $photo_id ) {
		$attrClass     = ! empty( $attr['class'] ) ? ' ' . $attr['class'] : '';
		$attr['class'] = 'rtcl-thumbnail' . $attrClass;
		$bannerImage   = wp_get_attachment_image( $photo_id, $size, false, $attr );
	}

	return apply_filters( 'rtcl_seller_get_the_photo', $bannerImage, $size, $attr );
}

function rtcl_seller_verification_the_document_name( $user_id = 0 ) {
	$user_id = empty( $user_id ) ? get_current_user_id() : $user_id;

	echo apply_filters( 'rtcl_seller_the_document_name', rtcl_seller_verification_get_document_file_name( $user_id ) );
}

function rtcl_seller_verification_get_document_file_name( $user_id = 0 ) {
	$user_id  = empty( $user_id ) ? get_current_user_id() : $user_id;
	$file_id  = get_user_meta( $user_id, 'other_document_id', true );
	$fileName = $filePath = '';

	if ( $file_id ) {
		$filePath = wp_get_attachment_url( $file_id );
		if ( $filePath ) {
			$fileName = basename( $filePath );
		}
	}

	return apply_filters( 'rtcl_seller_get_document_name', $fileName, $filePath );
}

function rtcl_seller_verification_get_document_file_url( $user_id = 0 ) {
	$user_id = empty( $user_id ) ? get_current_user_id() : $user_id;

	$file_id = get_user_meta( $user_id, 'other_document_id', true );

	return $file_id ? wp_get_attachment_url( $file_id ) : '';
}

function rtcl_seller_verification_get_max_file_upload_size() {
	$max_size = absint( apply_filters( 'rtcl_seller_verification_max_file_upload_size', 5 ) );

	return $max_size * ( 1024 * 1024 );
}

function rtcl_sv_check_verified_user( $user_id = 0 ) {
	return (bool) get_user_meta( $user_id, 'rtcl_verified_seller', true );
}

function rtcl_sv_get_user_status( $user_id = 0 ) {
	$verified = rtcl_sv_check_verified_user( $user_id );
	$status   = 1; // Verified document

	if ( ! $verified ) {
		$photo_id = get_user_meta( $user_id, 'photo_id', true );
		$file_id  = get_user_meta( $user_id, 'other_document_id', true );

		if ( $photo_id || $file_id ) {
			$status = 2; // Submitted document
		} else {
			$status = 3; // Not submitted document
		}
	}

	return $status;
}

function rtcl_sv_get_user_status_title( $status ) {
	switch ( $status ) {
		case 1:
			$title = esc_html__( 'Verified', 'rtcl-seller-verification' );
			break;
		case 2:
			$title = esc_html__( 'Submitted', 'rtcl-seller-verification' );
			break;
		default:
			$title = esc_html__( '---', 'rtcl-seller-verification' );
	}

	return sprintf( "<span class='%s'>%s</span>", strtolower( $title ), $title );
}

function rtcl_sv_send_mail_to_admin( $user_id, $documentType = '' ) {
	$document = esc_html__( 'Photo Id', 'rtcl-seller-verification' );

	if ( 'business_document' == $documentType ) {
		$document = esc_html__( 'Business File', 'rtcl-seller-verification' );
	}

	$data = [
		'document_type' => $document
	];

	if ( ! rtcl()->mailer()->emails['Seller_Document_Email']->trigger( $user_id, $data ) ) {
		wp_send_json_error( [ 'error' => __( "An error to send mail!", "rtcl-seller-verification" ) ] );
	}

}

function rtcl_sv_get_pdf_download_file() {
	return RTCL_SELLER_URL . '/inc/helpers/pdf-reader.php';
}