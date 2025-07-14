<?php

/**
 * @author     RadiusTheme
 * @package    classified-listing/templates
 * @version    1.0.0
 *
 * @var Rtcl\Models\Listing $listing
 */

use Rtcl\Helpers\Functions;
use RtclElb\Helpers\Fns;

$wrap_class = Fns::get_block_wrapper_class($settings);

$hidden_fields = Functions::get_option_item('rtcl_moderation_settings', 'hide_form_fields', []);
if (in_array('features', $hidden_fields)) {
	esc_html_e('Feature List In not enable', 'rtcl-elementor-builder');
	return;
}

$spec_items = [];

if (!empty($listing)) {
	$listing_id = $listing->get_id();
	$spec_info = get_post_meta($listing_id, 'classima_spec_info', true);
	$spec      = isset($spec_info['specs']) ? $spec_info['specs'] : '';
	if ($spec) {
		$spec_items = explode(PHP_EOL, $spec);
	}
}
?>

<?php if (!empty($spec_items)) : ?>
	<div class="<?php echo esc_attr($wrap_class); ?>">
		<div class="rtcl-single-block el-single-addon classima-listing-single">
			<div class="classima-single-details">
				<div class="rtin-specs">
					<?php if ($settings['showTitle']) { ?>
						<h3 class="rtin-specs-title"><?php esc_html_e('Features:', 'rtcl-elementor-builder'); ?></h3>
					<?php } ?>
					<ul class="rtin-spec-items">
						<?php foreach ($spec_items as $spec_item) : ?>
							<li><?php echo wp_kses_post($spec_item); ?></li>
						<?php endforeach ?>
					</ul>
				</div>
			</div>
		</div>
	</div>
<?php endif; ?>