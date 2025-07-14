<?php

use RtclElb\Helpers\Fns;

$wrap_class = Fns::get_block_wrapper_class($settings);
?>
<?php if (!empty($listing)) { ?>
	<div class="<?php echo esc_attr($wrap_class); ?>">
		<?php
		$title_text = $listing->get_the_title();
		printf('<%1$s class="rtcl-listings-header-title page-title" >%2$s</%1$s>', esc_html($settings['titleTag']), esc_html($title_text));
		?>
	</div>
<?php } ?>