<?php
/**
 * @author        RadiusTheme
 * @package       classified-listing/templates
 * @version       1.0.0
 *
 * @var boolean $can_add_favourites
 * @var boolean $can_report_abuse
 * @var boolean $social
 * @var integer $listing_id
 */

use Rtcl\Helpers\Functions;
use RtclJobManager\Helpers\Functions as JobFunction;

$users_restriction = Functions::get_option_item( 'rtcl_job_manager_settings', 'job_allow_register_users' );
?>

    <ul class='list-group list-group-flush rtcl-single-listing-action'>
		<?php do_action( 'rtcl_single_action_before_list_item', $listing_id ); ?>
		<?php if ( $can_add_favourites ) : ?>
            <li class="list-group-item"
                id="rtcl-favourites">
				<?php
				// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
				echo Functions::get_favourites_link( $listing_id );
				?>
            </li>
		<?php endif; ?>

		<?php do_action( 'rtcl_single_action_after_list_item', $listing_id ); ?>
		<?php if ( $social ) : ?>
            <li class="list-group-item rtcl-sidebar-social">
				<?php echo wp_kses_post( $social ); ?>
            </li>
		<?php endif; ?>

		<?php
		$job_cf_form   = JobFunction::job_form_builder();
		$external_link = $is_submission_form = '';
		if ( $job_cf_form ) {
			$external_form_key = "job_external_link_{$job_cf_form}";
			$meta_key_from_opt = Functions::get_option_item( 'rtcl_job_manager_settings', $external_form_key, '' );
			$external_link     = get_post_meta( $listing_id, $meta_key_from_opt, true );

			//Job submission
			$submission_form_key          = "job_submission_{$job_cf_form}";
			$submission_meta_key_from_opt = Functions::get_option_item( 'rtcl_job_manager_settings', $submission_form_key, '' );
			$is_submission_form           = get_post_meta( $listing_id, $submission_meta_key_from_opt, true );

			//Job submission
			$apply_now_btn_key      = "job_apply_btn_text_{$job_cf_form}";
			$apply_now_btn_from_opt = Functions::get_option_item( 'rtcl_job_manager_settings', $apply_now_btn_key, '' );

			$apply_now_btn_text = $apply_now_btn_from_opt ? get_post_meta( $listing_id, $apply_now_btn_from_opt, true ) : '';
		} else {
			$external_link      = get_post_meta( $listing_id, 'rtcl-job-external-link', true );
			$apply_now_btn_text = get_post_meta( $listing_id, 'rtcl-apply-btn-text', true );
			$is_submission_form = get_post_meta( $listing_id, 'rtcl-job-submission-form', true );
		}

		$apply_now_btn_text = $apply_now_btn_text ?: __( "Apply Now", "rtcl-job-manager" );


		if ( $external_link ) { ?>
            <li class="list-group-item rtcl-apply-list">
                <a target="_blank" class="rtcl-apply-btn"
                   href="<?php echo esc_url( $external_link ) ?>"><?php echo esc_html( $apply_now_btn_text ); ?></a>
            </li>
			<?php
		} elseif ( 'yes' == $is_submission_form ) { ?>
			<?php if ( 'yes' == $users_restriction && ! is_user_logged_in() ) : ?>
                <li class="list-group-item rtcl-apply-list">
                    <a href="javascript:void(0)" class='rtcl-apply-btn' data-toggle="modal"
                       data-target="#rtcl-job-login-modal"><?php echo esc_html__( 'Login to Apply', 'rtcl-job-manager' ); ?></a>
                </li>
			<?php else : ?>
                <li class="list-group-item rtcl-apply-list">
                    <a id="rtcl-job-apply-btn" class="rtcl-apply-btn"
                       href="#"><?php echo esc_html( $apply_now_btn_text ); ?></a>
                </li>
			<?php endif; ?>
		<?php } ?>

    </ul>

<?php do_action( 'rtcl_single_listing_after_action', $listing_id ); ?>