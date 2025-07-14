<?php

/**
 * Store single content
 *
 * @author     RadiusTheme
 * @package    classified-listing/templates
 * @version    1.3.21
 */

use RtclStore\Controllers\Hooks\TemplateHooks;
use RtclElb\Helpers\Fns;

if (empty($settings['showStatus'])) {
	remove_action('rtcl_single_store_information', [TemplateHooks::class, 'store_hours'], 10);
}
if (empty($settings['showAddress'])) {
	remove_action('rtcl_single_store_information', [TemplateHooks::class, 'store_address'], 20);
}
if (empty($settings['showPhone'])) {
	remove_action('rtcl_single_store_information', [TemplateHooks::class, 'store_phone'], 30);
}
if (empty($settings['showSocialMedia'])) {
	remove_action('rtcl_single_store_information', [TemplateHooks::class, 'store_social_media'], 40);
}
if (empty($settings['showEmail'])) {
	remove_action('rtcl_single_store_information', [TemplateHooks::class, 'store_social_email'], 50);
}
$wrap_class = Fns::get_block_wrapper_class($settings);
?>

<?php if (!empty($store)) : ?>
	<div class="<?php echo esc_attr($wrap_class); ?>">
		<div class="store-information">
			<div class="store-info">
				<?php do_action('rtcl_single_store_information', $store); ?>
			</div>
		</div>
	</div>
<?php endif; ?>

<?php
if (empty($settings['showStatus'])) {
	add_action('rtcl_single_store_information', [TemplateHooks::class, 'store_hours'], 10);
}
if (empty($settings['showAddress'])) {
	add_action('rtcl_single_store_information', [TemplateHooks::class, 'store_address'], 20);
}
if (empty($settings['showPhone'])) {
	add_action('rtcl_single_store_information', [TemplateHooks::class, 'store_phone'], 30);
}
if (empty($settings['showSocialMedia'])) {
	add_action('rtcl_single_store_information', [TemplateHooks::class, 'store_social_media'], 40);
}
if (empty($settings['showEmail'])) {
	add_action('rtcl_single_store_information', [TemplateHooks::class, 'store_social_email'], 50);
}
