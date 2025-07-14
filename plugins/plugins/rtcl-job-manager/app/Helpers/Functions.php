<?php

namespace RtclJobManager\Helpers;

use Rtcl\Controllers\Hooks\Filters;
use Rtcl\Helpers\Functions as RtclFns;
use Rtcl\Helpers\Functions as RtclFunctions;

/**
 * Job Function
 */
class Functions {

	/**
	 * @return string
	 */
	public function get_plugin_template_path() {
		return RTCL_JOB_MANAGER_PATH . '/templates/';
	}

	/**
     * Get job archive page id from the settings
     *
	 * @return mixed|null
	 */
	public static function job_archive_page() {
		$page_id = RtclFns::get_option_item( 'rtcl_job_manager_settings', 'job_archive_page', '' );

		return apply_filters( 'rtcl_get_page_id', $page_id );
	}

	/**
     * Get job form builder data from settings
     *
	 * @return bool|int|mixed|null
	 */
	public static function job_form_builder() {
		return RtclFns::get_option_item( 'rtcl_job_manager_settings', 'job_form_builder', '' );
	}

	/**
	 * Sanitize all input
	 *
	 * @param $key
	 * @param $cbf
	 * @param $default
	 *
	 * @return mixed|string
	 */
	public static function sanitize( $key, $cbf = 'sanitize_text_field', $default = '' ) {
		return ! empty( $_POST[ $key ] ) ? $cbf( $_POST[ $key ] ) : $default; //phpcs:ignore
	}

	/**
	 * Sanitize Files
	 *
	 * @param $key
	 *
	 * @return string[]
	 */
	public static function process_files( $key ) {
		if ( ! function_exists( 'wp_handle_upload' ) ) {
			require_once ABSPATH . 'wp-admin/includes/file.php';
		}

		$uploadedfile = $_FILES[ $key ]; // phpcs::ignore
		$data         = [
			'message' => '',
			'file'    => '',
			'status'  => 'error',
		];

		// Define allowed MIME types.
		$allowed_mime_types = [
			'application/pdf',
			'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
		];

		// Check for upload errors.
		if ( $uploadedfile['error'] !== UPLOAD_ERR_OK ) {
			$data['message'] = 'File upload error: ' . $uploadedfile['error'];

			return $data;
		}

		// Validate the file type.
		$file_mime_type = mime_content_type( $uploadedfile['tmp_name'] );
		if ( ! in_array( $file_mime_type, $allowed_mime_types ) ) {
			$data['message'] = 'Invalid file type.';

			return $data;
		}

		// Check the file size (limit to 5MB).
		$max_file_size = 2 * 1024 * 1024; // 5MB in bytes
		if ( $uploadedfile['size'] > $max_file_size ) {
			$data['message'] = 'File size exceeds the limit of 2MB.';

			return $data;
		}

		// Sanitize the file name.
		$uploadedfile['name'] = preg_replace( '/[^a-zA-Z0-9-_\.]/', '_', basename( $uploadedfile['name'] ) );

		$upload_overrides = [ 'test_form' => false ];

		Filters::beforeUpload();
		$movefile = wp_handle_upload( $uploadedfile, $upload_overrides );
		Filters::afterUpload();

		if ( $movefile && ! isset( $movefile['error'] ) ) {
			$data = [
				'message' => 'File uploaded successfully.',
				'file'    => esc_url( $movefile['url'] ),
				'status'  => 'ok',
			];
		} else {
			$data['message'] = 'File upload failed: ' . $movefile['error'];
		}

		return $data;
	}

	/**
	 * Twitter X icon
	 *
	 * @return string
	 */
	public static function twitterX() {
		return '<svg width="17" height="15" viewBox="0 0 17 15" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M5.11618 0.885742H0.0252686L6.03254 8.59624L0.352541 14.8857H2.27981L6.92563 9.74144L10.9344 14.8857H16.0253L9.76491 6.85114L15.1525 0.885742H13.2253L8.87181 5.70594L5.11618 0.885742ZM11.6616 13.4857L2.93436 2.28574H4.38891L13.1162 13.4857H11.6616Z" fill="currentColor"/></svg>';
	}

	/**
	 * Job Status
	 *
	 * @return mixed|null
	 */
	public static function job_status() {
		return apply_filters(
			'rtcl_job_status',
			[
				'interviewed' => 'Interviewed',
				'rejected'    => 'Rejected',
				'selected'    => 'Selected',
				'pending'     => 'Pending',
				'onhold'      => 'On Hold',
				'waiting'     => 'Waiting',
				'archived'    => 'Archived',
			]
		);
	}


	/**
	 * Custom pagination for page template
	 *
	 * @param $query
	 *
	 * @return string|void
	 */
	static function pagination( $query = '' ) {
		if ( ! $query ) {
			global $wp_query;
			$query = $wp_query;
		}
		if ( $query->max_num_pages > 1 ) :
			$big = 999999999; // need an unlikely integer
			?>
            <nav class="rtcl-pagination">
				<?php
				$base   = str_replace( $big, '%#%', esc_url( get_pagenum_link( $big ) ) );
				$format = '?paged=%#%';
				// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
				echo paginate_links(
					apply_filters(
						'rtcl_pagination_args',
						[ // WPCS: XSS ok.
							'base'      => $base,
							'format'    => $format,
							'add_args'  => false,
							'current'   => max( 1, get_query_var( 'paged' ) ),
							'total'     => $query->max_num_pages,
							'prev_text' => '&larr;',
							'next_text' => '&rarr;',
							'type'      => 'list',
							'end_size'  => 1,
							'mid_size'  => 2,
						]
					)
				);
				?>
            </nav>
		<?php
		endif;

		return '';
	}

	/**
     * Breadcrumbs
     *
	 * @param $args
	 *
	 * @return void
	 */
	public static function breadcrumb( $args = [] ) {
		$args = wp_parse_args(
			$args,
			apply_filters(
				'rtcl_breadcrumb_defaults',
				[
					'delimiter'   => '&nbsp;&#47;&nbsp;',
					'wrap_before' => '<nav class="rtcl-breadcrumb rtcl-job-breadcrumb">',
					'wrap_after'  => '</nav>',
					'before'      => '',
					'after'       => '',
					'home'        => esc_html_x( 'Home', 'breadcrumb', 'classified-listing' ),
				]
			)
		);

		$breadcrumbs = new JobBreadcrumb();

		if ( ! empty( $args['home'] ) ) {
			$breadcrumbs->add_crumb( $args['home'], apply_filters( 'rtcl_breadcrumb_home_url', home_url() ) );
		}

		$args['breadcrumb'] = $breadcrumbs->generate();

		do_action( 'rtcl_breadcrumb', $breadcrumbs, $args );

		RtclFns::get_template( 'global/breadcrumb', $args );
	}

	/**
     * Job Details style
     *
	 * @return bool|int|mixed|string|null
	 */
	public static function job_details_style() {
		if ( ! empty( $_GET['style'] ) ) {
			return sanitize_text_field( $_GET['style'] );
		}

		return RtclFunctions::get_option_item( 'rtcl_job_manager_settings', 'job_details_style', '2' );
	}

}
