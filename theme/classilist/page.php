<?php
/**
 * @author  RadiusTheme
 * @since   1.0
 * @version 1.15
 */

namespace radiustheme\ClassiList;
?>
<?php get_header(); ?>
<?php get_template_part( 'template-parts/content', 'top' ); ?>
<div id="primary" class="content-area">
	<div class="container">
		<div class="row">
			<?php
			if ( RDTheme::$layout == 'left-sidebar' ) {
				get_sidebar();
			}
			?>
			<div class="<?php Helper::the_layout_class();?>">
				<main id="main" class="site-content-block">
					<div class="main-content">
						<?php while ( have_posts() ) : the_post(); ?>
							<?php
							get_template_part( 'template-parts/content', 'page' );
							if ( comments_open() || get_comments_number() ){
								comments_template();
							}
							?>
						<?php endwhile; ?>
					</div>
				</main>
			</div>
			<?php
			if ( RDTheme::$layout == 'right-sidebar' ) {
				get_sidebar();
			}
			?>
		</div>
	</div>
</div>
<?php get_footer(); ?>