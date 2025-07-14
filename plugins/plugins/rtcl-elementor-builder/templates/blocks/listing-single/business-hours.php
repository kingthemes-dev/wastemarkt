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

?>
<?php if (!empty($listing)) : ?>
	<div class="<?php echo esc_attr($wrap_class); ?>">
		<div class="rtcl el-single-addon business-hours">
			<?php do_action('rtcl_single_listing_business_hours', $listing); ?>
		</div>
	</div>
<?php endif; ?>