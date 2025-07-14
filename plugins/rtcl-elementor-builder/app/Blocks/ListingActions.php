<?php

namespace RtclElb\Blocks;

use RtclElb\Abstracts\BlockBase;
use Rtcl\Helpers\Functions;
use RtclElb\Traits\BlockSingleListingTraits;
use RtclElb\Traits\Singleton;

class ListingActions extends BlockBase
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
			'showView'    => array(
				'type'    => 'boolean',
				'default' => true
			),
			'showFavourit' => array(
				'type'    => 'boolean',
				'default' => true
			),
			'showAbuse'   => array(
				'type'    => 'boolean',
				'default' => true
			),
			'showSocialShare'   => array(
				'type'    => 'boolean',
				'default' => true
			),
			'inlineStyle' => array(
				'type'    => 'boolean',
				'default' => false
			),
			'actionTypo' => [
				'type' => 'object',
				'default' => [
					'size' => ['lg' => '', 'unit' => 'px'],
					'spacing' => ['lg' => '', 'unit' => 'px'],
					'height' => ['lg' => '', 'unit' => 'px'],
					'transform' => '',
					'weight' => ''
				]
			],
			'actionColor' => array(
				'type'    => 'string',
				'default' => ''
			),
			'actionAlign' => array(
				'type'    => 'object',
				'default' => [
					'lg' => ''
				],
			),
			"actionBorder" => array(
				"type"    => "object",
				"default" => array(
					'borderStyle' => 'solid',
					'borderColor' => '#ddd',
					'lg' => [
						"isLinked" => false,
						"unit"     => "px",
						"value"    => '0 0 1 0'
					]
				)
			),
			"actionPadding" => array(
				"type"    => "object",
				"default" => array(
					'lg' => [
						"isLinked" => true,
						"unit"     => "px",
						"value"    => ''
					]
				)
			),
			'socialShareType' => array(
				'type'    => 'string',
				'default' => 'normal'
			),
			'socialColor' => array(
				'type'    => 'string',
				'default' => ''
			),
			'socialBgColor' => array(
				'type'    => 'string',
				'default' => ''
			),
			"socialMargin" => array(
				"type"    => "object",
				"default" => array(
					'lg' => [
						"isLinked" => true,
						"unit"     => "px",
						"value"    => ''
					]
				)
			),
			"socialIconDimension" => array(
				'type'    => 'object',
				'default' => [
					'lg' => '',
					'unit' => 'px'
				],
			),
			'socialIconSize'   => array(
				'type'    => 'number',
			),

			'socialIconGap'   => array(
				'type'    => 'object',
				'default' => [
					'lg' => '',
					'unit' => 'px'
				],
			),

			'socialHoverColor' => array(
				'type'    => 'string',
				'default' => ''
			),
			'socialHoverBgColor' => array(
				'type'    => 'string',
				'default' => ''
			)

		] + parent::common_attributes();

		return apply_filters('rtcl_listing_action_block_attributes', $attributes);
	}

	public function register_block()
	{
		register_block_type(
			RTCL_ELB_PATH . 'app/Blocks/listing-actions',
			[
				'render_callback' => [$this, 'render_block'],
				'attributes'      => $this->block_attributes(),
			]
		);
	}

	public function render_block($attributes)
	{
		$this->attributes = $attributes;
		$template_style = 'listing-single/listing-actions';
		$data = [
			'template'              => $template_style,
			'settings'              => $attributes,
			'listing'               => $this->listing,
			'default_template_path' => rtclElb()->get_plugin_block_template_path(),
		];
		$data = apply_filters('rtcl_block_listing_actions_data', $data);
		ob_start();
		Functions::get_template($data['template'], $data, '', $data['default_template_path']);
		return ob_get_clean();
	}
}
