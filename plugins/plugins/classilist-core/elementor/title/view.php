<?php
/**
 * @author  RadiusTheme
 * @since   1.0
 * @version 1.0
 */

namespace radiustheme\ClassiList_Core;

$title_style    = "color:{$data['title_color']};";
$subtitle_style = "color:{$data['subtitle_color']};";

?>
<div class="rt-el-title">
	<h2 class="rtin-title" style="<?php echo esc_attr( $title_style );?>"><?php echo esc_html( $data['title'] );?></h2>
	<p class="rtin-subtitle" style="<?php echo esc_attr( $subtitle_style );?>"><?php echo wp_kses_post( $data['subtitle'] );?></p>
</div>