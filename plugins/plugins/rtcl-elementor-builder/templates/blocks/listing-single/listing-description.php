<?php

use RtclElb\Helpers\Fns;

$wrap_class = Fns::get_block_wrapper_class($settings);
?>

<?php if (!empty($listing)) { ?>
	<div class="<?php echo esc_attr($wrap_class); ?>">
		<div class="rtcl rtcl-listing-description el-single-addon <?php echo !empty($settings['dropCap']) ? 'enabled-drop-cap' : ''; ?> ">
			<?php echo wpautop(get_the_content(null, false, $listing->get_id())); ?>
		</div>
	</div>
<?php } ?>