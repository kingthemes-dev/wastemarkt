<?php

/**
 *
 * @author     RadiusTheme
 * @package    classified-listing/templates
 * @version    1.0.0
 */

use Rtcl\Helpers\Functions;
use Rtcl\Helpers\Pagination;
use RtclPro\Helpers\Fns;
use RtclElb\Helpers\Fns as RtclElbFns;

$wrap_class = RtclElbFns::get_block_wrapper_class($instance, 'rtcl rtcl-listings-sc-wrapper rtcl-elementor-widget rtcl-block-archive-listing');

?>
<div class="<?php echo esc_attr($wrap_class); ?>">

	<div class="rtcl-listings-wrapper">
		<?php
		$class  = '';
		$class .= !empty($view) ? 'rtcl-' . $view . '-view ' : 'rtcl-list-view ';
		$class .= !empty($style) ? 'rtcl-' . $style . '-view ' : 'rtcl-style-1-view ';

		if ('grid' === $view) {
			$class .= !empty($instance['gridColumn']['lg']) ? 'columns-' . $instance['gridColumn']['lg'] . ' ' : ' columns-1';
			$class .= !empty($instance['gridColumn']['md']) ? 'tab-columns-' . $instance['gridColumn']['md'] . ' ' : ' tab-columns-2';
			$class .= !empty($instance['gridColumn']['sm']) ? 'mobile-columns-' . $instance['gridColumn']['sm'] . ' ' : ' mobile-columns-2';
		}

		/**
		 * Hook: rtcl_before_listing_loop.
		 *
		 * @hooked TemplateHooks::output_all_notices() - 10
		 * @hooked TemplateHooks::listing_actions - 20
		 */
		do_action('rtcl_before_listing_loop');

		?>
		<div class="rtcl-listings <?php echo esc_attr($class); ?> ">
			<?php
			if (rtcl()->has_pro() && Fns::is_enable_top_listings() && $top_query->have_posts()) {
				/**
				 * Prepend listings
				 */
				while ($top_query->have_posts()) :
					$top_query->the_post();
					$top_data = [
						'template'              => 'listing-archive/listing-archive/' . $view . '/' . $style,
						'instance'              => $instance,
						'style'                 => $style,
						'item_class'            => 'as-top',
						'default_template_path' => rtclElb()->get_plugin_block_template_path(),
					];
					$top_data = apply_filters('rtcl_block_listing_archive_top_post_data', $top_data);
					Functions::get_template($top_data['template'], $top_data, '', $top_data['default_template_path']);

				endwhile;
				wp_reset_postdata();
			}
			if ($the_query->have_posts()) {
				// General list.
				while ($the_query->have_posts()) :
					$the_query->the_post();

					$content_data = [
						'template'              => 'listing-archive/listing-archive/' . $view . '/' . $style,
						'instance'              => $instance,
						'style'                 => $style,
						'item_class'            => '',
						'default_template_path' => rtclElb()->get_plugin_block_template_path(),
					];
					$content_data = apply_filters('rtcl_block_listing_archive_content_data', $content_data);
					Functions::get_template($content_data['template'], $content_data, '', $content_data['default_template_path']);
				endwhile;
			} elseif (!$top_query->have_posts()) {
				/**
				 * Hook: rtl_no_listings_found.
				 *
				 * @hooked no_listings_found - 10
				 */
				do_action('rtcl_no_listings_found');
			}

			?>
		</div>
		<?php
		/**
		 * Hook: rtcl_after_listing_loop.
		 *
		 * @hooked TemplateHook::pagination() - 10
		 */
		do_action('rtcl_after_listing_loop');

		if (!empty($instance['pagination'])) {
			Pagination::pagination($the_query, true);
		}
		?>
	</div>

</div>