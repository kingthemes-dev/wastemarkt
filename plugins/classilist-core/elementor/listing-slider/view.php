<?php
/**
 * @author  RadiusTheme
 * @since   1.0
 * @version 1.0
 */

namespace radiustheme\ClassiList_Core;

use radiustheme\ClassiList\Helper;

$layout = 1;
$display = array(
	'cat'   => $data['cat_display'] ? true : false,
    'views'   => $data['views_display'] == 'yes' ? true : false,
    'fields'   => $data['field_display']==='yes' ? true : false,
	'label' => false,
    'type'  => $data['type_display'] == 'yes' ? true : false,
);

$rand = substr(md5(mt_rand()), 0, 7);
$swiper_data = [
    "navigation"        => [
        "nextEl" => ".rtin-custom-nav-$rand .owl-next",
        "prevEl" => ".rtin-custom-nav-$rand .owl-prev",
    ],
    "loop"              => boolval( $data['slider_loop'] ),
    "autoplay"          => !empty( $data['slider_autoplay'] ) ? [
        "delay" => $data['slider_interval'],
        "disableOnInteraction"  => false,
        "pauseOnMouseEnter"     => boolval($data['slider_stop_on_hover'])
    ] : boolval( $data['slider_autoplay'] ),
    "speed"             => $data['slider_autoplay_speed'],
    "spaceBetween"      => 20,
    "breakpoints"       => [
        0   => [
            "slidesPerView" => $data['col_mobile']
        ],
        575   => [
            "slidesPerView" => $data['col_xs']
        ],
        767   => [
            "slidesPerView" => $data['col_sm']
        ],
        991   => [
            "slidesPerView" => $data['col_md']
        ],
        1199 => [
            "slidesPerView" => $data['col_lg']
        ]
    ]
];
$swiper_data = json_encode( $swiper_data );
$query = $data['query'];

?>
<div class="rt-el-listing-slider owl-wrap rtin-<?php echo esc_attr($layout); ?>">
    <?php if (!empty($data['slider_navigation'])) { ?>
    <div class="owl-shortcode-nav owl-custom-nav rtin-custom-nav-<?php echo esc_attr( $rand ) ?>">
        <div class="owl-prev"><i class="fa fa-angle-left"></i></div>
        <div class="owl-next"><i class="fa fa-angle-right"></i></div>
    </div>
    <?php } ?>
	<div class="rtcl-carousel-slider" data-options="<?php echo esc_attr( $swiper_data ) ?>">
        <div class="swiper-wrapper">
    		<?php if ( $query->have_posts() ) :?>
    				<?php while ( $query->have_posts() ) : $query->the_post(); ?>
                        <div class="swiper-slide">
    					   <?php Helper::get_template_part( 'classified-listing/custom/grid', compact( 'layout', 'display' ) ); ?>
                        </div>
    				<?php endwhile;?>
    		<?php endif;?>
            <?php wp_reset_postdata(); ?>
    	</div>
    </div>
</div>