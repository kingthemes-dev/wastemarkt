<?php
/**
 * @author  RadiusTheme
 * @since   1.0
 * @version 1.0
 */

namespace radiustheme\ClassiList_Core;

$attr = '';
if ( !empty( $data['url']['url'] ) ) {
	$attr  = 'href="' . $data['url']['url'] . '"';
	$attr .= !empty( $data['url']['is_external'] ) ? ' target="_blank"' : '';
	$attr .= !empty( $data['url']['nofollow'] ) ? ' rel="nofollow"' : '';

	$start_tag = '<a class="rt-el-info-box" ' . $attr . '>';
	$end_tag   = '</a>';
}
else {
	$start_tag = '<div class="rt-el-info-box">';
	$end_tag   = '</div>';
}

if ( $data['icontype'] == 'image' ) {
	$icon = wp_get_attachment_image( $data['image']['id'], 'full' );
}
else {
	$icon = '<i class="'.$data['icon'].'" aria-hidden="true"></i>';
}
?>
<?php echo wp_kses_post( $start_tag ); ?>
<div class="rtin-icon"><?php echo wp_kses_post( $icon );?></div>
<h3 class="rtin-title"><?php echo wp_kses_post( $data['title'] );?></h3>
<?php echo wp_kses_post( $end_tag ); ?>