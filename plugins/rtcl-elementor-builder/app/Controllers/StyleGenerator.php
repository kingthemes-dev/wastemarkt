<?php

namespace RtclElb\Controllers;

use RtclElb\Traits\ELTempleateBuilderTraits;
use RtclElb\Helpers\Fns;
use RtclElb\Traits\Singleton;

class StyleGenerator
{
	use Singleton;
	use ELTempleateBuilderTraits;
	/**
	 * Init constructor.
	 */
	public function __construct()
	{
		//add_action('wp_head', array($this, 'global_style_generator'));
		add_action('wp_head', array($this, 'template_style_generator'));
		add_action('wp_head', array($this, 'content_style_generator'));
		//add_action('wp_head', array($this, 'embeed_font_generator'));
	}

	/**
	 * Generate style for template.
	 */
	public function template_style_generator()
	{
		global $_wp_current_template_content;
		$style = null;

		if (!empty($_wp_current_template_content)) {
			$blocks = $this->parse_blocks($_wp_current_template_content);
			$blocks = $this->flatten_blocks($blocks);
			$this->loop_blocks($blocks, $style);
		}

		if (!empty($style) && !empty(trim($style))) {
			Fns::rtcl_block_print_header_style('rtcl-template-generator', $style);
		}
	}

	/**
	 * Content Style Generator.
	 */
	public function content_style_generator()
	{
		global $post;
		$style = null;

		if (has_blocks($post) && isset($post->post_content)) {
			$blocks = $this->parse_blocks($post->post_content);
			$blocks = $this->flatten_blocks($blocks);
			$this->loop_blocks($blocks, $style);
		} else {
			$pageBuilderId = self::builder_page_id_by_page();
			$bulderPost = get_post($pageBuilderId);
			if (!empty($bulderPost) && $bulderPost->post_type === 'rtcl_builder') {
				$blocks = $this->parse_blocks($bulderPost->post_content);
				$blocks = $this->flatten_blocks($blocks);
				$this->loop_blocks($blocks, $style);
			}
		}

		if ($style) {
			Fns::rtcl_block_print_header_style('rtcl-block-content-generator', $style);
		}
	}

	/**
	 * Loop Block.
	 *
	 * @param array  $blocks Array of blocks.
	 * @param string $style Style string.
	 */
	public function loop_blocks($blocks, &$style)
	{

		foreach ($blocks as $block) {

			$this->generate_block_style($block, $style);
			if ('core/template-part' === $block['blockName']) {
				$parts = $this->get_template_part_content($block['attrs']);
				$parts = parse_blocks($parts);
				$parts = $this->flatten_blocks($parts);
				$this->loop_blocks($parts, $style);
			}

			if ('core/pattern' === $block['blockName']) {
				$parts = $this->get_pattern_content($block['attrs']);
				$parts = parse_blocks($parts);
				$parts = $this->flatten_blocks($parts);
				$this->loop_blocks($parts, $style);
			}

			if ('core/block' === $block['blockName'] && isset($block['attrs']) && isset($block['attrs']['ref'])) {
				$reusables = get_post($block['attrs']['ref']);
				if ($reusables) {
					$reusables = $this->parse_blocks($reusables->post_content);
					$reusables = $this->flatten_blocks($reusables);
					$this->loop_blocks($reusables, $style);
				}
			}

			do_action_ref_array('rtcl_loop_blocks', array($block, &$style, $this));
		}
	}


	/**
	 * Callback function Flatten Blocks for lower version.
	 *
	 * @param blocks $blocks .
	 *
	 * @return blocks.
	 */
	public function flatten_blocks($blocks)
	{
		return _flatten_blocks($blocks);
	}

	/**
	 * Generate Block Style.
	 *
	 * @param array  $block Detail of block.
	 * @param string $style Style string.
	 */
	public function generate_block_style($block, &$style)
	{
		if (isset($block['blockName']) && str_contains($block['blockName'], 'rtcl/')) {
			do_action('rtcl_block_render_block', $block);
			if (isset($block['attrs']['blockCSS'])) {
				$get_style = $this->get_block_style($block['attrs']['blockCSS']);
			}
		}

		if (!empty($get_style)) {
			$style    .= $get_style;
		}
	}

	/**
	 * Get Pattern Content.
	 *
	 * @param array $attributes Attributes.
	 */
	public function get_pattern_content($attributes)
	{
		$content = '';

		if (isset($attributes['slug'])) {
			$block   = \WP_Block_Patterns_Registry::get_instance()->get_registered($attributes['slug']);
			$content = isset($block) ? $block['content'] : $content;
		}

		return $content;
	}

	/**
	 * Get Template Part Content.
	 *
	 * @param array $attributes Attributes.
	 */
	public function get_template_part_content($attributes)
	{
		$template_part_id = null;
		$area             = \WP_TEMPLATE_PART_AREA_UNCATEGORIZED;
		return Fns::rtcl_template_part_content($attributes, $template_part_id, $area);
	}

	/**
	 * Get Block Style.
	 *
	 * @param array  $style Block Attribute.
	 */
	public function get_block_style($style)
	{
		$css = null;
		if (isset($style['desktop']) && strlen($style['desktop']) > 0) {
			$css .= $style['desktop'];
		}
		if (isset($style['tablet']) && strlen($style['tablet']) > 0) {
			$css .= sprintf(
				'@media all and (max-width: 1024px) {%1$s}',
				$style['tablet']
			);
		}
		if (isset($style['mobile']) && strlen($style['mobile']) > 0) {
			$css .= sprintf(
				'@media all and (max-width: 767px) {%1$s}',
				$style['mobile']
			);
		}
		if (isset($style['customCss']) && strlen($style['customCss']) > 0) {
			$css .= $style['customCss'];
		}
		return $css;
	}

	/**
	 * Parse Guten Block.
	 *
	 * @param string $content the content string.
	 * @since 1.1.0
	 */
	public function parse_blocks($content)
	{
		global $wp_version;

		return (version_compare($wp_version, '5', '>=')) ? parse_blocks($content) : parse_blocks($content);
	}
}
