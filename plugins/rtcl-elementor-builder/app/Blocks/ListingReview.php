<?php

namespace RtclElb\Blocks;

use RtclElb\Abstracts\BlockBase;
use Rtcl\Helpers\Functions;
use RtclElb\Traits\BlockSingleListingTraits;
use RtclElb\Traits\Singleton;

class ListingReview extends BlockBase
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
			'showCommentList' => array(
				'type'    => 'boolean',
				'default' => true
			),
			'showReviewSectionTitle' => array(
				'type'    => 'boolean',
				'default' => true
			),
			"reviewTitleText" => array(
				"type" => "string",
				"default" => "Reviews",
			),
			'showLeaveBtn' => array(
				'type'    => 'boolean',
				'default' => true
			),
			"leaveBtnText" => array(
				"type" => "string",
				"default" => 'Leave Review'
			),
			'showReviewMeta' => array(
				'type'    => 'boolean',
				'default' => true
			),
			'showContactForm' => array(
				'type'    => 'boolean',
				'default' => true
			),
			'contactFormTitleText' => array(
				'type'    => 'string',
				'default' => 'Leave Review'
			),

			'mainBackground' => array(
				'type'    => 'string',
				'default' => ''
			),
			"mainMargin" => array(
				"type"    => "object",
				"default" => array(
					'lg' => [
						"isLinked" => true,
						"unit"     => "px",
						"value"    => ''
					]
				)
			),
			"mainPadding" => array(
				"type"    => "object",
				"default" => array(
					'lg' => [
						"isLinked" => true,
						"unit"     => "px",
						"value"    => ''
					]
				)
			),
			'sectionTitleTypo' => [
				'type' => 'object',
				'default' => [
					'size' => ['lg' => '', 'unit' => 'px'],
					'spacing' => ['lg' => '', 'unit' => 'px'],
					'height' => ['lg' => '', 'unit' => 'px'],
					'transform' => '',
					'weight' => ''
				]
			],
			'sectionTitleColor' => array(
				'type'    => 'string',
				'default' => ''
			),
			'averageRatingTypo' => [
				'type' => 'object',
				'default' => [
					'size' => ['lg' => '', 'unit' => 'px'],
					'spacing' => ['lg' => '', 'unit' => 'px'],
					'height' => ['lg' => '', 'unit' => 'px'],
					'transform' => '',
					'weight' => ''
				]
			],
			'averageRatingColor' => array(
				'type'    => 'string',
				'default' => ''
			),
			"averageRatingBorder" => array(
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

			'headerBtnTypo' => [
				'type' => 'object',
				'default' => [
					'size' => ['lg' => '', 'unit' => 'px'],
					'spacing' => ['lg' => '', 'unit' => 'px'],
					'height' => ['lg' => '', 'unit' => 'px'],
					'transform' => '',
					'weight' => ''
				]
			],
			'headerBtnTextColor' => array(
				'type'    => 'string',
				'default' => '',
			),
			'headerBtnHoverTextColor' => array(
				'type'    => 'string',
				'default' => '',
			),
			'headerBtnBgColor' => array(
				'type'    => 'string',
				'default' => '',
			),
			'headerBtnHoverBgColor' => array(
				'type'    => 'string',
				'default' => '',
			),
			'headerBtnBorderColor' => array(
				'type'    => 'string',
				'default' => '',
			),

			"headerBtnPadding" => array(
				"type"    => "object",
				"default" => array(
					'lg' => [
						"isLinked" => true,
						"unit"     => "px",
						"value"    => ''
					]
				)
			),

			"commentListPadding" => array(
				"type"    => "object",
				"default" => array(
					'lg' => [
						"isLinked" => true,
						"unit"     => "px",
						"value"    => ''
					]
				)
			),
			"commentListMargin" => array(
				"type"    => "object",
				"default" => array(
					'lg' => [
						"isLinked" => true,
						"unit"     => "px",
						"value"    => ''
					]
				)
			),
			'authorTypo' => [
				'type' => 'object',
				'default' => [
					'size' => ['lg' => '', 'unit' => 'px'],
					'spacing' => ['lg' => '', 'unit' => 'px'],
					'height' => ['lg' => '', 'unit' => 'px'],
					'transform' => '',
					'weight' => ''
				]
			],
			'commentListGap'   => array(
				'type'    => 'object',
				'default' => [
					'lg' => 15,
					'unit' => 'px'
				],
			),
			'commentListRatingGap'   => array(
				'type'    => 'object',
				'default' => [
					'lg' => '',
					'unit' => 'px'
				],
			),
			'commentListTitleTypo' => [
				'type' => 'object',
				'default' => [
					'size' => ['lg' => '', 'unit' => 'px'],
					'spacing' => ['lg' => '', 'unit' => 'px'],
					'height' => ['lg' => '', 'unit' => 'px'],
					'transform' => '',
					'weight' => ''
				]
			],
			'commentListTitleGap'   => array(
				'type'    => 'object',
				'default' => [
					'lg' => '',
					'unit' => 'px'
				],
			),

			'formBgColor' => array(
				'type'    => 'string',
				'default' => '',
			),
			'formTitleTypo' => [
				'type' => 'object',
				'default' => [
					'size' => ['lg' => '', 'unit' => 'px'],
					'spacing' => ['lg' => '', 'unit' => 'px'],
					'height' => ['lg' => '', 'unit' => 'px'],
					'transform' => '',
					'weight' => ''
				]
			],
			'formTitleGap'   => array(
				'type'    => 'object',
				'default' => [
					'lg' => '',
					'unit' => 'px'
				],
			),
			'formLabelTypo' => [
				'type' => 'object',
				'default' => [
					'size' => ['lg' => '', 'unit' => 'px'],
					'spacing' => ['lg' => '', 'unit' => 'px'],
					'height' => ['lg' => '', 'unit' => 'px'],
					'transform' => '',
					'weight' => ''
				]
			],
			'formFieldHeight'   => array(
				'type'    => 'object',
				'default' => [
					'lg' => '',
					'unit' => 'px'
				],
			),
			'formTextareaHeight'   => array(
				'type'    => 'object',
				'default' => [
					'lg' => '',
					'unit' => 'px'
				],
			),
			'formFieldBorderColor' => array(
				'type'    => 'string',
				'default' => '',
			),

			'formBtnTypo' => [
				'type' => 'object',
				'default' => [
					'size' => ['lg' => '', 'unit' => 'px'],
					'spacing' => ['lg' => '', 'unit' => 'px'],
					'height' => ['lg' => '', 'unit' => 'px'],
					'transform' => '',
					'weight' => ''
				]
			],
			'formBtnTextColor' => array(
				'type'    => 'string',
				'default' => '',
			),
			'formBtnHoverTextColor' => array(
				'type'    => 'string',
				'default' => '',
			),
			'formBtnBgColor' => array(
				'type'    => 'string',
				'default' => '',
			),
			'formBtnHoverBgColor' => array(
				'type'    => 'string',
				'default' => '',
			),
			"formBtnPadding" => array(
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

		return apply_filters('rtcl_listing_review_block_attributes', $attributes);
	}

	public function register_block()
	{
		register_block_type(
			RTCL_ELB_PATH . 'app/Blocks/listing-review',
			[
				'render_callback' => [$this, 'render_block'],
				'attributes'      => $this->block_attributes(),
			]
		);
	}

	public function render_block($attributes)
	{
		$this->attributes = $attributes;
		$template_style = 'listing-single/listing-review';
		$data = [
			'template'              => $template_style,
			'settings'              => $attributes,
			'listing'               => $this->listing,
			'default_template_path' => rtclElb()->get_plugin_block_template_path(),
		];

		$data = apply_filters('rtcl_block_listing_review_data', $data);
		ob_start();
		Functions::get_template($data['template'], $data, '', $data['default_template_path']);
		return ob_get_clean();
	}
}
