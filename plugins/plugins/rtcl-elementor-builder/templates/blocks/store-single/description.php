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
<?php if (!empty($store)) :
	$store_description = $store->get_the_description();
?>
	<div class="<?php echo esc_attr($wrap_class); ?>">
		<div class="store-details store-description-content">
			<?php printf('<%1$s>%2$s</%1$s>', $settings['titleTag'], esc_html($store_description));
			?>
		</div>
	</div>
<?php
endif;
