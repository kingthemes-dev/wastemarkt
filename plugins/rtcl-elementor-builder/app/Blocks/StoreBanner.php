<?php

namespace RtclElb\Blocks;

use RtclElb\Abstracts\SingleStroeBlockBase;
use Rtcl\Helpers\Functions;
use RtclElb\Traits\Singleton;


class StoreBanner extends SingleStroeBlockBase
{
	use Singleton;

	public function __construct()
	{
		parent::__construct();
		add_action('template_redirect', [$this, 'set_store']);
	}

	public function block_attributes()
	{
		$attributes = [
			'lastStoreId' => array(
				'type'    => 'number',
				'default' => 0,
			),
			'wrapClass' => array(
				'type'    => 'boolean',
				'default' => true,
			),
			//content visibility
			'showStoreLogo' => array(
				'type'    => 'boolean',
				'default' => true,
			),
			'showStoreName' => array(
				'type'    => 'boolean',
				'default' => true,
			),
			'showStoreCategory' => array(
				'type'    => 'boolean',
				'default' => true,
			),
			'showStoreRating' => array(
				'type'    => 'boolean',
				'default' => true,
			),
			'bannerHeight' => array(
				'type'    => 'object',
				'default' => [
					'lg' => '',
					'unit' => 'px'
				]
			),
			'bannerWidth' => array(
				'type'    => 'object',
				'default' => [
					'lg' => '',
					'unit' => '%'
				]
			),
			'logoWrapHeight' => array(
				'type'    => 'object',
				'default' => [
					'lg' => '',
					'unit' => 'px'
				]
			),
			'logoWrapWidth' => array(
				'type'    => 'object',
				'default' => [
					'lg' => '',
					'unit' => 'px'
				]
			),
			'nameTypo' => [
				'type' => 'object',
				'default' => [
					'size' => ['lg' => '', 'unit' => 'px'],
					'spacing' => ['lg' => '', 'unit' => 'px'],
					'height' => ['lg' => '', 'unit' => 'px'],
					'transform' => '',
					'weight' => ''
				]
			],
			'nameColor' => array(
				'type'    => 'string',
				'default' => '',
			),
			'catTypo' => [
				'type' => 'object',
				'default' => [
					'size' => ['lg' => '', 'unit' => 'px'],
					'spacing' => ['lg' => '', 'unit' => 'px'],
					'height' => ['lg' => '', 'unit' => 'px'],
					'transform' => '',
					'weight' => ''
				]
			],
			'catColor' => array(
				'type'    => 'string',
				'default' => '',
			),
			'catIconColor' => array(
				'type'    => 'string',
				'default' => '',
			),
			'reviewTypo' => [
				'type' => 'object',
				'default' => [
					'size' => ['lg' => '', 'unit' => 'px'],
					'spacing' => ['lg' => '', 'unit' => 'px'],
					'height' => ['lg' => '', 'unit' => 'px'],
					'transform' => '',
					'weight' => ''
				]
			],
			'reviewColor' => array(
				'type'    => 'string',
				'default' => '',
			),

			"logoBorder" => array(
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
			'logoImageWidth' => array(
				'type'    => 'object',
				'default' => [
					'lg' => '',
					'unit' => 'px'
				]
			),
			'logoWrapBgColor' => array(
				'type'    => 'string',
				'default' => '',
			),

		] + parent::common_attributes();

		return apply_filters('rtcl_store_name_block_attributes', $attributes);
	}

	public function register_block()
	{
		register_block_type(
			RTCL_ELB_PATH . 'app/Blocks/store-banner',
			[
				'render_callback' => [$this, 'render_block'],
				'attributes'      => $this->block_attributes(),
			]
		);
	}

	public function render_block($attributes)
	{
		$template_style = 'store-single/banner';
		$data = [
			'template'              => $template_style,
			'settings'              => $attributes,
			'store'					=> $this->store,
			'default_template_path' => rtclElb()->get_plugin_block_template_path(),
		];
		$data = apply_filters('rtcl_block_store_banner_data', $data);
		ob_start();
		Functions::get_template($data['template'], $data, '', $data['default_template_path']);
		return ob_get_clean();
	}
}
