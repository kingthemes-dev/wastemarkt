<?php

namespace RtclElb\Blocks;

use RtclElb\Abstracts\BlockBase;
use Rtcl\Helpers\Functions;
use RtclElb\Traits\BlockSingleListingTraits;
use RtclElb\Traits\Singleton;

class SellerInformation extends BlockBase
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
			'showAuthor' => array(
				'type' => 'boolean',
				'default' => true
			),
			'showAuthorImg' => array(
				'type' => 'boolean',
				'default' => true
			),
			'showLocation' => array(
				'type' => 'boolean',
				'default' => true
			),
			'showContactNum' => array(
				'type' => 'boolean',
				'default' => true
			),
			'showContactForm' => array(
				'type' => 'boolean',
				'default' => true
			),
			'showOnlineStatus' => array(
				'type' => 'boolean',
				'default' => true
			),
			'showSellerWebsite' => array(
				'type' => 'boolean',
				'default' => true
			),

			'dataTypo' => [
				'type' => 'object',
				'default' => [
					'size' => ['lg' => '', 'unit' => 'px'],
					'spacing' => ['lg' => '', 'unit' => 'px'],
					'height' => ['lg' => '', 'unit' => 'px'],
					'transform' => '',
					'weight' => ''
				]
			],
			'labelTypo' => [
				'type' => 'object',
				'default' => [
					'size' => ['lg' => '', 'unit' => 'px'],
					'spacing' => ['lg' => '', 'unit' => 'px'],
					'height' => ['lg' => '', 'unit' => 'px'],
					'transform' => '',
					'weight' => ''
				]
			],
			'labelColor' => array(
				'type'    => 'string',
				'default' => '',
			),
			'dataColor' => array(
				'type'    => 'string',
				'default' => '',
			),
			"itemPadding" => array(
				"type"    => "object",
				"default" => array(
					'lg' => [
						"isLinked" => true,
						"unit"     => "px",
						"value"    => ''
					]
				)
			),
			"itemBorder" => array(
				"type"    => "object",
				"default" => array(
					'borderStyle' => 'solid',
					'borderColor' => '#ddd',
					'lg' => [
						"isLinked" => true,
						"unit"     => "px",
						"value"    => '1'
					]
				)
			),

			'btnTypo' => [
				'type' => 'object',
				'default' => [
					'size' => ['lg' => '', 'unit' => 'px'],
					'spacing' => ['lg' => '', 'unit' => 'px'],
					'height' => ['lg' => '', 'unit' => 'px'],
					'transform' => '',
					'weight' => ''
				]
			],
			'btnTextColor' => array(
				'type'    => 'string',
				'default' => '',
			),
			'btnHoverTextColor' => array(
				'type'    => 'string',
				'default' => '',
			),
			'btnBgColor' => array(
				'type'    => 'string',
				'default' => '',
			),
			'btnHoverBgColor' => array(
				'type'    => 'string',
				'default' => '',
			),
			"btnPadding" => array(
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

		return apply_filters('rtcl_seller_information_block_attributes', $attributes);
	}

	public function register_block()
	{
		register_block_type(
			RTCL_ELB_PATH . 'app/Blocks/seller-information',
			[
				'render_callback' => [$this, 'render_block'],
				'attributes'      => $this->block_attributes(),
			]
		);
	}

	public function render_block($attributes)
	{
		$this->attributes = $attributes;
		$template_style = 'listing-single/seller-information';
		$data = [
			'template'              => $template_style,
			'settings'              => $attributes,
			'listing'               => $this->listing,
			'default_template_path' => rtclElb()->get_plugin_block_template_path(),
		];
		$data = apply_filters('rtcl_block_listing_seller_info_data', $data);
		ob_start();
		Functions::get_template($data['template'], $data, '', $data['default_template_path']);
		return ob_get_clean();
	}
}
