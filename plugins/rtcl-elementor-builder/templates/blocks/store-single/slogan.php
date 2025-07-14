<?php

/**
 * Store single content
 *
 * @author     RadiusTheme
 * @package    classified-listing/templates
 * @version    1.3.21
 */

use RtclElb\Helpers\Fns;

$wrap_class = Fns::get_block_wrapper_class($settings);

?>
<?php if (!empty($store)) : ?>
	<div class="<?php echo esc_attr($wrap_class); ?>">
		<div class="store-details">
			<?php
			$sologan = $store->get_the_slogan();
			printf('<%1$s class="is-sogan">%2$s</%1$s>', $settings['titleTag'], $sologan);
			?>
		</div>
	</div>
<?php endif; ?>