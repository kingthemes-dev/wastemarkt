<?php

namespace RtclElb\Blocks;

use RtclElb\Abstracts\BlockBase;
use RtclElb\Traits\BlockSingleListingTraits;
use RtclElb\Traits\Singleton;
use RtclStore\Helpers\Functions as StoreFunctions;

class RelatedListing extends BlockBase
{
	use Singleton;
	use BlockSingleListingTraits;

	public function __construct()
	{
		parent::__construct();
		add_action('template_redirect', [$this, 'set_listing']);
	}

	public function block_attributes()
	{
		$attributes = [
			'style' => array(
				'type'    => 'string',
				'default' => '1'
			),
			'layout' => array(
				'type'    => 'string',
				'default' => 'grid'
			),
			'listingPerPage' => array(
				'type'    => 'number',
				'default' => 10
			),
			'listingFilter' => array(
				'type'    => 'array',
				'default' => [
					['value' => 'category', 'label' => 'Same Category']
				]
			),
			'listingImageSize' => array(
				'type'    => 'string',
				'default' => 'rtcl-thumbnail'
			),
			'content_limit' => array(
				'type'    => 'number',
				'default' => 20
			),
			'sliderOptions' => array(
				"type" => "object",
				"default" => array(
					"autoHeight" => false,
					"loop" => true,
					"autoPlay" => true,
					"stopOnHover" => true,
					"autoPlayDelay" => 2000,
					"autoPlaySlideSpeed" => 2000,
					"spaceBetween" => 20,
					"arrowNavigation" => true,
					"arrowPosition" => "1",
					"arrowStyle" => "1",
					"dotNavigation" => true,
					"dotStyle" => "1",
					"sliderLoader" => true
				),
			),
			'slidesItem'  => [
				'type'    => "object",
				'default' =>  [
					'lg' => 3,
					'md' => 2,
					'sm' => 1
				]
			],

			"contentVisibility" => array(
				"type" => "object",
				"default" => array(
					"badge" => true,
					"location" => true,
					"category" => true,
					"date" => true,
					"price" => true,
					"author" => true,
					"view" => true,
					"content" => true,
					"grid_content" => false,
					"title" => true,
					"thumbnail" => true,
					"listing_type" => true,
					"thumb_position" => "",
					"details_btn" => true,
					"favourit_btn" => true,
					"phone_btn" => true,
					"compare_btn" => true,
					"quick_btn" => true,
					"sold" => true,
					'custom_feild' => false,
					"actionLayout" => "horizontal-layout",
				),
			),
			//style
			'colBgColor' => array(
				'type'    => 'string',
				'default' => ''
			),

			"colPadding" => array(
				"type"    => "object",
				"default" => array(
					'lg' => [
						"isLinked" => true,
						"unit"     => "px",
						"value"    => ''
					]
				)
			),
			"contentPadding" => array(
				"type"    => "object",
				"default" => array(
					'lg' => [
						"isLinked" => true,
						"unit"     => "px",
						"value"    => ''
					]
				)
			),

			"colBorder" => array(
				"type"    => "object",
				"default" => array(
					'borderStyle' => 'solid',
					'borderColor' => '#0000001a',
					'lg' => [
						"isLinked" => true,
						"unit"     => "px",
						"value"    => '1 1 1 1'
					]
				)
			),
			'colBoxShadowType' => array(
				'type'    => 'string',
				'default' => 'normal'
			),

			'colBoxShadow' => [
				'type' => 'object',
				'default' => [
					'width' => ['top' => 1, 'right' => 1, 'bottom' => 1, 'left' => 1],
					'color' => ''
				]
			],
			'colHoverBoxShadow' => [
				'type' => 'object',
				'default' => [
					'width' => ['top' => 1, 'right' => 1, 'bottom' => 1, 'left' => 1],
					'color' => ''
				]
			],

			'titleTypo' => [
				'type' => 'object',
				'default' => [
					'size' => ['lg' => '', 'unit' => 'px'],
					'spacing' => ['lg' => '', 'unit' => 'px'],
					'height' => ['lg' => '', 'unit' => 'px'],
					'transform' => '',
					'weight' => ''
				]
			],

			'titleColorStyle' => array(
				'type'    => 'string',
				'default' => 'normal'
			),
			'titleColor' => array(
				'type'    => 'string',
				'default' => ''
			),
			'titleHoverColor' => array(
				'type'    => 'string',
				'default' => ''
			),
			"titleMargin" => array(
				"type"    => "object",
				"default" => array(
					'lg' => [
						"isLinked" => true,
						"unit"     => "px",
						"value"    => ''
					]
				)
			),

			'descTypo' => [
				'type' => 'object',
				'default' => [
					'size' => ['lg' => '', 'unit' => 'px'],
					'spacing' => ['lg' => '', 'unit' => 'px'],
					'height' => ['lg' => '', 'unit' => 'px'],
					'transform' => '',
					'weight' => ''
				]
			],
			'descColor' => array(
				'type'    => 'string',
				'default' => ''
			),
			"descMargin" => array(
				"type"    => "object",
				"default" => array(
					'lg' => [
						"isLinked" => true,
						"unit"     => "px",
						"value"    => ''
					]
				)
			),

			'metaTypo' => [
				'type' => 'object',
				'default' => [
					'size' => ['lg' => '', 'unit' => 'px'],
					'spacing' => ['lg' => '', 'unit' => 'px'],
					'height' => ['lg' => '', 'unit' => 'px'],
					'transform' => '',
					'weight' => ''
				]
			],
			"metaPadding" => array(
				"type"    => "object",
				"default" => array(
					'lg' => [
						"isLinked" => true,
						"unit"     => "px",
						"value"    => ''
					]
				)
			),
			'metaColorStyle' => array(
				'type'    => 'string',
				'default' => 'normal'
			),
			'metaColor' => array(
				'type'    => 'string',
				'default' => ''
			),
			'metaIconColor' => array(
				'type'    => 'string',
				'default' => ''
			),
			'metaCatColor' => array(
				'type'    => 'string',
				'default' => ''
			),
			'metaHoverColor' => array(
				'type'    => 'string',
				'default' => ''
			),
			'metaIconHoverColor' => array(
				'type'    => 'string',
				'default' => ''
			),
			'metaCatHoverColor' => array(
				'type'    => 'string',
				'default' => ''
			),
			'badgeTypo' => [
				'type' => 'object',
				'default' => [
					'size' => ['lg' => '', 'unit' => 'px'],
					'spacing' => ['lg' => '', 'unit' => 'px'],
					'height' => ['lg' => '', 'unit' => 'px'],
					'transform' => '',
					'weight' => ''
				]
			],
			"badgePadding" => array(
				"type"    => "object",
				"default" => array(
					'lg' => [
						"isLinked" => true,
						"unit"     => "px",
						"value"    => ''
					]
				)
			),
			"badgeMargin" => array(
				"type"    => "object",
				"default" => array(
					'lg' => [
						"isLinked" => true,
						"unit"     => "px",
						"value"    => ''
					]
				)
			),
			'soldBGColor' => array(
				'type'    => 'string',
				'default' => ''
			),
			'soldColor' => array(
				'type'    => 'string',
				'default' => ''
			),

			'newBGColor' => array(
				'type'    => 'string',
				'default' => ''
			),
			'newColor' => array(
				'type'    => 'string',
				'default' => ''
			),

			'featuredBGColor' => array(
				'type'    => 'string',
				'default' => ''
			),
			'featuredColor' => array(
				'type'    => 'string',
				'default' => ''
			),
			'topBGColor' => array(
				'type'    => 'string',
				'default' => ''
			),
			'topColor' => array(
				'type'    => 'string',
				'default' => ''
			),

			'popularBGColor' => array(
				'type'    => 'string',
				'default' => ''
			),
			'popularColor' => array(
				'type'    => 'string',
				'default' => ''
			),
			'bumpBGColor' => array(
				'type'    => 'string',
				'default' => ''
			),
			'bumpColor' => array(
				'type'    => 'string',
				'default' => ''
			),

			'priceTypo' => [
				'type' => 'object',
				'default' => [
					'size' => ['lg' => '', 'unit' => 'px'],
					'spacing' => ['lg' => '', 'unit' => 'px'],
					'height' => ['lg' => '', 'unit' => 'px'],
					'transform' => '',
					'weight' => ''
				]
			],
			'priceBGColor' => array(
				'type'    => 'string',
				'default' => ''
			),
			'priceColor' => array(
				'type'    => 'string',
				'default' => ''
			),
			'unitTypo' => [
				'type' => 'object',
				'default' => [
					'size' => ['lg' => '', 'unit' => 'px'],
					'spacing' => ['lg' => '', 'unit' => 'px'],
					'height' => ['lg' => '', 'unit' => 'px'],
					'transform' => '',
					'weight' => ''
				]
			],
			'unitLabelColor' => array(
				'type'    => 'string',
				'default' => ''
			),
			"priceMargin" => array(
				"type"    => "object",
				"default" => array(
					'lg' => [
						"isLinked" => true,
						"unit"     => "px",
						"value"    => ''
					]
				)
			),
			'btnColorStyle' => array(
				'type'    => 'string',
				'default' => 'normal'
			),
			'btnBGColor' => array(
				'type'    => 'string',
				'default' => ''
			),
			'btnColor' => array(
				'type'    => 'string',
				'default' => ''
			),
			'btnBorderColor' => array(
				'type'    => 'string',
				'default' => ''
			),
			'btnBGHoverColor' => array(
				'type'    => 'string',
				'default' => ''
			),
			'btnHoverColor' => array(
				'type'    => 'string',
				'default' => ''
			),
			'btnHoverBorderColor' => array(
				'type'    => 'string',
				'default' => ''
			),

			'arrowBGColor' => array(
				'type'    => 'string',
				'default' => ''
			),
			'arrowBGHoverColor' => array(
				'type'    => 'string',
				'default' => ''
			),
			'arrowIconColor' => array(
				'type'    => 'string',
				'default' => ''
			),
			'arrowIconHoverColor' => array(
				'type'    => 'string',
				'default' => ''
			),
			'dotSpacing' => array(
				'type'    => 'number',
				'default' => ''
			),
			'dotBGColor' => array(
				'type'    => 'string',
				'default' => ''
			),
			'dotActiveBGColor' => array(
				'type'    => 'string',
				'default' => ''
			),

		] + parent::common_attributes();

		return apply_filters('rtcl_related_listing_block_attributes', $attributes);
	}

	public function register_block()
	{
		register_block_type(
			RTCL_ELB_PATH . 'app/Blocks/related-listing',
			[
				'render_callback' => [$this, 'render_block'],
				'attributes'      => $this->block_attributes(),
			]
		);
	}

	public function render_block($attributes)
	{

		$this->attributes = $attributes;
		add_filter('excerpt_length', [$this, 'excerpt_limit']);
		add_filter('rtcl_related_listing_query_arg', [$this, 'related_listing_query_arg']);
		add_filter('rtcl_related_listings_data', [$this, 'related_listings_data']);

		ob_start();
		if (!empty($this->listing)) {
			$this->listing->the_related_listings();
		}
		return ob_get_clean();
	}

	public function related_listings_data($data)
	{
		$settings                          = $this->attributes;
		$template_style                    = 'listing-single/related-listing/related-listing';
		$data                              = array_merge(
			$data,
			[
				'template'              => $template_style,
				'settings'              => $settings,
				'view'                  => 'grid',
				'listing'               => $this->listing,
				'default_template_path' => rtclElb()->get_plugin_block_template_path(),
			]
		);

		return $data;
	}

	public function excerpt_limit($length)
	{
		$settings = $this->attributes;
		$length   = !empty($settings['content_limit']) ? $settings['content_limit'] : $length;

		return $length;
	}

	public function related_listing_query_arg($data)
	{
		$settings                = $this->attributes;
		$listings_filter = !empty($settings['listingFilter']) ? $settings['listingFilter'] : ['category'];
		$listings_filter = wp_list_pluck($listings_filter, 'value');

		if (!in_array('category', $listings_filter)) {
			unset($data['tax_query']);
		}
		$related_post_per_page = 6;
		if (!empty($settings['listingPerPage'])) {
			$related_post_per_page = $settings['listingPerPage'];
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
}
