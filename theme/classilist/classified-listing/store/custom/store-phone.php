<?php
/**
 * @author  RadiusTheme
 * @since   1.0
 * @version 1.0
 */

namespace radiustheme\ClassiList;

$mobileClass = wp_is_mobile() ? ' rtcl-mobile' : null;
$last        = substr( $phone, - 3 );
$phone       = substr_replace( $phone, 'XXX', - 3 );
?>
<div class="rtcl-contact-reveal-wrapper reveal-phone<?php echo esc_attr( $mobileClass) ?>" data-last="<?php echo esc_attr( $last ); ?>">
	<div class='numbers'><?php echo esc_html( $phone ); ?></div>
	<small class='text-muted'><?php esc_html_e( 'Click to reveal phone number', 'classilist' ); ?></small>
</div>