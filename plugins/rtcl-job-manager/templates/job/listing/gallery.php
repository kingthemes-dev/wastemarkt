<?php
/**
 * @author     RadiusTheme
 * @package    classified-listing/templates
 * @version    1.0.0
 *
 * @var array[] $images
 * @var array[] $videos
 * @var Listing $listing
 */


use Rtcl\Helpers\Functions;
use Rtcl\Models\Listing;

if ( ! $listing ) {
	global $listing;
}

if ( ! $listing ) {
	return;
}

$total_gallery_image  = count( $images );
$total_gallery_videos = count( $videos );
$total_gallery_item   = $total_gallery_image + $total_gallery_videos;
$isSliderEnable       = Functions::is_gallery_slider_enabled();
if ( $total_gallery_item ) :
	?>

	<div id="rtcl-slider-wrapper" class="rtcl-slider-wrapper mb-4" data-options="">
		<!-- Slider -->
		<div class="rtcl-slider<?php echo esc_attr( $isSliderEnable ? '' : ' off' ) ?>">
			<div class="swiper-wrapper">
				<?php
				if ( $total_gallery_videos ) {
					foreach ( $videos as $index => $video_url ) { ?>
						<div class="swiper-slide rtcl-slider-item rtcl-slider-video-item">
							<iframe class="rtcl-lightbox-iframe"
									src="<?php echo esc_url(Functions::get_sanitized_embed_url( $video_url )) ?>"
									style="width: 100%; height: 400px; margin: 0;padding: 0; background-color: #000"
									frameborder="0" webkitAllowFullScreen
									mozallowfullscreen allowFullScreen></iframe>
						</div>
						<?php
					}
				}
				if ( $total_gallery_image ) {
					foreach ( $images as $index => $image ) :
						$image_attributes = wp_get_attachment_image_src( $image->ID, 'large' );
						$image_full = wp_get_attachment_image_src( $image->ID, 'full' ); ?>
						<div class="swiper-slide rtcl-slider-item">
							<img src="<?php echo esc_html( $image_attributes[0] ); ?>"
								 alt="<?php echo esc_attr( $listing->get_the_title() ); ?>"
								 data-caption="<?php echo esc_attr( wp_get_attachment_caption( $image->ID ) ); ?>"
								 class="rtcl-responsive-img"/>
						</div>
					<?php endforeach;
				} ?>
			</div>
			<div class="swiper-button-next"></div>
			<div class="swiper-button-prev"></div>
		</div>
	</div>
<?php endif;
