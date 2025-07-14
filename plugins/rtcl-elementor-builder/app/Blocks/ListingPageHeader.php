<?php

namespace RtclElb\Blocks;

use RtclElb\Abstracts\BlockBase;
use Rtcl\Helpers\Functions;
use RtclElb\Traits\BlockSingleListingTraits;
use RtclElb\Traits\Singleton;

class ListingPageHeader extends BlockBase
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
				'type' => 'string',
				'default' => 'style-1'
			),

			'showPageTitle' => array(
				'type' => 'boolean',
				'default' => true
			),

			'titleTag' => array(
				'type' => 'string',
				'default' => 'h3'
			),

			'titleAlign' => array(
				'type'    => 'object',
				'default' => [
					'lg' => ''
				],
			),

			'showBreadcrumb' => array(
				'type' => 'boolean',
				'default' => true
			),

			'breadcrumbPosition' => array(
				'type' => 'string',
				'default' => 'bottom'
			),

			'breadcrumbAlign' => array(
				'type'    => 'object',
				'default' => [
					'lg' => ''
				],
			),

			"titleSpacing" => array(
				"type"    => "object",
				"default" => array(
					'lg' => [
						"isLinked" => true,
						"unit"     => "px",
						"value"    => ''
					]
				)
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

			"breadcrumbSpacing" => array(
				"type"    => "object",
				"default" => array(
					'lg' => [
						"isLinked" => true,
						"unit"     => "px",
						"value"    => ''
					]
				)
			),
			'breadcrumbTypo' => [
				'type' => 'object',
				'default' => [
					'size' => ['lg' => '', 'unit' => 'px'],
					'spacing' => ['lg' => '', 'unit' => 'px'],
					'height' => ['lg' => '', 'unit' => 'px'],
					'transform' => '',
					'weight' => ''
				]
			],

			'breadcrumbColor' => array(
				'type'    => 'string',
				'default' => '',
			),

		] + parent::common_attributes();

		return apply_filters('rtcl_listing_page_header_block_attributes', $attributes);
	}

	public function register_block()
	{
		register_block_type(
			RTCL_ELB_PATH . 'app/Blocks/listing-page-header',
			[
				'render_callback' => [$this, 'render_block'],
				'attributes'      => $this->block_attributes(),
			]
		);
	}

	public function render_block($attributes)
	{
		$template_style = 'listing-page-header/page-header';
		$data = [
			'template'              => $template_style,
			'settings'              => $attributes,
			'listing'               => $this->listing,
			'default_template_path' => rtclElb()->get_plugin_block_template_path(),
		];
		$data = apply_filters('rtcl_block_listing_page_header_data', $data);
		ob_start();
		Functions::get_template($data['template'], $data, '', $data['default_template_path']);
		return ob_get_clean();
	}
}
