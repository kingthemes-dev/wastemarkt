<?php

namespace RtclJobManager\Controller;

use Rtcl\Helpers\Functions;

class Scripts {

	public static $version;

	/**
	 * @return void
	 */
	public static function init(): void {
		self::$version = ( defined( 'WP_DEBUG' ) && WP_DEBUG ) ? time() : RTCL_JOB_MANAGER_VERSION;
		add_action( 'wp_enqueue_scripts', [ __CLASS__, 'front_end_script' ], 99 );
		add_action( 'admin_enqueue_scripts', [ __CLASS__, 'admin_script' ] );
		add_action( 'wp_enqueue_scripts', [ __CLASS__, 'load_common_script' ] );
		add_action( 'admin_enqueue_scripts', [ __CLASS__, 'load_common_script' ] );
	}

	/**
	 * Front-end script
	 *
	 * @return void
	 */
	public static function front_end_script() {
		global $post;
		wp_register_style(
			'rtcl-job-manager-frontend',
			RTCL_JOB_MANAGER_URL . '/assets/css/frontend.css',
			'',
			self::$version
		);
		wp_enqueue_style( 'rtcl-job-manager-frontend' );

		wp_enqueue_script( 'rtcl-job-manager-frontend', RTCL_JOB_MANAGER_URL . '/assets/js/frontend.js', [ 'jquery', 'select2' ], self::$version );

		wp_localize_script(
			'rtcl-job-manager-frontend',
			'rtclJobManager',
			[
				'ajaxurl'    => admin_url( 'admin-ajax.php' ),
				'nonce'      => wp_create_nonce( 'rtcl_job_submission' ),
				'validation' => [
					'first_name' => [
						'required' => esc_html__( 'Please enter your name', 'rtcl-job-manager' ),
					],
					'birth_date' => [
						'required' => esc_html__( 'Please enter your date of birth', 'rtcl-job-manager' ),
						'minAge'   => esc_html__( 'You must meet the minimum age requirement (15 years) to apply for this job.', 'rtcl-job-manager' ),
						'maxAge'   => esc_html__( 'You must meet the maximum age requirement (45 years) to apply for this job.', 'rtcl-job-manager' ),
					],
					'email'      => [
						'required' => esc_html__( 'Please enter your email address', 'rtcl-job-manager' ),
						'email'    => esc_html__( 'Please enter a valid email address', 'rtcl-job-manager' ),
					],
					'phone'      => [
						'required' => esc_html__( 'Please enter your phone number', 'rtcl-job-manager' ),
						'pattern'  => esc_html__( 'Please enter a valid phone number', 'rtcl-job-manager' ),
					],
					'website'    => [
						'url' => esc_html__( 'Please enter a valid website URL', 'rtcl-job-manager' ),
					],
					'resume'     => [
						'required'  => esc_html__( 'Please upload your CV', 'rtcl-job-manager' ),
						'extension' => esc_html__( 'Only pdf files are allowed', 'rtcl-job-manager' ),
					],
				],
			]
		);
	}

	/**
	 * Admin Scripts
	 *
	 * @return void
	 */
	public static function admin_script( $hook_suffix ) {
		if ( in_array( $hook_suffix, [ 'rtcl_listing_page_rtcl-settings', 'toplevel_page_job-applications' ] ) ) {
			wp_enqueue_style( 'rtcl-public', rtcl()->get_assets_uri( 'css/rtcl-public.min.css' ), [], RTCL_VERSION );
			wp_enqueue_style( 'rtcl-job-manager-admin', RTCL_JOB_MANAGER_URL . '/assets/css/admin.css', '', self::$version );
			wp_enqueue_script( 'rtcl-job-manager-admin', RTCL_JOB_MANAGER_URL . '/assets/js/admin.js', [ 'jquery', 'select2' ], self::$version );
			wp_localize_script(
				'rtcl-job-manager-admin',
				'rtclJobManager',
				[
					'ajaxurl' => admin_url( 'admin-ajax.php' ),
					'jobType' => rtcl_job_manager()->job_type_id(),
				]
			);
		}
	}

	/**
	 * Load Admin Assets
	 *
	 * @return void
	 */
	public static function load_common_script( $hook_suffix ) {
		global $pagenow, $post_type;
		$is_listing_edit = in_array( $pagenow, [ 'edit.php', 'post.php', 'post-new.php' ] ) && rtcl()->post_type == $post_type;

		if ( Functions::is_listing_form_page() || $is_listing_edit ) {
			wp_enqueue_media();
			wp_enqueue_script( 'media-upload' );
			wp_enqueue_style( 'rtcl-job-manager-common', RTCL_JOB_MANAGER_URL . '/assets/css/common.css', '', self::$version );
			wp_enqueue_script( 'jquery-ui-draggable' );
			wp_enqueue_script(
				'rtcl-job-manager-common',
				RTCL_JOB_MANAGER_URL . '/assets/js/common.js',
				[ 'media-upload', 'jquery-ui-draggable' ],
				self::$version,
				TRUE
			);
			wp_localize_script(
				'rtcl-job-manager-common',
				'rtclJobManager',
				[
					'ajaxurl' => admin_url( 'admin-ajax.php' ),
					'nonce'   => wp_create_nonce( 'file_upload_nonce' ),
				]
			);
		}
	}

}
