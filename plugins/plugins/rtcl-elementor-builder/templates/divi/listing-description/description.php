<?php

/**
 * @author     RadiusTheme
 * @package    classified-listing/templates
 * @version    1.0.0
 *
 * @var Rtcl\Models\Listing $listing
 */
?>
<!-- Description -->
<div class="rtcl rtcl-listing-description el-single-addon <?php echo !empty($settings['rtcl_show_drop_cap']) &&  $settings['rtcl_show_drop_cap'] === 'on' ?'enabled-drop-cap' : ''; ?> ">
	<?php 
		echo $listing->the_content();
	?>
</div>