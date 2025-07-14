<?php

/**
 * @var array $data
 * @var Filter $object
 */

use Rtcl\Helpers\Functions;
use RtclElb\Helpers\Fns;

$wrap_class = Fns::get_block_wrapper_class($settings, 'elementor-widget rtcl-block-widget-filter');
?>
<?php if (!empty($object)) : ?>
	<div class="<?php echo esc_attr($wrap_class); ?>">
		<div class="rtcl-widget-filter-wrapper ">
			<div id="rtcl-widget-filter" class="widget rtcl rtcl-widget-filter-class">

				<?php if ($settings['showFilterTitle'] && !empty($settings['filterTitle'])) : ?>
					<h3><?php echo esc_html($settings['filterTitle']); ?></h3>
				<?php endif ?>

				<div class="panel-block">
					<?php do_action('rtcl_widget_before_filter_form', $object, $data) ?>
					<form class="rtcl-filter-form" action="<?php echo esc_url(Functions::get_filter_form_url()) ?>">
						<?php do_action('rtcl_widget_filter_form_start', $object, $data) ?>
						<div class="ui-accordion">
							<?php do_action('rtcl_widget_filter_form', $object, $data); ?>
						</div>
						<?php do_action('rtcl_widget_filter_form_end', $object, $data)
						?>
					</form>
					<?php do_action('rtcl_widget_after_filter_form', $object, $data) ?>
				</div>

			</div>
		</div>
	</div>
<?php endif; ?>