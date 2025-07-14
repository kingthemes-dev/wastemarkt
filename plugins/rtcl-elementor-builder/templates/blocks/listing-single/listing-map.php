<?php

/**
 * @author     RadiusTheme
 *
 * @version    1.0.0
 *
 * @var Rtcl\Models\Listing $listing
 */

use RtclElb\Helpers\Fns;
use Rtcl\Controllers\Hooks\TemplateHooks;

$wrap_class = Fns::get_block_wrapper_class($settings);
?>

<?php if (!empty($listing)) { ?>
	<div class="<?php echo esc_attr($wrap_class); ?>">
		<div class="rtcl el-single-addon rtin-content-area">
			<?php TemplateHooks::single_listing_map_content($listing); ?>
		</div>
	</div>
<?php } ?>