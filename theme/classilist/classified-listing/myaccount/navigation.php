<?php
/**
 *
 * @author 		RadiusTheme
 * @package 	classified-listing/templates
 * @version     1.0.0
 */

use radiustheme\ClassiList\RDTheme;
use radiustheme\ClassiList\Helper;
use Rtcl\Helpers\Functions;
use Rtcl\Helpers\Link;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
do_action( 'rtcl_before_account_navigation' );

$light_logo = empty( RDTheme::$options['logo_light']['url'] ) ? Helper::get_img( 'logo-light.png' ) : RDTheme::$options['logo_light'];
?>
<nav class="rtcl-MyAccount-navigation">
    <div class="rtcl-myaccount-logo">
		<?php if ( ! empty( $light_logo['url'] ) ) { ?>
			<a class="light-logo" href="<?php echo esc_url( home_url( '/' ) ); ?>">
				<img src="<?php echo esc_url( $light_logo['url'] ); ?>"
						height="<?php echo isset( $light_logo['height'] ) ? esc_attr( $light_logo['height'] ) : '45'; ?>"
						width="<?php echo isset( $light_logo['width'] ) ? esc_attr( $light_logo['width'] ) : '150'; ?>"
						alt="<?php bloginfo( 'name' ); ?>">
			</a>
		<?php } else { ?>
			<a class="light-logo" href="<?php echo esc_url( home_url( '/' ) ); ?>">
				<img src="<?php echo esc_url( $light_logo ); ?>" width="150" height="45" alt="<?php bloginfo( 'name' ); ?>">
			</a>
		<?php } ?>
    </div>
	<ul>
		<?php foreach ( Functions::get_account_menu_items() as $endpoint => $label ) : ?>
			<?php if ( 'add-listing' === $endpoint ): ?>
                <li class="<?php echo esc_attr( Functions::get_account_menu_item_classes( $endpoint ) ); ?>">
                    <a href="<?php echo esc_url( Link::get_listing_form_page_link() ); ?>"><?php echo esc_html( $label ); ?></a>
                </li>
			<?php else: ?>
                <li class="<?php echo esc_attr( Functions::get_account_menu_item_classes( $endpoint ) ); ?>">
                    <a data-href="<?php echo esc_url( Link::get_account_endpoint_url( $endpoint ) ); ?>"
                       href="<?php echo esc_url( Link::get_account_endpoint_url( $endpoint ) ); ?>"><?php echo esc_html( $label ); ?></a>
                </li>
			<?php endif; ?>
		<?php endforeach; ?>
	</ul>

	<?php do_action( 'rtcl_after_account_navigation_list' ); ?>
</nav>

<?php do_action( 'rtcl_after_account_navigation' ); ?>

