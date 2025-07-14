<?php
/**
 * @author  RadiusTheme
 * @since   1.0
 * @version 1.0
 */

namespace radiustheme\ClassiList;

if (!class_exists('RtclPro')) return;

use Rtcl\Helpers\Link;
use RtclPro\Helpers\Fns;
use Rtcl\Helpers\Functions;
use RtclMarketplace\Hooks\ActionHooks;
use RtclPro\Controllers\Hooks\TemplateHooks;

?>
<div class="listing-list-each listing-list-each-2<?php echo esc_attr( $class ); ?>">
	<div class="rtin-item">
		<div class="rtin-thumb">
			<a class="rtin-thumb-inner rtcl-media" href="<?php the_permalink(); ?>"><?php $listing->the_thumbnail(); ?></a>
            <?php TemplateHooks::sold_out_banner(); ?>
        </div>
		<div class="rtin-content">
			<h3 class="rtin-title listing-title"><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h3>

			<?php
			if ( $display['label'] ) {
				$listing->the_badges();
			}
			?>

			<?php
			if ( $display['fields'] ) {
				TemplateHooks::loop_item_listable_fields();
			}
			?>

			<ul class="rtin-meta">
				<?php if ( $display['type'] && $type ): ?>
                    <li><i class="fa fa-fw <?php echo esc_attr( $type['icon'] );?>" aria-hidden="true"></i><?php echo esc_html( $type['label'] ); ?></li>
				<?php endif; ?>
				<?php if ( $display['date'] ): ?>
					<li><i class="far fa-clock" aria-hidden="true"></i><?php $listing->the_time();?></li>
				<?php endif; ?>

				<?php if ($listing->can_show_user()): ?>
					<li class="rtin-usermeta"><i class="far fa-user"></i>
						<?php if ($listing->can_add_user_link() && !is_author()) : ?>
		                    <a href="<?php echo esc_url($listing->get_the_author_url()); ?>"><?php $listing->the_author(); ?></a>
		                <?php else: ?>
		                    <?php $listing->the_author(); ?>
		                <?php endif; ?>
		                <?php do_action('rtcl_after_author_meta', $listing->get_owner_id() ); ?>
	                </li>
                <?php endif; ?>
                
				<?php if ( $display['location'] && $listing->has_location() ): ?>
					<li><i class="fas fa-map-marker-alt"></i><?php $listing->the_locations( true, true ); ?></li>
				<?php endif; ?>
				<?php if ( $display['cat'] ):
					if ( $listing->has_category() && $listing->can_show_category() ){
						$categories = $listing->get_categories();
						?>
                        <li>
                            <i class="fas fa-tag"></i>
							<?php
							foreach ( $categories as $category ) {
								echo $glue ?? '';
								?>
                                <a href="<?php echo esc_url( get_term_link( $category ) ); ?>"><?php echo esc_html( $category->name ); ?></a>
								<?php
								$glue = '<span class="rtcl-separator">, </span>';
							}
							?>
                        </li>
					<?php } endif; ?>
				<?php if ( $display['views'] ): ?>
					<li><i class="fa fa-eye" aria-hidden="true"></i><?php echo sprintf( esc_html__( '%1$s Views', 'classilist' ) , number_format_i18n( $listing->get_view_counts() ) ); ?></li>
				<?php endif; ?>
			</ul>
            <?php if ( Functions::is_enable_favourite() || Fns::is_enable_quick_view() || Fns::is_enable_compare() ) { ?>
                <div class="rtin-listing-features">
                    <!-- Favourite -->
                    <?php if (Functions::is_enable_favourite()) { ?>
                        <div class="rtin-fav"><?php echo Functions::get_favourites_link( $listing->get_id() ); ?></div>
                    <?php } ?>
                    <!-- Quick View -->
                    <?php if (Fns::is_enable_quick_view()) { ?>
                        <div class="rtin-quick-view">
                            <a class="rtcl-quick-view" href="#" data-listing_id="<?php echo absint($listing->get_id()) ?>">
                                <i class="fa fa-search-plus"></i><span><?php esc_html_e("Quick View", "classilist"); ?></span>
                            </a>
                        </div>
                    <?php } ?>
                    <!-- Compare -->
                    <?php if (Fns::is_enable_compare()) {
                        $compare_ids = !empty($_SESSION['rtcl_compare_ids']) ? $_SESSION['rtcl_compare_ids'] : [];
                        $selected_class = '';
                        if (is_array($compare_ids) && in_array($listing->get_id(), $compare_ids)) {
                            $selected_class = ' selected';
                        }
                        ?>
                        <div class="rtin-compare">
                            <a class="rtcl-compare <?php echo esc_attr($selected_class); ?>" href="#" data-listing_id="<?php echo absint($listing->get_id()) ?>">
                                <i class="fa fa-retweet"></i><span><?php esc_html_e("Compare", "classilist") ?></span>
                            </a>
                        </div>
                    <?php } ?>
                </div>
            <?php } ?>

			<?php do_action( 'classilist_listing_list_view_after_content', $listing ); ?>

			<?php
                if ( class_exists('RtclMarketplace') ) {
                    ActionHooks::add_buy_button();
                }
			?>

		</div>
		
		<?php if ( $display['price'] ): ?>
			<div class="rtin-right">
				<div class="rtin-price"><?php Functions::print_html($listing->get_price_html()); ?></div>
			</div>
		<?php endif; ?>	

	</div>
	<?php do_action( 'classilist_listing_list_items_after_content_ends' );?>
	<?php if ( $map ) $listing->the_map_lat_long();?>
</div>