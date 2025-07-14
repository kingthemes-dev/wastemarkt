<?php

namespace RtclJobManager\Hooks;

use Rtcl\Helpers\Functions;
use RtclJobManager\Helpers\Functions as JobFns;


class AjaxHooks {

	public static function init() {
		add_action( 'wp_ajax_rtcl_job_submission', [ __CLASS__, 'job_submission' ] );
		add_action( 'wp_ajax_nopriv_rtcl_job_submission', [ __CLASS__, 'job_submission' ] );

		add_action( 'wp_ajax_load_application_info', [ __CLASS__, 'load_application_info' ] );
		add_action( 'wp_ajax_nopriv_load_application_info', [ __CLASS__, 'load_application_info' ] );

		add_action( 'wp_ajax_update_job_status', [ __CLASS__, 'update_job_status' ] );
		add_action( 'wp_ajax_nopriv_update_job_status', [ __CLASS__, 'update_job_status' ] );

		add_action( 'wp_ajax_rtclJobLocationChange', [ __CLASS__, 'rtclJobLocationChange' ] );
		add_action( 'wp_ajax_nopriv_rtclJobLocationChange', [ __CLASS__, 'rtclJobLocationChange' ] );
	}


	public static function rtclJobLocationChange() {
		$parent_location = ! empty( $_POST['location'] ) ? sanitize_text_field( $_POST['location'] ) : '';
		if ( ! $parent_location ) {
			wp_send_json_error();
		}
		$locations = Functions::get_one_level_locations( $parent_location );

		if ( ! empty( $locations ) ) {
			?>
            <label for="rtcl-job-location"></label><select id="rtcl-job-location" name="sub_location" class="rtcl-select2 rtcl-select rtcl-form-control">
                <option value="">--<?php esc_html_e( 'Select Location', 'rtcl-job-manager' ); ?>--</option>
				<?php
				foreach ( $locations as $location ) {
					$location_id = $location->term_id;
					printf(
						"<option  value='%s'>%s</option>",
						esc_attr( $location_id ),
						$location->name
					);
				}
				?>
            </select>
			<?php
		}
		wp_die();
	}

	public static function load_application_info() {

		$info = $_POST['info'];
		$user_id        = $info['user_id'] ?? '';
		$social         = $info['social'] ?? '';
		$job_id         = $info['job_id'] ?? '';
		$status         = $info['status'] ?? '';
		$application_id = $info['application_id'] ?? '';
		?>

        <div class="application-info">

            <div class="user-info-wrap">
				<?php if ( $user_id ) : ?>
                    <div class="user-image">
						<?php echo get_avatar( $user_id ); ?>
                    </div>
				<?php endif; ?>
                <div class="user-info">

                    <div class="info-group name">
                        <span class="label"><?php echo esc_html__( 'Name', 'rtcl-job-manager' ); ?></span>
                        <span class="info"><?php echo $info['first_name'] . ' ' . $info['last_name']; ?></span>
                    </div>

                    <div class="info-group email">
                        <span class="label"><?php echo esc_html__( 'Email', 'rtcl-job-manager' ); ?></span>
                        <span class="info"><a href="mailto:<?php echo $info['email']; ?>"><?php echo $info['email']; ?></a></span>
                    </div>

                    <div class="info-group phone">
                        <span class="label"><?php echo esc_html__( 'Phone', 'rtcl-job-manager' ); ?></span>
                        <span class="info"><a href="tel:<?php echo $info['phone']; ?>"><?php echo $info['phone']; ?></a></span>
                    </div>

                    <div class="info-group social">
                        <span class="label"><?php echo esc_html__( 'Social', 'rtcl-job-manager' ); ?></span>
						<?php if ( $social && is_array( $social ) ) { ?>
                            <div class="info social-icon">
								<?php
								foreach ( $social as $item => $value ) {
									?>
                                    <a target="_blank" href="<?php echo esc_url( $value ); ?>">
										<?php
										if ( 'twitter' == $item ) {
											echo JobFns::twitterX();
										} else {
											?>
                                            <i class="rtcl-icon rtcl-icon-<?php echo esc_attr( $item ); ?>"></i>
										<?php } ?>
                                    </a>
									<?php
								}
								?>
                            </div>
							<?php
						}
						?>
                    </div>


                    <div class="job-action">
						<?php
						$job_info = rtcl()->factory->get_listing( $job_id );
						?>

                        <div class="info-group job-title">
                            <span class="label"><?php echo esc_html__( 'Job Title', 'rtcl-job-manager' ); ?></span>
                            <span class="info"><a href="<?php echo esc_url( $job_info->get_the_permalink() ); ?>"><?php echo esc_html( $job_info->get_the_title() ); ?></a></span>
                        </div>

                        <div class="info-group job-status">
                            <span class="label"><?php echo esc_html__( 'Job Status', 'rtcl-job-manager' ); ?></span>
                            <span class="info rtclJobStatusLabel <?php echo esc_attr( $status ); ?>" data-id="<?php echo esc_attr( $application_id ); ?>"><?php echo esc_html( $status ); ?></span>
                            <select name="rtcl-job-status" data-application-id="<?php echo esc_attr( $application_id ); ?>">
                                <option value=""><?php echo esc_html__( 'Change Status', 'rtcl-job-manager' ); ?></option>
								<?php
								foreach ( JobFns::job_status() as $value => $name ) {
									printf( '<option value="%s">%s</option>', esc_html( $value ), esc_html( $name ) );
								}
								?>
                            </select>
                        </div>


                    </div>

                </div>
            </div>


            <div class="info-group address">
                <span class="label"><?php echo esc_html__( 'Address', 'rtcl-job-manager' ); ?></span>
                <span class="info"><?php echo $info['address'] . ' ' . $info['zipcode']; ?></span>
            </div>

            <div class="info-group birth-date">
                <span class="label"><?php echo esc_html__( 'Date of Birth', 'rtcl-job-manager' ); ?></span>
                <span class="info"><?php echo $info['birth_date']; ?></span>
            </div>


            <div class="info-group whatsapp_number">
                <span class="label"><?php echo esc_html__( 'Whatsapp number', 'rtcl-job-manager' ); ?></span>
                <span class="info"><?php echo $info['whatsapp_number']; ?></span>
            </div>

            <div class="info-group website">
                <span class="label"><?php echo esc_html__( 'Website', 'rtcl-job-manager' ); ?></span>
                <span class="info"><a target="_blank" href="<?php echo esc_url( $info['website'] ); ?>"><?php echo esc_html( $info['website'] ); ?></a></span>
            </div>

            <div class="info-group cover-letter">
                <span class="label"><?php echo esc_html__( 'Cover letter', 'rtcl-job-manager' ); ?></span>
                <span class="info">
				<?php
				$cover_letter = preg_replace( "/\\\\+'/", "'", $info['cover_letter'] );
				echo nl2br( esc_html( wp_unslash( $cover_letter ) ) );
				?>
				</span>
            </div>

            <br>
			<?php if ( ! empty( $info['resume'] ) ) : ?>
                <div class="resume">
                    <iframe src="<?php echo esc_url( $info['resume'] ); ?>">
                </div>
			<?php endif; ?>
        </div>

		<?php

		// if ( ! empty( $results ) ) {
		// wp_send_json_success( $results );
		// } else {
		// wp_send_json_error( 'no post found' );
		// }

		wp_die();
	}


	public static function job_submission() {

		$errors = new \WP_Error();
		global $wpdb;

		$nonce             = isset( $_REQUEST['nonce'] ) ? sanitize_text_field( $_REQUEST['nonce'] ) : null;
		$users_restriction = Functions::get_option_item( 'rtcl_job_manager_settings', 'job_allow_register_users' );

		if ( ! wp_verify_nonce( $nonce, 'rtcl_job_submission' ) ) {
			$errors->add( 'rtcl_session_error', esc_html__( 'Authentication error!!', 'rtcl-job-manager' ) );
		}

		if ( 'yes' == $users_restriction && ! get_current_user_id() ) {
			$errors->add( 'rtcl_invalid_user', esc_html__( 'You are not authorized to apply for the job.', 'rtcl-job-manager' ) );
		}

		$listing_id = JobFns::sanitize( 'listing_id' );
		$listing    = rtcl()->factory->get_listing( $listing_id );

		if ( ! $listing ) {
			$errors->add( 'rtcl_invalid_listing', esc_html__( 'You are not authorized to apply for the job.', 'rtcl-job-manager' ) );
		}

		$first_name = JobFns::sanitize( 'first_name' );
		$last_name  = JobFns::sanitize( 'last_name' );

		// Sanitize and validate form data.
		$application_data = [
			'birth_date'      => JobFns::sanitize( 'birth_date' ),
			'current_address' => JobFns::sanitize( 'current_address', 'sanitize_textarea_field' ),
			'email'           => JobFns::sanitize( 'email', 'sanitize_email' ),
			'linkedin'        => JobFns::sanitize( 'linkedin', 'esc_url_raw' ),
			'github'          => JobFns::sanitize( 'github', 'esc_url_raw' ),
			'cover_letter'    => JobFns::sanitize( 'cover_letter', 'sanitize_textarea_field' ),
			'phone'           => JobFns::sanitize( 'phone' ),
			'whatsapp_number' => JobFns::sanitize( 'whatsapp_number' ),
			'website'         => JobFns::sanitize( 'website', 'esc_url_raw' ),
			'zipcode'         => JobFns::sanitize( 'zipcode' ),
			'address'         => JobFns::sanitize( 'address', 'esc_textarea' ),
		];

		// Set Location.
		$location = [];
		$state    = JobFns::sanitize( 'location', 'absint' );
		$city     = JobFns::sanitize( 'sub_location', 'absint' );
		$town     = JobFns::sanitize( 'sub_sub_location', 'absint' );
		if ( $state ) {
			$location[] = $state;
		}
		if ( $city ) {
			$location[] = $city;
		}
		if ( $town ) {
			$location[] = $town;
		}
		$application_data['location'] = $location;

		if ( ! empty( $_POST['social_media'] ) ) {
			if ( is_array( $_POST['social_media'] ) ) {
				$_social = [];
				foreach ( $_POST['social_media'] as $_sm_key => $_sm_url ) { //phpcs:ignore
					if ( ! empty( $_sm_url ) ) {
						$_social[ sanitize_text_field( $_sm_key ) ] = esc_url_raw( $_sm_url );
					}
				}
				if ( ! empty( $_social ) ) {
					$application_data['social'] = $_social;
				}
			}
		}

		$resume = JobFns::process_files( 'resume' );

		if ( 'ok' !== $resume['status'] ) {
			$errors->add( 'rtcl_file_error', $resume['message'] );
		} else {
			$application_data['resume'] = $resume['file'];
		}

		if ( is_wp_error( $errors ) && $errors->has_errors() ) {
			wp_send_json_error(
				[
					'error' => $errors->get_error_message(),
				]
			);

			return;
		}

		// Convert form data to JSON.
		$application_json = wp_json_encode( $application_data );
		$table_name       = $wpdb->prefix . 'rtcl_job_applications';
		$user_id          = get_current_user_id() ?? 0;

		$existing_job = get_user_meta( $user_id, 'rtcl_applied_job', true );

		if ( ! empty( $existing_job ) && array_key_exists( $listing_id, $existing_job ) ) {
			wp_send_json_error( [ 'error' => esc_html__( 'You have already submitted an application for this position.', 'rtcl-job-manager' ) ] );
		}
		// Insert the data.
		// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery
		$result = $wpdb->insert(
			$table_name,
			[
				'first_name'       => $first_name,
				'last_name'        => $last_name,
				'application_data' => $application_json,
				'user_id'          => $user_id,
				'listing_id'       => $listing_id,
				'listing_cat'      => $listing->get_parent_category()->term_id ?? '0',
			],
			[
				'%s',
				'%s',
				'%s',
				'%d',
				'%d',
				'%d',
			]
		);

		if ( false === $result ) {
			wp_send_json_error( [ 'error' => esc_html__( 'Failed to submit your application into the database.', 'rtcl-job-manager' ) ] );
		} else {
			$application_id = $wpdb->insert_id;
			do_action( 'rtcl_job_submission_complete', $user_id, $listing_id, $application_id );
			wp_send_json_success( [ 'message' => esc_html__( 'Your application submitted successfully.', 'rtcl-job-manager' ) ] );
		}
	}

	public static function update_job_status() {
		global $wpdb;

		// Get the values from the AJAX request
		$status        = isset( $_POST['status'] ) ? sanitize_text_field( $_POST['status'] ) : '';
		$applicationId = isset( $_POST['applicationId'] ) ? intval( $_POST['applicationId'] ) : 0;

		// Check if the status is valid and the post ID is valid
		if ( ! $applicationId > 0 ) {
			wp_send_json_error(
				[
					'status'  => 'Error',
					'message' => 'Invalid input.',
				]
			);
		}

		// Update the database
		$table_name = $wpdb->prefix . 'rtcl_job_applications';
		$result     = $wpdb->update(
			$table_name,
			[ 'status' => $status ],
			[ 'id' => $applicationId ],
			[ '%s' ],
			[ '%d' ]
		);

		if ( $result !== false ) {
			$sendData = [
				'message' => 'Job status change successfully',
				'value'   => $status,
			];
			wp_send_json_success( $sendData );
		} else {
			$sendData = [
				'message' => 'Failed to update status.',
			];
			wp_send_json_error( $sendData );
		}

		wp_die(); // Terminate immediately and return the response
	}
}
