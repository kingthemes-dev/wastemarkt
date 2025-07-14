<?php
/**
 * @author  RadiusTheme
 * @since   1.0
 * @version 1.0
 */

namespace radiustheme\ClassiList_Core;

$btn = $attr = '';

if ( !empty( $data['btnurl']['url'] ) ) {
	$attr  = 'href="' . $data['btnurl']['url'] . '"';
	$attr .= !empty( $data['btnurl']['is_external'] ) ? ' target="_blank"' : '';
	$attr .= !empty( $data['btnurl']['nofollow'] ) ? ' rel="nofollow"' : '';
	
}
if ( !empty( $data['btntext'] ) ) {
	$btn = '<a class="btn rdtheme-button-1" ' . $attr . '>' . $data['btntext'] . '</a>';
}
?>
<div class="rt-el-cta-1 rtin-<?php echo esc_attr( $data['theme'] )?>">
	<h3 class="rtin-title"><?php echo esc_html( $data['title'] );?></h3>
	<p class="rtin-content"><?php echo wp_kses_post( $data['content'] );?></p>
	<?php
	if ( $btn ){
		echo wp_kses_post( $btn );
	}
	?>
</div>