<?php

/**
 * @author  RadiusTheme
 * @since   5.0
 * @version 5.0.8
 */

use Rtcl\Helpers\Functions;
use Rtcl\Models\Listing;
use RtclElb\Helpers\Fns;

$wrap_class = Fns::get_block_wrapper_class($settings);

do_action('rtcl_builder_before_header');

?>
<div class="<?php echo esc_attr($wrap_class); ?>">
	<div class="rtcl el-single-addon header-inner-wrapper header-<?php echo !empty($settings['style']) ? esc_attr($settings['style']) : ''; ?>">

		<?php if (!empty($settings['showPageTitle'])) { ?>
			<header class="rtcl-listing-header">
				<?php
				$title_text = '';
				if (Fns::is_builder_page_archive()) {
					$title_text = Functions::page_title(false);
				} elseif (Fns::is_builder_page_single()) {
					//$title_text = Functions::page_title(false);
					$_id        = Fns::get_prepared_listing_id();
					$listing    = new Listing($_id);
					$title_text = $listing->get_the_title();
				}
				printf('<%1$s class="rtcl-listings-header-title page-title" >%2$s</%1$s>', esc_html($settings['titleTag']), esc_html($title_text));
				?>
				<?php
				if (Fns::is_builder_page_archive()) {
					/**
					 * Hook: rtcl_archive_description.
					 *
					 * @hooked TemplateHooks::taxonomy_archive_description - 10
					 * @hooked TemplateHooks::listing_archive_description - 10
					 */
					do_action('rtcl_archive_description');
				}
				?>
			</header>
		<?php
		}
		?>

		<div class="breadcrumb-section">
			<?php if (!empty($settings['showBreadcrumb'])) {
				Functions::breadcrumb();
			} ?>
		</div>

	</div>
</div>
<?php
do_action('rtcl_builder_after_header');
