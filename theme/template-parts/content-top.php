<?php
/**
 * @author  RadiusTheme
 * @since   1.0
 * @version 1.0
 */

namespace radiustheme\ClassiList;

use RtclMarketplace\Hooks\ActionHooks;

$has_breadcrumb = $title = '';

if ( RDTheme::$has_breadcrumb ) {
	$has_breadcrumb = true;
}

if ( !empty( $page_title ) ) {
	$title = $page_title;
}
elseif ( is_search() ) {
	$title = esc_html__( 'Search Results for : ', 'classilist' ) . get_search_query();
}
elseif ( is_home() ) {
	if ( get_option( 'page_for_posts' ) ) {
		$title = get_the_title( get_option( 'page_for_posts' ) );
	}
	else {
		$title = apply_filters( "rdtheme_blog_title", esc_html__( 'All Posts', 'classilist' ) );
	}
}
elseif (is_post_type_archive('store')) {
    $title =  esc_html__( 'Store', 'classilist' );
}
elseif ( is_archive() ) {
	$title = get_the_archive_title();
}
elseif ( is_page() ) {
	$title = get_the_title();
}
?>
<?php if ( $title || $has_breadcrumb ): ?>
	<div class="top-content">
		<div class="container">

			<?php do_action( 'classilist_header_top' );?>

			<?php if ( $title ): ?>
				<h1 class="top-title"><?php echo wp_kses_post( $title );?></h1>
			<?php endif; ?>

			<?php if ( $has_breadcrumb ): ?>
				<div class="main-breadcrumb"><?php Helper::the_breadcrumb();?></div>
			<?php endif; ?>

			<?php
                if ( class_exists('RtclMarketplace') ) {
                    ActionHooks::add_wc_notice();
                }
			?>

		</div>
	</div>
<?php else: ?>
	<div class="top-content-none">
		<div class="container">
			<?php do_action( 'classilist_header_top' );?>
		</div>
	</div>
<?php endif; ?>

