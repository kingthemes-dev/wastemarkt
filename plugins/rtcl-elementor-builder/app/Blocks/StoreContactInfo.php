<?php

namespace RtclElb\Blocks;

use RtclElb\Abstracts\SingleStroeBlockBase;
use Rtcl\Helpers\Functions;
use RtclElb\Traits\Singleton;


class StoreContactInfo extends SingleStroeBlockBase
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
			'titleTag' => array(
				'type'    => 'string',
				'default' => 'h3',
			),

			'showStatus' => array(
				'type'    => 'boolean',
				'default' => true,
			),
			'showAddress' => array(
				'type'    => 'boolean',
				'default' => true,
			),
			'showPhone' => array(
				'type'    => 'boolean',
				'default' => true,
			),
			'showSocialMedia' => array(
				'type'    => 'boolean',
				'default' => true,
			),
			'showEmail' => array(
				'type'    => 'boolean',
				'default' => true,
			),

			'contactTypo' => [
				'type' => 'object',
				'default' => [
					'size' => ['lg' => '', 'unit' => 'px'],
					'spacing' => ['lg' => '', 'unit' => 'px'],
					'height' => ['lg' => '', 'unit' => 'px'],
					'transform' => '',
					'weight' => ''
				]
			],

			"contactMargin" => array(
				"type"    => "object",
				"default" => array(
					'lg' => [
						"isLinked" => true,
						"unit"     => "px",
						"value"    => ''
					]
				)
			),

			"contactPadding" => array(
				"type"    => "object",
				"default" => array(
					'lg' => [
						"isLinked" => true,
						"unit"     => "px",
						"value"    => ''
					]
				)
			),

			"contactSocialIconWidth" => array(
				"type"    => "object",
				"default" => array(
					'lg' => '',
					'unit' => 'px'
				)
			),
			"contactSocialIconSize" => array(
				"type"    => "object",
				"default" => array(
					'lg' => '',
					'unit' => 'px'
				)
			),
			"contactFieldHeight" => array(
				"type"    => "object",
				"default" => array(
					'lg' => '',
					'unit' => 'px'
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
			"btnBorder" => array(
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

		] + parent::common_attributes();

		return apply_filters('rtcl_store_contactinfo_block_attributes', $attributes);
	}

	public function register_block()
	{
		register_block_type(
			RTCL_ELB_PATH . 'app/Blocks/store-contact-info',
			[
				'render_callback' => [$this, 'render_block'],
				'attributes'      => $this->block_attributes(),
			]
		);
	}

	public function render_block($attributes)
	{
		$template_style = 'store-single/contact-info';
		$data = [
			'template'              => $template_style,
			'settings'              => $attributes,
			'store'					=> $this->store,
			'default_template_path' => rtclElb()->get_plugin_block_template_path(),
		];
		$data = apply_filters('rtcl_block_store_contact_info_data', $data);
		ob_start();
		Functions::get_template($data['template'], $data, '', $data['default_template_path']);
		return ob_get_clean();
	}
}
