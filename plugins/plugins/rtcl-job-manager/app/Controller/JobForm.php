<?php

namespace RtclJobManager\Controller;

use Rtcl\Helpers\Functions;
use Rtcl\Controllers\Hooks\TemplateHooks;
use Rtcl\Helpers\Text;

class JobForm {

	/**
	 * @return void
	 */
	public static function init(): void {
		add_shortcode( 'rtcl_job_form', [ __CLASS__, 'job_form' ] );
	}

	/**
	 * Job Form
	 *
	 * @param $atts
	 *
	 * @return void
	 */
	public static function job_form( $atts ) {
		$users_restriction      = Functions::get_option_item( 'rtcl_job_manager_settings', 'job_allow_register_users' );
		$enable_submission_form = Functions::get_option_item( 'rtcl_job_manager_settings', 'enable_submission_form', 'yes' );
		if ( 'yes' !== $enable_submission_form ) {
			return;
		}
		$job_submission_fields = [
			'birth_date'   => Functions::get_option_item( 'rtcl_job_manager_settings', 'enable_submission_birth_date', 'yes' ),
			'whatsup'      => Functions::get_option_item( 'rtcl_job_manager_settings', 'enable_submission_whatsup', 'yes' ),
			'phone'        => Functions::get_option_item( 'rtcl_job_manager_settings', 'enable_submission_phone', 'yes' ),
			'website'      => Functions::get_option_item( 'rtcl_job_manager_settings', 'enable_submission_website', 'yes' ),
			'location'     => Functions::get_option_item( 'rtcl_job_manager_settings', 'enable_submission_location', 'yes' ),
			'address'      => Functions::get_option_item( 'rtcl_job_manager_settings', 'enable_submission_address', 'yes' ),
			'social'       => Functions::get_option_item( 'rtcl_job_manager_settings', 'enable_submission_social', 'yes' ),
			'cv'           => Functions::get_option_item( 'rtcl_job_manager_settings', 'enable_submission_cv', 'yes' ),
			'cover_letter' => Functions::get_option_item( 'rtcl_job_manager_settings', 'enable_submission_cover_letter', 'yes' ),
		];

		global $listing;

		$default_args = [
			'listing_id'        => $listing->get_id(),
			'user_restrictions' => $users_restriction,
			'location_id'       => $data['sub_location_id'] ?? 0,
			'state_text'        => Text::location_level_first(),
			'city_text'         => Text::location_level_second(),
			'town_text'         => Text::location_level_third(),
		];
		$user_id      = 0;
		if ( is_user_logged_in() ) {
			$user_id      = get_current_user_id();
			$user         = get_userdata( $user_id );
			$user_info    = [
				'user'            => $user,
				'username'        => $user->user_login, // @deprecated
				'email'           => $user->user_email, // @deprecated
				'first_name'      => $user->first_name,  // @deprecated
				'last_name'       => $user->last_name,  // @deprecated
				'phone'           => get_user_meta( $user_id, '_rtcl_phone', true ),
				'whatsapp_number' => get_user_meta( $user_id, '_rtcl_whatsapp_number', true ),
				'website'         => get_user_meta( $user_id, '_rtcl_website', true ),
				'user_locations'  => (array) get_user_meta( $user_id, '_rtcl_location', true ),
				'zipcode'         => get_user_meta( $user_id, '_rtcl_zipcode', true ),
				'address'         => get_user_meta( $user_id, '_rtcl_address', true ),
				'pp_id'           => get_user_meta( $user_id, '_rtcl_pp_id', true ),
			];
			$default_args = array_merge( $default_args, $user_info );
		}

		$args = shortcode_atts(
			$default_args,
			$atts
		);

        $args['form_fields'] = $job_submission_fields;

		wp_enqueue_script( 'rtcl-public-add-post' );

		$existing_job = get_user_meta( $user_id, 'rtcl_applied_job', true );

		if ( ! empty( $existing_job ) && array_key_exists( $listing->get_id(), $existing_job ) ) {
			?>
            <div id="rtcl-job-form-trigger"></div>
            <div class="rtcl-response rtcl-loading">
                <div class="alert alert-danger"><?php echo esc_html__( 'You have already submitted an application for this position.', 'rtcl-job-manager' ); ?></div>
            </div>
			<?php
		} else {
			Functions::get_template( 'job/application-form', $args, '', rtcl_job_manager()->get_plugin_template_path() );
		}
	}
}
