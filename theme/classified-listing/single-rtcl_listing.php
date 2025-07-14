<?php
/**
 *
 * @author        RadiusTheme
 * @package    classified-listing/templates
 * @version     1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

use Rtcl\Models\Listing;
use radiustheme\ClassiList\RDTheme;
use radiustheme\ClassiList\URI_Helper;
use RtclPro\Controllers\Hooks\TemplateHooks;

$listing = new Listing( $post->ID );

if ( RDTheme::$layout == 'full-width' ){
	$layout_class = 'col-xl-12 col-lg-12 col-sm-12 col-12';
} else {
	$layout_class = 'col-xl-9 col-lg-8 col-sm-12 col-12';
}

?>
<?php get_header(); ?>


<?php get_template_part( 'template-parts/content', 'top' ); ?>

<div id="primary" class="content-area classilist-listing-single rtcl">
	<div class="container">
		<div class="row">
			<?php
			if ( RDTheme::$layout == 'left-sidebar' ) {
				URI_Helper::get_custom_listing_template( 'sidebar-single' );
			}
			?>
			<div class="<?php echo esc_attr( $layout_class ); ?>">

				<?php URI_Helper::get_custom_listing_template( 'content-single' ); ?>

				<?php
				do_action( 'classilist_single_listing_after_product' );

				// Business Hours
                do_action('rtcl_single_listing_business_hours');

                // Map
				do_action('rtcl_single_listing_content_end', $listing);

                // Social Profiles
                do_action('rtcl_single_listing_social_profiles');

				do_action( 'classilist_single_listing_after_location' );

				// Related Listing
                if ( RDTheme::$options['listing_related'] ) {
                    $listing->the_related_listings();
                }
				do_action( 'classilist_single_listing_after_related' );

                // Review
                do_action('rtcl_single_listing_review');
				?>
			</div>
			<?php
			if ( RDTheme::$layout != 'left-sidebar' ) {
				URI_Helper::get_custom_listing_template( 'sidebar-single' );
			}
			?>
		</div>
	</div>
</div>
<?php get_footer(); ?>