<?php

/**
 *
 * @author     RadiusTheme
 * @package    classified-listing/templates
 * @version    1.0.0
 */

use Rtcl\Helpers\Functions;
use Rtcl\Helpers\Pagination;

?>
<?php
$wrap_class = '';
if ( isset( $instance['blockId'] ) ) {
	$wrap_class .= 'rtcl-block-' . $instance['blockId'];
}
$wrap_class .= ' rtcl-block-frontend ';
if ( isset( $instance['className'] ) ) {
	$wrap_class .= $instance['className'];
}
?>
<div class="<?php echo esc_attr( $wrap_class ); ?>">
	<div class="rtcl rtcl-gb-block">

		<?php
		$class = '';
		$class .= 'rtcl-grid-view';
		$class .= ! empty( $instance['col_desktop'] ) ? ' columns-' . $instance['col_desktop'] . ' ' : 'columns-1';
		$class .= ! empty( $instance['col_tablet'] ) ? 'tab-columns-' . $instance['col_tablet'] . ' ' : 'tab-columns-2';
		$class .= ! empty( $instance['col_mobile'] ) ? 'mobile-columns-' . $instance['col_mobile'] . ' ' : 'mobile-columns-2';
		$class .= 'rtcl-gb-grid-view';
		$class .= ! empty( $style ) ? ' rtcl-gb-grid-style-' . $style : 'rtcl-gb-grid-style-1';
		?>

		<div class="<?php echo esc_attr( $class ); ?>">

			<?php if ( ! empty( $the_loops['posts'] ) ) { ?>
				<?php foreach ( $the_loops['posts'] as $the_loop ) { ?>

					<div <?php Functions::listing_class( $the_loop['classes'] ); ?>>

						<?php if ( $instance['content_visibility']["thumbnail"] && ! empty( $the_loop['thumbnail'] ) ) : ?>
							<div class="listing-thumb">
								<a href="<?php echo esc_url( $the_loop['post_link'] ); ?>"
								   class="rtcl-media"><?php echo wp_kses_post( $the_loop['thumbnail'] ); ?></a>
								<?php if ( $instance['content_visibility']["sold"] && ! empty( $the_loop['sold'] ) ) : ?>
									<?php echo wp_kses_post( $the_loop['sold'] ); ?>
								<?php endif; ?>

								<?php if ( $instance['content_visibility']['price'] && ! empty( $the_loop['price'] ) ) : ?>
									<div class="item-price listing-price"><?php echo wp_kses_post( $the_loop['price'] ) ?></div>
								<?php endif; ?>
							</div>
						<?php endif; ?>


						<div class="item-content">

							<?php if ( $instance['content_visibility']['badge'] && ! empty( $the_loop['badges'] ) ) : ?>
								<div class="listing-badge-wrap"><?php echo wp_kses_post( $the_loop['badges'] ); ?></div>
							<?php endif; ?>

							<?php if ( $instance['content_visibility']['category'] && ! empty( $the_loop['categories'] ) ) : ?>
								<div class='listing-cat'><?php echo wp_kses_post( $the_loop['categories'] ); ?></div>
							<?php endif; ?>

							<?php if ( $instance['content_visibility']['title'] && ! empty( $the_loop['title'] ) ) { ?>
								<h3 class="listing-title"><a
										href="<?php echo esc_url( $the_loop['post_link'] ) ?>"><?php echo esc_html( $the_loop['title'] ) ?></a></h3>
							<?php } ?>

							<?php if ( $instance['content_visibility']['custom_field'] && ! empty( $the_loop['custom_field'] ) ) { ?>
								<div class="rtcl-custom-field-warp"><?php Functions::print_html( $the_loop['custom_field'] ); ?></div>
							<?php } ?>

							<ul class="rtcl-listing-meta-data">
								<?php if ( $instance['content_visibility']['listing_type'] && ! empty( $the_loop['listing_type'] ) ) : ?>
									<li class="listing-type"><i class="rtcl-icon rtcl-icon-tags"></i><?php echo esc_html( $the_loop['listing_type'] ); ?></li>
								<?php endif; ?>

								<?php if ( $instance['content_visibility']['date'] && ! empty( $the_loop['time'] ) ) : ?>
									<li class="updated"><i class="rtcl-icon rtcl-icon-clock"></i><?php echo esc_html( $the_loop['time'] ); ?></li>
								<?php endif; ?>

								<?php if ( $instance['content_visibility']['author'] && ! empty( $the_loop['author'] ) ) : ?>
									<li class="author"><i class="rtcl-icon rtcl-icon-user"></i><?php echo esc_html( $the_loop['author'] ); ?></li>
								<?php endif; ?>

								<?php if ( $instance['content_visibility']['location'] && ! empty( $the_loop['locations'] ) ) : ?>
									<li class="rt-location"><i
											class="rtcl-icon rtcl-icon-location"></i><?php Functions::print_html( $the_loop['locations'] ); ?></li>
								<?php endif; ?>

								<?php if ( $instance['content_visibility']['view'] && ! empty( $the_loop['views'] ) ) : ?>
									<li class="rt-view"><i class="rtcl-icon rtcl-icon-eye"></i><?php echo esc_html( $the_loop['views'] ); ?></li>
								<?php endif; ?>
							</ul>

							<?php if ( $instance['content_visibility']['grid_content'] && ! empty( $the_loop['excerpt'] ) ) : ?>
								<?php if ( $instance['content_limit'] && ! empty( $the_loop['excerpt'] ) ) { ?>
									<p class="rtcl-excerpt"><?php echo wp_trim_words( wpautop( $the_loop['excerpt'] ), $instance['content_limit'], '' ); ?></p>
								<?php } ?>
							<?php endif; ?>
						</div>

						<div class="rtcl-bottom">
							<ul>
								<li class="action-btn">
									<?php if ( $instance['content_visibility']["author"] && ! empty( $the_loop['author_image'] ) ) : ?>
										<div><a href="javascript:void(0)" class="item-img"><?php echo wp_kses_post( $the_loop['author_image'] ) ?></a></div>
									<?php endif; ?>
								</li>
								<li class="action-btn">
									<?php if ( $instance['content_visibility']["favourit_btn"] && ! empty( $the_loop['favourite_link'] ) ) : ?>
										<div class="rtcl-gb-button"><?php echo wp_kses_post( $the_loop['favourite_link'] ); ?></div>
									<?php endif; ?>
								</li>

								<li class="action-btn">
									<?php if ( $instance['content_visibility']["quick_btn"] && ! empty( $the_loop['quick_view'] ) ) : ?>
										<div class="rtcl-gb-button"><?php echo wp_kses_post( $the_loop['quick_view'] ); ?></div>
									<?php endif; ?>
								</li>
								<li class="action-btn">
									<?php if ( $instance['content_visibility']["compare_btn"] && ! empty( $the_loop['compare'] ) ) : ?>
										<div class="rtcl-gb-button"><?php echo wp_kses_post( $the_loop['compare'] ); ?></div>
									<?php endif; ?>
								</li>
							</ul>
						</div>

					</div>


				<?php } ?>
			<?php } ?>
		</div>

		<?php if ( $instance['content_visibility']['pagination'] && ! is_wp_error( $the_loops['query_obj'] ) ) { ?>
			<?php Pagination::pagination( $the_loops['query_obj'], true );
			?>
		<?php } ?>

	</div>
</div>