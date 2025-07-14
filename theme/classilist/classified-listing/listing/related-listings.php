<?php
/**
 *
 * @author  RadiusTheme
 * @package classified-listing/templates
 * @since   1.0
 * @version 2.2.4
 */

use Rtcl\Models\Listing;
use radiustheme\ClassiList\URI_Helper;

$layout = 1;
?>
<?php if ( $rtcl_related_query->have_posts() ) : ?>
    <div class="content-block-gap"></div>
    <div class="site-content-block classilist-single-related owl-wrap">
        <div class="main-title-block">
            <h3 class="main-title"><?php esc_html_e( 'Related Ads', 'classilist' );?></h3>
        </div>
        <div class="main-content">
            <div class="rtcl-carousel-slider" data-options="<?php echo htmlspecialchars(wp_json_encode($slider_options)); ?>">
                <div class="swiper-wrapper">
                    <?php while ( $rtcl_related_query->have_posts() ) : $rtcl_related_query->the_post(); ?>
                        <?php URI_Helper::get_template_part( 'classified-listing/custom/grid', compact( 'layout' ) );?>
                    <?php endwhile; ?>
                    <?php wp_reset_postdata(); ?>
                </div>
            </div>
        </div>
    </div>
<?php endif;