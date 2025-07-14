<?php

/**
 * Traits Elementor builder
 *
 * The Elementor builder.
 *
 * @package  RTCL_Elementor_Builder
 * @since    2.0.10
 */

namespace RtclElb\Traits;

use RtclElb\Helpers\Fns;
use Rtcl\Helpers\Functions;
use RtclElb\Helpers\Fns as ElFns;

trait ELTempleateBuilderTraits {


	/**
	 * Elementor Templeate builder post type
	 *
	 * @var string
	 */
	public static $post_type_tb = 'rtcl_builder';
	/**
	 * Elementor Templeate builder
	 *
	 * @var string
	 */
	public static $template_meta = 'rtcl_tb_template';

	/**
	 * Page builder id.
	 *
	 * @param [type] $type builder type.
	 * @return init
	 */
	public static function builder_page_id( $type, $get_the_id = false ) {
		if ( array_key_exists( $type, self::builder_page_types() ) ) {
			$option_name = self::option_name( $type );
			if ( 'single' === $type ) {
				$get_the_id = $get_the_id ?: get_the_ID();
				$fb_form_id = absint( get_post_meta( $get_the_id, ElFns::template_fb_form_id_key(), true ) );
				if ( $fb_form_id ) {
					$option_name = self::option_name( $type, $fb_form_id );
				}
			}
			$post_id = get_option( $option_name );
			if ( 'publish' === get_post_status( $post_id ) && self::builder_type( $post_id ) === $type ) {
				return $post_id;
			}
		}
		return 0;
	}
	/**
	 * Page builder Page for.
	 *
	 * @return array
	 */
	public static function builder_page_types() {
		$default = [
			'single'  => esc_html__( 'Single', 'rtcl-elementor-builder' ),
			'archive' => esc_html__( 'Archive', 'rtcl-elementor-builder' ),
		];
		if ( Fns::is_has_store() ) {
			$default['store-single'] = esc_html__( 'Store single', 'rtcl-elementor-builder' );
		}
		return $default;
	}
	/**
	 * Option name.
	 *
	 * @param [type] $type Builder type.
	 * @return string
	 */
	public static function option_name( $type, $tbid = null ) {
		$key = self::$template_meta . '_default_' . $type;
		if ( ! empty( $tbid ) ) {
			$key .= '_' . $tbid;
		}
		return $key;
	}

	/**
	 * Get builder type.
	 *
	 * @param [type] $post_id Post id.
	 * @return string
	 */
	public static function builder_type( $post_id ) {
		return get_post_meta( $post_id, self::template_type_meta_key(), true );
	}
	/**
	 * Elementor Templeate builder
	 *
	 * @var string
	 */
	public static function is_builder_page_archive() {
		$builder_id   = self::builder_page_id( 'archive' );
		$builder_type = self::builder_type( $builder_id );
		$is_archive   = 'archive' === $builder_type; // Ticket issue.
		$page_id      = absint( get_the_ID() ? get_the_ID() : ( $_GET['post'] ?? 0 ) );
		if ( 'archive' === self::builder_type( $page_id ) || ( $is_archive && ( is_post_type_archive( rtcl()->post_type ) || Functions::is_listing_taxonomy() ) ) ) {
			return true;
		}
		return false;
	}
	/**
	 * Elementor Templeate builder
	 *
	 * @var string
	 */
	public static function is_builder_page_single() {
		if ( self::is_single_page_builder( 'single', rtcl()->post_type ) ) {
			return true;
		}
		return false;
	}
	/**
	 * Elementor Templeate builder
	 *
	 * @var string
	 */
	public static function is_store_page_builder() {
		if ( Fns::is_has_store() && self::is_single_page_builder( 'store-single', rtclStore()->post_type ) ) {
			return true;
		}
		return false;
	}

	/**
	 * Single Page builder Detection
	 *
	 * @param [type] $type Builder type.
	 * @param [type] $singuler_post_type post type.
	 * @return boolean
	 */
	public static function is_single_page_builder( $type, $singuler_post_type ) {
		$builder_id   = self::builder_page_id( $type );
		$builder_type = self::builder_type( $builder_id );
		$is_single    = $type === $builder_type; // Ticket issue.
		$page_id      = absint( get_the_ID() ? get_the_ID() : ( $_GET['post'] ?? 0 ) );
		if ( self::builder_type( $page_id ) === $type || ( $is_single && is_singular( $singuler_post_type ) ) ) {
			return true;
		}
		return false;
	}
	/**
	 * Elementor Templeate builder
	 *
	 * @var string
	 */
	public static function template_type_meta_key() {
		return self::$template_meta . '_type';
	}
	/**
	 * Elementor Templeate builder
	 *
	 * @var string
	 */
	public static function template_fb_form_id_key() {
		return '_rtcl_form_id';
	}

	/**
	 * Get builder content function
	 *
	 * @param [type]  $template_id builder Template id.
	 * @param boolean $with_css with css.
	 * @return mixed
	 */
	public static function get_builder_content( $template_id, $with_css = false ) {
		return \Elementor\Plugin::instance()->frontend->get_builder_content_for_display( $template_id, $with_css );
	}

	/**
	 * Page builder
	 *
	 * @param int $post_id post id.
	 *
	 * @return string
	 */
	public static function page_edit_with( $post_id ) {
		if ( ! $post_id ) {
			return '';
		}

		$edit_with = get_post_meta( $post_id, '_elementor_edit_mode', true );

		if ( 'builder' === $edit_with ) {
			$edit_by = 'elementor';
		} else {
			$edit_by = 'gutenberg';
		}

		return $edit_by;
	}

	public static function page_edit_btn_text( $btn_text = '' ) {
		return $btn_text == 'elementor' ? 'Edit with elementor' : 'Edit with gutenberg';
	}

	/**
	 * Template builder
	 *
	 * @var boolean
	 */
	public static function is_single() {
		return self::is_page_builder( 'single', is_singular() );
	}
	/**
	 * Template builder
	 *
	 * @var boolean
	 */
	public static function is_archive() {
		return self::is_page_builder( 'archive', is_archive() );
	}

	public static function is_store_single() {
		return self::is_page_builder( 'store-single', is_singular() );
	}

	/**
	 * Builder page detector.
	 *
	 * @param string  $type builder Page type.
	 * @param boolean $is_page page status.
	 * @return boolean
	 */
	public static function is_page_builder( $type, $is_page ) {
		$builder_id   = self::builder_page_id( $type );
		$builder_type = self::builder_type( $builder_id );
		$type_status  = $type === $builder_type; // Ticket issue.
		if ( self::builder_type( get_the_ID() ) === $type || ( $type_status && $is_page ) ) {
			return true;
		}
		return false;
	}


	/**
	 * Page builder id.
	 *
	 * @return init
	 */
	public static function builder_page_id_by_page() {
		$type = apply_filters( 'rtclblock/builder/set/current/page/type', '' );
		return self::builder_page_id( $type );
	}
}
