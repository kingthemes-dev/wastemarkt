<?php

namespace RtclElb\Blocks;

use RtclElb\Traits\Singleton;
use RtclElb\Helpers\Fns;

class StoreAjax
{
	use Singleton;

	protected $attributes = [];

	public function __construct()
	{
		add_action('wp_ajax_rtcl_block_get_store', [$this, 'rtcl_block_get_store']);
	}

	public function rtcl_block_get_store()
	{
		if (!wp_verify_nonce($_POST['rtcl_block_nonce'], 'rtcl-block-nonce')) {
			wp_send_json_error(esc_html__('Session Expired!!', 'rtcl-elementor-builder'));
		}
		$this->attributes = isset($_POST['attributes']) ? map_deep(wp_unslash($_POST['attributes']), 'sanitize_text_field') : [];
		$store_obj = rtclStore()->factory->get_store(Fns::last_store_id());
		$store_info = !empty($store_obj) ? $this->rtcl_block_store_results($store_obj) : [];
		if (!empty($store_info)) {
			wp_send_json_success($store_info);
		} else {
			wp_send_json_error("No Store found");
		}
	}


	public function rtcl_block_store_results($store)
	{
		$store_info = [
			'name' => $store->get_the_title(),
			'slogan' => $store->get_the_slogan(),
			'description' => $store->get_the_description(),
			'contact_info' => $this->get_contact_info($store),
			'opening_hour' => $this->get_opening_hour($store)
		];

		return $store_info;
	}

	public function get_contact_info($store)
	{
		ob_start();
		do_action('rtcl_single_store_information', $store);
		return ob_get_clean();
	}

	public function get_opening_hour($store)
	{
		$store_oh_type  = get_post_meta($store->get_id(), 'oh_type', true);
		$store_oh_hours = get_post_meta($store->get_id(), 'oh_hours', true);
		$store_oh_hours = is_array($store_oh_hours) ? $store_oh_hours : ($store_oh_hours ? (array) $store_oh_hours : []);
		$today          = strtolower(date('l'));
		ob_start(); ?>

		<?php if ('selected' === $store_oh_type) : ?>
			<?php if (is_array($store_oh_hours) && !empty($store_oh_hours)) : ?>
				<?php foreach ($store_oh_hours as $hKey => $oh_hour) : ?>
					<div class="store-hour<?php echo esc_attr(($hKey == $today) ? ' current-store-hour' : ''); ?>">
						<div class=" hour-day"><?php echo esc_html($hKey); ?>
						</div>
						<div class="oh-hours-wrap">
							<?php if (isset($oh_hour['active'])) : ?>
								<div class="oh-hours">
									<span class="open-hour"><?php echo isset($oh_hour['open']) ? esc_html($oh_hour['open']) : ''; ?></span>
									<span class="close-hour"><?php echo isset($oh_hour['close']) ? esc_html($oh_hour['close']) : ''; ?></span>
								</div>
							<?php else : ?>
								<span class="off-day"><?php esc_html_e('Closed', 'rtcl-elementor-builder'); ?></span>
							<?php endif; ?>
						</div>
					</div>
				<?php endforeach; ?>
			<?php else : ?>
				<div class="always-open"><?php esc_html_e('Permanently Close', 'rtcl-elementor-builder'); ?></div>
			<?php endif; ?>
		<?php elseif ('always' === $store_oh_type) : ?>
			<div class="always-open"><?php esc_html_e('Always Open', 'rtcl-elementor-builder'); ?></div>
		<?php endif; ?>
<?php
		return ob_get_clean();
	}
}
