<?php

namespace RtclElb\Blocks;

use RtclElb\Abstracts\BlockBase;
use Rtcl\Helpers\Functions;
use RtclElb\Traits\BlockSingleListingTraits;
use RtclElb\Traits\Singleton;

class ListingBooking extends BlockBase
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
			'listingId' => array(
				'type' => 'number',
				'default' => ''
			),
			'wrapClass' => array(
				'type' => 'boolean',
				'default' => true
			),
			'showHeading' => array(
				'type' => 'boolean',
				'default' => true
			),
			'showHeadingIndicator' => array(
				'type' => 'boolean',
				'default' => true
			),

			//style
			'boxBackground' => array(
				'type'    => 'string',
				'default' => '',
			),
			"boxPadding" => array(
				"type"    => "object",
				"default" => array(
					'lg' => [
						"isLinked" => true,
						"unit"     => "px",
						"value"    => ''
					]
				)
			),
			"boxBorder" => array(
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
			"boxRadius" => array(
				"type"    => "object",
				"default" => array(
					'lg' => [
						"isLinked" => true,
						"unit"     => "px",
						"value"    => ''
					]
				)
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
			'formLabelColor' => array(
				'type'    => 'string',
				'default' => '',
			),
			'formFieldBgColor' => array(
				'type'    => 'string',
				'default' => '',
			),
			'formFieldHeight' => array(
				'type'    => 'object',
				'default' => [
					'lg' => ''
				],
			),
			"formFieldBorder" => array(
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
			"formFieldRadius" => array(
				"type"    => "object",
				"default" => array(
					'lg' => [
						"isLinked" => true,
						"unit"     => "px",
						"value"    => ''
					]
				)
			),

			//booking button
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
			"formBtnBorder" => array(
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
			"formBtnRadius" => array(
				"type"    => "object",
				"default" => array(
					'lg' => [
						"isLinked" => true,
						"unit"     => "px",
						"value"    => ''
					]
				)
			),
			//Booking Info
			'infoTypo' => [
				'type' => 'object',
				'default' => [
					'size' => ['lg' => '', 'unit' => 'px'],
					'spacing' => ['lg' => '', 'unit' => 'px'],
					'height' => ['lg' => '', 'unit' => 'px'],
					'transform' => '',
					'weight' => ''
				]
			],
			'infoColor' => array(
				'type'    => 'string',
				'default' => '',
			),
			"infoSpacing" => array(
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

		return apply_filters('rtcl_listing_booking_block_attributes', $attributes);
	}

	public function register_block()
	{
		register_block_type_from_metadata(
			RTCL_ELB_PATH . 'app/Blocks/listing-booking',
			[
				'render_callback' => [$this, 'render_block'],
				'attributes'      => $this->block_attributes(),
			]
		);
	}

	public function render_block($attributes)
	{
		// Set the template style
		$template_style = 'listing-single/listing-booking';

		// Prepare data for template rendering
		$data = [
			'template'              => $template_style,
			'settings'              => $attributes,
			'listing'               => $this->listing,
			'default_template_path' => rtclElb()->get_plugin_block_template_path(),
		];

		// Allow modification of block data through a filter
		$data = apply_filters('rtcl_block_listing_booking_data', $data);

		// Start output buffering
		ob_start();

		// Get the template content
		Functions::get_template($data['template'], $data, '', $data['default_template_path']);

		// Get the buffered content and clean the buffer
		return ob_get_clean();
	}
}
