<?php
/**
 * @package ClassifiedListing/Templates
 * @version 1.5.4
 */

use Rtcl\Controllers\Hooks\TemplateHooks;
use Rtcl\Helpers\Functions;
use RtclJobManager\Helpers\Functions as JobFunction;
use Rtcl\Models\Form\Form;

defined( 'ABSPATH' ) || exit;

get_header( 'listing' );

remove_action( 'rtcl_listing_loop_item', [ TemplateHooks::class, 'loop_item_meta' ], 50 );

$job_archive_page        = JobFunction::job_archive_page();
$show_archive_page_title = Functions::get_option_item( 'rtcl_job_manager_settings', 'show_archive_page_title', '1' );

global $wp_query;
$listing_per_page = Functions::get_option_item( 'rtcl_general_settings', 'listings_per_page', 10 );
$paged            = get_query_var( 'paged' ) ?: 1;

$args = [
	'post_type'      => 'rtcl_listing',
	'posts_per_page' => $listing_per_page,
	'post_status'    => 'publish',
	'paged'          => $paged,
];

$args['meta_query'][] = [
	'key'     => 'ad_type',
	'value'   => 'job',
	'compare' => '=',
];

$category     = ! empty( $_GET['category'] ) ? sanitize_text_field( $_GET['category'] ) : '';
$location     = ! empty( $_GET['location'] ) ? sanitize_text_field( $_GET['location'] ) : '';
$sub_location = ! empty( $_GET['sub_location'] ) ? sanitize_text_field( $_GET['sub_location'] ) : '';
$min_salary   = ! empty( $_GET['min_salary'] ) ? sanitize_text_field( $_GET['min_salary'] ) : '';
$_location_id = ! empty( $sub_location ) ? $sub_location : $location;

$job_form_builder = JobFunction::job_form_builder();
$enable_top_job  = Functions::get_option_item( 'rtcl_job_manager_settings', 'enable_top_job', '1' );

$fg_id      = "job_search_fields_{$job_form_builder}";
$fieldGroup = Functions::get_option_item( 'rtcl_job_manager_settings', $fg_id, '' );


if ( $fieldGroup && is_array( $fieldGroup ) ) {
	foreach ( $fieldGroup as $cf ) {
		$custom_field = ! empty( $_GET[ $cf ] ) ? sanitize_text_field( $_GET[ $cf ] ) : '';
		if ( $custom_field ) {
			$args['meta_query'][] = [
				'key'     => $cf,
				'value'   => $custom_field,
				'compare' => '=',
			];
		}
	}
}


if ( $min_salary ) {
	$args['meta_query'][] = [
		'relation' => 'OR',
		[
			'key'     => 'price',
			'value'   => (int) $min_salary,
			'compare' => '>=',
			'type'    => 'NUMERIC',
		],
		[
			'key'     => '_rtcl_max_price',
			'value'   => (int) $min_salary,
			'compare' => '>=',
			'type'    => 'NUMERIC',
		],
	];
}

if ( $category ) {
	$args['tax_query'][] = [
		'taxonomy' => rtcl()->category,
		'field'    => 'term_id',
		'terms'    => $category,
	];
}

if ( $_location_id ) {
	$args['tax_query'][] = [
		'taxonomy' => rtcl()->location,
		'field'    => 'term_id',
		'terms'    => $_location_id,
	];
}

$query    = new WP_Query( $args );
$wp_query = $query;
do_action( 'before_job_archive_content' );
/**
 * Hook: rtcl_before_main_content.
 *
 * @hooked rtcl_output_content_wrapper - 10 (outputs opening divs for the content)
 */
do_action( 'rtcl_before_main_content' );
JobFunction::breadcrumb();

if ( 'yes' === $show_archive_page_title ) : ?>
    <header class="rtcl-listing-header">
		<?php $page_title = get_the_title( $job_archive_page ); ?>
        <h1 class="rtcl-listings-header-title page-title"><?php echo esc_html( $page_title ); ?></h1>
    </header>
<?php
endif;

/**
 * Hook: rtcl_before_listing_loop.
 *
 * @hooked TemplateHooks::output_all_notices() - 10
 * @hooked TemplateHooks::listings_actions - 20
 */

echo '<div class="rtcl-listings-actions">';
do_action( 'rtcl_before_job_loop' );
echo '</div>';

Functions::listing_loop_start();

/**
 * Prepend listings
 */
if ( $enable_top_job ) {
	do_action( 'rtcl_listing_loop_prepend_data' );
}

if ( rtcl()->wp_query()->have_posts() ) {
	while ( rtcl()->wp_query()->have_posts() ) :
		rtcl()->wp_query()->the_post();
		/**
		 * Hook: rtcl_listing_loop
		 */
		do_action( 'rtcl_listing_loop' );
		Functions::get_template( 'content-rtcl_job', '', '', rtcl_job_manager()->get_plugin_template_path() );

	endwhile;
}

Functions::listing_loop_end();

if ( ! rtcl()->wp_query()->have_posts() ) {
	echo esc_html__( 'No Job were found matching your selection.', 'rtcl-job-manager' );
}

JobFunction::pagination( $wp_query );
wp_reset_postdata();

/**
 * Hook: rtcl_after_main_content.
 *
 * @hooked rtcl_output_content_wrapper_end - 10 (outputs closing divs for the content)
 */
do_action( 'rtcl_after_main_content' );

/**
 * Hook: rtcl_sidebar.
 *
 * @hooked rtcl_get_sidebar - 10
 */
// do_action( 'rtcl_sidebar' );
if ( is_active_sidebar( 'rtcl-job-archive-sidebar' ) ) {
	?>
    <div id="rtcl-sidebar" class="rtcl-sidebar-wrapper">
		<?php dynamic_sidebar( 'rtcl-job-archive-sidebar' ); ?>
    </div>
	<?php
}
do_action( 'after_job_archive_content' );
get_footer( 'listing' );