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
<div class="rtcl-fb-element rtcl-wrapper-fb-<?php echo esc_attr( $field['element'] ); ?>">
	<div class="rtcl-icon-label-wrapper">
		<?php if ( $show_icon && ! empty( $field['icon'] ) ) { ?>
			<div class="rtcl-field-icon"><i class="<?php echo esc_attr( $field['icon']['class'] ); ?>"></i></div>
		<?php } ?>
		<h3 class="rtcl-file-heading"><?php echo esc_html( $field['label'] ); ?></h3>
	</div>
	<div class="rtcl-fb-<?php echo esc_attr( $field['element'] ); ?>">
	<?php
	if ( ! empty( $field['value'] ) ) {
		foreach ( $field['value'] as $key => $value ) {
			if ( strpos( $value['mime_type'], 'image/' ) === 0 ) {
				// Display the image.
				echo '<div class="rtcl-images-wrapper rtcl-file-item"><img src="' . esc_url( $value['url'] ) . '" alt="' . esc_attr( $value['name'] ) . '"></div>';
			} elseif ( strpos( $value['mime_type'], 'video/' ) === 0 ) {
				// Display the video.
				echo '<div class="rtcl-video-wrapper rtcl-file-item"><video controls>
                <source src="' . esc_url( $value['url'] ) . '" type="' . esc_attr( $value['mime_type'] ) . '">
                ' . esc_html__( 'Your browser does not support the video tag.', 'rtcl-elementor-builder' ) . '</video></div>';
			} else {
				// Handle other types.
				echo '<div class="rtcl-file-wrapper rtcl-file-item"><a href="' . esc_url( $value['url'] ) . '" download > ' . esc_html( $value['name'] ) . '</a></div>';
			}
		}
	}
	?>
	</div>
</div>