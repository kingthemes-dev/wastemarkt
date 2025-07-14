<?php
/**
 * new listing email notification to owner
 * This template can be overridden by copying it to yourtheme/classified-listing/emails/booking-approved-email-to-user.php
 *
 * @author        RadiusTheme
 * @package       ClassifiedListing/Templates/Emails
 * @version       1.3.0
 *
 * @var RtclEmail $email
 * @var array     $claimer
 * @var object    $listing
 */

use Rtcl\Helpers\Functions;
use Rtcl\Models\RtclEmail;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * @hooked RtclEmails::email_header() Output the email header
 */
do_action( 'rtcl_email_header', $email ); ?>
    <p><?php printf( esc_html__( 'Hi %s,', 'rtcl-claim-listing' ), $claimer->first_name ); ?></p>
    <p><?php printf( __( 'Your claim for <a href="%s"><strong>%s</strong></a> is approved at <a href="%s">%s</a>.', 'rtcl-claim-listing' ),
			$listing->get_the_permalink(), $listing->get_the_title(), get_site_url(), Functions::get_blogname()
		) ?></p>
<?php

/**
 * @hooked RtclEmails::email_footer() Output the email footer
 */
do_action( 'rtcl_email_footer', $email );