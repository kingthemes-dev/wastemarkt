<?php

namespace RtclElb\Blocks;

use Rtcl\Helpers\Functions;
use RtclElb\Traits\ELTempleateBuilderTraits;
use Rtcl\Models\Listing;
use Rtcl\Controllers\BusinessHoursController;
use Rtcl\Controllers\SocialProfilesController;
use RtclElb\Traits\BlockSingleListingTraits;
use RtclElb\Controllers\Hooks\ActionHooks;
use Rtcl\Controllers\Hooks\TemplateHooks;
use RtclPro\Controllers\Hooks\TemplateHooks as TemplateHooksPro;
use RtclPro\Helpers\Fns;
use Rtcl\Helpers\Link;
use RtclElb\Traits\Singleton;

class SingleListingAjax
{
	use Singleton;
	use ELTempleateBuilderTraits;
	use BlockSingleListingTraits;

	public function __construct()
	{
		add_action('wp_ajax_rtcl_get_listing_by_id', [$this, 'get_single_listing_by_id']);
		add_action('wp_ajax_rtcl_get_page_heding', [$this, 'get_page_heading']);
		add_action('wp_ajax_rtcl_custom_fields_list', [$this, 'custom_field_group_list']);
	}

	public function custom_field_group_list()
	{
		if (!wp_verify_nonce($_POST['rtcl_block_nonce'], 'rtcl-block-nonce')) {
			wp_send_json_error(esc_html__('Session Expired!!', 'rtcl-elementor-builder'));
		}

		$group_ids = Functions::get_cfg_ids();

		$list = [
			'0' => esc_html__('All Group', 'rtcl-elementor-builder'),
		];
		foreach ($group_ids as $id) {
			$list[$id] = get_the_title($id);
		}

		if (!empty($list)) {
			wp_send_json_success($list);
		} else {
			wp_send_json_error("no post found");
		}
	}

	public function get_page_heading()
	{
		if (!wp_verify_nonce($_POST['rtcl_block_nonce'], 'rtcl-block-nonce')) {
			wp_send_json_error(esc_html__('Session Expired!!', 'rtcl-elementor-builder'));
		}

		$builderPageId = map_deep(wp_unslash($_POST['builderPageId']), 'sanitize_text_field');
		$lastPostId = map_deep(wp_unslash($_POST['lastPostId']), 'sanitize_text_field');

		$title = '';
		if (self::builder_type($builderPageId) == 'archive') {
			//$title = get_the_title($builderPageId);
			$title = Functions::page_title(false);
		}

		if (self::builder_type($builderPageId) == 'single' && $lastPostId) {
			$listing = new Listing($lastPostId);
			$title = $listing->get_the_title();
		}

		$homeUrl = site_url();
		$breadcrumb = <<<EOT
		<nav class="rtcl-breadcrumb"><a href="$homeUrl">Home</a> / $title</nav>
		EOT;

		$results = [
			'title' =>	$title,
			'breadcrumb' => $breadcrumb
		];

		if (!empty($results)) {
			wp_send_json_success($results);
		} else {
			wp_send_json_error("no post found");
		}
	}

	public function get_single_listing_by_id()
	{
		if (!wp_verify_nonce($_POST['rtcl_block_nonce'], 'rtcl-block-nonce')) {
			wp_send_json_error(esc_html__('Session Expired!!', 'rtcl-elementor-builder'));
		}

		$results = '';
		$this->attributes = isset($_POST['attributes']) ? map_deep(wp_unslash($_POST['attributes']), 'sanitize_text_field') : [];
		$listingId = map_deep(wp_unslash($_POST['listingId']), 'sanitize_text_field');

		if (!empty($listingId)) {
			add_filter('rtcl_time_format', [$this, 'business_hours_time_format']);
			add_filter('rtcl_business_hours_display_options', [$this, 'business_hours_backend_options']);
			add_filter('rtcl_listing_is_social_share_for_single', function () {
				return true;
			}, 20);
			add_filter('rtcl_listing_get_custom_field_group_ids', [$this, 'custom_field_group_ids'], 10, 1);
			$results = $this->get_single_listing($listingId);
		}

		if (!empty($results)) {
			wp_send_json_success($results);
		} else {
			wp_send_json_error("No listing found");
		}
	}

	public function get_single_listing($listingId)
	{
		$listing =  rtcl()->factory->get_listing($listingId);

		//only for feature lists
		$theme_name = wp_get_theme()->get('Name');
		$feature_items = [];

		if (!empty($listing) && $theme_name == 'Classima') {
			$spec_info = get_post_meta($listingId, 'classima_spec_info', true);
			$spec = isset($spec_info['specs']) ? $spec_info['specs'] : '';
			if ($spec) {
				$feature_items = explode(PHP_EOL, $spec);
			}
		} else {
			$feature_items = 'Listing features block for classima theme';
		}

		$singleListing = [
			'title' => $listing->get_the_title(),
			'description' => get_the_content(null, false, $listing->get_id()),
			'price' => $listing->get_price_html(),
			'badge' => $listing->badges(),
			'locations' => $listing->the_locations(false),
			'view_counts' => $listing->get_view_counts(),
			'category' => $listing->the_categories(false),
			'time' => $listing->get_the_time(),
			'ad_type' => $listing->get_ad_type(),
			'author_name' => $listing->get_author_name(),
			'feature_lists' => $feature_items,
			'social_profiles' => $this->get_listing_social_profiles($listing),
			'business_hours' => $this->get_listing_business_hours($listing),
			'seller_info' => $this->get_seller_info($listing),
			'video_url' => $this->get_video_url($listing),
			'video_thumb' => !empty($this->get_video_url($listing)) ? Functions::get_embed_video_thumbnail_url($this->get_video_url($listing)[0]) : '',
			'map' => $this->get_map($listing),
			'actions' => $this->get_action_social_share($listing),
			'images' => $this->get_the_gallery($listing)['images'],
			'is_sold' => $this->is_sold_item($listing),
			'review'  => $this->get_review_html($listing),
			'customField' => $this->get_custom_fields_html($listing)
		];

		return $singleListing;
	}

	public function get_listing_social_profiles($listing)
	{
		ob_start();
		SocialProfilesController::display_social_profiles($listing);
		return ob_get_clean();
	}

	public function get_listing_business_hours($listing)
	{
		ob_start();
		BusinessHoursController::display_business_hours($listing);
		return ob_get_clean();
	}

	public function business_hours_backend_options($options)
	{
		$settings = $this->attributes;
		if (isset($settings['openStatusText']) && !empty($settings['openStatusText'])) {
			$options['open_status_text'] = $settings['openStatusText'];
		}
		if (isset($settings['closeStatusText']) && !empty($settings['closeStatusText'])) {
			$options['close_status_text'] = $settings['closeStatusText'];
		}
		if (isset($settings['showOpenStatus']) && $settings['showOpenStatus'] == 'true') {
			$options['show_open_status'] = boolval(1);
		}
		if (isset($settings['showOpenStatus']) && $settings['showOpenStatus'] == 'false') {
			$options['show_open_status'] = boolval(0);
		}
		return $options;
	}

	public function get_seller_info($listing)
	{
		ob_start();
		ActionHooks::show_author($listing);
		TemplateHooks::seller_location($listing);
		TemplateHooks::seller_phone_whatsapp_number($listing);
		TemplateHooks::seller_email($listing);
		TemplateHooks::seller_website($listing);
		if (rtcl()->has_pro()) {
			TemplateHooksPro::add_chat_link($listing);
			TemplateHooksPro::add_user_online_status($listing);
		}
		return ob_get_clean();
	}

	public function get_video_url($listing)
	{
		$video_urls = [];
		if (!Functions::is_video_urls_disabled() && !empty($listing)) {
			$video_urls = get_post_meta($listing->get_id(), '_rtcl_video_urls', true);
			$video_urls = !empty($video_urls) && is_array($video_urls) ? $video_urls : [];
		}
		return $video_urls;
	}
	public function get_map($listing)
	{
		ob_start();
		TemplateHooks::single_listing_map_content($listing);
		return ob_get_clean();
	}
	public function get_action_social_share($listing)
	{
		ob_start();
		if (!empty($listing)) {
			$listing->the_actions();
		}
		return ob_get_clean();
	}
	public function is_sold_item($listing)
	{
		if (rtcl()->has_pro() && Fns::is_enable_mark_as_sold() && Fns::is_mark_as_sold($listing->get_id())) {
			return true;
		}
		return false;
	}

	public function get_review_html($listing)
	{
		ob_start();
		$settings = $this->attributes;
		$total_comments =  get_option('comments_per_page');
		$comments  = get_comments(
			[
				'post_id' => $listing->get_id(),
				'number'  => $total_comments,
				'status'  => 'approve',
			]
		);
?>
		<?php if (isset($settings['showCommentList']) && $settings['showCommentList'] == 'true') { ?>
			<div id="comments">
				<?php $has_header_content = false;
				if (
					!empty($settings['showReviewSectionTitle']) ||
					!empty($settings['showReviewMeta']) ||
					!empty($settings['showLeaveBtn'])
				) {
					$has_header_content = true;
				}
				?>
				<?php if ($has_header_content) { ?>
					<div class="rtcl-reviews-meta">
						<?php if (!empty($settings['showReviewSectionTitle']) && $settings['showReviewSectionTitle'] == 'true') { ?>
							<h4 class="rtcl-single-listing-section-title">
								<?php echo esc_html($settings['reviewTitleText']); ?>
							</h4>
						<?php } ?>
						<?php
						if (count($comments) && !empty($settings['showReviewMeta']) && $settings['showReviewMeta'] == 'true') {
							$average      = $listing->get_average_rating();
							$rating_count = $listing->get_rating_count(); ?>
							<!-- Single Listing Review / Meta -->
							<div class="listing-meta">
								<!-- Listing / Rating -->
								<div class="listing-meta-rating"><?php echo esc_html($average); ?></div>
								<div class="reviews-rating">
									<?php
									echo Functions::get_rating_html($average, $rating_count); ?>
									<span class="reviews-rating-count">(<?php echo absint($rating_count); ?>)</span>
								</div>
							</div>
						<?php
						} ?>
						<?php if (!empty($settings['showLeaveBtn']) && $settings['showLeaveBtn'] == 'true') { ?>
							<div class="rtcl-reviews-meta-action">
								<a class="rtcl-animate" href="#respond"><?php echo esc_html($settings['leaveBtnText']); ?><i class="rtcl-icon-level-down"></i></a>
							</div>
						<?php } ?>
					</div>
				<?php } ?>

				<?php if (count($comments) && rtcl()->has_pro()) { ?>

					<ol class="comment-list">
						<?php
						wp_list_comments(
							apply_filters(
								'rtcl_listing_review_list_args',
								[
									'callback' => [
										Fns::class,
										'comments',
									],
								]
							),
							$comments
						);
						?>
					</ol>

					<?php
					if (get_comment_pages_count() > 1 && get_option('page_comments')) {
						echo '<nav class="rtcl-pagination">';
						paginate_comments_links(
							apply_filters(
								'rtcl_comment_pagination_args',
								[
									'prev_text' => '&larr;',
									'next_text' => '&rarr;',
									'type'      => 'list',
								]
							)
						);
						echo '</nav>';
					}
					?>

				<?php } else { ?>
					<p class="rtcl-noreviews"><?php esc_html_e('There are no reviews yet.', 'rtcl-elementor-builder'); ?></p>
				<?php } ?>

			</div>
		<?php } ?>
		<?php if (isset($settings['showContactForm']) && $settings['showContactForm'] == 'true') { ?>
			<div id="review-form-wrapper">
				<div id="review-form">
					<?php
					$comment_form_title = isset($settings['contactFormTitleText']) ? $settings['contactFormTitleText'] : '';
					$commenter = wp_get_current_commenter();

					$comment_form     = [
						// translators: %s: Listing Litle
						'title_reply'         => $comments ? $comment_form_title : sprintf(__('Be the first to review &ldquo;%s&rdquo;', 'rtcl-elementor-builder'), get_the_title()),
						// translators: %s: Autor Name
						'title_reply_to'      => __('Leave a Reply to %s', 'rtcl-elementor-builder'),
						'title_reply_before'  => '<h4 id="reply-title" class="comment-reply-title">',
						'title_reply_after'   => '</h4>',
						'comment_notes_after' => '',
						'fields'              => [
							'author' => '<div class="comment-form-author form-group"><label for="author">' . esc_html__('Name', 'rtcl-elementor-builder') . '&nbsp;<span class="required">*</span></label> ' .
								'<input id="author" class="rtcl-form-control" name="author" type="text" value="' . esc_attr($commenter['comment_author']) . '" size="30" aria-required="true" required /></div>',
							'email'  => '<div class="comment-form-email form-group"><label for="email">' . esc_html__('Email', 'rtcl-elementor-builder') . '&nbsp;<span class="required">*</span></label> ' .
								'<input id="email" name="email" class="rtcl-form-control" type="email" value="' . esc_attr($commenter['comment_author_email']) . '" size="30" aria-required="true" required /></div>',
						],
						'label_submit'        => esc_html__('Submit', 'rtcl-elementor-builder'),
						'class_submit'        => 'rtcl-btn btn-primary',
						'logged_in_as'        => '',
						'comment_field'       => '',
					];
					$account_page_url = Link::get_my_account_page_link();
					if ($account_page_url) {
						// translators: %s: Account page url
						$comment_form['must_log_in'] = '<p class="must-log-in">' . sprintf(__('You must be <a href="%s">logged in</a> to post a review.', 'rtcl-elementor-builder'), esc_url($account_page_url)) . '</p>';
					}

					$comment_form['comment_field'] = '<div class="comment-form-title  rtcl-form-group">
						<label for="title">' . esc_html__('Review title', 'rtcl-elementor-builder') . '&nbsp;<span class="required">*</span></label>
						<input type="text" class="rtcl-form-control" name="title" id="title"  aria-required="true" required/>
						</div>';

					// if (Functions::get_option_item('rtcl_moderation_settings', 'enable_review_rating', false, 'checkbox')) {
					// 	$comment_form['comment_field'] .= '<div class="comment-form-rating  form-group"><label for="rating">' . esc_html__('Your rating', 'rtcl-elementor-builder') . '<span class="required">*</span></label><select name="rating" id="rating" class="form-control" aria-required="true" required>
					// 				<option value="">' . esc_html__('Rate&hellip;', 'rtcl-elementor-builder') . '</option>
					// 				<option value="5">' . esc_html__('Perfect', 'rtcl-elementor-builder') . '</option>
					// 				<option value="4">' . esc_html__('Good', 'rtcl-elementor-builder') . '</option>
					// 				<option value="3">' . esc_html__('Average', 'rtcl-elementor-builder') . '</option>
					// 				<option value="2">' . esc_html__('Not that bad', 'rtcl-elementor-builder') . '</option>
					// 				<option value="1">' . esc_html__('Very poor', 'rtcl-elementor-builder') . '</option>
					// 			</select></div>';
					// }

					$comment_form['comment_field'] .= '<div class="comment-form-comment  rtcl-form-group"><label for="comment">' . esc_html__('Your review', 'rtcl-elementor-builder') . '&nbsp;<span class="required">*</span></label><textarea id="comment" class="rtcl-form-control" name="comment" cols="45" rows="8" aria-required="true" required></textarea></div>';

					comment_form(apply_filters('rtcl_listing_review_comment_form_args', $comment_form), $listing->get_id());
					?>
				</div>
			</div>
		<?php } ?>
<?php
		return ob_get_clean();
	}

	public function get_custom_fields_html($listing)
	{
		ob_start();
		$listing->the_custom_fields();
		return ob_get_clean();
	}
}
