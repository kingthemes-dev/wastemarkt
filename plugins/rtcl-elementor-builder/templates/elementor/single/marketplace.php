<?php

/**
 * @author     RadiusTheme
 * @package    classified-listing/templates
 * @version    1.0.0
 *
 * @var \Rtcl\Models\Listing $listing
 */

use Rtcl\Helpers\Functions;
use RtclMarketplace\Helpers\Functions as MarketplaceFunctions;

$listing_id = $listing->get_id();

$wrapClass = 'rtcl-el-marketplace-wrap el-single-addon';

if ( \Elementor\Plugin::$instance->editor->is_edit_mode() ) {
	$wrapClass .= ' widget';
}

if ( ! MarketplaceFunctions::is_enable_marketplace() ) {
	return;
}

?>
<div class="<?php echo esc_attr( $wrapClass ); ?>">
	<?php
	Functions::get_template(
		'buy-button',
		[
			'is_enable_buy_button' => $settings['rtcl_show_add_to_cart_btn'] ?? true,
			'button_text'          => MarketplaceFunctions::buy_button_text(),
			'is_enable_quantity'   => $settings['rtcl_show_quantity'] ?? true,
			'listing_id'           => $listing_id
		],
		'',
		rtcl_marketplace()->get_plugin_template_path()
	);
	?>
</div>