<?php

/**
 * Main Block BlockController Class.
 *
 * The main class that initiates and runs the plugin.
 *
 * @since  1.1.3
 */

namespace RtclElb\Controllers;

use RtclElb\Traits\Singleton;

use RtclElb\Blocks\ArchiveListingAjax;
use RtclElb\Blocks\RelatedListingAjax;
use RtclElb\Blocks\BusinessHours;
use RtclElb\Blocks\CustomFields;
use RtclElb\Blocks\ListingActions;
use RtclElb\Traits\ELTempleateBuilderTraits;
use RtclElb\Blocks\ListingTitle;
use RtclElb\Blocks\ListingDescription;
use RtclElb\Blocks\ListingPrice;
use RtclElb\Blocks\SingleListingAjax;
use RtclElb\Blocks\ListingPageHeader;
use RtclElb\Blocks\ListingBadge;
use RtclElb\Blocks\ListingMeta;
use RtclElb\Blocks\ListingFeatures;
use RtclElb\Blocks\ListingFilter;
use RtclElb\Blocks\ListingMap;
use RtclElb\Blocks\SocialProfiles;
use RtclElb\Blocks\SellerInformation;
use RtclElb\Blocks\ListingVideo;
use RtclElb\Blocks\ListingImage;
use RtclElb\Blocks\ListingReview;
use RtclElb\Blocks\RelatedListing;
use RtclElb\Blocks\ListingArchive;
use RtclElb\Blocks\StoreAjax;
use RtclElb\Blocks\StoreContactInfo;
use RtclElb\Blocks\Testing;
use RtclElb\Blocks\StoreDescription;
use RtclElb\Blocks\StoreListing;
use RtclElb\Blocks\StoreName;
use RtclElb\Blocks\StoreOpening;
use RtclElb\Blocks\StoreSlogan;
use RtclElb\Blocks\ListingBooking;
use RtclElb\Blocks\StoreBanner;

/**
 * Main Block BlockController Class.
 *
 * The main class that initiates and runs the plugin.
 *
 * @since 1.1.3
 */
class BlockController
{
	use Singleton;
	/*
	 * Template builder related traits.
	 */
	use ELTempleateBuilderTraits;

	public function __construct()
	{
		add_filter('rtcl_block_category_lists', [$this, 'add_new_categories'], 9, 1);
		//add_action('init', [$this, 'register_block_patterns']);
		Testing::getInstance();
		ArchiveListingAjax::getInstance();
		SingleListingAjax::getInstance();
		RelatedListingAjax::getInstance();
		StoreAjax::getInstance();
		ListingTitle::getInstance();
		ListingDescription::getInstance();
		ListingPrice::getInstance();
		ListingPageHeader::getInstance();
		ListingBadge::getInstance();
		ListingMeta::getInstance();
		ListingFeatures::getInstance();
		SocialProfiles::getInstance();
		BusinessHours::getInstance();
		SellerInformation::getInstance();
		ListingVideo::getInstance();
		ListingMap::getInstance();
		ListingActions::getInstance();
		ListingImage::getInstance();
		ListingReview::getInstance();
		CustomFields::getInstance();
		RelatedListing::getInstance();
		ListingFilter::getInstance();
		ListingArchive::getInstance();

		if (defined('RTCL_STORE_VERSION') && rtcl()->has_pro()) {
			StoreName::getInstance();
			StoreDescription::getInstance();
			StoreSlogan::getInstance();
			StoreContactInfo::getInstance();
			StoreOpening::getInstance();
			StoreListing::getInstance();
			StoreBanner::getInstance();
		}

		if (defined('RTCL_BOOKING_VERSION')) {
			ListingBooking::getInstance();
		}
	}

	public function add_new_categories($block_categories)
	{
		if (self::is_builder_page_single()) {
			$block_categories[] = [
				'title' => __('Single Listing', 'rtcl-elementor-builder'),
				'slug'  => 'rtcl-single-block',
			];
		}

		if (self::is_builder_page_archive()) {
			$block_categories[] = [
				'title' => __('Archive Listing', 'rtcl-elementor-builder'),
				'slug'  => 'rtcl-archive-block',
			];
		}

		if (self::is_store_page_builder()) {
			$block_categories[] = [
				'title' => __('Store Single', 'rtcl-elementor-builder'),
				'slug'  => 'rtcl-store-single-block',
			];
		}

		return $block_categories;
	}

	public function register_block_patterns()
	{
		$block_pattern_categories = array(
			'rtcl-elementor-builder-basic' => array(
				'label' => __('CL Block Pattern', 'rtcl-elementor-builder')
			),
		);

		foreach ($block_pattern_categories as $name => $properties) {
			if (!\WP_Block_Pattern_Categories_Registry::get_instance()->is_registered($name)) {
				register_block_pattern_category($name, $properties);
			}
		}

		if (function_exists('register_block_pattern')) {
			register_block_pattern(
				'rtcl/test-patern',
				array(
					'title'       => __('CL Block Pattern', 'rtcl-elementor-builder'),
					'description' => __('Description of CL block pattern.', 'rtcl-elementor-builder'),
					'content'     => '
					<!-- wp:paragraph {"backgroundColor":"pale-pink"} -->
					<p class="has-pale-pink-background-color has-background">Demo test</p>
					<!-- /wp:paragraph -->
					',
					'categories'  => array('rtcl-elementor-builder-basic'),
				)
			);
		}
	}
}
