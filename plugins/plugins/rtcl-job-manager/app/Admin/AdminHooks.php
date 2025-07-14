<?php

namespace RtclJobManager\Admin;

use RtclJobManager\Helpers\Functions;

class AdminHooks {

	/**
	 * @return void
	 */
	public static function init(): void {
		add_action( 'admin_menu', [ __CLASS__, 'job_application_menu' ] );
		add_action( 'admin_init', [ __CLASS__, 'delete_job_item' ] );
		add_filter( 'rtcl_licenses', [ __CLASS__, 'license' ], 20 );
		add_action( 'wp_ajax_rtcl_inline_job_search_autocomplete', [ __CLASS__, 'rtcl_inline_search_autocomplete' ] );
	}

	public static function job_application_menu() {
		add_menu_page(
			'Job Applications',
			'Job Applications',
			'manage_options',
			'job-applications',
			[ __CLASS__, 'display_job_applications' ],
			RTCL_JOB_MANAGER_URL . '/assets/images/icon.png',
			6
		);
	}

	public static function display_job_applications() {
		wp_enqueue_script( 'select2' );
		wp_enqueue_script( 'rtcl-admin' );

		$table = new ApplicationTable();
		$table->prepare_items();
		?>
        <div class="wrap">
            <h1><?php echo esc_html__( 'Job Applications', 'rtcl-job-manager' ); ?></h1>
            <form method="post">
                <ul class="subsubsub">
                    <li class="all"><a href="<?php echo admin_url( 'admin.php' ) . '?page=job-applications'; ?>">All
                            <span class="count"></span></a> |
                    </li>
                    <li class="publish"><a
                                href="<?php echo admin_url( 'admin.php' ) . '?page=job-applications&filter_job_status=archived'; ?>">Archived
                            <span class="count"></span></a></li>
                </ul>
				<?php $table->display(); ?>
            </form>
        </div>

        <div class="rtclModaPopup">
            <div class="modalInner">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title"
                            id="exampleModalLabel"><?php echo esc_html__( 'Application Information', 'rtcl-job-manager' ); ?></h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">×</span>
                        </button>
                    </div>
                    <div class="modal-body"></div>
                </div>
            </div>
        </div>

		<?php
	}

	public static function delete_job_item() {
		global $wpdb;

		if ( isset( $_GET['action'] ) && $_GET['action'] === 'delete' && isset( $_GET['application_id'] ) && isset( $_GET['_wpnonce'] ) ) {
			$application_id = absint( $_GET['application_id'] );
			$nonce          = $_GET['_wpnonce'];

			if ( ! wp_verify_nonce( $nonce, 'delete_application_' . $application_id ) ) {
				wp_die( 'Security check failed.' );
			}

			if ( current_user_can( 'manage_options' ) ) {
				$table_name = $wpdb->prefix . 'rtcl_job_applications';
				$wpdb->delete( $table_name, [ 'id' => $application_id ] );
				wp_redirect( admin_url( 'admin.php?page=job-applications' ) );
				exit;
			} else {
				wp_die( 'You do not have permission to delete this application.' );
			}
		}
	}

	/**
	 * @param $licenses
	 *
	 * @return mixed
	 */
	public static function license( $licenses ) {
		$licenses[] = [
			'plugin_file' => RTCL_JOB_MANAGER_PLUGIN_FILE,
			'api_data'    => [
				'key_name'    => 'job_manager_license_key',
				'status_name' => 'job_manager_license_status',
				'action_name' => 'rtcl_manage_job_manager_licensing',
				'product_id'  => 264349,
				'version'     => RTCL_JOB_MANAGER_VERSION,
			],
			'settings'    => [
				'title' => esc_html__( 'Job Manager license key', 'rtcl-job-manager' ),
			],
		];

		return $licenses;
	}

	public static function rtcl_inline_search_autocomplete() {
		$suggestions = [];
		$q           = isset( $_REQUEST['term'] ) ? (string) \Rtcl\Helpers\Functions::clean( wp_unslash( $_REQUEST['term'] ) ) : '';
		if ( ! $q ) {
			wp_send_json_error( esc_html__( "Please provide all field!!", "classified-listing" ) );
		}

		// Query for suggestions
		$args = [
			'post_type'        => rtcl()->post_type,
			'posts_per_page'   => 20,
			'post_status'      => 'publish',
			'orderby'          => 'title',
			'order'            => 'asc',
			'suppress_filters' => false,
			'fields'           => 'ids',
			's'                => $q,
			'meta_query'       => [
				[
					'key'     => 'ad_type',
					'value'   => 'job',
					'compare' => '==',
				],
			],
		];

		$result = new \WP_Query( $args );

		// Initialise suggestions array
		if ( ! empty( $result->posts ) ) {
			foreach ( $result->posts as $post_id ) {
				$post          = get_post( $post_id );
				$suggestions[] = [
					'id'     => $post_id,
					'label'  => ! empty( $post->post_title ) ? $post->post_title : esc_html__( "Empty listing title", 'classified-listing' ),
					'target' => get_the_permalink( $post_id )
				];
			}
		}


		wp_send_json( $suggestions );
	}
}
