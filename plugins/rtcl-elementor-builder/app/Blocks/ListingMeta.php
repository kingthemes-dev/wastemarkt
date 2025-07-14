<?php

namespace RtclElb\Blocks;

use RtclElb\Abstracts\BlockBase;
use Rtcl\Helpers\Functions;
use RtclElb\Traits\BlockSingleListingTraits;
use RtclElb\Traits\Singleton;

class ListingMeta extends BlockBase
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
			'showType' => array(
				'type' => 'boolean',
				'default' => true
			),

			'showDate' => array(
				'type' => 'boolean',
				'default' => true
			),

			'showUser' => array(
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

			'showViews' => array(
				'type' => 'boolean',
				'default' => true
			),

			'metaAlign' => array(
				'type'    => 'object',
				'default' => [
					'lg' => ''
				],
			),

			"metaMargin" => array(
				"type"    => "object",
				"default" => array(
					'lg' => [
						"isLinked" => true,
						"unit"     => "px",
						"value"    => ''
					]
				)
			),

			'metaSpacing'   => array(
				'type'    => 'object',
				'default' => [
					'lg' => '',
					'unit' => 'px'
				],
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

			'metaIconColor' => array(
				'type'    => 'string',
				'default' => '',
			),

			'metaTextColor' => array(
				'type'    => 'string',
				'default' => '',
			),

		] + parent::common_attributes();

		return apply_filters('rtcl_listing_meta_block_attributes', $attributes);
	}

	public function register_block()
	{
		register_block_type(
			RTCL_ELB_PATH . 'app/Blocks/listing-meta',
			[
				'render_callback' => [$this, 'render_block'],
				'attributes'      => $this->block_attributes(),
			]
		);
	}

	public function render_block($attributes)
	{
		$template_style = 'listing-single/listing-meta';
		$data = [
			'template'              => $template_style,
			'settings'              => $attributes,
			'listing'               => $this->listing,
			'default_template_path' => rtclElb()->get_plugin_block_template_path(),
		];
		$data = apply_filters('rtcl_block_listing_meta_data', $data);
		ob_start();
		Functions::get_template($data['template'], $data, '', $data['default_template_path']);
		return ob_get_clean();
	}
}
