<?php
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


	$_id                 = get_the_ID();
	$post_meta           = get_post_meta( $_id );
	$listing             = new Listing( $_id );
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
	$img_position_class  = '';
	$types               = null;
	$custom_field = null;

	$phone               = get_post_meta( $_id, 'phone', true );
?>

	<div <?php Functions::listing_class( [ 'rtcl-widget-listing-item', 'listing-item swiper-slide-customize', $img_position_class ] ); ?>>
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
					"<div class='listing-thumb'>%s<a href='%s' title='%s'>%s</a> %s </div>",
					$mark_as_sold,
					get_the_permalink(),
					esc_html( get_the_title() ),
					$the_thumbnail,
					$price
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
			if (strip_tags($listing->the_locations(false))) {
				$location = sprintf(
					'<li class="location"><i class="rtcl-icon rtcl-icon-location" aria-hidden="true"></i>%s</li>',
					$listing->the_locations( false )
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

		$views_html = '';
		if ( $instance['rtcl_show_views'] ) {
			$views = absint( get_post_meta( get_the_ID(), '_views', true ) );
			if ( $views ) {
				$views_html = sprintf(
					'<li class="view"><i class="rtcl-icon rtcl-icon-eye" aria-hidden="true"></i>%s</li>',
					sprintf(
					/* translators: %s: views count */
						_n( '%s view', '%s views', $views, 'rtcl-elementor-builder' ),
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
		if ( $types || $time || $location || $views_html ) {
			$listing_meta = sprintf( '<ul class="rtcl-listing-meta-data">%s %s %s %s</ul>', $types, $time, $location, $views_html );
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
				'<h3 class="listing-title rtcl-listing-title"><a href="%1$s" title="%2$s">%2$s</a> </h3>',
				get_the_permalink(),
				esc_html( get_the_title() )
			);
		}
		if ( $instance['rtcl_show_description'] ) {
			$excerpt             = wp_trim_words( get_the_excerpt( $_id ), $instance['rtcl_content_limit'], '' );
			$listing_description = sprintf(
				'<div class="rtcl-short-description"> %s </div>',
				wpautop( $excerpt )
			);
		}
		ob_start();
		?>
		<?php if ( Functions::is_enable_favourite() || $instance['rtcl_show_user'] ) : ?>
			<div class="rtin-bottom  <?php echo ! empty( $instance['rtcl_show_user'] ) ? 'has-author' : ''; ?>">
				<ul>
					<?php if ( $instance['rtcl_show_user'] ) : ?>
						<li class=" action-btn item-author">
							<a href="<?php echo esc_url( $listing->get_the_author_url() ); ?>">
								<div class="item-img">
									<?php
									$pp_id = absint( get_user_meta( $listing->get_owner_id(), '_rtcl_pp_id', true ) );
									echo( $pp_id ? wp_get_attachment_image( $pp_id, [ 40, 40 ] ) : get_avatar( $_id, 40 ) );
									?>
								</div>
							</a>
							<?php
							if( ! empty( $instance['rtcl_verified_user_base'] ) ){
								do_action('rtcl_after_author_meta', $listing->get_owner_id() );
							}
							?>
						</li>
					<?php endif; ?>
					<?php if ( Functions::is_enable_favourite() && $instance['rtcl_show_favourites'] ) : ?>
						<li class="action-btn">
							<?php
							echo Functions::get_favourites_link( $_id );
							?>
						</li>
					<?php endif; ?>
					<?php
					if ( rtcl()->has_pro() ) {
						if ( ! empty( $instance['rtcl_show_quick_view'] ) ) :
							?>
							<li class="action-btn">
								<a class="rtcl-quick-view" href="#"
								   title="<?php esc_attr_e( 'Quick View', 'rtcl-elementor-builder' ); ?>"
								   data-listing_id="<?php echo absint( $_id ); ?>">
									<i class="rtcl-icon rtcl-icon-zoom-in"></i>
								</a>
							</li>
							<?php
						endif;
					}
					?>
					<?php
					if ( rtcl()->has_pro() ) {
						if ( ! empty( $instance['rtcl_show_compare'] ) ) :
							?>
							<li class="action-btn">
								<?php
								$compare_ids    = ! empty( $_SESSION['rtcl_compare_ids'] ) ? $_SESSION['rtcl_compare_ids'] : [];
								$selected_class = '';
								if ( is_array( $compare_ids ) && in_array( $_id, $compare_ids ) ) {
									$selected_class = ' selected';
								}
								?>
								<a class="rtcl-compare <?php echo esc_attr( $selected_class ); ?>" href="#"
								   title="<?php esc_attr_e( 'Compare', 'rtcl-elementor-builder' ); ?>"
								   data-listing_id="<?php echo absint( $_id ); ?>">
									<i class="rtcl-icon rtcl-icon-retweet"></i>
								</a>
							</li>
							<?php
						endif;
					}
					?>
				</ul>
			</div>
		<?php endif; ?>
		<?php
		$rtin_bottom    = ob_get_clean();
		
		if (!empty($instance['rtcl_show_custom_fields'])) {
			ob_start();
			if ( rtcl()->has_pro() ) {
				TemplateHooks::loop_item_listable_fields();
			}
			$custom_field = ob_get_clean();
		}

		$item_content   = sprintf(
			'<div class="item-content"> %s %s %s %s %s %s %s</div>',
			$labels,
			$category,
			$listing_title,
			$custom_field,
			$listing_meta,
			$listing_description,
			$rtin_bottom
		);
		$final_contents = sprintf( '%s%s', $img, $item_content );
		echo wp_kses_post( $final_contents );
		?>

	</div>
