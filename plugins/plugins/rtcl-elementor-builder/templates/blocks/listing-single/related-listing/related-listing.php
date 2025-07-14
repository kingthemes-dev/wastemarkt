<?php

/**
 *
 * @author     RadiusTheme
 * @package    classified-listing/templates
 * @version    1.0.0
 */

use Rtcl\Helpers\Functions;

add_action('rtcl_listing_can_show_new_badge_settings', '__return_true');
add_action('rtcl_listing_can_show_featured_badge_settings', '__return_true');
add_action('rtcl_listing_can_show_top_badge_settings', '__return_true');
add_action('rtcl_listing_can_show_bump_up_badge_settings', '__return_true');


$header_data = [
	'template'              => 'slider-header-footer/header',
	'settings'              => $settings,
	'block_wrap_class' => 'rtcl-block-related-listing',
	'default_template_path' => rtclElb()->get_plugin_block_template_path(),
];
Functions::get_template($header_data['template'], $header_data, '', $header_data['default_template_path']);
?>

<?php
while ($rtcl_related_query->have_posts()) :
	$rtcl_related_query->the_post();
	$content_data = [
		'template'              => 'listing-single/related-listing/grid/style-' . $settings['style'],
		'instance'              => $settings,
		'item_class'            => '',
		'default_template_path' => rtclElb()->get_plugin_block_template_path(),
	];
	$content_data = apply_filters('rtcl_block_related_listing_data', $content_data);
	Functions::get_template($content_data['template'], $content_data, '', $content_data['default_template_path']);
endwhile;
?>
<?php wp_reset_postdata(); ?>


<?php
$footer_data = [
	'template'              => 'slider-header-footer/footer',
	'settings'              => $settings,
	'default_template_path' => rtclElb()->get_plugin_block_template_path(),
];
Functions::get_template($footer_data['template'], $footer_data, '', $footer_data['default_template_path']);
?>