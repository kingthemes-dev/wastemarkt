<?php

namespace RtclElb\Blocks;

use RtclElb\Abstracts\BlockBase;
use Rtcl\Helpers\Functions;
use Rtcl\Widgets\Filter;
use Rtcl\Controllers\Hooks\TemplateHooks;
use RtclElb\Traits\Singleton;

class FilterForm extends Filter
{
	function __construct($settings = [])
	{
		$this->instance = [
			'search_by_category' => 1,
			'search_by_location' => 1,
			'search_by_ad_type' => 1,
			'search_by_price' => 1,
			'radius_search' => 1,
			'show_icon_image_for_category' => 1,
		];

		if (isset($settings['showCategory'])) {
			$this->instance['search_by_category'] = $settings['showCategory'];
		}
		if (isset($settings['showLocation'])) {
			$this->instance['search_by_location'] = $settings['showLocation'];
		}
		if (isset($settings['showAdType'])) {
			$this->instance['search_by_ad_type'] = $settings['showAdType'];
		}
		if (isset($settings['showPrice'])) {
			$this->instance['search_by_price'] = $settings['showPrice'];
		}
		if (isset($settings['showRadiusSearch'])) {
			$this->instance['radius_search'] = $settings['showRadiusSearch'];
		}
		if (isset($settings['hideEmptyCatLocation'])) {
			$this->instance['hide_empty'] = $settings['hideEmptyCatLocation'];
		}
		if (isset($settings['showCount'])) {
			$this->instance['show_count'] = $settings['showCount'];
		}
		if (isset($settings['showAjaxLoad'])) {
			$this->instance['ajax_load'] = $settings['showAjaxLoad'];
		}
		if (isset($settings['showCatLocationLink'])) {
			$this->instance['taxonomy_reset_link'] = $settings['showCatLocationLink'];
		}
		if (isset($settings['showCatImageIcon'])) {
			$this->instance['show_icon_image_for_category'] = $settings['showCatImageIcon'];
		}
		if (isset($settings['showRating'])) {
			$this->instance['search_by_rating'] = $settings['showRating'];
		}
		if (isset($settings['showCustomField'])) {
			$this->instance['search_by_custom_fields'] = $settings['showCustomField'];
		}
	}
}

class ListingFilter extends BlockBase
{
	use Singleton;
	protected $attributes = [];

	public function __construct()
	{
		parent::__construct();
	}

	public function block_attributes()
	{
		$attributes = [
			'style' => array(
				'type' => 'string',
				'default' => 'style-1'
			),

			'filterTitle' => array(
				'type' => 'string',
				'default' => 'Filter'
			),

			'showFilterTitle' => array(
				'type' => 'boolean',
				'default' => true
			),

			'showCategory' => array(
				'type' => 'boolean',
				'default' => true
			),
			'showLocation' => array(
				'type' => 'boolean',
				'default' => true
			),
			'showRadiusSearch' => array(
				'type' => 'boolean',
				'default' => false
			),
			'showAdType' => array(
				'type' => 'boolean',
				'default' => true
			),
			'showPrice' => array(
				'type' => 'boolean',
				'default' => true
			),
			'hideEmptyCatLocation' => array(
				'type' => 'boolean',
				'default' => true
			),
			'showCount' => array(
				'type' => 'boolean',
				'default' => true
			),
			'showAjaxLoad' => array(
				'type' => 'boolean',
				'default' => false
			),
			'showCatLocationLink' => array(
				'type' => 'boolean',
				'default' => true
			),
			'showCatImageIcon' => array(
				'type' => 'boolean',
				'default' => true
			),
			'showRating' => array(
				'type' => 'boolean',
				'default' => false
			),
			'showCustomField' => array(
				'type' => 'boolean',
				'default' => false
			),

			'itemBoxBGColor' => array(
				'type'    => 'string',
				'default' => ''
			),
			"itemBoxMargin" => array(
				"type"    => "object",
				"default" => array(
					'lg' => [
						"isLinked" => true,
						"unit"     => "px",
						"value"    => ''
					]
				)
			),
			"itemBoxPadding" => array(
				"type"    => "object",
				"default" => array(
					'lg' => [
						"isLinked" => true,
						"unit"     => "px",
						"value"    => ''
					]
				)
			),

			"itemBoxBorder" => array(
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

			'itemBoxBoxShadow' => [
				'type' => 'object',
				'default' => [
					'width' => ['top' => 1, 'right' => 1, 'bottom' => 1, 'left' => 1],
					'color' => ''
				]
			],

			'itemTitleTypo' => [
				'type' => 'object',
				'default' => [
					'size' => ['lg' => '', 'unit' => 'px'],
					'spacing' => ['lg' => '', 'unit' => 'px'],
					'height' => ['lg' => '', 'unit' => 'px'],
					'transform' => '',
					'weight' => ''
				]
			],
			'itemTitleBgColor' => array(
				'type'    => 'string',
				'default' => '',
			),
			'itemTitleColor' => array(
				'type'    => 'string',
				'default' => '',
			),
			"itemTitlePadding" => array(
				"type"    => "object",
				"default" => array(
					'lg' => [
						"isLinked" => true,
						"unit"     => "px",
						"value"    => ''
					]
				)
			),

			'filterBtnTypo' => [
				'type' => 'object',
				'default' => [
					'size' => ['lg' => '', 'unit' => 'px'],
					'spacing' => ['lg' => '', 'unit' => 'px'],
					'height' => ['lg' => '', 'unit' => 'px'],
					'transform' => '',
					'weight' => ''
				]
			],
			'filterBtnBgColor' => array(
				'type'    => 'string',
				'default' => '',
			),
			'filterBtnColor' => array(
				'type'    => 'string',
				'default' => '',
			),
			"filterBtnPadding" => array(
				"type"    => "object",
				"default" => array(
					'lg' => [
						"isLinked" => true,
						"unit"     => "px",
						"value"    => ''
					]
				)
			),
			"filterBtnMargin" => array(
				"type"    => "object",
				"default" => array(
					'lg' => [
						"isLinked" => true,
						"unit"     => "px",
						"value"    => ''
					]
				)
			),
			"filterBtnBorder" => array(
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
			"filterBtnRadius" => array(
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

		return apply_filters('rtcl_listing_filter_block_attributes', $attributes);
	}

	public function register_block()
	{
		register_block_type(
			RTCL_ELB_PATH . 'app/Blocks/listing-filter',
			[
				'render_callback' => [$this, 'render_block'],
				'attributes'      => $this->block_attributes(),
			]
		);
	}

	public function render_block($attributes)
	{
		remove_action('rtcl_widget_filter_form_end', [TemplateHooks::class, 'add_hidden_field_filter_form'], 50);
		$this->attributes = $attributes;
		$filter_obj = new FilterForm($attributes);
		$template_style = 'listing-archive/listing-filter';
		$data = [
			'template'              => $template_style,
			'settings'              => $attributes,
			'object'                => $filter_obj,
			'data'                  => '',
			'default_template_path' => rtclElb()->get_plugin_block_template_path(),
		];
		$data = apply_filters('rtcl_block_listing_filter_data', $data);
		ob_start();
		Functions::get_template($data['template'], $data, '', $data['default_template_path']);
		return ob_get_clean();
	}
}
