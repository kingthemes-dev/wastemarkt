<?php

/**
 * Store single content
 *
 * @author     RadiusTheme
 * @package    classified-listing/templates
 * @version    1.3.21
 */

use RtclElb\Helpers\Fns;

$wrap_class = Fns::get_block_wrapper_class($settings, 'rtcl-block-store-name');

?>
<?php if (!empty($store)) : ?>
	<div class="<?php echo esc_attr($wrap_class); ?>">
		<div class="rtcl store-content-wrap">
			<div class="store-name">
				<?php
				$title_text = $store->get_the_title();
				printf('<%1$s >%2$s</%1$s>', $settings['titleTag'], $title_text);
				?>
			</div>
		</div>
	</div>
<?php endif; ?>