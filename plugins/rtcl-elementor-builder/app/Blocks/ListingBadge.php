<?php

namespace RtclElb\Blocks;

use RtclElb\Abstracts\BlockBase;
use Rtcl\Helpers\Functions;
use RtclElb\Traits\BlockSingleListingTraits;
use RtclElb\Traits\Singleton;

class ListingBadge extends BlockBase
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
			'showNew' => array(
				'type' => 'boolean',
				'default' => true
			),

			'showFeature' => array(
				'type' => 'boolean',
				'default' => true
			),
			'showPopular' => array(
				'type' => 'boolean',
				'default' => true
			),

			'showTop' => array(
				'type' => 'boolean',
				'default' => true
			),
			'showBumpUp' => array(
				'type' => 'boolean',
				'default' => true
			),

			'badgeAlign' => array(
				'type'    => 'object',
				'default' => [
					'lg' => ''
				],
			),

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

			'badgeSpacing'   => array(
				'type'    => 'object',
				'default' => [
					'lg' => '',
					'unit' => 'px'
				],
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

			'badgeBgColor' => array(
				'type'    => 'string',
				'default' => '',
			),

			'badgeTextColor' => array(
				'type'    => 'string',
				'default' => '',
			),

			'topBgColor' => array(
				'type'    => 'string',
				'default' => '',
			),

			'topTextColor' => array(
				'type'    => 'string',
				'default' => '',
			),

			'newBgColor' => array(
				'type'    => 'string',
				'default' => '',
			),

			'newTextColor' => array(
				'type'    => 'string',
				'default' => '',
			),

			'popularBgColor' => array(
				'type'    => 'string',
				'default' => '',
			),

			'popularTextColor' => array(
				'type'    => 'string',
				'default' => '',
			),

			'featureBgColor' => array(
				'type'    => 'string',
				'default' => '',
			),

			'featureTextColor' => array(
				'type'    => 'string',
				'default' => '',
			),

			'bumpUpBgColor' => array(
				'type'    => 'string',
				'default' => '',
			),

			'bumpUpTextColor' => array(
				'type'    => 'string',
				'default' => '',
			),

		] + parent::common_attributes();

		return apply_filters('rtcl_listing_badge_block_attributes', $attributes);
	}

	public function register_block()
	{
		register_block_type(
			RTCL_ELB_PATH . 'app/Blocks/listing-badge',
			[
				'render_callback' => [$this, 'render_block'],
				'attributes'      => $this->block_attributes(),
			]
		);
	}

	public function render_block($attributes)
	{
		$template_style = 'listing-single/listing-badge';
		$data = [
			'template'              => $template_style,
			'settings'              => $attributes,
			'listing'               => $this->listing,
			'default_template_path' => rtclElb()->get_plugin_block_template_path(),
		];
		$data = apply_filters('rtcl_block_listing_badge_data', $data);
		ob_start();
		Functions::get_template($data['template'], $data, '', $data['default_template_path']);
		return ob_get_clean();
	}
}
