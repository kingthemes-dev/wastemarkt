<?php
/**
 * @author  RadiusTheme
 * @since   1.0
 * @version 1.0
 */

namespace radiustheme\ClassiList;

$has_top_info = RDTheme::$options['address'] || RDTheme::$options['phone'] || RDTheme::$options['email'] ? true : false;
$socials = Helper::socials();

if ( !$has_top_info && !$socials ) {
	return;
}
?>
<div class="top-header">
	<div class="container">
		<div class="top-header-inner">
			<?php if ( $has_top_info ): ?>
				<div class="tophead-left">
					<ul class="tophead-info">
						<?php if ( RDTheme::$options['address'] ): ?>
							<li><i class="fas fa-map-marker-alt"></i><span><?php echo wp_kses_post( RDTheme::$options['address'] );?></span></li>
						<?php endif; ?>
						<?php if ( RDTheme::$options['phone'] ): ?>
							<li><i class="fas fa-phone-alt"></i><a href="tel:<?php echo esc_attr( RDTheme::$options['phone'] );?>"><?php echo esc_html( RDTheme::$options['phone'] );?></a></li>
						<?php endif; ?>
						<?php if ( RDTheme::$options['email'] ): ?>
							<li><i class="far fa-envelope"></i><a href="mailto:<?php echo esc_attr( RDTheme::$options['email'] );?>"><?php echo esc_html( RDTheme::$options['email'] );?></a></li>
						<?php endif; ?>
					</ul>
				</div>
			<?php endif; ?>
			<?php if ( $socials ): ?>
				<div class="tophead-right">
					<ul class="tophead-social">
						<?php foreach ( $socials as $social ): ?>
							<li><a target="_blank" href="<?php echo esc_url( $social['url'] );?>"><i class="<?php echo esc_attr( $social['icon'] );?>"></i></a></li>
						<?php endforeach; ?>					
					</ul>
				</div>
			<?php endif; ?>
		</div>
	</div>
</div>