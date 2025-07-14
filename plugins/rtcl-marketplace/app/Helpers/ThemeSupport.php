<?php

namespace RtclMarketplace\Helpers;

use RtclMarketplace\Hooks\ActionHooks;

class ThemeSupport {

	/**
	 * Current Theme name
	 *
	 * @var string
	 */
	private static $current_theme = '';

	/**
	 * @return void
	 */
	public static function init() {
		self::$current_theme = get_template();
		if ( 'classima' === strtolower( self::$current_theme ) ) {
			self::classima_support();
		} elseif ( 'cl-classified' === strtolower( self::$current_theme ) ) {
			self::cl_classified_support();
		} elseif ( 'radius-directory' === strtolower( self::$current_theme ) ) {
			self::radius_directory_support();
		}
	}

	/**
	 * @return void
	 */
	public static function classima_support() {
		add_action( 'classima_grid_view_after_content', array( ActionHooks::class, 'add_buy_button' ) );
		add_action( 'classima_list_view_after_content', array( ActionHooks::class, 'add_buy_button' ) );
		add_action( 'classima_header_top', array( ActionHooks::class, 'add_wc_notice' ), 5 );
		add_action( 'classima_before_sidebar', array( __CLASS__, 'add_cart_button' ) );
	}

	/**
	 * @return void
	 */
	public static function add_cart_button() {
		if ( \Rtcl\Helpers\Functions::is_listing() ) {
			ActionHooks::add_buy_button();
		}
	}

	/**
	 * @return void
	 */
	public static function cl_classified_support() {
		remove_action( 'rtcl_before_main_content', [ ActionHooks::class, 'add_wc_notice' ], 5 );
		add_action( 'rtcl_before_main_content', [ ActionHooks::class, 'add_wc_notice' ], 25 );
		add_action( 'cl_classified_before_user_information', array( __CLASS__, 'add_cart_button' ) );
	}

	public static function radius_directory_support() {
		remove_action( 'rtcl_before_main_content', [ ActionHooks::class, 'add_wc_notice' ], 5 );
		add_action( 'rtcl_before_main_content', [ ActionHooks::class, 'add_wc_notice' ], 25 );
		add_action( 'rtcl_before_user_info', array( __CLASS__, 'add_cart_button' ) );
	}

}