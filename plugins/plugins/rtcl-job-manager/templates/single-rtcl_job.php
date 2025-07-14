<?php
/**
 *
 * @author     RadiusTheme
 * @package    classified-listing/templates
 * @version    1.0.0
 */

use Rtcl\Helpers\Functions;
use RtclJobManager\Helpers\Functions as JobFns;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}
get_header( 'listing' );
$job_details_style = JobFns::job_details_style()
?>

    <div class="rtcl-wrapper job-style-<?php echo esc_attr($job_details_style) ?>">
		<?php while ( have_posts() ) : ?>
			<?php the_post(); ?>
			<?php Functions::get_template( "job/job-template-$job_details_style", '', '', rtcl_job_manager()->get_plugin_template_path() ); ?>
		<?php endwhile; // end of the loop. ?>
    </div>
<?php
get_footer( 'listing' );
