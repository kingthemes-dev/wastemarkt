<?php
/**
 * @author  RadiusTheme
 * @since   1.0
 * @version 1.18
 */

namespace radiustheme\ClassiList;

if (!class_exists('RtclPro')) return;

use Rtcl\Helpers\Functions;

$loc_class      = 'rtin-loc-space';
$radius_class   = 'rtin-radius-space';
$typ_class      = 'rtin-type-space';
$cat_class      = 'rtin-cat-space';
$key_class      = 'rtin-key-space';
$btn_class      = 'rtin-btn-holder';

$loc_text = esc_attr__( 'Select Location', 'classilist' );
$cat_text = esc_attr__( 'Select Category', 'classilist' );
$typ_text = esc_attr__( 'Select Type', 'classilist' );

$selected_location = $selected_category = false;

if ( get_query_var( 'rtcl_location' ) && $location = get_term_by( 'slug', get_query_var( 'rtcl_location' ), rtcl()->location ) ) {
	$selected_location = $location;
}

if ( get_query_var( 'rtcl_category' ) && $category = get_term_by( 'slug', get_query_var( 'rtcl_category' ), rtcl()->category ) ) {
	$selected_category = $category;
}

$style = RDTheme::$options['listing_search_style'];

?>
<div class="rtcl rtcl-search rtcl-search-inline classilist-listing-search">
	<form action="<?php echo esc_url( Functions::get_filter_form_url() ); ?>" class="form-vertical rtcl-widget-search-form rtcl-search-inline-form">
        <?php if ( !empty( RDTheme::$options['listing_search_items']['location'] ) ): ?>
            <?php if (method_exists('Rtcl\Helpers\Functions','location_type') && 'local' === Functions::location_type() ): ?>
                <div class="<?php echo esc_attr( $loc_class ); ?>">
                    <div class="form-group <?php echo esc_attr($style); ?>">
                        <?php if ( $style == 'suggestion' ): ?>
                        <div class="rtcl-search-input-button classilist-search-style-2 rtin-location">
                            <input type="text" data-type="location" class="rtcl-autocomplete rtcl-location" placeholder="<?php echo esc_attr( $loc_text ); ?>" value="<?php echo $selected_location ? $selected_location->name : '' ?>">
                            <input type="hidden" name="rtcl_location" value="<?php echo $selected_location ? $selected_location->slug : '' ?>">
                        </div>
                        <?php elseif ( $style == 'standard' ): ?>
                            <div class="rtcl-search-input-button classilist-search-style-2 rtin-location">
                                <?php
                                wp_dropdown_categories( array(
                                    'show_option_none'  => $loc_text,
                                    'option_none_value' => '',
                                    'taxonomy'          => rtcl()->location,
                                    'name'              => 'rtcl_location',
                                    'id'                => 'rtcl-location-search-' . wp_rand(),
                                    'class'             => 'form-control rtcl-location-search',
                                    'selected'          => get_query_var( 'rtcl_location' ),
                                    'hierarchical'      => true,
                                    'value_field'       => 'slug',
                                    'depth'             => Functions::get_location_depth_limit(),
                                    'show_count'        => false,
                                    'hide_empty'        => false,
                                ) );
                                ?>
                            </div>
                        <?php elseif ( $style == 'dependency' ): ?>
                            <div class="rtcl-search-input-button classilist-search-style-2 rtin-location">
                                <?php
                                Functions::dropdown_terms( array(
                                    'show_option_none' => $loc_text,
                                    'taxonomy'         => rtcl()->location,
                                    'name'             => 'l',
                                    'class'            => 'form-control',
                                    'selected'         => $selected_location ? $selected_location->term_id : 0
                                ) );
                                ?>
                            </div>
                        <?php else: ?>
                        <div class="rtcl-search-input-button rtcl-search-input-location">
                            <span class="search-input-label location-name">
                                <?php echo $selected_location ? esc_html( $selected_location->name ) : esc_html( $loc_text ) ?>
                            </span>
                            <input type="hidden" class="rtcl-term-field" name="rtcl_location" value="<?php echo $selected_location ? esc_attr( $selected_location->slug ) : '' ?>">
                        </div>
                        <?php endif; ?>
                    </div>
                </div>
            <?php else: ?>
                <div class="<?php echo esc_attr( $loc_class ); ?>">
                    <div class="form-group">
                        <div class="rtcl-search-input-button classilist-search-style-2 rtin-location">
                            <input type="text" name="geo_address" autocomplete="off"
                                   value="<?php echo !empty($_GET['geo_address']) ? esc_attr($_GET['geo_address']) : '' ?>"
                                   placeholder="<?php esc_html_e("Select a location", "classilist"); ?>"
                                   class="form-control rtcl-geo-address-input"/>
                            <i class="rtcl-get-location rtcl-icon rtcl-icon-target"></i>
                            <input type="hidden" class="latitude" name="center_lat"
                                   value="<?php echo !empty($_GET['center_lat']) ? esc_attr($_GET['center_lat']) : '' ?>">
                            <input type="hidden" class="longitude" name="center_lng"
                                   value="<?php echo !empty($_GET['center_lng']) ? esc_attr($_GET['center_lng']) : '' ?>">
                        </div>
                    </div>
                </div>
                <?php if ( !empty( RDTheme::$options['listing_search_items']['radius'] ) ): ?>
                    <div class="<?php echo esc_attr( $radius_class ); ?>">
                        <div class="form-group">
                            <div class="rtcl-search-input-button classilist-search-style-2 rtin-radius">
                                <input type="number" class="form-control" name="distance"
                                       value="<?php echo !empty($_GET['distance']) ? absint($_GET['distance']) : '' ?>"
                                       placeholder="<?php esc_html_e("Radius in Miles", "classilist"); ?>">
                            </div>
                        </div>
                    </div>
                <?php else: ?>
                    <input type="hidden" class="distance" name="distance" value="30">
                <?php endif; ?>
            <?php endif; ?>

        <?php endif; ?>

        <?php if ( !empty( RDTheme::$options['listing_search_items']['category'] ) ): ?>
            <div class="<?php echo esc_attr( $cat_class ); ?>">
                <div class="form-group <?php echo esc_attr($style); ?>">
                    <?php if ( $style == 'suggestion' || $style == 'standard' ): ?>
                        <div class="rtcl-search-input-button classilist-search-style-2 rtin-category">
                            <?php
                            wp_dropdown_categories( array(
                                'show_option_none'  => $cat_text,
                                'option_none_value' => '',
                                'taxonomy'          => rtcl()->category,
                                'name'              => 'rtcl_category',
                                'id'                => 'rtcl-category-search-' . wp_rand(),
                                'class'             => 'form-control rtcl-category-search',
                                'selected'          => get_query_var( 'rtcl_category' ),
                                'hierarchical'      => true,
                                'value_field'       => 'slug',
                                'depth'             => Functions::get_category_depth_limit(),
                                'show_count'        => false,
                                'hide_empty'        => false,
                            ) );
                            ?>
                        </div>
                    <?php elseif ( $style == 'dependency' ): ?>
                        <div class="rtcl-search-input-button classilist-search-style-2 classilist-search-dependency rtin-category">
                            <?php
                            Functions::dropdown_terms( array(
                                'show_option_none'  => $cat_text,
                                'option_none_value' => - 1,
                                'taxonomy'          => rtcl()->category,
                                'name'              => 'c',
                                'class'             => 'form-control rtcl-category-search',
                                'selected'          => $selected_category ? $selected_category->term_id : 0
                            ) );
                            ?>
                        </div>
                    <?php else: ?>
                        <div class="rtcl-search-input-button rtcl-search-input-category">
                            <span class="search-input-label category-name">
                                <?php echo $selected_category ? esc_html( $selected_category->name ) : esc_html( $cat_text ); ?>
                            </span>
                            <input type="hidden" name="rtcl_category" class="rtcl-term-field" value="<?php echo $selected_category ? esc_attr( $selected_category->slug ) : '' ?>">
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        <?php endif; ?>

        <?php if ( !empty( RDTheme::$options['listing_search_items']['type'] ) ): ?>
            <div class="<?php echo esc_attr( $typ_class ); ?>">
                <div class="form-group <?php echo esc_attr($style); ?>">
                    <div class="rtcl-search-input-button classilist-search-style-2 rtcl-search-input-type">
                        <?php
                        $listing_types = Functions::get_listing_types();
                        $listing_types = empty( $listing_types ) ? array() : $listing_types;
                        ?>
                        <select name="filters[ad_type]" class="form-control">
                            <option value=""><?php echo esc_html( $typ_text ); ?></option>
                            <?php foreach ( $listing_types as $key => $listing_type ): ?>
                                <option value="<?php echo esc_attr( $key ); ?>"><?php echo esc_html( $listing_type ); ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>
            </div>
        <?php endif; ?>

        <?php if ( !empty( RDTheme::$options['listing_search_items']['keyword'] ) ): ?>
            <div class="<?php echo esc_attr( $key_class ); ?>">
                <div class="form-group">
                    <div class="rtcl-search-input-button rtin-keyword">
                        <input type="text" data-type="listing" name="q" class="rtcl-autocomplete" placeholder="<?php esc_html_e('Enter Keyword here ...', 'classilist'); ?>" value="<?php if (isset($_GET['q'])) {echo esc_attr($_GET['q']);} ?>" />
                    </div>
                </div>
            </div>
        <?php endif; ?>

        <div class="<?php echo esc_attr( $btn_class ); ?>">
            <button type="submit" class="rtin-search-btn"><i class="fa fa-search" aria-hidden="true"></i><?php esc_html_e( 'Search', 'classilist' );?></button>
        </div>
	</form>
</div>