<?php

namespace  RtclElb\DiviModule\ListingRelated;


use Rtcl\Models\Listing;
use RtclElb\Helpers\Fns;
use RtclStore\Helpers\Functions as StoreFunctions;

Class ListingRelatedHelper
{
	public $settings;
	public $listing;
	public function __construct($settings, $listing) {
		$this->settings = $settings;
		$this->listing = $listing;
	}

	/**
	 * Overwrite related listing data.
	 *
	 * @param [type] $data related listing data
	 *
	 * @return array
	 */
	public function related_listing_query_arg($data) {
		$settings                = $this->settings;
		$listings_filter = !empty($settings['rtcl_listings_filter']) ? $settings['rtcl_listings_filter'] : ['category'];
		$listings_filter = explode(',', $listings_filter);
		if (!in_array('category', $listings_filter)) {
			unset($data['tax_query']);
		}
		$related_post_per_page = 3;
		if (!empty($settings['rtcl_listings_per_page']) ) {
			$related_post_per_page = $settings['rtcl_listings_per_page'];
		}
		$data['posts_per_page'] = $related_post_per_page;
		if (in_array('author', $listings_filter)) {
			$store = false;
			$author_id = $this->listing->get_author_id();
			if (class_exists('RtclPro') && class_exists('RtclStore')) {
				$store = StoreFunctions::get_user_store($author_id);
				if ($store) {
					$author_id = $store->owner_id();
				}
			}
			$data['author__in'] = $author_id;
		}
		if (in_array('location', $listings_filter)) {
			$the_tax                       = wp_get_object_terms($this->listing->get_id(), rtcl()->location);
			$terms                         = !empty($the_tax) ? end($the_tax)->term_id : 0;
			$data['tax_query']['relation'] = 'AND';
			$data['tax_query'][]           = [
				[
					'taxonomy'         => rtcl()->location,
					'field'            => 'term_id',
					'terms'            => $terms,
				],
			];
		}

		if (in_array('listing_type', $listings_filter)) {
			$data['meta_key']   = 'ad_type';
			$data['meta_value'] = $this->listing->get_ad_type();
		}

		return $data;
	}

	/**
	 * Widget excerpt_limit.
	 *
	 * @param array $length default limit
	 *
	 * @return init
	 */
	public function excerpt_limit( $length ) {
		$settings = $this->settings;
		$length   = !empty($settings['rtcl_content_limit']) ? $settings['rtcl_content_limit'] : $length;

		return $length;
	}
	/**
	 * listable fields.
	 *
	 * @param [obj] $listing functionality.
	 * @return mixed
	 */
	public static function listable_fields_arg( $args ) {
		unset($args['meta_query']);
		return $args;
	}
	public function related_listings_data( $data, ) {
		$settings = $this->settings;
		$settings['rtcl_listings_view'] = 'grid';
		
		$template_style = 'divi/related-listing/related-listing';
		$listing = $this->listing;
		$data = array_merge(
			$data,
			[
				'template'              => $template_style,
				'instance'              => $settings,
				'view'                  => $settings['rtcl_listings_view'] ?? 'grid',
				'listing'               => $listing,
				'default_template_path' => Fns::get_plugin_template_path(),
			]
		);

		return $data;
	}
}