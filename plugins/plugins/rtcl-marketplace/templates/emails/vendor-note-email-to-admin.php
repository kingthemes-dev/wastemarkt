<?php
/**
 * New payout email to admin
 * This template can be overridden by copying it to yourtheme/classified-listing/emails/payout-request-email-to-admin.php
 *
 * @var RtclEmail $email
 * @var WC_Order  $order
 * @var string    $seller_name
 * @var string    $customer_note
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
    <p><?php printf( esc_html__( 'The following note has been added to sub-order %s%s', 'rtcl-marketplace' ), esc_html( $order->get_id() ),
			$order->get_parent_id() ? sprintf( esc_html__( ' which main order is %s', 'rtcl-marketplace' ), $order->get_parent_id() ) : '' ); ?></p>
    <blockquote><?php echo wpautop( wptexturize( make_clickable( $customer_note ) ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></blockquote>
<?php
/**
 * @hooked RtclEmails::email_footer() Output the email footer
 */
do_action( 'rtcl_email_footer', $email );
