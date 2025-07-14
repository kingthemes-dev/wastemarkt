<?php

namespace RtclElb\Blocks;

use RtclElb\Abstracts\BlockBase;
use Rtcl\Helpers\Functions;
use RtclElb\Traits\BlockSingleListingTraits;
use RtclElb\Traits\Singleton;

class SocialProfiles extends BlockBase
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
			'showLabel' => array(
				'type' => 'boolean',
				'default' => true
			),

			'labelText' => array(
				'type' => 'string',
				'default' => 'Social Profiles:'
			),

			'showFacebook' => array(
				'type' => 'boolean',
				'default' => true
			),
			'showTwitter' => array(
				'type' => 'boolean',
				'default' => true
			),
			'showYoutube' => array(
				'type' => 'boolean',
				'default' => true
			),
			'showInstagram' => array(
				'type' => 'boolean',
				'default' => true
			),
			'showLinkedin' => array(
				'type' => 'boolean',
				'default' => true
			),
			'showPinterest' => array(
				'type' => 'boolean',
				'default' => true
			),
			'showReddit' => array(
				'type' => 'boolean',
				'default' => true
			),


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

			'iconColor' => array(
				'type'    => 'string',
				'default' => '',
			),
			'iconHoverColor' => array(
				'type'    => 'string',
				'default' => '',
			),
			'iconSize' => array(
				'type'    => 'number',
				'default' => '',
			),
			'iconSpacing' => array(
				'type'    => 'object',
				'default' => [
					'lg' => ''
				],
			),
			'iconAlign' => array(
				'type'    => 'object',
				'default' => [
					'lg' => ''
				],
			),

		] + parent::common_attributes();

		return apply_filters('rtcl_social_profiles_block_attributes', $attributes);
	}

	public function register_block()
	{
		register_block_type(
			RTCL_ELB_PATH . 'app/Blocks/social-profiles',
			[
				'render_callback' => [$this, 'render_block'],
				'attributes'      => $this->block_attributes(),
			]
		);
	}

	public function render_block($attributes)
	{
		$template_style = 'listing-single/social-profiles';
		$data = [
			'template'              => $template_style,
			'settings'              => $attributes,
			'listing'               => $this->listing,
			'default_template_path' => rtclElb()->get_plugin_block_template_path(),
		];
		$data = apply_filters('rtcl_block_listing_social_profile_data', $data);
		ob_start();
		Functions::get_template($data['template'], $data, '', $data['default_template_path']);
		return ob_get_clean();
	}
}
