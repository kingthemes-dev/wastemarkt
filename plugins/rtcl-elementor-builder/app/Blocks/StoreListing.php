<?php

namespace RtclElb\Blocks;

use RtclElb\Abstracts\BlockBase;
use Rtcl\Helpers\Functions;
use RtclElb\Traits\Singleton;
use Rtcl\Controllers\Blocks\ListingsAjaxController;
use RtclElb\Helpers\Fns;

class StoreListing extends BlockBase
{
	use Singleton;
	protected $attributes = [];
	protected $store;

	public function __construct()
	{
		parent::__construct();
	}

	public function block_attributes()
	{
		$attributes = [
			'listStyle' => array(
				'type' => 'string',
				'default' => 'style-1'
			),

			'gridStyle' => array(
				'type' => 'string',
				'default' => 'style-1'
			),

			'layout' => array(
				'type' => 'string',
				'default' => 'list'
			),

			"cats" => array(
				"type" => "array",
			),
			"locations" => array(
				"type" => "array",
			),
			"listing_type" => array(
				"type" => "string",
				"default" => "all",
			),

			"image_size" => array(
				"type" => "string",
				"default" => "rtcl-thumbnail",
			),
			"custom_image_width" => array(
				"type" => "number",
				"default" => 400,
			),
			"custom_image_height" => array(
				"type" => "number",
				"default" => 280,
			),

			"promotion_in" => array(
				"type" => "array",
			),
			"promotion_not_in" => array(
				"type" => "array",
			),
			"orderby" => array(
				"type" => "string",
				"default" => "date",
			),
			"sortby" => array(
				"type" => "string",
				"default" => "desc",
			),
			"perPage" => array(
				"type" => "number",
				"default" => 9,
			),
			"offset" => array(
				"type" => "number",
				"default" => 0,
			),

			"listingImageSize" => array(
				"type" => "string",
				"default" => "rtcl-thumbnail",
			),
			'pagination' => array(
				'type'    => 'boolean',
				'default' => true,
			),
			'page'   => array(
				'type'    => 'number',
				'default' => 1,
			),

			'content_limit' => array(
				'type'    => 'number',
				'default' => 20
			),
			'gridColumn' => array(
				'type'    => 'object',
				'default' => [
					'lg' => 3,
					'md' => 2,
					'sm' => 1
				]
			),

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
					"list_content" => true,
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

			'colBgColor' => array(
				'type'    => 'string',
				'default' => ''
			),
			'colGutterSpacing'  => [
				'type'    => "object",
				'default' =>  [
					'lg' => 30,
					'unit' => 'px'
				]
			],
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

			'promotionalPostType' => [
				'type' => 'string',
				'default' => 'normal'
			],
			'promTopBgColor' => [
				'type' => 'string',
				'default' => '#FFFDEA'
			],
			'promTopBorderColor' => [
				'type' => 'string',
				'default' => ''
			],
			'promFeaturedBgColor' => [
				'type' => 'string',
				'default' => ''
			],
			'promFeaturedBorderColor' => [
				'type' => 'string',
				'default' => ''
			],
			'promHoverTopBgColor' => [
				'type' => 'string',
				'default' => ''
			],
			'promHoverTopBorderColor' => [
				'type' => 'string',
				'default' => ''
			],
			'promHoverFeaturedBgColor' => [
				'type' => 'string',
				'default' => ''
			],
			'promHoverFeaturedBorderColor' => [
				'type' => 'string',
				'default' => ''
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

			'customFieldLabelTypo' => [
				'type' => 'object',
				'default' => [
					'size' => ['lg' => '', 'unit' => 'px'],
					'spacing' => ['lg' => '', 'unit' => 'px'],
					'height' => ['lg' => '', 'unit' => 'px'],
					'transform' => '',
					'weight' => ''
				]
			],
			'customFieldValTypo' => [
				'type' => 'object',
				'default' => [
					'size' => ['lg' => '', 'unit' => 'px'],
					'spacing' => ['lg' => '', 'unit' => 'px'],
					'height' => ['lg' => '', 'unit' => 'px'],
					'transform' => '',
					'weight' => ''
				]
			],
			"customFieldPadding" => array(
				"type"    => "object",
				"default" => array(
					'lg' => [
						"isLinked" => true,
						"unit"     => "px",
						"value"    => ''
					]
				)
			),

			'customFieldLabelColor' => array(
				'type'    => 'string',
				'default' => ''
			),
			'customFieldValColor' => array(
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

			'detailsBGColor' => array(
				'type'    => 'string',
				'default' => ''
			),
			'detailsColor' => array(
				'type'    => 'string',
				'default' => ''
			),
			'actionTextColor' => array(
				'type'    => 'string',
				'default' => ''
			),

			'detailsBGColor' => array(
				'type'    => 'string',
				'default' => ''
			),
			'detailsColor' => array(
				'type'    => 'string',
				'default' => ''
			),
			'actionTextColor' => array(
				'type'    => 'string',
				'default' => ''
			),
			'detailsBGHoverColor' => array(
				'type'    => 'string',
				'default' => ''
			),
			'detailsHoverColor' => array(
				'type'    => 'string',
				'default' => ''
			),
			'actionTextHoverColor' => array(
				'type'    => 'string',
				'default' => ''
			),

			'paginationBg' => array(
				'type'    => 'string',
				'default' => ''
			),
			'paginationActiveBg' => array(
				'type'    => 'string',
				'default' => ''
			),
			'paginationTextColor' => array(
				'type'    => 'string',
				'default' => ''
			),
			'paginationActiveTextColor' => array(
				'type'    => 'string',
				'default' => ''
			),
			"paginationBorder" => array(
				"type"    => "object",
				"default" => array(
					'borderStyle' => '',
					'borderColor' => '',
					'lg' => [
						"isLinked" => true,
						"unit"     => "px",
						"value"    => ''
					]
				)
			),
			"paginationMargin" => array(
				"type"    => "object",
				"default" => array(
					'lg' => [
						"isLinked" => true,
						"unit"     => "px",
						"value"    => ''
					]
				)
			),

		] + parent::common_attributes();

		return apply_filters('rtcl_archive_block_attributes', $attributes);
	}

	public function register_block()
	{
		register_block_type(
			RTCL_ELB_PATH . 'app/Blocks/store-listing',
			[
				'render_callback' => [$this, 'render_block'],
				'attributes'      => $this->block_attributes(),
			]
		);
	}

	public function render_block($attributes)
	{
		add_filter('excerpt_length', [$this, 'excerpt_limit']);
		$settings = $attributes;
		$this->attributes = $settings;
		$this->store = rtclStore()->factory->get_store(Fns::last_store_id());
		$args = ListingsAjaxController::rtcl_gb_listing_args($settings);
		if (!empty($this->store)) {
			$args['author'] = $this->store->owner_id();
		}
		$the_query = new \WP_Query($args);
		$view      = !empty($settings['layout']) ? $settings['layout'] : 'list';
		$style = 'style-1';
		if ('list' === $view) {
			$style = $settings['listStyle'] ? $settings['listStyle'] : 'style-1';
		}

		if ('grid' === $view) {
			$view  = 'grid';
			$style = $settings['gridStyle'] ? $settings['gridStyle'] : 'style-1';
		}

		$template_style = 'store-single/store-listing';
		$data  = [
			'template'              => $template_style,
			'view'                  => $view,
			'style'                 => $style,
			'instance'              => $settings,
			'the_query'             => $the_query,
			'store'                 => $this->store,
			'default_template_path' =>  rtclElb()->get_plugin_block_template_path(),
		];

		$data = apply_filters('rtcl_block_store_listing_data', $data);
		ob_start();
		Functions::get_template($data['template'], $data, '', $data['default_template_path']);
		return ob_get_clean();
	}

	public function excerpt_limit($length)
	{
		$settings = $this->attributes;
		$length   = !empty($settings['content_limit']) ? $settings['content_limit'] : $length;
		return $length;
	}
}
