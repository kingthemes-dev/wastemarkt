<?php

/**
 * @author     RadiusTheme
 * @package    classified-listing/templates
 * @version    1.0.0
 *
 * @var Rtcl\Models\Listing $listing
 */
?>
<!-- Description -->
<div class="rtcl rtcl-listing-description el-single-addon <?php echo !empty($instance['rtcl_drop_cap']) ? 'enabled-drop-cap' : ''; ?> ">
	<?php if (\Elementor\Plugin::$instance->editor->is_edit_mode()) {
		printf(
			'<p>%s</p>',
			esc_html__('This is demo text for editor mode. Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. In eu mi bibendum neque egestas congue quisque. At urna condimentum mattis pellentesque id nibh tortor. Aliquam eleifend mi in nulla posuere. Sed sed risus pretium quam vulputate. Sit amet dictum sit amet justo donec enim diam vulputate.', 'rtcl-elementor-builder')
		);
	} else {
		echo $listing->the_content();
	}
	?>
</div>