<?php

namespace RtclJobManager\Controller;

use Rtcl\Helpers\Functions;
use RtclJobManager\Helpers\Functions as JobFunction;

class JobArchive {

	/**
	 * @return void
	 */
	public static function init(): void {
		add_filter( 'template_include', [ __CLASS__, 'template_loader' ] );
		add_filter( 'body_class', [ __CLASS__, 'body_class' ] );
		add_filter( 'display_post_states', [ __CLASS__, 'add_display_post_states' ], 10, 2 );
	}

	public static function add_display_post_states( $post_states, $post ) {
		$job_archive_page = JobFunction::job_archive_page();

		if ( $post && $post->ID == $job_archive_page ) {
			$post_states[] = "Job Page";
		}

		return $post_states;
	}

	public static function body_class( $classes ) {
		$job_archive_page             = JobFunction::job_archive_page();
		$job_archive_sidebar_position = Functions::get_option_item( 'rtcl_job_manager_settings', 'job_archive_sidebar_pos', '' );

		if ( $job_archive_page && is_page( $job_archive_page ) ) {
			$classes[] = 'rtcl rtcl-job-archive';

			$classes[] = 'job-' . $job_archive_sidebar_position;
		}

		return $classes;
	}

	public static function template_loader( $template ) {
		$job_archive_page = JobFunction::job_archive_page();

		if ( $job_archive_page && is_page( $job_archive_page ) ) {
			add_filter( 'rtcl_listing_orderby_options', function ( $options ) {
				unset( $options['price-asc'] );
				unset( $options['price-desc'] );

				return $options;
			} );

			add_filter( 'rtcl_top_listings_query_args', function ( $query_args ) {
				$query_args['meta_query'][] = [
					'key'     => 'ad_type',
					'value'   => 'job',
					'compare' => '=',
				];

				return $query_args;
			} );

			$filename = 'archive-rtcl_job.php';
			$template = rtcl_job_manager()->plugin_path() . '/templates/' . $filename;

			$theme_file_path = get_stylesheet_directory() . '/classified-listing/' . $filename;
			if ( file_exists( $theme_file_path ) ) {
				$template = $theme_file_path;
			}

		}

		return $template;
	}
}
