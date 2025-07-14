<?php

use RtclElb\Helpers\Fns;

$wrap_class = Fns::get_block_wrapper_class($settings);
?>

<?php if (!empty($listing)) { ?>
	<div class="<?php echo esc_attr($wrap_class); ?>">
		<div class="rtcl el-single-addon item-price <?php echo esc_attr($settings['style']); ?> ">
			<?php echo $listing->get_price_html(); ?>
		</div>
	</div>
<?php } ?>