<?php

/**
 * @author     RadiusTheme
 * @package    classified-listing/templates
 * @version    1.0.0
 *
 * @var Rtcl\Models\Listing $listing
 */

use RtclElb\Helpers\Fns;

$wrap_class = Fns::get_block_wrapper_class($settings);

$class = !empty($settings['style']) ? $settings['style'] : '';
$class .= !empty($settings['labelValueNewLine']) ? ' label-new-line' : '';
?>
<?php if (!empty($listing)) { ?>
	<div class="<?php echo esc_attr($wrap_class); ?>">
		<div class="rtcl el-single-addon custom-field-content-area <?php echo esc_attr($class); ?>">
			<?php $listing->the_custom_fields(); ?>
		</div>
	</div>
<?php } ?>