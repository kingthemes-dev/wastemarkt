<?php
/**
 * @var number  $id    Random id
 * @var         $orientation
 * @var         $style [classic , modern]
 * @var array   $classes
 * @var int     $active_count
 * @var WP_Term $selected_location
 * @var WP_Term $selected_category
 * @var bool    $radius_search
 * @var bool    $can_search_by_location
 * @var bool    $can_search_by_category
 * @var array   $data
 * @var bool    $can_search_by_listing_types
 * @var bool    $can_search_by_price
 * @var bool    $settings
 * @var bool    $widget_base
 *
 */
use \Elementor\Icons_Manager;
?>
<div class="form-group ws-item ws-button rtcl-action-buttons rtcl-flex rtcl-flex-column">
	<?php if( $settings['fields_label'] ){ ?>
		<label style="visibility: hidden; opacity: 0;"> Submit </label>
	<?php } ?>
	<?php
		ob_start();
			if( ! empty( $settings['button_icon'] )){
				echo '<span class="icon-wrapper">';
				    Icons_Manager::render_icon( $settings['button_icon'], array( 'aria-hidden' => 'true' ) );
				echo '</span>';
			}
		$button_icon = ob_get_clean();
	?>
	<button type="submit" class="btn btn-primary">
		<?php if( ! empty( $settings['button_icon_alignment'] ) && 'left' === $settings['button_icon_alignment'] ){
			echo $button_icon;
		} ?>
		<?php if(!empty($settings['button_text'])){
				echo esc_html($settings['button_text']);
		 } ?>
		<?php if( ! empty( $settings['button_icon_alignment'] ) && 'right' === $settings['button_icon_alignment'] ){
			echo $button_icon;
		} ?>
	</button>
</div>