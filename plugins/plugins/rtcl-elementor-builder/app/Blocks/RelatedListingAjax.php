<?php

namespace RtclElb\Blocks;

use Rtcl\Helpers\Functions;
use Rtcl\Models\Listing;
use RtclPro\Helpers\Fns;
use RtclStore\Helpers\Functions as StoreFunctions;
use RtclElb\Traits\Singleton;

class RelatedListingAjax
{
	use Singleton;
	public function __construct()
	{
		add_action('wp_ajax_rtcl_block_related_listing_ajax', [$this, 'rtcl_block_related_listing_ajax']);
		add_filter('excerpt_more', '__return_empty_string');
	}

	public static function rtcl_block_listings_query($settings, $last_listing)
	{
		$results      = [];

		$listings_filter = !empty($settings['listingFilter']) ? $settings['listingFilter'] : ['category'];
		$listings_filter = wp_list_pluck($listings_filter, 'value');
		$settings['listingPerPage'] = isset($settings['listingPerPage']) ? intval($settings['listingPerPage']) : 10;

		$args = [
			'post_type'      => 'rtcl_listing',
			'post_status'    => 'publish',
			'posts_per_page' => intval($settings['listingPerPage']),
			'post__not_in' => [$last_listing->get_id()],
			'tax_query'      => [
				'relation' => 'AND',
			],

		];

		if (in_array('author', $listings_filter)) {
			$store = false;
			$author_id = $last_listing->get_author_id();
			if (class_exists('RtclPro') && class_exists('RtclStore')) {
				$store = StoreFunctions::get_user_store($author_id);
				if ($store) {
					$author_id = $store->owner_id();
				}
			}
			$args['author__in'] = $author_id;
		}

		if (in_array('category', $listings_filter)) {
			$the_tax                       = wp_get_object_terms($last_listing->get_id(), rtcl()->category);
			$terms                         = !empty($the_tax) ? end($the_tax)->term_id : 0;
			$args['tax_query'][]           = [
				[
					'taxonomy'         => rtcl()->category,
					'field'            => 'term_id',
					'terms'            => $terms,
				],
			];
		}
		if (in_array('location', $listings_filter)) {
			$the_tax                       = wp_get_object_terms($last_listing->get_id(), rtcl()->location);
			$terms                         = !empty($the_tax) ? end($the_tax)->term_id : 0;
			//$args['tax_query']['relation'] = 'AND';
			$args['tax_query'][]           = [
				[
					'taxonomy'         => rtcl()->location,
					'field'            => 'term_id',
					'terms'            => $terms,
				],
			];
		}

		if (in_array('listing_type', $listings_filter)) {
			$args['meta_key']   = 'ad_type';
			$args['meta_value'] = $last_listing->get_ad_type();
		}

		$loop_obj = new \WP_Query($args);

		while ($loop_obj->have_posts()) :
			$loop_obj->the_post();
			$_id          = get_the_ID();
			$listing      = new Listing($_id);
			$liting_class = Functions::get_listing_class(['rtcl-widget-listing-item', 'listing-item'], $_id);
			$phone        = get_post_meta($_id, 'phone', true);
			$compare      = $quick_view = $sold_item = '';

			ob_start();
			do_action('rtcl_listing_badges', $listing);
			$badge = ob_get_contents();
			ob_end_clean();

			if (rtcl()->has_pro()) {
				if ($listing && Fns::is_enable_mark_as_sold() && Fns::is_mark_as_sold($listing->get_id())) {
					$sold_item = '<span class="rtcl-sold-out">' . apply_filters('rtcl_sold_out_banner_text', esc_html__("Sold Out", 'rtcl-elementor-builder')) . '</span>';
				}
			}

			if (rtcl()->has_pro()) {
				if (Fns::is_enable_compare()) {
					$compare_ids    = !empty($_SESSION['rtcl_compare_ids']) ? $_SESSION['rtcl_compare_ids'] : [];
					$selected_class = '';
					if (is_array($compare_ids) && in_array($_id, $compare_ids)) {
						$selected_class = ' selected';
					}
					$compare = sprintf(
						'<a class="rtcl-compare %s" href="#" data-listing_id="%s"><i class="rtcl-icon rtcl-icon-retweet"></i><span class="compare-label">%s</span></a>',
						$selected_class,
						absint($_id),
						esc_html__("Compare", "rtcl-elementor-builder")
					);
				}
			}

			if (rtcl()->has_pro()) {
				if (Fns::is_enable_quick_view()) {
					$quick_view = sprintf(
						'<a class="rtcl-quick-view" href="#" data-listing_id="%s"><i class="rtcl-icon rtcl-icon-zoom-in"></i><span class="quick-label">%s</span></a>',
						absint($_id),
						esc_html__("Quick View", "rtcl-elementor-builder")
					);
				}
			}

			$pp_id        = absint(get_user_meta($listing->get_owner_id(), '_rtcl_pp_id', true));
			$author_image = $pp_id ? wp_get_attachment_image($pp_id, [40, 40]) : get_avatar($_id, 40);

			//image size
			$image_size = isset($settings['listingImageSize']) ? $settings['listingImageSize'] : 'rtcl-thumbnail';
			if ('custom' == $image_size) {
				if (isset($settings['custom_image_width']) && isset($settings['custom_image_height'])) {
					$image_size = [
						$settings['custom_image_width'],
						$settings['custom_image_height'],
					];
				}
			}

			$results[] = [
				"ID"             => $_id,
				"title"          => get_the_title(),
				"thumbnail"      => $listing->get_the_thumbnail($image_size),
				"locations"      => $listing->the_locations(false),
				"categories"     => $listing->the_categories(false, true),
				"price"          => $listing->get_price_html(),
				"excerpt"        => get_the_excerpt($_id),
				"time"           => $listing->get_the_time(),
				"badges"         => $badge,
				"views"          => absint(get_post_meta(get_the_ID(), '_views', true)),
				"author"         => get_the_author(),
				"classes"        => $liting_class,
				"post_link"      => get_post_permalink(),
				"listing_type"   => $listing->get_ad_type(),
				"favourite_link" => Functions::get_favourites_link($_id),
				"compare"        => $compare,
				"quick_view"     => $quick_view,
				"phone"          => $phone,
				"author_image"   => $author_image,
				"sold"           => $sold_item,

			];

		endwhile; ?>
		<?php wp_reset_postdata(); ?>

		<?php return [
			"total_post" => $loop_obj->found_posts,
			"posts"      => $results,
			"query_obj"  => $loop_obj
		];
	}

	public function rtcl_block_related_listing_ajax()
	{
		if (!wp_verify_nonce($_POST['rtcl_block_nonce'], 'rtcl-block-nonce')) {
			wp_send_json_error(esc_html__('Session Expired!!', 'rtcl-elementor-builder'));
		}
		$listings = [];
		$listingId = map_deep(wp_unslash($_POST['listingId']), 'sanitize_text_field');
		$settings = isset($_POST['attributes']) ? map_deep(wp_unslash($_POST['attributes']), 'sanitize_text_field') : [];

		if (!empty($listingId)) {
			$listing =  rtcl()->factory->get_listing($listingId);
			$listings = self::rtcl_block_listings_query($settings, $listing);
		}

		if (!empty($listings["posts"])) {
			wp_send_json_success($listings);
		} else {
			wp_send_json_error("No listing found");
		}
	}
}
