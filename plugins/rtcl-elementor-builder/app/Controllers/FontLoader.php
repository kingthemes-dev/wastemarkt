<?php

namespace RtclElb\Controllers;

use RtclElb\Traits\Singleton;

class FontLoader
{
	use Singleton;

	private static $all_fonts = [];

	public function __construct()
	{
		add_filter('render_block', array($this, 'render_block'), 10, 2);
		add_action('rtcl_block_render_block', array($this, 'font_generator'));
		add_action('wp_head', array($this, 'fonts_loader'));
		add_action('admin_enqueue_scripts', array($this, 'fonts_loader'));
	}

	public function render_block($block_content, $block)
	{
		if (isset($block['blockName']) && str_contains($block['blockName'], 'rtcl/')) {
			do_action('rtcl_block_render_block', $block);
			return $block_content;
		}
		return $block_content;
	}

	public function font_generator($block)
	{
		if (isset($block['attrs']) && is_array($block['attrs'])) {
			$attributes = $block['attrs'];
			foreach ($attributes as $key => $value) {
				if (isset($value['family']) && !empty($value['family'])) {
					self::$all_fonts[] = $value['family'];
				}
			}
		}
	}

	public function fonts_loader()
	{
		if (is_array(self::$all_fonts) && count(self::$all_fonts) > 0) {
			$fonts = array_filter(array_unique(self::$all_fonts));
			if (!empty($fonts)) {
				$system = array(
					'Arial',
					'Tahoma',
					'Verdana',
					'Helvetica',
					'Times New Roman',
					'Trebuchet MS',
					'Georgia',
				);

				$gfonts = '';
				$gfonts_attr = ':100,100italic,200,200italic,300,300italic,400,400italic,500,500italic,600,600italic,700,700italic,800,800italic,900,900italic';

				foreach ($fonts as $font) {
					if (!in_array($font, $system, true) && !empty($font)) {
						$gfonts .= str_replace(' ', '+', trim($font)) . $gfonts_attr . '|';
					}
				}

				if (!empty($gfonts)) {
					$query_args = array(
						'family' => $gfonts,
					);
					wp_register_style(
						'rtcl-block-fonts',
						add_query_arg($query_args, '//fonts.googleapis.com/css'),
						array()
					);
					wp_enqueue_style('rtcl-block-fonts');
				}
				$gfonts = '';
			}
		}
	}
}
