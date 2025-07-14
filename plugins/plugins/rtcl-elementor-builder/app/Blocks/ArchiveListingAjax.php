<?php

namespace RtclElb\Blocks;

use Rtcl\Helpers\Functions;
use Rtcl\Models\Listing;
use RtclPro\Helpers\Fns;
use Rtcl\Helpers\Pagination;
use Rtcl\Traits\Addons\TopQueryTrait;
use Rtcl\Widgets\Filter;
use RtclElb\Traits\Singleton;
use RtclPro\Controllers\Hooks\TemplateHooks;
use Rtcl\Controllers\Blocks\ListingsAjaxController;
use RtclElb\Helpers\Fns as RtclElbFns;

class ArchiveListingAjax extends Filter
{
	use Singleton;
	use TopQueryTrait;
	protected $store;
	public function __construct()
	{
		add_action('wp_ajax_rtcl_block_archive_listing_ajax', [$this, 'rtcl_block_archive_listing_ajax']);
		add_action('wp_ajax_rtcl_block_top_listing_ajax', [$this, 'rtcl_block_top_listing_ajax']);
		add_filter('excerpt_more', '__return_empty_string');
		add_action('wp_ajax_rtcl_block_listing_filter_ajax', [$this, 'rtcl_block_listing_filter_ajax']);
		add_action('wp_ajax_rtcl_store_listing_ajax', [$this, 'rtcl_store_listing_ajax']);
	}

	public function rtcl_store_listing_ajax()
	{
		$listings = [];
		if (!wp_verify_nonce($_POST['rtcl_block_nonce'], 'rtcl-block-nonce')) {
			wp_send_json_error(esc_html__('Session Expired!!', 'rtcl-elementor-builder'));
		}

		$settings = isset($_POST['attributes']) ? map_deep(wp_unslash($_POST['attributes']), 'sanitize_text_field') : [];
		$this->store = rtclStore()->factory->get_store(RtclElbFns::last_store_id());
		$args = ListingsAjaxController::rtcl_gb_listing_args($settings);
		if (!empty($this->store)) {
			$args['author'] = $this->store->owner_id();
			$loop_obj = new \WP_Query($args);
			$listings = self::rtcl_block_listings_query_results($loop_obj, $settings);
		}
		if (!empty($listings["posts"])) {
			wp_send_json_success($listings);
		} else {
			wp_send_json_error("No Store found");
		}
	}

	public function rtcl_block_listing_filter_ajax()
	{
		if (!wp_verify_nonce($_POST['rtcl_block_nonce'], 'rtcl-block-nonce')) {
			wp_send_json_error(esc_html__('Session Expired!!', 'rtcl-elementor-builder'));
		}

		$settings = isset($_POST['attributes']) ? map_deep(wp_unslash($_POST['attributes']), 'sanitize_text_field') : [];

		$this->instance = [
			'search_by_category' => 1,
			'search_by_location' => 1,
			'search_by_ad_type' => 1,
			'search_by_price' => 1,
			'radius_search' => 1,
			'show_icon_image_for_category' => 1
		];

		if (isset($settings['showCategory'])) {
			$this->instance['search_by_category'] = $settings['showCategory'] == 'true' ? 1 : 0;
		}
		if (isset($settings['showLocation'])) {
			$this->instance['search_by_location'] = $settings['showLocation'] == 'true' ? 1 : 0;
		}
		if (isset($settings['showAdType'])) {
			$this->instance['search_by_ad_type'] = $settings['showAdType'] == 'true' ? 1 : 0;
		}
		if (isset($settings['showPrice'])) {
			$this->instance['search_by_price'] = $settings['showPrice'] == 'true' ? 1 : 0;
		}
		if (isset($settings['showRadiusSearch'])) {
			$this->instance['radius_search'] = $settings['showRadiusSearch'] == 'true' ? 1 : 0;
		}
		if (isset($settings['hideEmptyCatLocation'])) {
			$this->instance['hide_empty'] = $settings['hideEmptyCatLocation'] == 'true' ? 1 : 0;
		}
		if (isset($settings['showCount'])) {
			$this->instance['show_count'] = $settings['showCount'] == 'true' ? 1 : 0;
		}
		if (isset($settings['showAjaxLoad'])) {
			$this->instance['ajax_load'] = $settings['showAjaxLoad'] == 'true' ? 1 : 0;
		}
		if (isset($settings['showCatLocationLink'])) {
			$this->instance['taxonomy_reset_link'] = $settings['showCatLocationLink'] == 'true' ? 1 : 0;
		}
		if (isset($settings['showCatImageIcon'])) {
			$this->instance['show_icon_image_for_category'] = $settings['showCatImageIcon'] == 'true' ? 1 : 0;
		}
		if (isset($settings['showRating'])) {
			$this->instance['search_by_rating'] = $settings['showRating'] == 'true' ? 1 : 0;
		}
		if (isset($settings['showCustomField'])) {
			$this->instance['search_by_custom_fields'] = $settings['showCustomField'] == 'true' ? 1 : 0;
		}

		$object = $this;
		$filterForm = self::listing_filter_form($object, $settings);

		if (!empty($filterForm)) {
			wp_send_json_success($filterForm);
		} else {
			wp_send_json_error("no post found");
		}
	}

	public function rtcl_block_archive_listing_ajax()
	{
		if (!wp_verify_nonce($_POST['rtcl_block_nonce'], 'rtcl-block-nonce')) {
			wp_send_json_error(esc_html__('Session Expired!!', 'rtcl-elementor-builder'));
		}

		$settings = isset($_POST['attributes']) ? map_deep(wp_unslash($_POST['attributes']), 'sanitize_text_field') : [];
		$top_listing = self::top_listing_query_prepared();
		$the_args          = array(
			'post_type'      => rtcl()->post_type,
			'posts_per_page' => self::posts_per_page(),
			'post__not_in' => $top_listing['top_items'] ?? [],
			'post_status'    => 'publish',
		);
		$the_args['paged'] = Pagination::get_page_number();
		$loop_obj = new \WP_Query($the_args);

		$listings = self::rtcl_block_listings_query_results($loop_obj, $settings);

		if (!empty($listings["posts"])) {
			wp_send_json_success($listings);
		} else {
			wp_send_json_error("no post found");
		}
	}

	public function  rtcl_block_top_listing_ajax()
	{
		if (!wp_verify_nonce($_POST['rtcl_block_nonce'], 'rtcl-block-nonce')) {
			wp_send_json_error(esc_html__('Session Expired!!', 'rtcl-elementor-builder'));
		}

		$settings = isset($_POST['attributes']) ? map_deep(wp_unslash($_POST['attributes']), 'sanitize_text_field') : [];
		$top_listing = self::top_listing_query_prepared()['top_query'] ?? [];
		$listings = self::rtcl_block_listings_query_results($top_listing, $settings);

		if (!empty($listings["posts"])) {
			wp_send_json_success($listings);
		} else {
			wp_send_json_error("no post found");
		}
	}

	public function rtcl_block_listings_query_results($loop_obj, $settings)
	{
		$results = [];
		if (empty($loop_obj)) {
			return [];
		}
		while ($loop_obj->have_posts()) :
			$loop_obj->the_post();
			$_id          = get_the_ID();
			$listing      = new Listing($_id);
			$liting_class = Functions::get_listing_class(['rtcl-widget-listing-item', 'listing-item'], $_id);
			$phone        = get_post_meta($_id, 'phone', true);
			$compare      = $quick_view = $sold_item = $custom_feilds = '';

			if (rtcl()->has_pro()) {
				ob_start();
				TemplateHooks::loop_item_listable_fields();
				$custom_feilds = ob_get_clean();
			}

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
				"custom_feilds"  => $custom_feilds

			];

		endwhile; ?>
		<?php wp_reset_postdata(); ?>

	<?php return [
			"total_post" => $loop_obj->found_posts,
			"total_page" => $loop_obj->max_num_pages,
			"posts"      => $results,
			"query_obj"  => $loop_obj
		];
	}

	public function posts_per_page()
	{
		$listings_per_page = Functions::get_option_item('rtcl_general_settings', 'listings_per_page');
		return apply_filters('rtcl_loop_listing_per_page', $listings_per_page);
	}

	public function listing_filter_form($object, $data)
	{
		ob_start();
	?>
		<div id="rtcl-widget-filter" class="widget rtcl rtcl-widget-filter-class">
			<?php if ($data['showFilterTitle'] == 'true' && !empty($data['filterTitle'])) : ?>
				<h3><?php echo esc_html($data['filterTitle']); ?></h3>
			<?php endif ?>

			<div class="panel-block">
				<?php do_action('rtcl_widget_before_filter_form', $object, $data) ?>
				<form class="rtcl-filter-form" action="<?php echo esc_url(Functions::get_filter_form_url()) ?>">
					<?php do_action('rtcl_widget_filter_form_start', $object, $data) ?>
					<div class="ui-accordion">
						<?php do_action('rtcl_widget_filter_form', $object, $data); ?>
					</div>
					<?php do_action('rtcl_widget_filter_form_end', $object, $data) ?>
				</form>
				<?php do_action('rtcl_widget_after_filter_form', $object, $data) ?>
			</div>

		</div>
<?php
		return ob_get_clean();
	}
}
