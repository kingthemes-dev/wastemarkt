<?php
$parse_uri = explode( 'wp-content', $_SERVER['SCRIPT_FILENAME'] );

if ( ! isset( $parse_uri[0] ) && ! file_exists( $parse_uri[0] . 'wp-load.php' ) ) {
	exit;
}

require_once( $parse_uri[0] . 'wp-load.php' );

if ( ! wp_verify_nonce( $_REQUEST['__rtcl_wpnonce'], rtcl()->nonceText ) ) {
	exit;
}

$user_id   = (int) ( isset( $_REQUEST['user_id'] ) ? $_REQUEST['user_id'] : get_current_user_id() );
$attach_id = get_user_meta( $user_id, 'other_document_id', true );
$src       = wp_get_attachment_url( $attach_id );

if ( $src ) {
	$file_name = basename( $src );
	$delimeter = 'wp-content';
	$file_uri  = explode( $delimeter, $src );

	if ( isset( $file_uri[1] ) ) {
		$file_path = ABSPATH . $delimeter . $file_uri[1];
		if ( file_exists( $file_path ) ) {
			header( "Content-Type: application/octet-stream" );

			header( "Content-Disposition: attachment; filename=$file_name" );
			header( "Content-Type: application/download" );
			header( "Content-Description: File Transfer" );
			header( "Content-Length: " . filesize( $file_path ) );

			flush(); // This doesn't really matter.

			$fp = fopen( $file_path, "r" );
			while ( ! feof( $fp ) ) {
				echo fread( $fp, 65536 );
				flush(); // This is essential for large downloads
			}

			fclose( $fp );
		}
	}
}

exit;