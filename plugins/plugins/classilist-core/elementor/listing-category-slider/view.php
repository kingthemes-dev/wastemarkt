<?php
/**
 * @author  RadiusTheme
 * @since   1.0
 * @version 1.0
 */

namespace radiustheme\ClassiList_Core;

if ( !$data['rt_results'] ) {
	return;
}

$link = '';

if ( !empty( $data['link_url']['url'] ) ) {
    $attr  = 'href="' . $data['link_url']['url'] . '"';
    $attr .= !empty( $data['link_url']['is_external'] ) ? ' target="_blank"' : '';
    $attr .= !empty( $data['link_url']['nofollow'] ) ? ' rel="nofollow"' : '';
    $link = '<a ' . $attr . '>' . $data['link_text'] . ' <i class=\'fa fa-angle-right\'></i> </a>';
}

$rand = substr(md5(mt_rand()), 0, 7);
$swiper_data = [
    "navigation"        => [
        "nextEl"            => ".rtin-custom-nav-$rand .owl-next",
        "prevEl"            => ".rtin-custom-nav-$rand .owl-prev",
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

?>
<div class="rt-el-listing-cat-slider rt-el-listing-slider">
    <?php if (!empty($data['slider_navigation'])) { ?>
    <div class="owl-shortcode-nav owl-custom-nav rtin-custom-nav-<?php echo esc_attr( $rand ) ?>">
        <div class="owl-prev"><i class="fa fa-angle-left"></i></div>
        <div class="owl-next"><i class="fa fa-angle-right"></i></div>
    </div>
    <?php } ?>
	<div class="rtcl-carousel-slider" data-options="<?php echo esc_attr( $swiper_data ) ?>">
        <div class="swiper-wrapper">
    		<?php foreach ( $data['rt_results'] as $result ): ?>
                <a class="rtin-item swiper-slide" href="<?php echo esc_attr( $result['permalink'] );?>">
                    <?php if ( $data['icon'] && $result['icon_html'] ): ?>
                        <div class="rtin-icon"><?php echo wp_kses_post( $result['icon_html'] );?></div>
                    <?php endif; ?>
                    <h3 class="rtin-title"><?php echo esc_html( $result['name'] );?></h3>
                    <?php if ( $data['count'] ): ?>
                        <div class="rtin-count">(<?php echo esc_html( $result['count'] );?>)</div>
                    <?php endif; ?>
                </a>
    		<?php endforeach; ?>
        </div>
	</div>
</div>