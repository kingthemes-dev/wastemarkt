<?php

/**
 * Store single content
 *
 * @author     RadiusTheme
 * @package    classified-listing/templates
 * @version    1.3.21
 */

use RtclElb\Helpers\Fns;
use Rtcl\Helpers\Functions;
use RtclStore\Controllers\Hooks\TemplateHooks;

if (empty($instance['rtcl_show_store_status']) || $instance['rtcl_show_store_status'] === 'off') {
	remove_action('rtcl_single_store_information', [TemplateHooks::class, 'store_hours'], 10);
}
if (empty($instance['rtcl_show_store_address']) || $instance['rtcl_show_store_address'] === 'off') {
	remove_action('rtcl_single_store_information', [TemplateHooks::class, 'store_address'], 20);
}
if (empty($instance['rtcl_show_store_phone']) || $instance['rtcl_show_store_phone'] === 'off'	) {
	remove_action('rtcl_single_store_information', [TemplateHooks::class, 'store_phone'], 30);
}
if (empty($instance['rtcl_show_store_social_media']) || $instance['rtcl_show_store_social_media'] === 'off') {
	remove_action('rtcl_single_store_information', [TemplateHooks::class, 'store_social_media'], 40);
}
if (empty($instance['rtcl_show_store_email']) || $instance['rtcl_show_store_email'] === 'off') {
	remove_action('rtcl_single_store_information', [TemplateHooks::class, 'store_social_email'], 50);
}

if(!get_the_ID() && $instance['rtcl_show_store_email'] === 'on' ){
	echo '<style>
		div#store-email-area {
			display: block !important;
		}</style>';
}
?>
<div class="rtcl store-content-wrap">
	<div class="store-information <?php echo \Elementor\Plugin::$instance->editor->is_edit_mode() ? 'edit-mode' : ''; ?>">
	<div class="store-info">
		<?php do_action('rtcl_single_store_information', $store); ?>
	</div>
</div>
<?php

if (empty($instance['rtcl_show_store_status']) || $instance['rtcl_show_store_status'] === 'off') {
	add_action('rtcl_single_store_information', [TemplateHooks::class, 'store_hours'], 10);
}
if (empty($instance['rtcl_show_store_address']) || $instance['rtcl_show_store_address'] === 'off') {
	add_action('rtcl_single_store_information', [TemplateHooks::class, 'store_address'], 20);
}
if (empty($instance['rtcl_show_store_phone']) || $instance['rtcl_show_store_phone'] === 'off') {
	add_action('rtcl_single_store_information', [TemplateHooks::class, 'store_phone'], 30);
}
if (empty($instance['rtcl_show_store_social_media']) || $instance['rtcl_show_store_social_media'] === 'off') {
	add_action('rtcl_single_store_information', [TemplateHooks::class, 'store_social_media'], 40);
}
if (empty($instance['rtcl_show_store_email']) || $instance['rtcl_show_store_email'] === 'off') {
	
	add_action('rtcl_single_store_information', [TemplateHooks::class, 'store_social_email'], 50);
}
	?>
</div>

