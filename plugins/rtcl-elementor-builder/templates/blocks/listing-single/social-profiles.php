<?php

/**
 * The template to display the Social profile
 *
 * @author  RadiousTheme
 * @package classified-listing/Templates
 * @var Rtcl\Models\Listing $listing
 */

use RtclElb\Helpers\Fns;

$wrap_class = Fns::get_block_wrapper_class($settings);
add_filter(
	'rtcl_social_profile_label',
	function ($text) use ($settings) {
		if (!$settings['showLabel']) {
			$text = '';
		} elseif ($settings['labelText']) {
			$text = $settings['labelText'];
		}
		return $text;
	}
);
?>

<?php if (!empty($listing)) { ?>
	<div class="<?php echo esc_attr($wrap_class); ?>">
		<div class="rtcl el-single-addon social-profile">
			<?php do_action('rtcl_single_listing_social_profiles', $listing); ?>
		</div>
	</div>
<?php } ?>