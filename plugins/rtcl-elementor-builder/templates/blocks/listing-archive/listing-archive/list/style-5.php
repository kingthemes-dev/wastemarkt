<?php

/**
 *
 * @author     RadiusTheme
 * @package    classified-listing/templates
 * @version    1.0.0
 */

use Rtcl\Helpers\Functions;
use Rtcl\Helpers\Pagination;
use Rtcl\Models\Listing;
use RtclPro\Helpers\Fns;
use RtclPro\Controllers\Hooks\TemplateHooks;

$_id                 = get_the_ID();
$post_meta           = get_post_meta($_id);
$listing             = new Listing($_id);
$phone               = get_post_meta($_id, 'phone', true);
$listing_title       = null;
$listing_meta        = null;
$listing_description = null;
$img                 = null;
$labels              = null;
$u_info              = null;
$time                = null;
$location            = null;
$category            = null;
$price               = null;
$types               = null;
$img_position_class  = '';
$custom_field = null;
?>

<div <?php Functions::listing_class(['rtcl-widget-listing-item', $item_class, 'listing-item', $img_position_class]); ?>>
	<?php
	if ($instance['contentVisibility']['thumbnail']) {
		ob_start();
		if (rtcl()->has_pro()) {
			TemplateHooks::sold_out_banner();
		}
		$mark_as_sold = ob_get_clean();

		$image_size    = $instance['listingImageSize'];
		$the_thumbnail = $listing->get_the_thumbnail($image_size);

		if ($the_thumbnail) {
			$img = sprintf(
				"<div class='listing-thumb'>%s<a href='%s' title='%s'>%s</a></div>",
				$mark_as_sold,
				get_the_permalink(),
				esc_html(get_the_title()),
				$the_thumbnail
			);
		}
	}
	if ($instance['contentVisibility']['badge']) {
		$labels = $listing->badges();
	}
	if ($instance['contentVisibility']['date']) {
		$time = sprintf(
			'<li class="date"><i class="rtcl-icon rtcl-icon-clock" aria-hidden="true"></i>%s</li>',
			$listing->get_the_time()
		);
	}
	if ($instance['contentVisibility']['location']) {
		if (strip_tags($listing->the_locations(false))) {
			$location = sprintf(
				'<li class="location"><i class="rtcl-icon rtcl-icon-location" aria-hidden="true"></i>%s</li>',
				$listing->the_locations(false)
			);
		}
	}

	if ($instance['contentVisibility']['price']) {
		$price_html = $listing->get_price_html();
		$price      = sprintf('<div class="item-price">%s</div>', $price_html);
	}

	$author_html = '';
	if ($instance['contentVisibility']['author']) {
		ob_start();
		if (!empty($instance['contentVisibility']['rtcl_verified_user_base'])) {
			do_action('rtcl_after_author_meta', $listing->get_owner_id());
		}
		$after_author_meta = ob_get_clean();
		$author_html = sprintf('<li class="author" ><i class="rtcl-icon rtcl-icon-user" aria-hidden="true"></i>%s %s</li>', get_the_author(), $after_author_meta);
	}
	$views_html = '';
	if ($instance['contentVisibility']['view']) {
		$views      = absint(get_post_meta(get_the_ID(), '_views', true));
		$views_html = sprintf(
			'<li class="view"><i class="rtcl-icon rtcl-icon-eye" aria-hidden="true"></i>%s</li>',
			sprintf(
				/* translators: %s: views count */
				_n('%s view', '%s views', $views, 'rtcl-elementor-builder'),
				number_format_i18n($views)
			)
		);
	}

	if ($instance['contentVisibility']['listing_type'] && $listing->get_ad_type()) {
		$listing_types = Functions::get_listing_types();
		$types         = !empty($listing_types) ? $listing_types[$listing->get_ad_type()] : '';
		if ($types) {
			$types = sprintf(
				'<li class="rtin-type"><i class="rtcl-icon-tags" aria-hidden="true"></i>%s</li>',
				$types
			);
		}
	}

	if ($types || $author_html || $time || $location || $views_html) {
		$listing_meta = sprintf('<ul class="rtcl-listing-meta-data">%s %s %s %s %s</ul>', $types, $author_html, $time, $location, $views_html);
	}

	if ($instance['contentVisibility']['category']) {
		$category = sprintf(
			'<div class="category">%s</div>',
			$listing->the_categories(false, true)
		);
	}

	if ($instance['contentVisibility']['title']) {
		$listing_title = sprintf(
			' <h3 class="listing-title rtcl-listing-title"><a href="%1$s" title="%2$s">%2$s</a> </h3>',
			get_the_permalink(),
			esc_html(get_the_title())
		);
	}

	if ($instance['contentVisibility']['list_content']) {
		$excerpt = get_the_excerpt($_id);

		$listing_description = sprintf(
			'<div class="rtcl-short-description"> %s </div>',
			wpautop($excerpt)
		);
	}

	ob_start();
	?>
	<div class="rtin-bottom">
		<div class="rtcl-meta-buttons-withtext">
			<?php if ($phone && $instance['contentVisibility']['phone_btn']) : ?>
				<div class="rtin-phn rtin-el-button">
					<a class="rtcl-phone-reveal not-revealed" href="tel:<?php echo esc_attr($phone); ?>" data-phone="<?php echo esc_attr($phone); ?>"><i class="rtcl-icon rtcl-icon-phone" aria-hidden="true"></i><span><?php esc_html_e('Show Phone No', 'rtcl-elementor-builder'); ?></span></a>
				</div>
			<?php endif; ?>
			<?php if (Functions::is_enable_favourite() && $instance['contentVisibility']['favourit_btn']) : ?>
				<div class="rtin-fav rtcl-text-el-button">
					<?php echo Functions::get_favourites_link($_id); ?>
				</div>
			<?php endif; ?>

			<?php
			if (rtcl()->has_pro()) {
				if (!empty($instance['contentVisibility']['quick_btn'])) {
			?>
					<div class="rtin-quick-view rtcl-text-el-button">
						<a class="rtcl-quick-view" href="#" data-listing_id="<?php echo absint($_id); ?>">
							<i class="rtcl-icon rtcl-icon-zoom-in"></i><span><?php esc_html_e('Quick View', 'rtcl-elementor-builder'); ?></span>
						</a>
					</div>
			<?php
				}
			}
			?>

			<?php
			if (rtcl()->has_pro()) {
				if (!empty($instance['contentVisibility']['compare_btn'])) {
					$compare_ids    = !empty($_SESSION['rtcl_compare_ids']) ? $_SESSION['rtcl_compare_ids'] : [];
					$selected_class = '';
					if (is_array($compare_ids) && in_array($_id, $compare_ids)) {
						$selected_class = ' selected';
					}
			?>
					<div class="rtin-compare rtcl-text-el-button">
						<a class="rtcl-compare <?php echo esc_attr($selected_class); ?>" href="#" data-listing_id="<?php echo absint($_id); ?>">
							<i class="rtcl-icon rtcl-icon-retweet"></i><span><?php esc_html_e('Compare', 'rtcl-elementor-builder'); ?></span>
						</a>
					</div>
			<?php
				}
			}
			?>
		</div>
	</div>
	<?php
	$rtin_bottom        = ob_get_clean();
	$item_content_right = sprintf(
		'<div class="rtin-right"> %s </div>',
		$rtin_bottom
	);

	if (!empty($instance['contentVisibility']['custom_field'])) {
		ob_start();
		TemplateHooks::loop_item_listable_fields();
		$custom_field = ob_get_clean();
	}

	$item_content   = sprintf(
		'<div class="item-content">%s %s %s %s %s %s %s </div>%s',
		$labels,
		$category,
		$listing_title,
		$custom_field,
		$listing_meta,
		$listing_description,
		$price,
		$item_content_right
	);
	$final_contents = sprintf('%s <div class="rtin-content-area">%s</div>', $img, $item_content);
	echo wp_kses_post($final_contents);
	?>

</div>