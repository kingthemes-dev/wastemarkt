<?php
/**
 * @author  RadiusTheme
 * @since   1.18
 * @version 1.18
 */

if ( !defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly
}

if (!class_exists('RtclPro')) return;

use radiustheme\ClassiList\URI_Helper;
use radiustheme\ClassiList\Listing_Functions;

global $listing;

$listing_post = $listing->get_listing();

$category = $listing->get_categories();
$category = end( $category );

$type = Listing_Functions::get_listing_type( $listing );

$class  = ' rtcl-listing-item';
$class .= isset( $top_listing ) ? ' rtin-top' : '';
$class .= $listing->is_featured() ? ' featured-listing' : '';

if ( !isset( $layout ) ) {
	$layout = 1;
}

if ( !isset( $display ) ) {
	$display = array();
}

if ( !isset( $map ) ) {
	$map = false;
}

$display_defaults = array(
	'cat'      => $listing->can_show_category(),
	'excerpt'  => $listing->can_show_excerpt(),
	'date'     => $listing->can_show_date(),
	'user'     => $listing->can_show_user(),
	'location' => $listing->can_show_location(),
	'views'    => $listing->can_show_views(),
	'price'    => $listing->can_show_price(),
	'fields'   => true,
	'label'    => true,
	'type'     => true,
);

$display = wp_parse_args( $display, $display_defaults );
$display = apply_filters( 'classilist_list_view_display_args', $display );

if ( !$category ) {
	$display['cat'] = false;
}

URI_Helper::get_custom_listing_template( 'list-items/archive-list-' . $layout, true, compact( 'listing', 'listing_post', 'category', 'class', 'display', 'type', 'map' ) );