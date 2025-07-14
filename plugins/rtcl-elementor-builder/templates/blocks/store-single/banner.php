<?php

/**
 * Store single content
 *
 * @author     RadiusTheme
 * @package    classified-listing/templates
 * @version    1.3.21
 */

use RtclElb\Helpers\Fns;
use Rtcl\Helpers\Functions;

$wrap_class = Fns::get_block_wrapper_class($settings, 'rtcl-block-store-name');
$store = !empty($store) ? $store : rtclStore()->factory->get_store(Fns::last_store_id());
?>
<?php if (!empty($store)) : ?>
	<?php if (!empty($settings['wrapClass'])) : ?>
		<div class="<?php echo esc_attr($wrap_class); ?>">
			<div class="rtcl store-content-wrap">
			<?php endif; ?>
			<div class="store-banner">
				<div class="banner"><?php $store->the_banner(); ?></div>
				<div class="store-name-logo">

					<?php if (!empty($settings['showStoreLogo'])) : ?>
						<div class="store-logo"><?php $store->the_logo(); ?></div>
					<?php endif; ?>

					<div class="store-info">
						<?php if (!empty($settings['showStoreName'])) : ?>
							<div class="store-name">
								<h2><?php $store->the_title(); ?></h2>
							</div>
						<?php endif; ?>
						<?php if (!empty($settings['showStoreCategory']) && $store->get_category()) : ?>
							<div class="rtcl-store-cat">
								<i class="rtcl-icon rtcl-icon-tags"></i>
								<?php Functions::print_html($store->get_category()); ?>
							</div>
						<?php endif; ?>
						<?php
						if (!empty($settings['showStoreRating'])) {
							$review_counts = $store->get_review_counts();
							if ($store->is_rating_enable() && $review_counts) :
						?>
								<div class="reviews-rating">
									<?php echo Functions::get_rating_html($store->get_average_rating(), $review_counts); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped 
									?>
									<span class="reviews-rating-count">(<?php echo absint($review_counts); ?>)</span>
								</div>
						<?php
							endif;
						}
						?>
					</div>
				</div>
			</div>
			<?php if (!empty($settings['wrapClass'])) : ?>
			</div>
		</div>
	<?php endif; ?>
<?php endif; ?>