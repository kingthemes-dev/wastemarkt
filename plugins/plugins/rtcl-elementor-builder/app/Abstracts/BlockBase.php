<?php

namespace RtclElb\Abstracts;

abstract class  BlockBase
{
	public function __construct()
	{
		add_action('init', [$this, 'register_block']);
	}

	abstract public function register_block();

	public function common_attributes()
	{
		$attributes = [
			'blockId' => array(
				'type'    => 'string',
				'default' => '',
			),

			'resDevice' => array(
				'type' => "string",
				'default' => "lg"
			),

			'blockCSS' => array(
				'type' => "object",
			),

			'preview' => array(
				'type'    => 'boolean',
				'default' => false,
			),

			//advanced 
			"mainWrapMargin" => array(
				"type"    => "object",
				"default" => array(
					'lg' => [
						"isLinked" => true,
						"unit"     => "px",
						"value"    => ''
					]
				)
			),
			"mainWrapPadding" => array(
				"type"    => "object",
				"default" => array(
					'lg' => [
						"isLinked" => true,
						"unit"     => "px",
						"value"    => ''
					]
				)
			),

			'mainWrapBGType'   => array(
				'type'    => 'string',
				'default' => 'normal',
			),

			"mainWrapBG" => array(
				"type"    => "object",
				"default" => [
					'type' => 'classic',
					'classic' => [
						'color' => '',
						'img' => ['imgURL' => '', 'imgID' => ''],
						'imgProperty' => [
							'imgPosition' => ['lg' => ''],
							'imgAttachment' => ['lg' => ''],
							'imgRepeat' => ['lg' => ''],
							'imgSize' => ['lg' => ''],
						]
					],
					'gradient' => null
				],
			),

			"mainWrapHoverBG" => array(
				"type"    => "object",
				"default" => [
					'type' => 'classic',
					'classic' => [
						'color' => '',
						'img' => ['imgURL' => '', 'imgID' => ''],
						'imgProperty' => [
							'imgPosition' => ['lg' => ''],
							'imgAttachment' => ['lg' => ''],
							'imgRepeat' => ['lg' => ''],
							'imgSize' => ['lg' => ''],
						]
					],
					'gradient' => null
				],
			),

			'mainWrapHoverBGTransition'   => array(
				'type'    => 'number',
				'default' => 0.5,
			),

			'mainWrapBGOverlayEnable' => array(
				'type'    => 'boolean',
				'default' => false,
			),

			"mainWrapBGOverlay" => array(
				"type"    => "object",
				"default" => (object)[
					'openBGColor' => 0,
					'type' => 'classic',
					'classic' => (object)[
						'color' => '',
						'img' => (object)['imgURL' => '', 'imgID' => ''],
						'imgProperty' => (object)[
							'imgPosition' => (object)['lg' => ''],
							'imgAttachment' => (object)['lg' => ''],
							'imgRepeat' => (object)['lg' => ''],
							'imgSize' => (object)['lg' => ''],
						]
					],
					'gradient' => null
				]
			),


			'mainWrapBorderType'   => array(
				'type'    => 'string',
				'default' => 'normal',
			),

			"mainWrapBorder" => array(
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

			"mainWrapHoverBorder" => array(
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

			"mainWrapRadius" => array(
				"type"    => "object",
				"default" => array(
					'lg' => [
						"isLinked" => true,
						"unit"     => "px",
						"value"    => ''
					]
				)
			),

			"mainWrapHoverRadius" => array(
				"type"    => "object",
				"default" => array(
					'lg' => [
						"isLinked" => true,
						"unit"     => "px",
						"value"    => ''
					]
				)
			),

			'mainWrapShadowType'   => array(
				'type'    => 'string',
				'default' => 'normal',
			),

			'mainWrapShadow' => [
				'type' => 'object',
				'default' => [
					'width' => ['top' => 1, 'right' => 1, 'bottom' => 1, 'left' => 1],
					'color' => ''
				]
			],

			'mainWrapHoverShadow' => [
				'type' => 'object',
				'default' => [
					'width' => ['top' => 1, 'right' => 1, 'bottom' => 1, 'left' => 1],
					'color' => ''
				]
			],

			'mainWrapZindex' => array(
				'type' => "number",
				'default' => 1
			),

			'mainWrapShowHide' => array(
				'type' => "object",
				'default' => [
					'lg' => false,
					'md' => false,
					'sm' => false
				]
			),

			'blockCustomCss' => [
				'type' => "string",
				'default' => "",
			]

		];

		return apply_filters('rtcl-block-attributes', $attributes);
	}

	abstract public function render_block($attributes);
}
