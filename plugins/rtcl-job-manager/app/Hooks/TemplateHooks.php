<?php

namespace RtclJobManager\Hooks;

use Rtcl\Helpers\Functions;
use Rtcl\Models\Listing;
use Rtcl\Controllers\Hooks\TemplateHooks as RtclTemplateHooks;
use RtclJobManager\Helpers\Functions as JobFns;
use RtclPro\Controllers\Hooks\TemplateHooks as ProTemplateHooks;

class TemplateHooks {
	public static $version;

	/**
	 * @return void
	 */
	public static function init(): void {
		add_action( 'wp', [ __CLASS__, 'initHook' ] );
		add_action( 'rtcl_job_single_listing_inner_sidebar', [ __CLASS__, 'add_single_job_inner_sidebar_custom_field' ], 10 );
		add_action( 'rtcl_job_single_listing_inner_sidebar', [ __CLASS__, 'job_manager_after_custom_fields' ], 11 );
		add_action( 'rtcl_job_single_listing_inner_sidebar', [ __CLASS__, 'job_single_listing_sidebar' ], 15 );
		add_filter( 'template_include', [ __CLASS__, 'custom_single_template' ], 9999 );

		//Archive job
		add_action( 'rtcl_job_loop_item', [ RtclTemplateHooks::class, 'loop_item_wrapper_start' ], 10 );
		add_action( 'rtcl_job_loop_item', [ RtclTemplateHooks::class, 'loop_item_listing_title' ], 20 );
		add_action( 'rtcl_job_loop_item', [ RtclTemplateHooks::class, 'loop_item_badges' ], 30 );
//		add_action( 'rtcl_job_loop_item', [ __CLASS__, 'loop_item_meta' ], 50 );
		add_action( 'rtcl_job_loop_item', [ RtclTemplateHooks::class, 'loop_item_meta' ], 50 );
		if ( rtcl()->has_pro() ) {
			add_action( 'rtcl_job_loop_item', [ ProTemplateHooks::class, 'loop_item_listable_fields' ], 48 );
		}
		add_action( 'rtcl_job_loop_item', [ RtclTemplateHooks::class, 'loop_item_excerpt' ], 70 );
		add_action( 'rtcl_job_loop_item', [ RtclTemplateHooks::class, 'listing_price' ], 80 );
		add_action( 'rtcl_job_loop_item', [ RtclTemplateHooks::class, 'loop_item_wrapper_end' ], 100 );

		add_action( 'rtcl_single_job_listing_content', [ RtclTemplateHooks::class, 'add_single_listing_title' ], 5 );
		add_action( 'rtcl_single_job_listing_content', [ RtclTemplateHooks::class, 'add_single_listing_meta' ], 10 );
		add_action( 'rtcl_single_job_listing_content', [ RtclTemplateHooks::class, 'add_single_listing_gallery' ], 30 );
		//Remove Action
		remove_action( 'rtcl_before_main_content', [ RtclTemplateHooks::class, 'breadcrumb' ], 6 );
	}

	public static function loop_item_meta() {
		global $listing;
		if ( empty( $listing ) ) {
			return;
		}
		Functions::get_template( "job/listing/meta", [ 'listing' => $listing ], '', rtcl_job_manager()->get_plugin_template_path() );
	}

	public static function job_single_listing_sidebar( $listing ) {
		$the_actions = [
			'can_add_favourites' => (bool) Functions::get_option_item( 'rtcl_moderation_settings', 'has_favourites', '', 'checkbox' ),
			'social'             => $listing->the_social_share( false ),
			'listing_id'         => $listing->get_id()
		];
		$ths_actions = apply_filters( 'rtcl_job_listing_the_actions', $the_actions );
		//Functions::get_template( "listing/actions", $ths_actions );

		Functions::get_template( 'listing/actions', $ths_actions, '', rtcl_job_manager()->get_plugin_template_path() );
	}

	public static function job_manager_after_custom_fields( $listing ) {
		if ( 2 == JobFns::job_details_style() ) {
			if ( get_the_content( $listing->get_id() ) ) {
				echo "<div class='job-description-content'><hr>";
				echo get_the_content( $listing->get_id() );
				echo "</div>";
			}
		}
	}

	public static function add_single_job_inner_sidebar_custom_field( $listing ) {
		/** @var Listing $listing */

		if ( Functions::isEnableFb() ) {

			$form = $listing->getForm();

			Functions::get_template( "job/listing/c-fields", [
				'form'       => $form,
				'listing_id' => $listing->get_id()
			], '', rtcl_job_manager()->get_plugin_template_path() );
		}
	}

	/**
	 * Init Hooks
	 * Template change runtime
	 *
	 * @return void
	 */
	public static function initHook() {
		if ( ! is_admin() ) {
			global $post;
			if ( ! empty( $post->ID ) ) {
				$ad_type = get_post_meta( $post->ID, 'ad_type', true );
				if ( 'job' === $ad_type ) {
					add_filter(
						'rtcl_locate_template',
						function ( $template, $template_name ) {
							switch ( $template_name ) {
//								case 'listing/c-fields.php':
								case 'listing/actions.php':
								case 'listing/gallery.php':
									$template = RTCL_JOB_MANAGER_PATH . 'templates/job/' . $template_name;

									$theme_file_path = get_stylesheet_directory() . '/classified-listing/job/' . $template_name;
									if ( file_exists( $theme_file_path ) ) {
										$template = $theme_file_path;
									}
									break;
							}

							return $template;
						},
						10,
						2
					);
				}
			}
		}
	}

	/**
	 * Listing Single Template
	 *
	 * @param $template
	 *
	 * @return mixed|string
	 */
	public static function custom_single_template( $template ) {
		global $post;

		if ( ! empty( $post->post_type ) && 'rtcl_listing' == $post->post_type && is_singular( 'rtcl_listing' ) ) {

			$ad_type = get_post_meta( $post->ID, 'ad_type', true );

			if ( rtcl_job_manager()->job_type_id() == $ad_type ) {
				$filename = 'single-rtcl_job.php';
				$template = rtcl_job_manager()->plugin_path() . '/templates/' . $filename;

				$theme_file_path = get_stylesheet_directory() . '/classified-listing/' . $filename;
				if ( file_exists( $theme_file_path ) ) {
					$template = $theme_file_path;
				}
			}
		}

		return $template;
	}
}
