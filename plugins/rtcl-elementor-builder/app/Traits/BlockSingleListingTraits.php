<?php

namespace RtclElb\Traits;

use Rtcl\Helpers\Functions;
use RtclElb\Helpers\Fns;

trait BlockSingleListingTraits
{
	/**
	 * Block Listings.
	 *
	 * @var object
	 */
	protected  $listing;

	protected $attributes = [];

	/**
	 * Undocumented function
	 *
	 * @param array $data default data.
	 * @param array $args default arg.
	 */
	public function set_listing()
	{
		$this->listing  = rtcl()->factory->get_listing($this->listing_id());
	}
	/**
	 * Set style controlls
	 *
	 * @return int
	 */
	public function listing_id(): int
	{
		$_id = Fns::get_prepared_listing_id();
		return absint($_id);
	}

	public function get_listing($listingId)
	{
		$this->listing  = rtcl()->factory->get_listing($listingId);

		return [
			'listingId' => $listingId,
			'listing' => $this->listing
		];
	}

	public function business_hours_time_format($formate)
	{
		$settings = $this->attributes;
		if (isset($settings['dateFormate']) && '24' === $settings['dateFormate']) {
			$formate = 'H:i';
		}
		return $formate;
	}

	public function get_the_gallery($listing)
	{
		$data     = [
			'images' => [],
			'videos' => [],
		];
		$settings = $this->attributes;
		if (!Functions::is_gallery_disabled()) {
			$video_urls = [];
			if (!empty($listing) && isset($settings['showVideo']) && !Functions::is_video_urls_disabled() && !apply_filters('rtcl_disable_gallery_video', Functions::is_video_gallery_disabled())) {
				$video_urls = get_post_meta($listing->get_id(), '_rtcl_video_urls', true);
				$video_urls = !empty($video_urls) && is_array($video_urls) ? $video_urls : [];
			}
			$data['images'] = !empty($listing) ? $listing->get_images() : [];
			$data['videos'] = $video_urls;
		}
		return $data;
	}
	public function custom_field_group_ids($group_id)
	{
		$settings = $this->attributes;
		if (isset($settings['customFields']) && !empty($settings['customFields']) && is_array($settings['customFields'])) {
			$ids = array_filter($settings['customFields']);
			if (count($ids)) {
				$group_id = $ids;
			}
		}
		return $group_id;
	}
}
