<?php

use RtclInvoice\Helpers\Functions;

$parse_uri = explode( 'wp-content', $_SERVER['SCRIPT_FILENAME'] );

if ( ! isset( $parse_uri[0] ) && ! file_exists( $parse_uri[0] . 'wp-load.php' ) ) {
	exit;
}

require_once( $parse_uri[0] . 'wp-load.php' );

if ( ! wp_verify_nonce( $_REQUEST['__rtcl_wpnonce'], rtcl()->nonceText ) ) {
	exit;
}

$order_id = isset( $_REQUEST['order_id'] ) ? absint( $_REQUEST['order_id'] ) : 0;

Functions::generate_pdf( $order_id );

exit;