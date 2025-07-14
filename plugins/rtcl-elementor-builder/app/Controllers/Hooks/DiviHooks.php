<?php


namespace RtclElb\Controllers\Hooks;
use Rtcl\Helpers\Functions;
use RtclElb\DiviModule\ArchiveListing\ArchiveListing;
use RtclElb\DiviModule\ListingActionSocialShare\ListingActionSocialShare;
use RtclElb\DiviModule\ListingAjaxFilter\ListingAjaxFilter;
use RtclElb\DiviModule\ListingAjaxFilterResult\ListingAjaxFilterResult;
use RtclElb\DiviModule\ListingBadge\ListingBadge;
use RtclElb\DiviModule\ListingBusinessHour\ListingBusinessHour;
use RtclElb\DiviModule\ListingCustomFields\ListingCustomFields;
use RtclElb\DiviModule\ListingDescription\ListingDescription;
use RtclElb\DiviModule\ListingImage\ListingImage;
use RtclElb\DiviModule\ListingMap\ListingMap;
use RtclElb\DiviModule\ListingMeta\ListingMeta;
use RtclElb\DiviModule\ListingPrice\ListingPrice;
use RtclElb\DiviModule\ListingRelated\ListingRelated;
use RtclElb\DiviModule\ListingReview\ListingReview;
use RtclElb\DiviModule\ListingSellerInformation\ListingSellerInformation;
use RtclElb\DiviModule\ListingSocialProfile\ListingSocialProfile;
use RtclElb\DiviModule\ListingTitle\ListingTitle;
use RtclElb\DiviModule\ListingVideo\ListingVideo;
use RtclElb\DiviModule\PageHeader\PageHeader;
use RtclElb\DiviModule\Store\StoreBanner\StoreBanner;
use RtclElb\DiviModule\Store\StoreContactInfo\StoreContactInfo;
use RtclElb\DiviModule\Store\StoreDescription\StoreDescription;
use RtclElb\DiviModule\Store\StoreListingSearch\StoreListingSearch;
use RtclElb\DiviModule\Store\StoreName\StoreName;
use RtclElb\DiviModule\Store\StoreOpeningHour\StoreOpeningHour;
use RtclElb\DiviModule\Store\StoreSlogan\StoreSlogan;
use RtclElb\DiviModule\Store\StoreListing\StoreListing;

class DiviHooks {

	/**
	 * @return void
	 */
	public static function init(): void {
		add_action( 'et_builder_ready', [ __CLASS__, 'load_modules' ], 9 );
		add_action('pre_get_posts', array(__CLASS__, 'divi_archive_listing'),9999999);

	}

	public static function load_modules() {
		if ( ! class_exists( \ET_Builder_Element::class ) ) {
			return;
		}
		new PageHeader();
		new ArchiveListing();
		new ListingTitle();
		new ListingDescription();
		new ListingBadge();
		new ListingVideo();
		new ListingMap();
		new ListingSocialProfile();
		new ListingPrice();
		new ListingActionSocialShare();
		new ListingBusinessHour();
		new ListingMeta();
		new ListingImage();
		new ListingReview();
		new ListingSellerInformation();
		new ListingRelated();
		new ListingCustomFields();
		new ListingAjaxFilter();
		new ListingAjaxFilterResult();
		if ( class_exists( 'RtclStore' ) ){
			new StoreName();
			new StoreDescription();
			new StoreSlogan();
			new StoreContactInfo();
			new StoreOpeningHour();
			new StoreBanner();
			new StoreListing();
			new StoreListingSearch();
		}
		
		
	}




	public static function divi_archive_listing($query) {
		if ( is_admin() ) {
			return;
		}

		if ( ! is_a( $query, 'WP_Query' ) || ( ! $query->is_main_query()  ) ) {
			return;
		}
		if(Functions::is_listings()){
			$listings_per_page = Functions::get_option_item('rtcl_general_settings', 'listings_per_page');

			$query->set( 'posts_per_page',$listings_per_page);
		}
	}

}