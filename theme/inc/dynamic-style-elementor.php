<?php
/**
 * @author  RadiusTheme
 * @since   1.0
 * @version 1.0
 */

namespace radiustheme\ClassiList;

/*-------------------------------------
INDEX
=======================================
#. EL: Owl Nav
#. EL: Info Box
#. EL: CTA
#. EL: Pricing Box
#. EL: Accordian
#. EL: Listing Category Box
-------------------------------------*/

$prefix = Constants::$theme_prefix;
$primary_color    = apply_filters( "{$prefix}_primary_color", RDTheme::$options['primary_color'] ); // #1aa78e
$secondery_color  = apply_filters( "{$prefix}_secondery_color", RDTheme::$options['secondery_color'] ); // #fcaf01
$primary_rgb      = Helper::hex2rgb( $primary_color ); // 26, 167, 142
$secondery_rgb    = Helper::hex2rgb( $secondery_color ); // 252, 175, 1
?>

<?php /* EL: Owl Nav */ ?>
.owl-custom-nav-area .owl-custom-nav-title:after {
	background-color: <?php echo esc_html( $primary_color ); ?>;
}
.owl-custom-nav-area .owl-custom-nav .owl-prev:hover,
.owl-custom-nav-area .owl-custom-nav .owl-next:hover {
	background-color: <?php echo esc_html( $primary_color ); ?>;
}

<?php /* EL: Info Box */ ?>
body .rt-el-info-box .rtin-icon i {
	color: <?php echo esc_html( $secondery_color ); ?>;
}

<?php /* EL: CTA 1 */ ?>
.rt-el-cta-1 .btn {
	background: <?php echo esc_html( $secondery_color ); ?>;
}

<?php /* EL: CTA 2 */ ?>
.rt-el-cta-2 .rtin-right a i {
	color: <?php echo esc_html( $primary_color ); ?>;
}

<?php /* EL: Accordian */ ?>
.rt-el-accordian .card .card-header a {
	background-color: <?php echo esc_html( $primary_color ); ?>;
}

<?php /* EL: Listing Category Box */ ?>
.rt-el-listing-cat-box .rtin-item .rtin-icon {
	color: <?php echo esc_html( $primary_color ); ?>;
}
.rt-el-listing-cat-box .rtin-item:hover .rtin-icon {
	color: <?php echo esc_html( $secondery_color ); ?>;
}
.rt-el-listing-cat-box .rtin-item:hover .rtin-title {
	color: <?php echo esc_html( $primary_color ); ?>;
}