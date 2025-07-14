<?php
/**
 * @author  RadiusTheme
 * @since   1.0
 * @version 1.0
 */

namespace radiustheme\ClassiList;

use Rtcl\Models\Listing;
use RtclPro\Helpers\Fns;
use Rtcl\Helpers\Functions;
use RtclPro\Controllers\Hooks\TemplateHooks;

if (!class_exists('RtclPro')) return;

$id = get_the_id();
$listing = new Listing( $id );

$prefix = Constants::$theme_prefix;

$video = [];
if (!Functions::is_video_urls_disabled()) {
    $video = get_post_meta($listing->get_id(), '_rtcl_video_urls', true);
    $video = !empty($video) && is_array($video) ? $video : [];
}
$images = Functions::get_listing_images( $id );
$slider_class = !empty($video) || Functions::get_listing_images( $id ) ? '' : ' no-gallery-image';
$slider_class .= method_exists('RtclPro\Helpers\Fns', 'is_mark_as_sold') && Fns::is_mark_as_sold($listing->get_id()) ? ' is-sold' : '';

$time_format = apply_filters( 'classilist_single_listing_time_format', 'F j, Y g:i a' );
$date        = date_i18n( $time_format,  get_the_time( 'U' ) );

?>

<div class="site-content-block classilist-single-details">
	<div class="main-content">
		<?php do_action( 'classilist_single_listing_before_contents' ); ?>
		<div class="single-listing-meta-wrap">
			<div class="rtin-left">
				<h1 class="single-listing-title"><?php the_title();?></h1>
				<ul class="single-listing-meta">
					<?php 
					if ( $listing->can_show_user() ):
						if ($listing->can_add_user_link() && !is_author()) : ?>
							<li class="rtin-usermeta"><i class="far fa-user"></i>
								<a href="<?php echo esc_url($listing->get_the_author_url()); ?>">
									<?php $listing->the_author(); ?>
									<?php do_action('rtcl_after_author_meta', $listing->get_owner_id() ); ?>
								</a>
							</li>
						<?php else: ?>
							<li class="rtin-usermeta"><i class="far fa-user"></i>
								<?php $listing->the_author(); ?>
								<?php do_action('rtcl_after_author_meta', $listing->get_owner_id() ); ?>
							</li>
						<?php endif; 
					endif;
					?>
					<?php if ( $listing->can_show_date() ): ?>
						<li><i class="far fa-clock"></i><?php echo esc_html( $date );?></li>
					<?php endif; ?>

					<?php if ( $listing->has_location() && $listing->can_show_location() ): ?>
						<li><i class="fas fa-map-marker-alt"></i><?php $listing->the_locations( true, true );?></li>
					<?php endif; ?>
				</ul>
			</div>
			<div class="rtin-right">
				<?php $listing->the_badges(); ?>
			</div>
		</div>
        <?php
            if (!empty( $images || $video )){
        ?>
		<div class="rtin-slider-box<?php echo esc_attr( $slider_class ); ?>">
            <?php TemplateHooks::sold_out_banner(); ?>
			<?php $listing->the_gallery(); ?>
		</div>
		<?php } if ( $listing->can_show_price() ): ?>
			<div class="rtin-price"><?php Functions::print_html($listing->get_price_html()); ?></div>
		<?php endif; ?>

		<div class="row">
			<div class="col-12 col-md-8">
				<div class="rtin-content"><?php $listing->the_content(); ?></div>
			</div>
			<div class="col-12 col-md-4">
				<?php $listing->the_actions(); ?>
			</div>
		</div>
		<?php do_action( 'classilist_single_listing_after_contents' );?>
	</div>
</div>

<?php
    if ( Functions::isEnableFb() && Listing_Functions::form_builder_custom_group_field_check() ){
        URI_Helper::get_custom_listing_template( 'form-builder-cfg' );
    } else {
        $listing->the_custom_fields();
    }
?>