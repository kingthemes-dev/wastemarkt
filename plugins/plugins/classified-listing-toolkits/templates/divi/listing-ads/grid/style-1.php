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
use RtclPro\Controllers\Hooks\TemplateHooks;

?>

<div class="rtcl rtcl-listings-wrapper rtcl-elementor-widget rtcl-divi-module">
	<?php
	$class = ! empty( $style ) ? 'rtcl-grid-' . $style : 'rtcl-grid-style-1';
	$class .= ! empty( $instance['rtcl_grid_column'] ) ? ' columns-' . $instance['rtcl_grid_column'] : ' columns-3';
	$class .= ! empty( $instance['rtcl_grid_column_tablet'] ) ? ' tab-columns-' . $instance['rtcl_grid_column_tablet'] : ' tab-columns-2';
	$class .= ! empty( $instance['rtcl_grid_column_phone'] ) ? ' mobile-columns-' . $instance['rtcl_grid_column_phone'] : ' mobile-columns-1';
	?>
    <div class="rtcl-listings rtcl-grid-view <?php echo esc_attr( $class ); ?>">
		<?php

		while ( $the_loops->have_posts() ) :
			$the_loops->the_post();
			$_id                 = get_the_ID();
			$post_meta           = get_post_meta( $_id );
			$listing             = rtcl()->factory->get_listing( $_id );
			$listing_title       = null;
			$listing_meta        = null;
			$listing_description = null;
			$img                 = null;
			$labels              = null;
			$u_info              = null;
			$time                = null;
			$location            = null;
			$category            = null;
			$price               = null;
			$types               = null;
			$img_position_class  = '';
			$custom_field        = null;
			?>

            <div <?php Functions::listing_class( [ 'rtcl-widget-listing-item', 'listing-item', $img_position_class ] ); ?>>
				<?php
				$button_icon = 0;
				ob_start();
				if ( 'on' === $instance['rtcl_show_favourites'] ) {
					$button_icon ++;
					?>
                    <div class="rtcl-fav rtcl-el-button">
						<?php  echo wp_kses_post( Functions::get_favourites_link( $_id ) );
                        ?>
                    </div>
				<?php } ?>
				<?php
				$dispaly_favourites = ob_get_clean();
				?>

				<?php
				ob_start();
				if ( rtcl()->has_pro() ) {
					if ( 'on' === $instance['rtcl_show_quick_view'] ) :
						?>
                        <div class="rtcl-el-button">
                            <a class="rtcl-quick-view" href="#" title="<?php esc_attr_e( 'Quick View', 'classified-listing-toolkits' ); ?>"
                               data-listing_id="<?php echo absint( $_id ); ?>">
                                <i class="rtcl-icon rtcl-icon-zoom-in"></i>
                            </a>
                        </div>
						<?php
						$button_icon ++;
					endif;
				}
				$dispaly_quick_view = ob_get_clean();
				?>

				<?php ob_start(); ?>
				<?php
				if ( rtcl()->has_pro() ) {
					if ( 'on' === $instance['rtcl_show_compare'] ) :
						?>
                        <div class="rtcl-el-button">
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
				<?php
				$dispaly_compare = ob_get_clean();

				$button = sprintf( '<div class="rtcl-meta-buttons-wrap meta-button-count-%s">%s %s %s</div>',
					$button_icon, $dispaly_favourites, $dispaly_quick_view, $dispaly_compare );

				if ( 'on' === $instance['rtcl_show_image'] ) {
					$image_size    = $instance['rtcl_image_size'];
					$the_thumbnail = $listing->get_the_thumbnail( $image_size );
					if ( $the_thumbnail ) {
						$img = sprintf(
							"<div class='listing-thumb'><div class='listing-thumb-inner'><a href='%s' title='%s'>%s</a>%s</div></div>",
							get_the_permalink(),
							esc_html( get_the_title() ),
							$the_thumbnail,
							$button
						);
					}
				}
				if ( 'on' === $instance['rtcl_show_labels'] ) {
					$labels = $listing->badges();
				}
				if ( 'on' === $instance['rtcl_show_date'] ) {
					$time = sprintf(
						'<li class="listing-date"><i class="rtcl-icon rtcl-icon-clock" aria-hidden="true"></i>%s</li>',
						$listing->get_the_time()
					);
				}
				if ( 'on' === $instance['rtcl_show_location'] ) {
					if ( wp_strip_all_tags( $listing->the_locations( false ) ) ) {
						$location = sprintf(
							'<li class="listing-location"><i class="rtcl-icon rtcl-icon-location" aria-hidden="true"></i>%s</li>',
							$listing->the_locations( false, true )
						);
					}
				}

				if ( 'on' === $instance['rtcl_show_price'] ) {
					$price_html = $listing->get_price_html();
					$price      = sprintf( '<div class="item-price listing-price">%s</div>', $price_html );
				}
				$author_html = '';
				if ( 'on' === $instance['rtcl_show_user'] ) {
					ob_start();
					if ( ! empty( $instance['rtcl_verified_user_base'] ) ) {
						do_action( 'rtcl_after_author_meta', $listing->get_owner_id() );
					}
					$after_author_meta = ob_get_clean();
					$author_html       = sprintf( '<li class="listing-author" ><i class="rtcl-icon rtcl-icon-user" aria-hidden="true"></i>%s %s</li>',
						get_the_author(), $after_author_meta );
				}
				$views_html = '';
				if ( 'on' === $instance['rtcl_show_views'] ) {
					$views      = absint( get_post_meta( get_the_ID(), '_views', true ) );
					$views_html = sprintf(
						'<li class="listing-views"><i class="rtcl-icon rtcl-icon-eye" aria-hidden="true"></i>%s</li>',
						sprintf(
						/* translators: %s: views count */
							_n( '%s view', '%s views', $views, 'classified-listing-toolkits' ),
							number_format_i18n( $views )
						)
					);
				}

				if ( 'on' === $instance['rtcl_show_ad_types'] && $listing->get_ad_type() ) {

					$listing_types = Functions::get_listing_types();
					$types         = ! empty( $listing_types ) && isset( $listing_types[ $listing->get_ad_type() ] )
						? $listing_types[ $listing->get_ad_type() ] : '';

					if ( $types ) {
						$types = sprintf(
							'<li class="listing-type"><i class="rtcl-icon rtcl-icon-tags" aria-hidden="true"></i>%s</li>',
							$types
						);
					}
				}

				if ( $types || $author_html || $time || $location || $views_html ) {
					$listing_meta = sprintf( '<ul class="rtcl-listing-meta-data">%s %s %s %s %s</ul>', $types, $author_html, $time, $location,
						$views_html );
				}

				if ( 'on' === $instance['rtcl_show_category'] ) {
					$category = sprintf(
						'<div class="listing-cat">%s</div>',
						$listing->the_categories( false, true )
					);
				}
				$listing_title = sprintf(
					'<h3 class="rtcl-listing-title"><a href="%1$s" title="%2$s">%2$s</a></h3>',
					get_the_permalink(),
					esc_html( get_the_title() )
				);
				if ( 'on' === $instance['rtcl_show_description'] ) {
					$excerpt = get_the_excerpt( $_id );

					if ( $instance['rtcl_content_limit'] ) {
						$listing_description = sprintf(
							'<p class="rtcl-excerpt"> %s </p>',
							esc_html(wp_trim_words( wpautop( $excerpt ), $instance['rtcl_content_limit'] ))
						);
					} else {
						$listing_description = sprintf(
							'<p class="rtcl-excerpt"> %s </p>',
							wpautop( $excerpt )
						);
					}
				}

				if ( rtcl()->has_pro() && 'on' === $instance['rtcl_show_custom_fields'] ) {
					ob_start();
                    if ( rtcl()->has_pro() ) {
                        TemplateHooks::loop_item_listable_fields();
                    }
					$custom_field = ob_get_clean();
				}

				$item_content_right = sprintf( '%s', $price );

				$item_content = sprintf(
					'<div class="item-content">%s %s %s %s %s %s %s</div>',
					$labels,
					$category,
					$listing_title,
					$custom_field,
					$listing_meta,
					$listing_description,
					$item_content_right
				);

				$final_contents = sprintf( '%s <div class="rtin-content-area">%s</div>', $img, $item_content );

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
