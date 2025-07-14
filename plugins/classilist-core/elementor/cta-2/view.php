<?php
/**
 * @author  RadiusTheme
 * @since   1.0
 * @version 1.0
 */

namespace radiustheme\ClassiList_Core;
use Elementor\Icons_Manager;

$attr1  = 'href="' . $data['btnurl1']['url'] . '"';
$attr1 .= !empty( $data['btnurl1']['is_external'] ) ? ' target="_blank"' : '';
$attr1 .= !empty( $data['btnurl1']['nofollow'] ) ? ' rel="nofollow"' : '';

$attr2  = 'href="' . $data['btnurl2']['url'] . '"';
$attr2 .= !empty( $data['btnurl2']['is_external'] ) ? ' target="_blank"' : '';
$attr2 .= !empty( $data['btnurl2']['nofollow'] ) ? ' rel="nofollow"' : '';

?>
<div class="rt-el-cta-2 rtin-<?php echo esc_attr( $data['theme'] )?>">

	<div class="rtin-left">
		<?php if ( $data['title'] ): ?>
			<h3 class="rtin-title"><?php echo esc_html( $data['title'] );?></h3>
		<?php endif; ?>

		<?php if ( $data['subtitle'] ): ?>
			<p class="rtin-subtitle"><?php echo esc_html( $data['subtitle'] );?></p>
		<?php endif; ?>			
	</div>

	<?php if ( $data['btnurl1']['url'] || $data['btnurl2']['url'] ): ?>
		<div class="rtin-right">
			<?php if ( $data['btnurl1']['url']){ ?>
				<a <?php echo $attr1; ?> class="button-1">
					<?php 
						Icons_Manager::render_icon( $data['btnicon1'], [ 'aria-hidden' => 'true' ] );
						echo esc_html( $data['btntext1'] ); 
					?>
				</a>
			<?php } if ( $data['btnurl2']['url'] ){ ?>
				<a <?php echo $attr2; ?> class="button-2">
					<?php 
						Icons_Manager::render_icon( $data['btnicon2'], [ 'aria-hidden' => 'true' ] );
						echo esc_html( $data['btntext2'] ); 
					?>
				</a>
			<?php } ?>
		</div>		
	<?php endif; ?>

</div>