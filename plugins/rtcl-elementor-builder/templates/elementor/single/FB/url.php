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

if ( empty( $field['fields_for'] ) ) {
	return;
}
?>

<div class="rtcl-fb-element rtcl-fb-<?php echo esc_attr( $field['element'] ); ?>">
	<?php
	if ( 'image' === $field['fields_for'] ) {
		// Display the image.
		echo '<div class="rtcl-images-wrapper"><img src="' . esc_url( $field['value'] ) . '" alt="' . esc_attr( $field['label'] ) . '"></div>';
	} elseif ( 'video' === $field['fields_for'] ) {
		// Check if the URL can be embedded using oEmbed.
		$embed_code = ! empty( $field['value'] ) ? wp_oembed_get( $field['value'] ) : '';
		if ( $embed_code ) {
			// Output the oEmbed HTML.
			echo '<div class="rtcl-video-wrapper">' . $embed_code . '</div>';
		} else {
			// Assume it's a direct video file and use <video> tag.
			$mime_type = ! empty( $field['value'] ) && function_exists( 'mime_content_type' ) ? mime_content_type( $field['value'] ) : 'video/mp4';
			echo '<div class="rtcl-video-wrapper"><video controls>
			<source src="' . esc_url( $field['value'] ) . '" type="' . esc_attr( $mime_type ) . '">
			' . esc_html__( 'Your browser does not support the video tag.', 'rtcl-elementor-builder' ) . '</video></div>';
		}
	} elseif ( 'link' === $field['fields_for'] ) {
		echo '<a href="' . esc_url( $field['value'] ) . '">' . esc_html( $field['label'] ) . '</a>';
	} elseif ( 'audio' === $field['fields_for'] ) {
		// Get the MIME type of the audio file if possible.
		$mime_type = ! empty( $field['value'] ) && function_exists( 'mime_content_type' ) ? mime_content_type( $field['value'] ) : 'audio/mpeg';
		// Display the audio.
		echo '<div class="rtcl-audio-wrapper"><audio controls>
		<source src="' . esc_url( $field['value'] ) . '" type="' . esc_attr( $mime_type ) . '">
		' . esc_html__( 'Your browser does not support the audio element.', 'rtcl-elementor-builder' ) . '</audio></div>';
	} else {
		// Handle other types.
		echo '<div class="rtcl-text-wrapper">' . esc_url( $field['value'] ) . '</div>';
	}
	?>
</div>