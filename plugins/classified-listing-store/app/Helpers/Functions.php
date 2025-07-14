<?php

namespace RtclStore\Helpers;

use DateInterval;
use DateTime;
use Exception;
use Rtcl\Helpers\Functions as RtclFunctions;
use Rtcl\Models\Payment;
use RtclStore\Models\Membership;
use RtclStore\Models\Store;
use WP_Term;


class Functions {

    public static function get_store_image_sizes() {
        $banner = wp_parse_args( RtclFunctions::get_option_item( 'rtcl_misc_settings', 'store_banner_size', [] ), [
            'width'  => 992,
            'height' => 300,
            'crop'   => 'yes'
        ] );
        $logo = wp_parse_args( RtclFunctions::get_option_item( 'rtcl_misc_settings', 'store_logo_size', [] ), [
            'width'  => 200,
            'height' => 150,
            'crop'   => 'yes'
        ] );

        return [
            'rtcl-store-banner' => $banner,
            'rtcl-store-logo'   => $logo,
        ];
    }

    public static function get_store_banner_sizes() {
        return apply_filters( 'rtcl_store_banner_sizes', [ 'rtcl-store-banner' => self::get_store_image_sizes()['rtcl-store-banner'] ] );
    }
    public static function get_store_Logo_sizes() {
        return apply_filters( 'rtcl_store_banner_sizes', [ 'rtcl-store-logo' => self::get_store_image_sizes()['rtcl-store-logo'] ] );
    }


    /**
     * @param $user_id
     *
     * @return int|string|null
     */
    public static function get_posted_ads_as_free( $user_id = null ) {
        $user_id = empty( $user_id ) ? get_current_user_id() : $user_id;
        $adsCount = 0;
        if ( $user_id ) {
            global $wpdb;
            $settings = RtclFunctions::get_option( 'rtcl_membership_settings' );
            $days = 30; // default days
            if ( isset( $settings['renewal_days_for_free_ads'] ) && ( $settings['renewal_days_for_free_ads'] !== '' ) ) {
                $days = absint( $settings['renewal_days_for_free_ads'] );
            }
            try {
                $current_date = new DateTime( current_time( 'mysql' ) );
                $end_date = $current_date->format( 'Y-m-d H:i:s' );
                $current_date->sub( new DateInterval( "P{$days}D" ) );
                $start_date = $current_date->format( 'Y-m-d H:i:s' );
            } catch ( Exception $e ) {
                return $adsCount;
            }
            $table = $wpdb->prefix . "rtcl_posting_log";
            $adsCount = $wpdb->get_var(
                $wpdb->prepare( "SELECT COUNT(*) FROM 
											{$table} 
											WHERE user_id = %d 
											AND (created_at BETWEEN %s AND %s)",
                    $user_id,
                    $start_date,
                    $end_date
                )
            );
        }

        return $adsCount;
    }

    /**
     * @param  $user_id
     *
     * @return int
     */
    static function user_is_valid_to_post_as_free( $user_id = null ) {
        $settings = RtclFunctions::get_option( 'rtcl_membership_settings' );
        if ( isset( $settings['enable_free_ads'] ) && $settings['enable_free_ads'] == "yes" ) {
            $ads = self::get_posted_ads_as_free( $user_id );
            $limit_ads = isset( $settings['number_of_free_ads'] ) ? absint( $settings['number_of_free_ads'] ) : 3;
            $remaining = $limit_ads - $ads;
            $remaining_ads_as_free = $remaining && $remaining > 0 ? $remaining : 0;

            return $remaining_ads_as_free ?: 0;
        }

        return 0;
    }


    /**
     * @param int $cat_id
     *
     * @return bool
     */
    static function is_valid_to_post_at_category( int $cat_id ): bool {
        $settings = RtclFunctions::get_option( 'rtcl_membership_settings' );
        $cats = isset( $settings['categories_of_free_ads'] ) && is_array( $settings['categories_of_free_ads'] ) ? $settings['categories_of_free_ads'] : [];
        if ( empty( $cats ) ) {
            return true;
        }
        $parents = get_ancestors( $cat_id, rtcl()->category, 'taxonomy' );
        if ( !empty( $parents ) ) {
            $parents = array_reverse( $parents );
            $cat_id = $parents[0];
        }

        return in_array( $cat_id, $cats );
    }

    static function is_enable_store_manager() {
        return RtclFunctions::get_option_item( 'rtcl_membership_settings', 'enable_store_manager', false, 'checkbox' );
    }

    static function is_renew_only_membership(): bool {
        return (bool)RtclFunctions::get_option_item( 'rtcl_membership_settings', 'renew_only_membership', false, 'checkbox' );
    }

    /**
     * @param Payment $payment
     *
     * @throws \Exception
     */
    public static function apply_membership( Payment $payment ) {
        if ( !$payment->is_applied() ) {
            $user_id = $payment->get_customer_id();
            $user = get_user_by( 'id', $user_id );
            $membership = new Membership( $user );
            $membership->apply_membership( $payment );
        }
    }

    /**
     * @param int $parent
     *
     * @return array|WP_Term[]
     */
    static function get_store_category( $parent = 0 ): array {

        $term_args = [
            'taxonomy'   => rtclStore()->category,
            'parent'     => $parent,
            'orderby'    => 'name',
            'order'      => 'ASC',
            'hide_empty' => false
        ];

        $terms = get_terms( $term_args );
        if ( is_wp_error( $terms ) ) {
            return [];
        }

        return $terms;
    }

    /**
     * @param int $store_id
     *
     * @return array
     */
    static function get_store_selected_term_id( int $store_id ): array {
        $selectedTermId = $parent = [];
        $selectedTerms = wp_get_post_terms(
            $store_id,
            rtclStore()->category,
            [
                'orderby' => 'term_id'
            ]
        );

        if ( !empty( $selectedTerms ) ) {
            $childTerm[] = end( $selectedTerms );
        }

        if ( !empty( $childTerm ) ) {
            foreach ( $childTerm as $term ) {
                if ( !in_array( $term->term_id, $selectedTermId ) ) {
                    $selectedTermId[] = $term->term_id;
                }
                $parent = get_term( $term, rtclStore()->category );
                while ( $parent->parent != '0' ) {
                    $term_id = $parent->parent;
                    if ( !in_array( $term_id, $selectedTermId ) ) {
                        $selectedTermId[] = $term_id;
                    }
                    $parent = get_term( $term_id, rtclStore()->category );
                }
            }
            $selectedTermId = array_reverse( $selectedTermId );
        }

        return [
            'termId' => $selectedTermId,
            'parent' => $parent
        ];
    }

    /**
     * @return array
     */
    static function get_first_level_category_array() {
        $terms = [];
        $termObjs = RtclFunctions::get_sub_terms( rtcl()->category );
        if ( !empty( $termObjs ) ) {
            $terms = wp_list_pluck( $termObjs, 'name', 'term_id' );
        }

        return $terms;
    }

    /**
     * @param $user_id
     *
     * @return Store || null
     */
    public static function get_user_store( $user_id ) {
        $user_id = $user_id ? absint( $user_id ) : null;
        $post = null;
        $getStore = get_posts( [
            'post_type'        => rtclStore()->post_type,
            'posts_per_page'   => 1,
            'post_status'      => [ 'publish', 'pending' ],
            'suppress_filters' => false,
            'meta_query'       => [
                [
                    'key'     => 'store_owner_id',
                    'value'   => $user_id,
                    'type'    => 'numeric',
                    'compare' => '=',
                ]
            ]
        ] );

        if ( !empty( $getStore ) && !empty( $getStore[0] ) ) {
            $post = $getStore[0];
        }

        $store = self::is_store_enabled() && $post ? rtclStore()->factory->get_store( $post->ID ) : null;

        return apply_filters( 'rtcl_store_get_user_store', $store, $user_id );
    }

    /**
     * @param int $user_id User id
     *
     * @return false|Store
     */
    public static function get_manager_store( $user_id = 0 ) {
        if ( ( $user_id = $user_id ? absint( $user_id ) : get_current_user_id() ) && ( $store_id = absint( get_user_meta( $user_id, '_rtcl_store_id', true ) ) ) && $store = rtclStore()->factory->get_store( $store_id ) ) {
            return $store;
        }

        return false;
    }

    /**
     * @return Store | null
     */
    public static function get_current_user_store() {
        return apply_filters( 'rtcl_store_get_current_user_store', self::get_user_store( get_current_user_id() ) );
    }

    /**
     * @param     $string
     * @param int $limit
     *
     * @return string
     */
    public static function limit_length( $string, $limit = 127 ) {
        if ( strlen( $string ) > $limit ) {
            $string = substr( $string, 0, $limit - 3 ) . '...';
        }

        return apply_filters( 'rtcl_store_limit_length', $string, $limit );
    }

    /**
     * @param \WP_Post|false $store
     *
     * @return bool
     */
    static function is_store_expired( $store = false ) {
        if ( !$store ) {
            global $post;
            $store = !empty( $post->ID ) ? rtclStore()->factory->get_store( $post->ID ) : null;
        }
        if ( $store && is_a( $store, Store::class ) && RtclFunctions::get_option_item( 'rtcl_membership_settings', 'display_store_only_valid_membership', false, 'checkbox' ) ) {
            if ( $user_id = absint( get_post_meta( $store->get_id(), 'store_owner_id', true ) ) ) {
                $member = rtclStore()->factory->get_membership( $user_id );
                if ( $member && $member->is_expired() ) {
                    return true;
                }
            }
        }

        return false;
    }

    /**
     * @param string $term
     *
     * @return bool
     */
    static function is_store_category( $term = '' ) {
        return is_tax( rtclStore()->category, $term );
    }

    /**
     * @return bool
     */
    static function is_store_taxonomy() {
        return is_tax( get_object_taxonomies( rtclStore()->post_type ) );
    }

    /**
     * Is it is Store Archive page
     *
     * @return bool
     */
    static function is_store() {
        return is_post_type_archive( rtclStore()->post_type ) || ( ( $store_page_id = RtclFunctions::get_page_id( 'store' ) ) && is_page( $store_page_id ) );
    }

    /**
     * Is it Single Store
     *
     * @return boolean
     */
    static function is_single_store() {
        return is_singular( [ rtclStore()->post_type ] );
    }

    /**
     * Is Store option is enabled
     *
     * @return mixed|void
     */
    static function is_store_enabled() {
        return apply_filters( 'rtcl_store_option_is_store_enabled', RtclFunctions::get_option_item( 'rtcl_membership_settings', 'enable_store', false, 'checkbox' ) );
    }


    /**
     * Main function for returning Store, uses the StoreFactory class.
     *
     * @param mixed $store Post object or post ID of the product.
     *
     * @return Store|null|false
     */
    public static function get_store( $store = false ) {
        return rtclStore()->factory->get_store( $store );
    }

    /**
     * @return String $format Store time format
     */
    public static function get_store_time_format() {
        $options = apply_filters( 'rtcl_store_time_options', [
            "icons" => [
                "up"   => 'rtcl-icon-up-open',
                "down" => 'rtcl-icon-down-open'
            ]
        ] );
        $format = "g:i A";
        if ( isset( $options['showMeridian'] ) && $options['showMeridian'] === false ) {
            $format = 'H:i';
        }

        return apply_filters( 'rtcl_store_time_format', $format );
    }

    public static function is_membership_enabled() {
        return RtclFunctions::get_option_item( 'rtcl_membership_settings', 'enable', false, 'checkbox' );
    }

    public static function is_enable_free_ads() {
        return self::is_membership_enabled() && RtclFunctions::get_option_item( 'rtcl_membership_settings', 'enable_free_ads', false, 'checkbox' );
    }

    /**
     * Output the pagination.
     */
    static function pagination() {
        if ( !self::get_loop_prop( 'is_paginated' ) ) {
            return;
        }

        $args = [
            'total'   => self::get_loop_prop( 'total_pages' ),
            'current' => self::get_loop_prop( 'current_page' ),
            'base'    => esc_url_raw( add_query_arg( 'store-page', '%#%', false ) ),
            'format'  => '?store-page=%#%',
        ];

        if ( !self::get_loop_prop( 'is_shortcode' ) ) {
            $args['format'] = '';
            $args['base'] = esc_url_raw( str_replace( 999999999, '%#%', get_pagenum_link( 999999999, false ) ) );
        }

        RtclFunctions::get_template( 'listing/loop/pagination', $args );
    }


    /**
     * Gets a property from the rtcl_loop global.
     *
     * @param string $prop Prop to get.
     * @param string $default Default if the prop does not exist.
     *
     * @return mixed
     * @since 1.2.31
     */
    static function get_loop_prop( $prop, $default = '' ) {
        self::setup_loop(); // Ensure shop loop is setup.

        return isset( $GLOBALS['rtcl_store_loop'], $GLOBALS['rtcl_store_loop'][$prop] ) ? $GLOBALS['rtcl_store_loop'][$prop] : $default;
    }

    /**
     * Sets a property in the rtcl_store_loop global.
     *
     * @param string $prop Prop to set.
     * @param string $value Value to set.
     *
     * @since 1.5.5
     */
    static function set_loop_prop( $prop, $value = '' ) {
        if ( !isset( $GLOBALS['rtcl_store_loop'] ) ) {
            self::setup_loop();
        }
        $GLOBALS['rtcl_store_loop'][$prop] = $value;
    }

    /**
     * Resets the rtcl_loop global.
     *
     * @since 1.5.5
     */
    static function reset_loop() {
        unset( $GLOBALS['rtcl_store_loop'] );
    }

    /**
     * Sets up the rtcl_loop global from the passed args or from the main query.
     *
     * @param array $args Args to pass into the global.
     *
     * @since 1.5.5
     */
    static function setup_loop( $args = [] ) {
        $default_args = [
            'loop'         => 0,
            'is_shortcode' => false,
            'is_paginated' => true,
            'is_search'    => false,
            'total'        => 0,
            'total_pages'  => 0,
            'per_page'     => 0,
            'current_page' => 1,
        ];

        // If this is a main RTCL query, use global args as defaults.
        if ( $GLOBALS['wp_query']->get( 'rtcl_store_query' ) ) {
            $default_args = array_merge(
                $default_args,
                [
                    'is_search'    => $GLOBALS['wp_query']->is_search(),
                    'total'        => $GLOBALS['wp_query']->found_posts,
                    'total_pages'  => $GLOBALS['wp_query']->max_num_pages,
                    'per_page'     => $GLOBALS['wp_query']->get( 'posts_per_page' ),
                    'current_page' => max( 1, $GLOBALS['wp_query']->get( 'paged', 1 ) ),
                ]
            );
        }

        // Merge any existing values.
        if ( isset( $GLOBALS['rtcl_store_loop'] ) ) {
            $default_args = array_merge( $default_args, $GLOBALS['rtcl_store_loop'] );
        }

        $GLOBALS['rtcl_store_loop'] = wp_parse_args( $args, $default_args );
    }

    public static function store_loop_start( $echo = true ) {
        self::set_loop_prop( 'loop', 0 );
        $loop_start = apply_filters( 'rtcl_store_loop_start', RtclFunctions::get_template_html( 'store/loop/loop-start', null, '', rtclStore()->get_plugin_template_path() ) );

        if ( $echo ) {
            echo $loop_start; // WPCS: XSS ok.
        } else {
            return $loop_start;
        }
    }

    public static function store_loop_end( $echo = true ) {

        $loop_end = apply_filters( 'rtcl_store_loop_end', RtclFunctions::get_template_html( 'store/loop/loop-end', null, '', rtclStore()->get_plugin_template_path() ) );

        if ( $echo ) {
            echo $loop_end; // WPCS: XSS ok.
        } else {
            return $loop_end;
        }
    }


    /**
     * Retrieves the classes for the post div as an array.
     *
     * @param string|array $class One or more classes to add to the class list.
     * @param int|\WP_Post|Store $store Store ID or store object.
     *
     * @return array
     * @since 1.2.31
     */
    static function get_store_class( $class = '', $store = null ) {
        if ( is_null( $store ) && !empty( $GLOBALS['store'] ) ) {
            // Product was null so pull from global.
            $store = $GLOBALS['store'];
        }

        if ( $store && !is_a( $store, Store::class ) ) {
            $store = rtclStore()->factory->get_store( $store );
        }
        $class = $class && !is_array( $class ) ? preg_split( '#\s+#', $class ) : [];

        $post_classes = array_map( 'esc_attr', $class );

        if ( !$store ) {
            return $post_classes;
        }


        $post_classes = apply_filters( 'post_class', $post_classes, $class, $store->get_id() );

        $classes = array_merge(
            $post_classes,
            [ 'post-' . $store->get_id() ],
            $store->get_label_class(),
            RtclFunctions::get_listing_taxonomy_class( $store->get_category_ids(), rtclStore()->category )
        );

        return array_map( 'esc_attr', array_unique( array_filter( $classes ) ) );
    }

    /**
     * Display the classes for the listing div.
     *
     * @param string|array $class One or more classes to add to the class list.
     * @param int|\WP_Post|Store $store_id Listing ID or product object.
     *
     * @since 1.5.4
     */
    static function store_class( $class = [], $store_id = null ) {
        $classes = self::get_store_class( $class, $store_id );
        if ( !empty( $classes ) ) {
            echo 'class="' . esc_attr( implode( ' ', $classes ) ) . '"';
        }
    }

    /**
     * Display the classes for the listing div
     *
     * @param string|array $classes One or more classes to add to the class list.
     *
     * @since 1.5.4
     */
    static function store_loop_start_class( $classes = [] ) {
        $columns = RtclFunctions::get_option_item( 'rtcl_membership_settings', 'stores_per_row', 4, 'number' );
        $columns_class = empty( $columns ) ? 'columns-4' : 'columns-' . $columns;

        $classes[] = 'rtcl-stores';
        $classes[] = apply_filters( 'rtcl_stores_grid_columns_class', $columns_class );
        $classes = array_map( 'esc_attr', array_unique( array_filter( $classes ) ) );
        $classes = apply_filters( 'rtcl_store_loop_start_class', $classes );
        if ( !empty( $classes ) ) {
            echo 'class="' . esc_attr( implode( ' ', $classes ) ) . '"';
        }
    }

    static function page_title( $echo = true ) {

        if ( is_search() ) {
            /* translators: %s: search query */
            $page_title = sprintf( esc_html__( 'Search results: &ldquo;%s&rdquo;', 'classified-listing-store' ), get_search_query() );

            if ( get_query_var( 'paged' ) ) {
                /* translators: %s: page number */
                $page_title .= sprintf( esc_html__( '&nbsp;&ndash; Page %s', 'classified-listing-store' ), get_query_var( 'paged' ) );
            }
        } elseif ( is_tax() ) {

            $page_title = single_term_title( '', false );

        } else {
            $listings_page_id = RtclFunctions::get_page_id( 'store' );
            $page_title = get_the_title( $listings_page_id );
        }

        $page_title = apply_filters( 'rtcl_page_title', $page_title );

        if ( $echo ) {
            echo $page_title; // WPCS: XSS ok.
        } else {
            return $page_title;
        }
    }


    /**
     * @param $data
     *
     * @return bool
     */
    static function update_posting_log( $data ): bool {
        global $wpdb;

        $data = wp_parse_args( $data, [
            'post_id'    => '',
            'user_id'    => null,
            'cat_id'     => null,
            'status'     => 'new',
            'created_at' => null
        ] );

        if ( empty( $data['post_id'] ) || empty( $data['user_id'] ) || empty( $data['cat_id'] ) || empty( $data['created_at'] ) || !in_array( $data['status'], [
                'new',
                'renew'
            ] ) ) {
            return false;
        }
        $wpdb->hide_errors();

        return $wpdb->insert(
            $wpdb->prefix . 'rtcl_posting_log',
            $data,
            [
                '%d',
                '%d',
                '%d',
                '%s',
                '%s'
            ]
        );
    }

}
