<?php
/**
 * Marketplace Pagination
 *
 * @author        RadiusTheme
 * @package       classified-listing/templates
 * @version       1.0.0
 *
 * @var int $post_per_page
 * @var int $current_page
 * @var int $pages
 */
?>
<nav class="rtcl-pagination">
    <ul class="page-numbers">
		<?php
		for ( $i = 1; $i <= $pages; $i ++ ) {
			$class = '';
			if ( $i == $current_page ) {
				?>
                <li><span class="page-numbers current"><?php echo esc_html( $i ); ?></span></li>
				<?php
			} else { ?>
                <li><a href="?item=<?php echo esc_attr( $i ); ?>" class="page-numbers"><?php echo esc_html( $i ); ?></a></li>
			<?php }
		} ?>
    </ul>
</nav>