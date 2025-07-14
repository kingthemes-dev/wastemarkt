<?php

namespace RtclElb\Blocks;

use RtclElb\Abstracts\BlockBase;
use Rtcl\Helpers\Functions;
use RtclElb\Traits\BlockSingleListingTraits;
use RtclElb\Traits\Singleton;

class ListingFeatures extends BlockBase
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
			'showTitle' => array(
				'type' => 'boolean',
				'default' => true
			),

			'listAlign' => array(
				'type'    => 'object',
				'default' => [
					'lg' => ''
				],
			),

			"listMargin" => array(
				"type"    => "object",
				"default" => array(
					'lg' => [
						"isLinked" => true,
						"unit"     => "px",
						"value"    => ''
					]
				)
			),

			'listGap'   => array(
				'type'    => 'object',
				'default' => [
					'lg' => '',
					'unit' => 'px'
				],
			),

			'listColumn'   => array(
				'type'    => 'object',
				'default' => [
					'lg' => 3,
					'unit' => ''
				],
			),

			'listTypo' => [
				'type' => 'object',
				'default' => [
					'size' => ['lg' => '', 'unit' => 'px'],
					'spacing' => ['lg' => '', 'unit' => 'px'],
					'height' => ['lg' => '', 'unit' => 'px'],
					'transform' => '',
					'weight' => ''
				]
			],

			'listIconColor' => array(
				'type'    => 'string',
				'default' => '',
			),
			'listIconBgColor' => array(
				'type'    => 'string',
				'default' => '',
			),

			'listIconSize' => array(
				'type'    => 'number',
				'default' => 12,
			),

			'listIconDimention' => array(
				'type'    => 'number',
				'default' => 16,
			),

			'listTextColor' => array(
				'type'    => 'string',
				'default' => '',
			),

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

			'titleColor' => array(
				'type'    => 'string',
				'default' => '',
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

		] + parent::common_attributes();

		return apply_filters('rtcl_listing_features_block_attributes', $attributes);
	}

	public function register_block()
	{
		register_block_type(
			RTCL_ELB_PATH . 'app/Blocks/listing-features',
			[
				'render_callback' => [$this, 'render_block'],
				'attributes'      => $this->block_attributes(),
			]
		);
	}

	public function render_block($attributes)
	{
		$template_style = 'listing-single/classima/listing-features';
		$data = [
			'template'              => $template_style,
			'settings'              => $attributes,
			'listing'               => $this->listing,
			'default_template_path' => rtclElb()->get_plugin_block_template_path(),
		];
		$data = apply_filters('rtcl_block_listing_features_data', $data);
		ob_start();
		Functions::get_template($data['template'], $data, '', $data['default_template_path']);
		return ob_get_clean();
	}
}
