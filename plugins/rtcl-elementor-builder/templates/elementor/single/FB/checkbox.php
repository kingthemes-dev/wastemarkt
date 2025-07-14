<?php
/**
 * @author     RadiusTheme
 *
 * @version    1.0.0
 *
 * @var object  $field
 * @var string $show_icon
 * @var array $instance;
 */

if ( empty( $field['value'] ) ) {
	return;
}

?>

<div class="rtcl-fb-element rtcl-fb-<?php echo esc_attr( $field['element'] ); ?>">
	<div class="rtcl-icon-label-wrapper">
		<?php if ( $show_icon && ! empty( $instance['rtcl_fbdata_list_main_icon'] ) && ! empty( $field['icon'] ) ) { ?>
			<div class="rtcl-field-icon"><i class="<?php echo esc_attr( $field['icon']['class'] ); ?>"></i></div>
		<?php } ?>
		<h3 class="rtcl-list-item-heading"><?php echo esc_html( $field['label'] ); ?></h3>
	</div>
	<ul class="rtcl-list-group rtcl-list-group-flush custom-field-properties">
		<?php foreach ( $field['value'] as $value ) : ?>
		<li class="rtcl-list-group-item rtcl-field-checkbox rtcl-field-slug-amenities cfp-value ">
			<?php if ( ! empty( $instance['rtcl_fbdata_list_item_show_icon'] && $value['icon'] ) ) { ?>
			<span class="list-icon">
				<i class="<?php echo esc_html( $value['icon'] ); ?>"></i>
			</span>
			<?php } if ( ! empty( $value['label'] ) ) { ?>
			<span class="rtcl-list-text">
				<?php echo esc_html( $value['label'] ); ?>
			</span>
			<?php } ?>
		</li>
		<?php endforeach; ?>
	</ul>
</div>