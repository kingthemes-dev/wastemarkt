<?php
/**
 * @package ClassifiedListing/Templates
 * @version 1.5.4
 */

use Rtcl\Helpers\Functions;
use Rtcl\Controllers\Hooks\TemplateHooks as FreeTemplateHooks;
use RtclPro\Controllers\Hooks\TemplateHooks;
use radiustheme\ClassiList\Listing_Functions;
use radiustheme\ClassiList\RDTheme;
use radiustheme\ClassiList\URI_Helper;
use RtclPro\Helpers\Fns;

defined('ABSPATH') || exit;

if (!class_exists('RtclPro')) return;


if ( RDTheme::$layout == 'full-width' ){
	$layout_class = 'col-xl-12 col-lg-12 col-sm-12 col-12';
} else {
	$layout_class = 'col-xl-9 col-lg-8 col-sm-12 col-12';
}

$rtcl_query = rtcl()->wp_query();

$post_num   = Listing_Functions::listing_post_num( $rtcl_query );
$count_text = Listing_Functions::listing_count_text( $post_num );

$rtcl_top_query = Fns::top_listings_query();

if ( ! empty( $rtcl_top_query ) && Fns::is_enable_top_listings() ) {
    Listing_Functions::set_top_query_globally( $rtcl_top_query );
}

$rtcl_query = rtcl()->wp_query();

$general_settings = Functions::get_option( 'rtcl_general_settings' );

if ( isset( $_GET['view'] ) && in_array( $_GET['view'], [ 'grid', 'list' ], true ) ) {
    $view = esc_attr( $_GET['view'] );
}
else {
    $view = Functions::get_option_item( 'rtcl_general_settings', 'default_view', 'list' );
}
$list_class = ( $view == 'grid' ) ? '' : 'rtcl-list-view';
$map = false;
?>
<?php get_header(); ?>
<?php get_template_part( 'template-parts/content', 'top' ); ?>
<div id="primary" class="content-area classilist-listing-archive rtcl">
	<div class="container">
		<div class="row">

			<?php if ( RDTheme::$layout == 'left-sidebar' ): ?>
				<div class="col-xl-3 col-lg-4 col-sm-12 col-12"><?php URI_Helper::get_custom_listing_template( 'sidebar-archive' );?></div>
			<?php endif; ?>

			<div class="<?php echo esc_attr( $layout_class ); ?>">

                <?php do_action('rtcl_archive_description'); ?>

				<div class="listing-archive-top rtcl-listings-actions">
					<h2 class="rtin-title rtcl-result-count"><?php echo esc_html( $count_text );?></h2>
	                <div class="listing-sorting">
	                    <?php FreeTemplateHooks::catalog_ordering(); ?>
	                    <?php TemplateHooks::view_switcher(); ?>
	                </div>
				</div>

				<?php Functions::listing_loop_start(); ?>

				<?php do_action( 'classilist_listing_before_items' );?>

				<div class="rtcl rtcl-listings rtcl-listings-<?php echo esc_attr( $view ); ?>">
					<div class="<?php echo esc_attr( $list_class ); ?>">
						<?php if ( $post_num ): ?>
							<?php Listing_Functions::listing_query( $view, $rtcl_query, $rtcl_top_query, $map );?>
						<?php else: ?>
							<?php URI_Helper::get_custom_listing_template( 'noresults' ); ?>
						<?php endif; ?>
					</div>
				</div>

				<?php do_action( 'classilist_listing_after_items' ); ?>

				<?php
				/**
				 * Hook: rtcl_after_listing_loop.
				 *
				 * @hooked TemplateHook::pagination() - 10
				 */
				do_action( 'rtcl_after_listing_loop' );
				?>
			</div>

			<?php if ( RDTheme::$layout == 'right-sidebar' ): ?>
				<div class="col-xl-3 col-lg-4 col-sm-12 col-12"><?php URI_Helper::get_custom_listing_template( 'sidebar-archive' );?></div>
			<?php endif; ?>

		</div>
	</div>
</div>
<?php get_footer(); ?>