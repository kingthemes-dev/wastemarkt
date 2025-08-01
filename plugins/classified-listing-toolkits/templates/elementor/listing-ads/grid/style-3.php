<?php
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly
/**
 *
 * @author     RadiusTheme
 * @package    classified-listing/templates
 * @version    1.0.0
 */

use Rtcl\Helpers\Functions;
use Rtcl\Helpers\Pagination;
use Rtcl\Models\Listing;
use RtclPro\Helpers\Fns;
use RtclPro\Controllers\Hooks\TemplateHooks;

?>

<div class="rtcl rtcl-listings-sc-wrapper rtcl-elementor-widget">
	<div class="rtcl-listings-wrapper">
		<?php
		$class = '';
		$class .= ! empty( $view ) ? 'rtcl-' . $view . '-view ' : 'rtcl-list-view ';
		$class .= ! empty( $style ) ? 'rtcl-' . $style . '-view ' : 'rtcl-style-1-view ';

		$class .= ! empty( $instance['rtcl_listings_column'] ) ? 'columns-' . $instance['rtcl_listings_column'] . ' ' : ' columns-1';
		$class .= ! empty( $instance['rtcl_listings_column_tablet'] ) ? 'tab-columns-' . $instance['rtcl_listings_column_tablet'] . ' ' : ' tab-columns-2';
		$class .= ! empty( $instance['rtcl_listings_column_mobile'] ) ? 'mobile-columns-' . $instance['rtcl_listings_column_mobile'] . ' '
			: ' mobile-columns-2';

		?>
		<div class="rtcl-listings rtcl-ajax-listings <?php echo esc_attr( $class ); ?> ">
			<?php

			while ( $the_loops->have_posts() ) :
				$the_loops->the_post();
				$_id                 = get_the_ID();
				$post_meta           = get_post_meta( $_id );
				$listing             = new Listing( $_id );
				$listing_title       = null;
				$listing_meta        = null;
				$listing_description = null;
				$img                 = null;
				$labels              = null;
				$time                = null;
				$location            = null;
				$category            = null;
				$price               = null;
				$img_position_class  = '';
				$types               = null;
				$phone               = get_post_meta( $_id, 'phone', true );
				$custom_field        = null;
				?>

				<div <?php Functions::listing_class( [ 'rtcl-widget-listing-item', 'listing-item', $img_position_class ] ); ?>>
					<?php

					if ( $instance['rtcl_show_price'] ) {
						$price_html = $listing->get_price_html();
						if ( $price_html ) {
							$price = sprintf( '<div class="item-price listing-price">%s</div>', $price_html );
						}
					}
					if ( $instance['rtcl_show_image'] ) {

						ob_start();
						if ( rtcl()->has_pro() ) {
							TemplateHooks::sold_out_banner();
						}
						$mark_as_sold = ob_get_clean();

						$image_size    = $instance['rtcl_thumb_image_size'];
						$the_thumbnail = $listing->get_the_thumbnail( $image_size );

						if ( $the_thumbnail ) {
							$img = sprintf(
								"<div class='listing-thumb'>%s<a href='%s' title='%s'>%s</a> </div>",
								$mark_as_sold,
								get_the_permalink(),
								esc_html( get_the_title() ),
								$the_thumbnail
							);
						}
					}

					if ( $instance['rtcl_show_labels'] ) {
						$labels = $listing->badges() ? $listing->badges() : '';
					}
					if ( $instance['rtcl_show_date'] ) {
						if ( $listing->get_the_time() ) {
							$time = sprintf(
								'<li class="date"><i class="rtcl-icon rtcl-icon-clock" aria-hidden="true"></i>%s</li>',
								$listing->get_the_time()
							);
						}
					}
					if ( $instance['rtcl_show_location'] ) {
						if ( wp_strip_all_tags( $listing->the_locations( false ) ) ) {
							$location = sprintf(
								'<li class="location"><i class="rtcl-icon rtcl-icon-location" aria-hidden="true"></i>%s</li>',
								$listing->the_locations( false, true )
							);
						}
					}
					if ( $instance['rtcl_show_category'] ) {
						if ( $listing->the_categories( false ) ) {
							$category = sprintf(
								'<li class="category"><i class="rtcl-icon rtcl-icon-tags" aria-hidden="true"></i>%s</li>',
								$listing->the_categories( false )
							);
						}
					}

					$author_html = '';
					if ( $instance['rtcl_show_user'] ) {
						ob_start();
						if ( ! empty( $instance['rtcl_verified_user_base'] ) ) {
							do_action( 'rtcl_after_author_meta', $listing->get_owner_id() );
						}
						$after_author_meta = ob_get_clean();
						$author_html       = sprintf( '<li class="author" ><i class="rtcl-icon rtcl-icon-user" aria-hidden="true"></i>%s %s</li>',
							get_the_author(), $after_author_meta );
					}
					$views_html = '';
					if ( $instance['rtcl_show_views'] ) {
						$views = absint( get_post_meta( get_the_ID(), '_views', true ) );
						if ( $views ) {
							$views_html = sprintf(
								'<li class="view"><i class="rtcl-icon rtcl-icon-eye" aria-hidden="true"></i>%s</li>',
								sprintf(
								/* translators: %s: views count */
									_n( '%s view', '%s views', $views, 'classified-listing-toolkits' ),
									number_format_i18n( $views )
								)
							);
						}
					}

					if ( $instance['rtcl_show_types'] && $listing->get_ad_type() ) {
						$listing_types = Functions::get_listing_types();
						$types         = ! empty( $listing_types ) ? $listing_types[ $listing->get_ad_type() ] : '';
						if ( $types ) {
							$types = sprintf(
								'<li class="rtin-type"><i class="rtcl-icon-tags" aria-hidden="true"></i>%s</li>',
								$types
							);
						}
					}

					if ( $types || $author_html || $time || $location || $views_html ) {
						$listing_meta = sprintf( '<ul class="rtcl-listing-meta-data">%s %s %s %s %s</ul>', $types, $author_html, $time, $location,
							$views_html );
					}

					if ( $instance['rtcl_show_category'] ) {
						if ( $listing->the_categories( false, true ) ) {
							$category = sprintf(
								'<div class="category">%s</div>',
								$listing->the_categories( false, true )
							);
						}
					}

					if ( $instance['rtcl_show_title'] ) {
						$listing_title = sprintf(
							'<h3 class="listing-title rtcl-listing-title"><a href="%1$s" title="%2$s">%2$s</a></h3>',
							get_the_permalink(),
							esc_html( get_the_title() )
						);
					}
					if ( $instance['rtcl_show_description'] ) {
						$excerpt = get_the_excerpt( $_id );

						if ( $instance['rtcl_content_limit'] ) {
							$listing_description = sprintf(
								'<div class="rtcl-short-description"> %s </div>',
								esc_html(wp_trim_words( wpautop( $excerpt ), $instance['rtcl_content_limit'] ) )
							);
						} else {
							$listing_description = sprintf(
								'<div class="rtcl-short-description"> %s </div>',
								wpautop( $excerpt )
							);
						}
					}
					ob_start();
					$button_icon = 0;
					?>
					<?php
					if ( $phone && $instance['rtcl_show_phone'] ) :
						$button_icon ++;
						?>
						<div class="rtin-phn rtin-el-button">
							<a class="rtcl-phone-reveal not-revealed" href="#" data-phone="<?php echo esc_attr( $phone ); ?>"><i
									class="rtcl-icon rtcl-icon-phone" aria-hidden="true"></i><span><?php esc_html_e( 'Show Phone No',
										'classified-listing-toolkits' ); ?></span></a>
						</div>
					<?php endif; ?>
					<?php $dispaly_phone = ob_get_clean(); ?>

					<?php
					ob_start();
					if ( $instance['rtcl_show_favourites'] ) {
						$button_icon ++;
						?>
						<div class="rtin-fav rtin-el-button">
							<?php echo wp_kses_post( Functions::get_favourites_link( $_id ) );?>
						</div>
					<?php } ?>
					<?php
					$dispaly_favourites = ob_get_clean();
					?>

					<?php ob_start(); ?>
					<?php
					if ( rtcl()->has_pro() ) {
						if ( ! empty( $instance['rtcl_show_quick_view'] ) ) :
							?>
							<div class="rtin-el-button">
								<a class="rtcl-quick-view" href="#" title="<?php esc_attr_e( 'Quick View', 'classified-listing-toolkits' ); ?>"
								   data-listing_id="<?php echo absint( $_id ); ?>">
									<i class="rtcl-icon rtcl-icon-zoom-in"></i>
								</a>
							</div>
							<?php
							$button_icon ++;
						endif;
					}
					?>
					<?php $dispaly_quick_view = ob_get_clean(); ?>

					<?php ob_start(); ?>
					<?php
					if ( rtcl()->has_pro() ) {
						if ( ! empty( $instance['rtcl_show_compare'] ) ) :
							?>
							<div class="rtin-el-button">
								<?php
                                $compare_ids    = ! empty( $_SESSION['rtcl_compare_ids'] ) ? array_map( 'absint', $_SESSION['rtcl_compare_ids'] ) : [];
								$selected_class = '';
								if ( is_array( $compare_ids ) && in_array( $_id, $compare_ids ) ) {
									$selected_class = ' selected';
								}
								?>
								<a class="rtcl-compare <?php echo esc_attr( $selected_class ); ?>" href="#"
								   title="<?php esc_attr_e( 'Compare', 'classified-listing-toolkits' ); ?>"
								   data-listing_id="<?php echo absint( $_id ); ?>">
									<i class="rtcl-icon rtcl-icon-retweet"></i>
								</a>
							</div>
							<?php
							$button_icon ++;
						endif;
					}
					?>
					<?php $dispaly_compare = ob_get_clean(); ?>

					<?php ob_start(); ?>
					<div class="rtin-bottom button-count-<?php echo esc_attr( $button_icon ); ?>">
						<?php
						printf( '%s %s %s %s', $dispaly_phone, $dispaly_favourites, $dispaly_quick_view, $dispaly_compare ); //phpcs:ignore
						?>
					</div>
					<?php
					$rtin_bottom = ob_get_clean();

					if ( ! empty( $instance['rtcl_show_custom_fields'] ) ) {
						ob_start();
                        if ( rtcl()->has_pro() ) {
                            TemplateHooks::loop_item_listable_fields();
                        }
						$custom_field = ob_get_clean();
					}

					$item_content   = sprintf(
						'<div class="item-content">%s %s %s %s %s %s %s %s</div>',
						$labels,
						$category,
						$listing_title,
						$custom_field,
						$listing_meta,
						$listing_description,
						$price,
						$rtin_bottom
					);
					$final_contents = sprintf( '%s%s', $img, $item_content );
					echo wp_kses_post( $final_contents );
					?>

				</div>

			<?php endwhile; ?>
			<?php wp_reset_postdata(); ?>

		</div>
		<?php if ( ! empty( $instance['rtcl_listing_pagination'] ) ) { ?>
			<?php Pagination::pagination( $the_loops, true ); ?>
		<?php } ?>
	</div>
</div>
