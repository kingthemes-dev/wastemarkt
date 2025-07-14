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
	<?php
	if ( is_array( $field['value'] ) ) {
		foreach ( $field['value'] as $value ) {
			echo $value;
		}
	}
	?>
</div>