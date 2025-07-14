<?php
/**
 * The template for displaying product content in the single-rtcl_listing.php template
 *
 * This template can be overridden by copying it to yourtheme/classified-listing/content-single-rtcl_listing.php.
 *
 * @package ClassifiedListing/Templates
 * @version 1.5.56
 */

use Rtcl\Controllers\Hooks\TemplateHooks;
use Rtcl\Helpers\Functions;
use RtclJobManager\Helpers\Functions as JobFunction;

defined( 'ABSPATH' ) || exit;

global $listing;

if ( post_password_required() ) {
	// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
	echo get_the_password_form();

	return;
}
$sidebar_position = Functions::get_option_item( 'rtcl_moderation_settings', 'detail_page_sidebar_position', 'right' );

/**
 * Hook: rtcl_before_single_product.
 *
 * @hooked rtcl_print_notices - 10
 */
do_action( 'rtcl_before_single_listing' );
$show_single_breadcrumb = Functions::get_option_item( 'rtcl_job_manager_settings', 'show_single_breadcrumb', 'yes' );
$job_cf_form            = JobFunction::job_form_builder();

if ( $job_cf_form ) {
	$submission_form_key = "job_submission_{$job_cf_form}";
	$meta_key_from_opt   = Functions::get_option_item( 'rtcl_job_manager_settings', $submission_form_key, '' );
	$is_submission_form  = get_post_meta( $listing->get_id(), $meta_key_from_opt, true );
} else {
	$is_submission_form = get_post_meta( $listing->get_id(), 'rtcl-job-submission-form', true );
}

?>
<div id="rtcl-listing-<?php the_ID(); ?>" <?php Functions::listing_class( 'rtcl-job-details', $listing ); ?>>

	<?php if ( 'yes' == $show_single_breadcrumb ) : ?>
        <div class="row">
            <div class="col-md-12">
				<?php Functions::breadcrumb(); ?>
            </div>
        </div>
	<?php endif; ?>

    <div class="row">
        <!-- Main content -->
        <div class="col-md-12">
            <div class="mb-4 rtcl-single-listing-details">
				<?php
				//do_action( 'rtcl_single_listing_content' );
				do_action( 'rtcl_single_job_listing_content' );
				?>
                <div class="row rtcl-main-content-wrapper">
                    <!--  Content -->
                    <div class="col-md-8">
                        <!-- Description -->
                        <div class="rtcl-listing-description">
                            <div class="job-description-content">
								<?php echo get_the_content( $listing->get_id() ); ?>
                            </div>
							<?php do_action( 'rtcl_single_listing_social_profiles' ); ?>
                        </div>

						<?php
						if ( 'yes' == $is_submission_form ) {
							echo do_shortcode( '[rtcl_job_form]' );
						}
						?>
                    </div>
                    <!--  Inner Sidebar -->
                    <div class="col-md-4">
                        <div class="single-listing-inner-sidebar">
							<?php do_action( 'rtcl_job_single_listing_inner_sidebar', $listing ); ?>
                        </div>
                    </div>
                </div>
            </div>

        </div>

    </div>
</div>

<?php do_action( 'rtcl_after_single_listing' ); ?>
