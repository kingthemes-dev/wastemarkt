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
<!-- Seller / User Information -->
<?php if (!empty($listing)) { ?>
	<div class="<?php echo esc_attr($wrap_class); ?>">
		<div class="rtcl el-single-addon seller-information">
			<div class="rtcl-listing-user-info">
				<div class="list-group">
					<?php do_action('rtcl_listing_seller_information', $listing); ?>
				</div>
			</div>
		</div>
	</div>
<?php } ?>