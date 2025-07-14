<?php

namespace RtclElb\Blocks;

use RtclElb\Abstracts\BlockBase;
use Rtcl\Helpers\Functions;
use RtclElb\Traits\BlockSingleListingTraits;
use RtclElb\Traits\Singleton;

class ListingVideo extends BlockBase
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
			'videoWidth'   => array(
				'type'    => 'object',
				'default' => [
					'lg' => 100,
					'unit' => '%'
				],
			),
			'videoHeight'   => array(
				'type'    => 'object',
				'default' => [
					'lg' => 400,
					'unit' => 'px'
				],
			),
		] + parent::common_attributes();

		return apply_filters('rtcl_listing_video_block_attributes', $attributes);
	}

	public function register_block()
	{
		register_block_type(
			RTCL_ELB_PATH . 'app/Blocks/listing-video',
			[
				'render_callback' => [$this, 'render_block'],
				'attributes'      => $this->block_attributes(),
			]
		);
	}

	public function render_block($attributes)
	{
		$this->attributes = $attributes;
		$template_style = 'listing-single/listing-video';
		$data = [
			'template'              => $template_style,
			'settings'              => $attributes,
			'listing'               => $this->listing,
			'default_template_path' => rtclElb()->get_plugin_block_template_path(),
		];
		$data = array_merge($data, $this->get_the_video());
		$data = apply_filters('rtcl_block_listing_video_data', $data);
		ob_start();
		Functions::get_template($data['template'], $data, '', $data['default_template_path']);
		return ob_get_clean();
	}

	public function get_the_video()
	{
		$data = ['videos' => []];
		$video_urls = [];
		if (!Functions::is_video_urls_disabled() && !empty($this->listing)) {
			$video_urls = get_post_meta($this->listing->get_id(), '_rtcl_video_urls', true);
			$video_urls = !empty($video_urls) && is_array($video_urls) ? $video_urls : [];
		}
		$data['videos'] = $video_urls;
		return $data;
	}
}
