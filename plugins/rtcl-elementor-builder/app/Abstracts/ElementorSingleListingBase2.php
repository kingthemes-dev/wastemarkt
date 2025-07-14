<?php

/**
 * Main Elementor ElementorSingleListingBase2 Class
 *
 * ElementorSingleListingBase2 main class
 *
 * @author  RadiusTheme
 * @since   2.0.10
 * @package  RTCL_Elementor_Builder
 * @version 1.2
 */

namespace RtclElb\Abstracts;

use RadisuTheme\ClassifiedListingToolkits\Abstracts\ElementorWidgetBaseV2;
use Rtcl\Traits\Addons\ListingItem;

if (!defined('ABSPATH')) {
	exit; // Exit if accessed directly.
}

/**
 * ElementorSingleListingBase2 class
 */
abstract class ElementorSingleListingBase2 extends ElementorWidgetBaseV2
{
	/**
	 * Prepared listing item
	 */
	use ListingItem;
	/**
	 * Widget Listings.
	 *
	 * @var object
	 */
	protected $listing;
	/**
	 * Undocumented function
	 *
	 * @param array $data default data.
	 * @param array $args default arg.
	 */
	public function __construct($data = [], $args = null)
	{
		parent::__construct($data, $args);
		$this->listing       = rtcl()->factory->get_listing($this->listing_id());
		$this->rtcl_category = 'rtcl-elementor-single-widgets'; // Category /@dev.
	}
	/**
	 * Set style controlls
	 *
	 * @return int
	 */
	public function listing_id(): int
	{
		$_id = self::get_prepared_listing_id();
		return absint($_id);
	}
}
