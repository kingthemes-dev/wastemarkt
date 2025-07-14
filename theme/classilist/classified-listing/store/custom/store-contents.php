<?php
/**
 * @author  RadiusTheme
 * @since   1.0
 * @version 1.0
 */

namespace radiustheme\ClassiList;

use Rtcl\Helpers\Functions;

global $store;
$store_oh_type = get_post_meta($store->get_id(), 'oh_type', true);
$store_oh_hours = get_post_meta($store->get_id(), 'oh_hours', true);
$store_oh_hours = is_array($store_oh_hours) ? $store_oh_hours : ($store_oh_hours ? (array)$store_oh_hours : []);
$today = strtolower(date('l'));

$days = array(
    esc_html__( 'Sunday', 'classilist' ),
    esc_html__( 'Monday', 'classilist' ),
    esc_html__( 'Tuesday', 'classilist' ),
    esc_html__( 'Wednesday', 'classilist' ),
    esc_html__( 'Thursday', 'classilist' ),
    esc_html__( 'Friday', 'classilist' ),
    esc_html__( 'Saturday', 'classilist' ),
);

$store_ads_query = Listing_Functions::store_query();

?> 
<div class="site-content-block classilist-single-details classilist-store-contents">
	<div class="main-content">

		<?php if ( $store_description = $store->get_the_description() ): ?>
			<h3 class="rtin-store-label"><?php esc_html_e( 'Details', 'classilist' );?></h3>
			<div class="rtin-store-description mb30"><?php echo wp_kses_post( $store_description ); ?></div>
		<?php endif; ?>

		<h3 class="rtin-store-label"><?php esc_html_e( 'Opening Hours', 'classilist' );?></h3>
        <div class="rtin-store-hours-list">
            <?php if ( $store_oh_type == "selected" ): ?>
                <?php if ( !empty( $store_oh_hours ) && is_array( $store_oh_hours ) ): ?>
                    <?php foreach ( $store_oh_hours as $hKey => $oh_hour ): ?>
                        <div class="row<?php echo esc_attr( ( $hKey == $today ) ? ' current-store-hour' : '' ); ?>">
                            <div class="col-4">
                                <span class="hour-day"><?php echo esc_html( $days[$hKey] ); ?></span>
                            </div>
                            <div class="col-8">
                                <?php if ( isset( $oh_hour['active'] ) ): ?>
                                    <div class="oh-hours">
                                        <span><?php echo isset( $oh_hour['open'] ) ? esc_html( $oh_hour['open'] ) : ''; ?></span>
                                        <span>-</span>
                                        <span><?php echo isset($oh_hour['close'] ) ? esc_html( $oh_hour['close'] ) : ''; ?></span>
                                    </div>
                                <?php else: ?>
                                    <div class="oh-hours"><?php esc_html_e( 'Closed' , 'classilist') ?></div>
                                <?php endif; ?>
                            </div>
                        </div>

                    <?php endforeach; ?>
                <?php else: ?>
                    <div class="oh-always always-close"><?php esc_html_e( 'Permanently Closed', 'classilist' ); ?></div>
                <?php endif; ?>
            <?php elseif ( $store_oh_type == 'always' ): ?>
                <div class="oh-always always-open"><?php esc_html_e( 'Always Open' , 'classilist' ); ?></div>
            <?php endif; ?>
        </div>
	</div>
</div>

<div class="content-block-gap"></div>

<div class="listing-archive-top">
    <h2 class="rtin-title"><?php printf( esc_html__( 'All Ads from %s', 'classilist' ), $store->get_the_title() );?></h2>
</div>

<div class="rtcl rtcl-listings store-ad-listing-wrapper">
    <div class="rtcl-list-view rtcl-listing-wrapper" data-pagination='{"max_num_pages":<?php echo esc_attr( $store_ads_query->max_num_pages ) ?>, "current_page": 1, "found_posts":<?php echo esc_attr( $store_ads_query->found_posts ) ?>, "posts_per_page":<?php echo esc_attr( $store_ads_query->query_vars['posts_per_page'] ) ?>}'>
        <?php
        while ( $store_ads_query->have_posts() ) : $store_ads_query->the_post();
            Functions::get_template('content-listing');
        endwhile;
        wp_reset_postdata();
        ?>
    </div>
</div>