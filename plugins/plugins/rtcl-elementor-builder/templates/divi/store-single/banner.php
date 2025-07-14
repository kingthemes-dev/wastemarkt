<?php
/**
 * Store single content
 *
 * @author     RadiusTheme
 * @package    classified-listing/templates
 * @version    1.3.21
 */

use Rtcl\Helpers\Functions;

?>
	<div class="rtcl store-content-wrap">
		<div class="store-banner">
			<div class="banner"><?php $store->the_banner(); ?></div>
			<div class="store-name-logo">

				<?php if ( ! empty( $instance['rtcl_show_store_logo'] ) && $instance['rtcl_show_store_logo'] === 'on' ) : ?>
					<div class="store-logo"><?php $store->the_logo(); ?></div>
				<?php endif; ?>
				<div class="store-info">
					<?php if ( ! empty( $instance['rtcl_show_store_name'] ) && $instance['rtcl_show_store_name'] === 'on' ) : ?>
						<div class="store-name"><h2><?php $store->the_title(); ?></h2></div>
					<?php endif; ?>
					<?php if ( ! empty( $instance['rtcl_show_category'] ) && $instance['rtcl_show_category'] === 'on'  && $store->get_category() ) : ?>
						<div class="rtcl-store-cat">
							<i class="rtcl-icon rtcl-icon-tags"></i>
							<?php Functions::print_html( $store->get_category() ); ?>
						</div>
					<?php endif; ?>
					<?php
					if ( ! empty( $instance['rtcl_show_rating'] ) && $instance['rtcl_show_rating'] === 'on' ) {
						?>

						<?php if ( $store->is_rating_enable() ): ?>
							<?php if ( comments_open() ): ?>
								<?php if ( class_exists( 'Rtrs' ) && $avg_rating = \Rtrs\Models\Review::getAvgRatings( $store->get_id() ) ): ?>
									<div class="reviews-rating">
										<?php
										echo \Rtrs\Helpers\Functions::review_stars( $avg_rating );
										$total_rating = \Rtrs\Models\Review::getTotalRatings( $store->get_id() );
										?>
										<span class="reviews-rating-count">(<?php echo absint( $total_rating ); ?>)</span>
									</div>
								<?php endif; ?>
							<?php else: ?>
								<div class="reviews-rating">
									<?php echo Functions::get_rating_html( $store->get_average_rating(), $store->get_review_counts() ); ?>
									<span class="reviews-rating-count">(<?php echo absint( $store->get_review_counts() ); ?>)</span>
								</div>
							<?php endif; ?>
						<?php endif; ?>
					<?php
					}
					?>
				</div>
			</div>
		</div>
	</div>
