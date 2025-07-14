<?php

/**
 * TemplateBuilderFrontend Class for Elementor builder
 *
 * TemplateBuilderFrontend Class.
 *
 * @package  RTCL_Elementor_Builder
 * @since    2.0.10
 */

namespace RtclElb\Controllers\Builder;

use RtclElb\Traits\ELTempleateBuilderTraits;

/**
 * TemplateBuilderFrontend Class
 */
class TemplateBuilderFrontend {


	/**
	 * Template builder related traits.
	 */
	use ELTempleateBuilderTraits;

	/**
	 * Initialize function.
	 *
	 * @return void
	 */
	public static function init() {
		add_filter( 'template_include', [ __CLASS__, 'el_template_loader_default_file' ], 100 );
		add_action( 'template_redirect', [ __CLASS__, 'frontend_init' ], 99 );
		add_filter( 'wp_kses_allowed_html', [ __CLASS__, 'override_wp_kses_post' ], 10, 2 );
	}

	/**
	 * Template Overider
	 *
	 * @param string $default_file file name.
	 * @return string
	 */
	public static function el_template_loader_default_file( $default_file ) {
		if ( self::is_builder_page_single() || self::is_builder_page_archive() || self::is_store_page_builder() ) {
			$default_file = rtclElb()->plugin_path() . '/templates/elementor/listing-fullwidth.php';
		}
		return $default_file;
	}

	public static function frontend_init() {
		add_action( 'el_builder_template_content', [ __CLASS__, 'display_template_content' ] );
	}

	/**
	 * Builder content display.
	 *
	 * @return mixed
	 */
	public static function display_template_content() {
		$builder_id = false;

		if ( self::is_builder_page_single() ) {
			$builder_id = self::builder_page_id( 'single' );
		} elseif ( self::is_builder_page_archive() ) {
			$builder_id = self::builder_page_id( 'archive' );
		} elseif ( self::is_store_page_builder() ) {
			$builder_id = self::builder_page_id( 'store-single' );
		}

		if ( $builder_id ) {
			$page_edit_with = self::page_edit_with( $builder_id );
			if ( did_action( 'elementor/loaded' ) && $page_edit_with == 'elementor' ) {
				self::elementor_template_main_content( $builder_id );
			} else {
				self::gutenberg_template_main_content( $builder_id );
			}
		}
	}


	/**
	 * Appl for Elementor.
	 *
	 * @return void
	 */
	public static function elementor_template_main_content( $builder_id ) {
		echo $builder_id ? self::get_builder_content( $builder_id ) : '';
	}

	/**
	 * Apply for Gutenberg.
	 *
	 * @return void
	 */
	public static function gutenberg_template_main_content( $builder_id ) {
		if ( $builder_id ) {
			$output  = '';
			$content = get_the_content( null, false, $builder_id );
			if ( has_blocks( $content ) ) {
				$blocks = parse_blocks( $content );
				foreach ( $blocks as $block ) {
					$output .= render_block( $block );
				}
			} else {
				$content = apply_filters( 'the_content', $content );
				$output  = str_replace( ']]>', ']]&gt;', $content );
			}
			echo $output;
		}
	}

	public static function override_wp_kses_post( $tags, $context ) {
		if ( 'post' === $context ) {
			$tags['iframe'] = [
				'class'			        => true,
				'src'                   => true,
				'style'			        => true,
				'height'                => true,
				'width'                 => true,
				'frameborder'           => true,
				'allowfullscreen'       => true,
				'webkitAllowFullScreen' => true,
				'mozallowfullscreen'    => true,
			];
		}

		return $tags;
	}
}
