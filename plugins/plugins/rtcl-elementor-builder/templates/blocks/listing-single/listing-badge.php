<?php

use RtclElb\Helpers\Fns;

$wrap_class = Fns::get_block_wrapper_class($settings);
?>

<?php if (!empty($listing)) { ?>
	<div class="<?php echo esc_attr($wrap_class); ?>">
		<div class="rtcl el-single-addon single-listing-meta-wrap">
			<?php $listing->the_badges(); ?>
		</div>
	</div>
<?php } ?>