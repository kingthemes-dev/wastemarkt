<?php
/**
 * Seller Verification mail
 *
 * @package rtcl-seller-verification/templates/emails
 * @version 1.0.0
 */

use Rtcl\Models\RtclEmail;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * @hooked RtclEmails::email_header() Output the email header
 */
/** @var RtclEmail $email */
do_action( 'rtcl_email_header', $email ); ?>
    <p><?php esc_html_e( 'Hi Administrator,', 'rtcl-seller-verification' ); ?></p>
    <p><?php printf( __( 'You have received document(%s) from <strong>%s</strong>.', 'rtcl-seller-verification' ), $data['document_type'], $data['name'] ); ?></p>
<?php
/**
 * @hooked RtclEmails::email_footer() Output the email footer
 */
do_action( 'rtcl_email_footer', $email );