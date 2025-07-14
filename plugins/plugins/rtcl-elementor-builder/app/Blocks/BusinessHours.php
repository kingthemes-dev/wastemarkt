<?php

namespace RtclElb\Blocks;

use RtclElb\Abstracts\BlockBase;
use Rtcl\Helpers\Functions;
use RtclElb\Traits\BlockSingleListingTraits;
use RtclElb\Traits\Singleton;

class BusinessHours extends BlockBase
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
			'dateFormate' => array(
				'type' => 'string',
				'default' => '12'
			),
			'openStatusText' => array(
				'type' => 'string',
				'default' => ''
			),
			'closeStatusText' => array(
				'type' => 'string',
				'default' => ''
			),
			'showOpenStatus' => array(
				'type' => 'boolean',
				'default' => true
			),

			'statusTextTypo' => [
				'type' => 'object',
				'default' => [
					'size' => ['lg' => '', 'unit' => 'px'],
					'spacing' => ['lg' => '', 'unit' => 'px'],
					'height' => ['lg' => '', 'unit' => 'px'],
					'transform' => '',
					'weight' => ''
				]
			],
			'openStatusTextColor' => array(
				'type'    => 'string',
				'default' => '',
			),
			'closeStatusTextColor' => array(
				'type'    => 'string',
				'default' => '',
			),
			'tableDataTypo' => [
				'type' => 'object',
				'default' => [
					'size' => ['lg' => '', 'unit' => 'px'],
					'spacing' => ['lg' => '', 'unit' => 'px'],
					'height' => ['lg' => '', 'unit' => 'px'],
					'transform' => '',
					'weight' => ''
				]
			],
			'tableLabelTypo' => [
				'type' => 'object',
				'default' => [
					'size' => ['lg' => '', 'unit' => 'px'],
					'spacing' => ['lg' => '', 'unit' => 'px'],
					'height' => ['lg' => '', 'unit' => 'px'],
					'transform' => '',
					'weight' => ''
				]
			],
			'tableLabelColor' => array(
				'type'    => 'string',
				'default' => '',
			),
			'tableDataColor' => array(
				'type'    => 'string',
				'default' => '',
			),
			'tableBorderColor' => array(
				'type'    => 'string',
				'default' => '',
			),
			"tablePadding" => array(
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

		return apply_filters('rtcl_business_hours_block_attributes', $attributes);
	}

	public function register_block()
	{
		register_block_type(
			RTCL_ELB_PATH . 'app/Blocks/business-hours',
			[
				'render_callback' => [$this, 'render_block'],
				'attributes'      => $this->block_attributes(),
			]
		);
	}

	public function render_block($attributes)
	{
		$this->attributes = $attributes;
		add_filter('rtcl_time_format', [$this, 'business_hours_time_format']);
		add_filter('rtcl_business_hours_display_options', [$this, 'business_hours_frontend_options']);

		$template_style = 'listing-single/business-hours';
		$data = [
			'template'              => $template_style,
			'settings'              => $attributes,
			'listing'               => $this->listing,
			'default_template_path' => rtclElb()->get_plugin_block_template_path(),
		];
		$data = apply_filters('rtcl_block_listing_business_hours_data', $data);
		ob_start();
		Functions::get_template($data['template'], $data, '', $data['default_template_path']);
		return ob_get_clean();
	}

	public function business_hours_frontend_options()
	{
		$settings = $this->attributes;
		if (!empty($settings['openStatusText'])) {
			$options['open_status_text'] = $settings['openStatusText'];
		}
		if (!empty($settings['closeStatusText'])) {
			$options['close_status_text'] = $settings['closeStatusText'];
		}
		$options['show_open_status'] = boolval($settings['showOpenStatus']);
		return $options;
	}
}
