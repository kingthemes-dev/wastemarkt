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
	<?php if ( $show_icon & ! empty( $field['icon'] ) ) { ?>
		<div class="rtcl-field-icon"><i class="<?php echo esc_attr( $field['icon']['class'] ); ?>"></i></div>
	<?php } ?>
	<span class="cfp-label">
		<?php if ( ! empty( $field['label'] ) ) { ?>
			<?php echo esc_html( $field['label'] ); ?>:
		<?php } ?>
	</span>
	<?php if ( ! empty( $field['value'] ) ) { ?>
	<span class="cfp-value">
		<?php echo esc_html( $field['value'] ); ?>				
	</span>
	<?php } ?>
</div>