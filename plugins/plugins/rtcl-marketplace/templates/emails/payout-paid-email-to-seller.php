<?php
/**
 * Payout paid email to seller
 * This template can be overridden by copying it to yourtheme/classified-listing/emails/payout-paid-email-to-seller.php
 *
 * @var RtclEmail $email
 * @var array     $payout
 * @var object    $user
 */

use Rtcl\Models\RtclEmail;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * @hooked RtclEmails::email_header() Output the email header
 */
do_action( 'rtcl_email_header', $email ); ?>
    <p><?php printf( esc_html__( 'Hi %s,', 'rtcl-marketplace' ), $user->display_name ); ?></p>
    <p><?php printf( esc_html__( 'You’ve received a payment %s', 'rtcl-marketplace' ), wc_price( $payout['amount'] ) ); ?></p>

<?php
/**
 * @hooked RtclEmails::email_footer() Output the email footer
 */
do_action( 'rtcl_email_footer', $email );
