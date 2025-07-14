<?php

namespace RtclJobManager\Hooks;

use Rtcl\Controllers\Hooks\TemplateHooks;
use Rtcl\Helpers\Functions;
use Rtcl\Helpers\Text;
use Rtcl\Resources\Options;
use RtclJobManager\Helpers\Functions as JobFunction;

class ActionHooks {

	/**
	 * @return void
	 */
	public static function init() {
		add_action( 'rtcl_listing_query', [ __CLASS__, 'modify_listing_query' ] );
		add_action( 'save_post', [ __CLASS__, 'job_save_posts' ], 10, 2 );

		add_action( 'rtcl_job_loaction', [ __CLASS__, 'edit_account_form_location_field' ] );
		add_action( 'rtcl_job_social_profile', [ __CLASS__, 'edit_account_form_social_profile_field' ] );
		add_action( 'rtcl_listing_loop_item', [ __CLASS__, 'loop_item_meta' ], 50 );
		add_action( 'rtcl_before_job_loop', [ __CLASS__, 'result_count' ], 10 );
		add_action( 'rtcl_before_job_loop', [ TemplateHooks::class, 'catalog_ordering' ], 20 );
		add_action( 'rtcl_job_submission_complete', [ __CLASS__, 'job_submission_complete' ], 10, 3 );

		add_action( 'quick_edit_custom_box', [ __CLASS__, 'job_quick_edit' ], 10, 2 );
		add_action( 'rtcl_listing_form_after_save_or_update', [ __CLASS__, 'rtcl_listing_form_after_save_or_update' ] );
		add_action( 'rtcl_cron_move_listing_publish_to_expired', [ __CLASS__, 'remove_listing_id_from_user' ] );

		if ( rtcl()->has_pro() ) {
			add_action( 'rtcl_before_job_loop', [ \RtclPro\Controllers\Hooks\TemplateHooks::class, 'view_switcher' ], 30 );
		}

		add_action( 'after_job_archive_content', [ __CLASS__, 'after_job_archive_content' ] );

		add_filter( 'rtcl_account_menu_items', [ __CLASS__, 'add_my_job_menu_item_at_account_menu' ] );
		add_filter( 'rtcl_my_account_endpoint', [ __CLASS__, 'add_my_job_end_points' ] );
		add_action( 'rtcl_account_my-jobs_endpoint', [ __CLASS__, 'account_my_bookings_endpoint' ] );
	}

	public static function after_job_archive_content() {
		$theme = get_option( 'current_theme' );
		if ( 'Hello Elementor' == $theme ) {
			echo '</div>';
		}
	}

	public static function account_my_bookings_endpoint() {
		// Process output

		$paged = get_query_var( 'paged' ) ?: 1;

		$user_id       = get_current_user_id();
		$user_job_info = get_user_meta( $user_id, 'rtcl_applied_job', true );

		$job_ids = is_array( $user_job_info ) ? array_keys( $user_job_info ) : '';
		if ( $job_ids ) {
			$qargs = [
				'post_type'      => 'rtcl_listing',
				'posts_per_page' => - 1,
				'post_status'    => [ 'publish', 'pending' ],
				'post__in'       => $job_ids,
				'paged'          => $paged,
				'meta_query'     => [
					[
						'key'     => 'ad_type',
						'value'   => 'job',
						'compare' => '==',
					],
				],
			];

			$rtcl_query = new \WP_Query( $qargs );

			$args = [
				'rtcl_query'   => $rtcl_query,
				'applications' => $user_job_info,
			];
		} else {
			$args = [
				'no_application' => true,
			];
		}

		Functions::get_template( 'myaccount/my-jobs', $args, '', rtcl_job_manager()->get_plugin_template_path() );
	}

	public static function add_my_job_menu_item_at_account_menu( $items ) {
		$position = array_search( 'edit-account', array_keys( $items ) );

		// $booking = \RtclBooking\Helpers\Functions::get_all_bookings();

		$menu['my-jobs'] = apply_filters( 'rtcl_my_jobs_title', esc_html__( 'Applied Jobs', 'rtcl-booking' ) );

		if ( $position > - 1 ) {
			Functions::array_insert( $items, $position, $menu );
		}

		return $items;
	}

	public static function add_my_job_end_points( $endpoints ) {
		$endpoints['my-jobs'] = 'my-jobs';// Functions::get_option_item( 'rtcl_booking_settings', 'myaccount_booking_endpoint', 'my-bookings' );

		return $endpoints;
	}


	public static function job_save_posts( $post_id, $post ) {

		if ( ! wp_verify_nonce( isset( $_REQUEST[ rtcl()->nonceId ] ) ? $_REQUEST[ rtcl()->nonceId ] : null, rtcl()->nonceText ) ) {
			return $post_id;
		}

		if ( ! isset( $_POST['post_type'] ) ) {
			return $post_id;
		}

		if ( rtcl()->post_type != $post->post_type ) {
			return $post_id;
		}

		if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
			return $post_id;
		}

		if ( ! current_user_can( 'edit_' . rtcl()->post_type, $post_id ) ) {
			return $post_id;
		}

		$job_deadline = get_post_meta( $post_id, 'job_deadline', true );

		if ( ! empty( $job_deadline ) ) {
			self::update_job_expiry_date( $post_id, $job_deadline );
		}
	}

	public static function job_quick_edit( $column_name, $screen ) {
		if ( 'rtcl_listing' !== $screen || 'expiry_date' !== $column_name || ! isset( $_GET['post_type'] ) || 'rtcl_listing' !== $_GET['post_type'] ) {
			return;
		}
		wp_nonce_field( rtcl()->nonceText, rtcl()->nonceId );
	}

	public static function rtcl_listing_form_after_save_or_update( $listing ) {
		$post_id      = $listing->get_id();
		$job_deadline = get_post_meta( $post_id, 'job_deadline', true );
		if ( ! empty( $job_deadline ) ) {
			self::update_job_expiry_date( $post_id, $job_deadline );
		}
	}

	public static function remove_listing_id_from_user( $listing_id ) {

		$batch_size = 50;
		$offset     = 0;
		while ( true ) {
			$args = [
				'meta_key' => 'rtcl_applied_job',
				'number'   => $batch_size,
				'offset'   => $offset,
				'fields'   => [ 'ID' ],
			];

			$users = get_users( $args );

			// Break if there are no more users.
			if ( empty( $users ) ) {
				break;
			}

			// Loop through each user.
			foreach ( $users as $user ) {
				$user_id       = $user->ID;
				$user_job_info = get_user_meta( $user_id, 'rtcl_applied_job', true );

				// Check if the user has the job ID we want to remove.
				if ( isset( $user_job_info[ $listing_id ] ) ) {
					unset( $user_job_info[ $listing_id ] ); // Remove the job ID.

					// Update the user meta with the modified array.
					update_user_meta( $user_id, 'rtcl_applied_job', $user_job_info );
				}
			}

			// Increase the offset for the next batch.
			$offset += $batch_size;
		}
	}

	public static function update_job_expiry_date( $post_id, $job_deadline = '' ) {

		// Create a DateTime object from the input date.
		$dateTime = new \DateTime( $job_deadline );

		// Extract the various components.
		$aa = $dateTime->format( 'Y' ); // Year
		$mm = $dateTime->format( 'm' ); // Month
		$jj = $dateTime->format( 'd' ); // Day
		$hh = $dateTime->format( 'H' ); // Hour
		$mn = $dateTime->format( 'i' ); // Minute
		$ss = $dateTime->format( 's' ); // Second

		// Format the expiry date.
		$expiry_date = "$aa-$mm-$jj $hh:$mn:$ss";

		update_post_meta( $post_id, 'expiry_date', $expiry_date );
	}


	public static function job_submission_complete( $user_id, $job_id, $application_id ) {
		if ( is_user_logged_in() ) {
			$userJobInfo = get_user_meta( $user_id, 'rtcl_applied_job', true );
			$dataToSave  = [ $job_id => $application_id ];
			if ( ! empty( $userJobInfo ) && is_array( $userJobInfo ) ) {
				if ( ! array_key_exists( $job_id, $userJobInfo ) ) {
					$userJobInfo[ $job_id ] = $application_id;
					update_user_meta( $user_id, 'rtcl_applied_job', $userJobInfo );
				}
			} else {
				update_user_meta( $user_id, 'rtcl_applied_job', $dataToSave );
			}
		}
	}

	/**
	 * Overwrite job meta data
	 *
	 * @return void
	 */
	public static function loop_item_meta() {
		global $listing;
		if ( empty( $listing ) ) {
			return;
		}

		if ( 'job' !== $listing->get_ad_type() ) {
			return;
		}

		Functions::get_template( 'job/listing/meta', [ 'listing' => $listing ], '', rtcl_job_manager()->get_plugin_template_path() );
	}

	/**
	 * Output the result count text (Showing x - x of x results).
	 */
	public static function result_count() {

		global $wp_query;
		$args = [
			'total'    => $wp_query->found_posts,
			'per_page' => $wp_query->get( 'posts_per_page' ),
			'current'  => max( 1, get_query_var( 'paged' ) ),
		];

		Functions::get_template( 'listing/loop/result-count', $args );
	}

	/**
	 * Modify Listing Query.
	 *
	 * @param $query
	 *
	 * @return void
	 */
	public static function modify_listing_query( $query ) {

		$job_separation   = Functions::get_option_item( 'rtcl_job_manager_settings', 'job_separation', 'yes' );
		$job_archive_page = JobFunction::job_archive_page();

		if ( 'yes' == $job_separation ) {
			$meta_query = $query->get( 'meta_query' );

			// Ensure it's an array.
			if ( ! is_array( $meta_query ) ) {
				$meta_query = [];
			}

			// Append your custom meta query.
			$meta_query[] = [
				'key'     => 'ad_type',
				'value'   => 'job',
				'compare' => '!=',
			];

			// Set the modified meta_query.
			$query->set( 'meta_query', $meta_query );
		}

		if ( $query->is_page( $job_archive_page ) ) {
			add_filter(
				'rtcl_top_listings_query_args',
				function ( $args ) {
					$args['meta_query']['relation'] = 'AND';
					$args['meta_query'][]           = [
						'key'     => 'ad_type',
						'value'   => 'job',
						'compare' => '==',
					];

					return $args;
				}
			);

			// Order by.

			if ( isset( $_GET['orderby'] ) ) { //phpcs:ignore WordPress.Security.NonceVerification.Recommended
				$parts = explode( '-', sanitize_text_field( wp_unslash( $_GET['orderby'] ) ) ); //phpcs:ignore WordPress.Security.NonceVerification.Recommended
				$query->set( 'orderby', $parts[0] ?? 'title' );
				$query->set( 'order', $parts[1] ?? 'DESC' );
			}
		}
	}

	public static function edit_account_form_location_field() {
		$user_id        = get_current_user_id();
		$location_id    = $sub_location_id = 0;
		$user_locations = (array) get_user_meta( $user_id, '_rtcl_location', true );
		$zipcode        = get_user_meta( $user_id, '_rtcl_zipcode', true );
		$address        = get_user_meta( $user_id, '_rtcl_address', true );
		$state_text     = Text::location_level_first();
		$city_text      = Text::location_level_second();
		$town_text      = Text::location_level_third();
		?>
        <div class="rtcl-form-group">
            <div class="rtcl-field-col" id="rtcl-location-row">
                <label for="rtcl-location" class="rtcl-field-label">
					<?php echo esc_html( $state_text ); ?>
                    <span class="require-star">*</span>
                </label>
                <select id="rtcl-location" name="location"
                        class="rtcl-select2 rtcl-select rtcl-form-control rtcl-map-field">
                    <option value="">--<?php esc_html_e( 'Select state', 'rtcl-job-manager' ); ?>--</option>
					<?php
					$locations = Functions::get_one_level_locations();
					if ( ! empty( $locations ) ) {
						foreach ( $locations as $location ) {
							$slt = '';
							if ( in_array( $location->term_id, $user_locations ) ) {
								$location_id = $location->term_id;
								$slt         = ' selected';
							}
							echo "<option value='" . esc_attr( $location->term_id ) . "'" . esc_attr( $slt ) . '>' .
							     // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
							     $location->name . '</option>';
						}
					}
					?>
                </select>
            </div>
			<?php
			$sub_locations = [];
			if ( $location_id ) {
				$sub_locations = Functions::get_one_level_locations( $location_id );
			}
			?>
            <div class="rtcl-field-col<?php echo empty( $sub_locations ) ? ' rtcl-hide' : ''; ?>" id="sub-location-row">
                <label class="rtcl-field-label" for='rtcl-sub-location'><?php echo esc_html( $city_text ); ?>
                    <span class="require-star">*</span>
                </label>
                <select id="rtcl-sub-location" name="sub_location"
                        class="rtcl-select2 rtcl-select rtcl-form-control rtcl-map-field">
                    <option value="">--<?php esc_html_e( 'Select location', 'rtcl-job-manager' ); ?>--</option>
					<?php
					if ( ! empty( $sub_locations ) ) {
						foreach ( $sub_locations as $location ) {
							$slt = '';
							if ( in_array( $location->term_id, $user_locations ) ) {
								$sub_location_id = $location->term_id;
								$slt             = ' selected';
							}
							echo "<option value='" . esc_attr( $location->term_id ) . "'" . esc_attr( $slt ) . '>' .
							     // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
							     $location->name . '</option>';
						}
					}
					?>
                </select>
            </div>
			<?php
			$sub_sub_locations = [];
			if ( $sub_location_id ) {
				$sub_sub_locations = Functions::get_one_level_locations( $sub_location_id );
			}
			?>
            <div class="rtcl-field-col<?php echo empty( $sub_sub_locations ) ? ' rtcl-hide' : ''; ?>"
                 id="sub-sub-location-row">
                <label for='rtcl-sub-sub-location' class="rtcl-field-label">
					<?php echo esc_html( $town_text ); ?>
                    <span class="require-star">*</span>
                </label>
                <select id="rtcl-sub-sub-location" name="sub_sub_location"
                        class="rtcl-select2 rtcl-select rtcl-form-control rtcl-map-field">
                    <option value="">--<?php esc_html_e( 'Select location', 'rtcl-job-manager' ); ?>--</option>
					<?php
					if ( ! empty( $sub_sub_locations ) ) {
						foreach ( $sub_sub_locations as $location ) {
							$slt = '';
							if ( in_array( $location->term_id, $user_locations ) ) {
								$slt = ' selected';
							}
							echo "<option value='" . esc_attr( $location->term_id ) . "'" . esc_attr( $slt ) . '>' .
							     // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
							     $location->name . '</option>';
						}
					}
					?>
                </select>
            </div>
            <div class="rtcl-field-col">
                <label for="rtcl-zipcode"
                       class="rtcl-field-label"><?php esc_html_e( 'Zip Code', 'rtcl-job-manager' ); ?></label>
                <input type="text" name="zipcode" value="<?php echo esc_attr( $zipcode ); ?>"
                       class="rtcl-map-field rtcl-form-control" id="rtcl-zipcode"/>
            </div>
<!--            <div class="rtcl-field-col">-->
<!--                <label for="rtcl-address"-->
<!--                       class="rtcl-field-label">--><?php //esc_html_e( 'Address', 'rtcl-job-manager' ); ?><!--</label>-->
<!--                <textarea name="address" rows="3" class="rtcl-map-field rtcl-form-control"-->
<!--                          id="rtcl-address">--><?php //echo esc_textarea( $address ); ?><!--</textarea>-->
<!--            </div>-->
        </div>
		<?php
	}

	public static function edit_account_form_social_profile_field() {
		?>
        <div class="rtcl-form-group rtcl-social-wrap-row">
            <label for="rtcl-social" class="rtcl-field-label">
				<?php esc_html_e( 'Social Profile', 'rtcl-job-manager' ); ?>
            </label>
            <div class="rtcl-field-col">
				<?php
				$social_options = Options::get_social_profiles_list();

				unset( $social_options['youtube'] );
				unset( $social_options['tiktok'] );

				$social_media = get_current_user_id() ? Functions::get_user_social_profile( get_current_user_id() ) : [];
				foreach ( $social_options as $key => $social_option ) {
					echo sprintf(
						'<input type="url" name="social_media[%1$s]" id="rtcl-account-social-%1$s" value="%2$s" placeholder="%3$s" class="rtcl-form-control"/>',
						esc_attr( $key ),
						esc_url( isset( $social_media[ $key ] ) ? $social_media[ $key ] : '' ),
						esc_html( $social_option )
					);
				}
				?>
            </div>
        </div>
		<?php
	}
}
