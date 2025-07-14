<?php
/**
 * New payout email to admin
 * This template can be overridden by copying it to yourtheme/classified-listing/emails/payout-request-email-to-admin.php
 *
 * @var RtclEmail $email
 * @var double    $amount
 * @var string    $seller_name
 */

use Rtcl\Models\RtclEmail;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * @hooked RtclEmails::email_header() Output the email header
 */
do_action( 'rtcl_email_header', $email ); ?>
    <p><?php esc_html_e( 'Hi Administrator,', 'rtcl-marketplace' ); ?></p>
    <p><?php printf( esc_html__( 'You’ve received a payout request from %s for %s', 'rtcl-marketplace' ), esc_html( $seller_name ),
			wc_price( $amount ) ); ?></p>

<?php
/**
 * @hooked RtclEmails::email_footer() Output the email footer
 */
do_action( 'rtcl_email_footer', $email );
