<?php

use RtclElb\Helpers\Fns;

$block_unique_class = '.' . $settings['blockId'];

$auto_height    = $settings['sliderOptions']['autoHeight'] ? $settings['sliderOptions']['autoHeight'] : '0';
$loop           = $settings['sliderOptions']['loop'] ? $settings['sliderOptions']['loop'] : '0';
$autoplay       = $settings['sliderOptions']['autoPlay'] ? $settings['sliderOptions']['autoPlay'] : '0';
$stop_on_hover  = $settings['sliderOptions']['stopOnHover'] ? $settings['sliderOptions']['stopOnHover'] : '0';
$delay          = $settings['sliderOptions']['autoPlayDelay'] ? $settings['sliderOptions']['autoPlayDelay'] : '5000';
$autoplay_speed = $settings['sliderOptions']['autoPlaySlideSpeed'] ? $settings['sliderOptions']['autoPlaySlideSpeed'] : '200';

$dots = $settings['sliderOptions']['dotNavigation'] ? $settings['sliderOptions']['dotNavigation'] : '0';
$nav  = $settings['sliderOptions']['arrowNavigation'] ? $settings['sliderOptions']['arrowNavigation'] : '0';
$space_between = isset($settings['sliderOptions']['spaceBetween']) ? $settings['sliderOptions']['spaceBetween'] : '20';

$autoplay   = boolval($autoplay) ? array(
	'delay' => absint($delay),
	'pauseOnMouseEnter' => boolval($stop_on_hover),
	'disableOnInteraction' => false,
) : boolval($autoplay);

$pagination = boolval($dots) ? array(
	'el'        => "$block_unique_class .rtcl-slider-pagination",
	'clickable' => true,
	'type'      => 'bullets',
) : boolval($dots);

$navigation = boolval($nav) ? array(
	'nextEl' => "$block_unique_class .button-left",
	'prevEl' => "$block_unique_class .button-right",
) : boolval($nav);

$break_0    = array(
	'slidesPerView'  => absint($settings['slidesItem']['sm']),
	'slidesPerGroup' => absint($settings['slidesItem']['sm']),
);
$break_576  = array(
	'slidesPerView'  => absint($settings['slidesItem']['sm']),
	'slidesPerGroup' => absint($settings['slidesItem']['sm']),
);
$break_768  = array(
	'slidesPerView'  => absint($settings['slidesItem']['md']),
	'slidesPerGroup' => absint($settings['slidesItem']['md']),
);
$break_1200 = array(
	'slidesPerView'  => absint($settings['slidesItem']['lg']),
	'slidesPerGroup' => absint($settings['slidesItem']['lg']),
);

$swiper_data = array(
	// Optional parameters
	'slidesPerView'  => absint($settings['slidesItem']['lg']),
	'slidesPerGroup' => absint($settings['slidesItem']['lg']),
	'spaceBetween'   => absint($space_between),
	'loop'           => boolval($loop),
	// If we need pagination
	//'slideClass'     => 'swiper-slide-customize',
	'autoplay'       => $autoplay,
	// If we need pagination
	'pagination'     => $pagination,
	'speed'          => absint($autoplay_speed),
	// allowTouchMove: true,
	// Navigation arrows
	'navigation'     => $navigation,
	'autoHeight'     => boolval($auto_height),
	'breakpoints'    => array(
		0    => $break_0,
		576  => $break_576,
		768  => $break_768,
		1200 => $break_1200,
	),
);
$swiper_data = wp_json_encode($swiper_data);

$slider_arow_dot_style = '';
if ($settings['sliderOptions']['dotNavigation'] && !empty($settings['sliderOptions']['dotStyle'])) {
	$slider_arow_dot_style = " rtcl-slider-pagination-style-" . $settings['sliderOptions']['dotStyle'];
}
if ($settings['sliderOptions']['arrowNavigation'] && !empty($settings['sliderOptions']['arrowPosition'])) {
	$slider_arow_dot_style .= " rtcl-slider-btn-style-" . $settings['sliderOptions']['arrowPosition'];
}

$wrap_class = Fns::get_block_wrapper_class($settings, 'rtcl rtcl-listings-sc-wrapper rtcl-elementor-widget rtcl-el-slider-wrapper rtcl-listings-slider' . $slider_arow_dot_style);

$block_wrapper_class = 'rtcl-listings-wrapper ';
if (isset($block_wrap_class) && !empty($block_wrap_class)) {
	$block_wrapper_class .= $block_wrap_class;
}

$swiper_wrapper_class = 'rtcl-listings rtcl-listings-slider-container swiper rtcl-carousel-slider';
if (!empty($settings['layout']) && !empty($settings['style'])) {
	$swiper_wrapper_class .= ' rtcl-' . $settings['layout'] . '-view ';
	$swiper_wrapper_class .= ' rtcl-style-' .  $settings['style'] . '-view ';
}

if ($settings['sliderOptions']['autoHeight'] != 'true') {
	$swiper_wrapper_class .= ' ' . 'rtrb-swiper-equal-height';
}
?>

<div class="<?php echo esc_attr($wrap_class); ?>">

	<?php if (isset($settings['sliderOptions']['sliderLoader']) && $settings['sliderOptions']['sliderLoader']) :  ?>
		<div class="rtcl-swiper-lazy-preloader">
			<svg class="spinner" viewBox="0 0 50 50">
				<circle class="path" cx="25" cy="25" r="20" fill="none" stroke-width="5"></circle>
			</svg>
		</div>
	<?php endif; ?>

	<div class="<?php echo esc_attr($block_wrapper_class); ?>" style="opacity:0">
		<div class="<?php echo esc_attr($swiper_wrapper_class); ?>" data-options="<?php echo esc_attr($swiper_data); ?>">


			<div class="swiper-wrapper">