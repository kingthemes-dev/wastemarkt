<?php

namespace RtclElb\Blocks;

use RtclElb\Abstracts\SingleStroeBlockBase;
use Rtcl\Helpers\Functions;
use RtclElb\Traits\Singleton;


class StoreSlogan extends SingleStroeBlockBase
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

			'titleAlignment' => array(
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

		] + parent::common_attributes();

		return apply_filters('rtcl_store_slogan_block_attributes', $attributes);
	}

	public function register_block()
	{
		register_block_type(
			RTCL_ELB_PATH . 'app/Blocks/store-slogan',
			[
				'render_callback' => [$this, 'render_block'],
				'attributes'      => $this->block_attributes(),
			]
		);
	}

	public function render_block($attributes)
	{
		$template_style = 'store-single/slogan';
		$data = [
			'template'              => $template_style,
			'settings'              => $attributes,
			'store'					=> $this->store,
			'default_template_path' => rtclElb()->get_plugin_block_template_path(),
		];
		$data = apply_filters('rtcl_block_store_slogan_data', $data);
		ob_start();
		Functions::get_template($data['template'], $data, '', $data['default_template_path']);
		return ob_get_clean();
	}
}
