<?php

use RadisuTheme\ClassifiedListingToolkits\Hooks\Helper;
use Rtcl\Helpers\Functions;
use Rtcl\Helpers\Pagination;

$column = ! empty( $instance['rtcl_store_column'] ) ? $instance['rtcl_store_column'] : 4;

$tab_column    = ! empty( $instance['rtcl_store_column_tablet'] ) ? $instance['rtcl_store_column_tablet'] : $column;
$mobile_column = ! empty( $instance['rtcl_store_column_mobile'] ) ? $instance['rtcl_store_column_mobile'] : $tab_column;

$col_class = 'column-lg-' . $column . ' column-sm-' . $tab_column . ' column-xs-' . $mobile_column;

?>
<div class="rtcl rtcl-elementor-widget rtcl-el-store-widget-wrapper">
    <div class="rtcl-elementor-widget rtcl-el-listing-store-grid <?php echo esc_attr( $col_class ); ?>">
		<?php
		$the_loops = $instance['stores'];
		while ( $the_loops->have_posts() ):
			$the_loops->the_post();
			$data = [
				'id'       => get_the_ID(),
				'instance' => $instance,
			];
			Functions::get_template( 'divi/listing-store/store-grid-item', $data, '', Helper::get_plugin_template_path() );
		endwhile; ?>
    </div>

	<?php if ( $the_loops->max_num_pages > 1 && $instance['rtcl_store_load_more_button'] == 'yes' ):
		$selectedQuery = [
			'cat'     => $instance['store_cat'],
			'orderby' => $instance['store_orderby'],
			'order'   => $instance['store_order'],
		];
		$options = [
			'rtcl_show_title' => $instance['rtcl_show_title'],
			'rtcl_show_image' => $instance['rtcl_show_image'],
			'rtcl_show_time'  => $instance['rtcl_show_time'],
			'rtcl_show_count' => $instance['rtcl_show_count'],
		];
		?>
        <div class="text-center load-more-wrapper layout-<?php echo esc_attr( $instance['rtcl_store_view'] ); ?>"
             data-total-pages="<?php echo esc_attr( $the_loops->max_num_pages ); ?>"
             data-page="1"
             data-layout="<?php echo esc_attr( $instance['rtcl_store_view'] ); ?>"
             data-query='<?php echo json_encode( $selectedQuery ); ?>'
             data-options='<?php echo json_encode( $options ); ?>'
             data-posts-per-page="<?php echo esc_attr( $instance['posts_per_page'] ); ?>">
            <button id="rtcl_store_load_more" class="btn load-more-btn">
                <i class="fas fa-sync-alt"></i>
				<?php esc_html_e( 'Load More', 'classified-listing-store' ); ?>
            </button>
        </div>
	<?php endif; ?>

	<?php wp_reset_postdata(); ?>

	<?php
	if ( ! empty( $instance['rtcl_store_pagination'] ) && $instance['rtcl_store_load_more_button'] !== 'yes' ) {
		Pagination::pagination( $the_loops );
	}
	?>
</div>