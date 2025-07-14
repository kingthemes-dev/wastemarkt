<?php
/**
 * @author  RadiusTheme
 * @since   1.0
 * @version 2.2.4
 */

namespace radiustheme\ClassiList;

use RtclPro\Helpers\Fns;
use Rtcl\Helpers\Functions;
use Rtcl\Helpers\Pagination;
use radiustheme\ClassiList\URI_Helper;
use Rtcl\Services\FormBuilder\FBField;
use Rtcl\Controllers\Hooks\TemplateHooks as FreeTemplateHooks;

class Listing_Functions {

	protected static $instance = null;

	public function __construct() {
		add_action( 'after_setup_theme',   array( $this, 'theme_support' ) );
		add_filter( 'get_the_archive_title', array( $this, 'archive_title' ) );
		add_filter( 'rtcl_default_placeholder_url', array( $this, 'placeholder_img_url' ) ); // change placeholder image

		add_action( 'init', function(){
				add_action( 'classilist_listing_list_view_after_content', array( $this, 'fav_listing_delete_btn' ) ); // delete from fav button

		} );

		add_action( 'classilist_listing_list_items_after_content_ends', array( $this, 'top_ad_sign' ) ); // Top Ad sign

        add_filter( 'classilist_single_listing_time_format', array( $this, 'classilist_change_listing_time_format'), 20, 1 );
        add_filter( 'classilist_listing_grid_col_class', array( $this, 'classilist_listing_archive_grid' ), 10, 2 );

		// Override plugin options
		add_filter( 'rtcl_general_settings', array( $this, 'override_general_settings' ) );
		add_filter( 'rtcl_style_settings', array( $this, 'override_style_settings' ) );
		add_filter( 'rtcl_bootstrap_dequeue', '__return_false' );

		// Remove price type from single listing
		add_filter( 'rtcl_add_price_type_at_price', '__return_empty_string' );

        // Store Filter
        add_filter('rtcl_stores_grid_columns_class', array( $this, 'classilist_rtcl_stores_grid_columns_class' ) );
        add_filter( 'rtcl_store_time_options', array( $this, 'classilist_rtcl_store_time_options_rt_cb') );

        // Add excerpt in quick view
        add_action('rtcl_quick_view_summary', [ $this, 'listing_excerpt' ], 60 );

        // Remove form submit button
        // Profile page
        remove_action( 'rtcl_before_main_content', [ FreeTemplateHooks::class, 'breadcrumb'], 6 );
		if (method_exists('Rtcl\Helpers\Functions','has_map') && Functions::has_map()) {
			if ('geo' === Functions::location_type()) {
				remove_action('rtcl_edit_account_form', [FreeTemplateHooks::class, 'edit_account_form_geo_location'], 50);
			}
			remove_action('rtcl_edit_account_form', [ FreeTemplateHooks::class, 'edit_account_map_field' ], 60);
		}
	    add_action( 'admin_notices', [ $this, 'rtcl_merge_notice'] );

	    // Override Related Listing Item Number
        add_filter('rtcl_related_slider_options', function ( $slider_options ) {
            $rand = substr(md5(mt_rand()), 0, 7);
            $slider_options = [
                "navigation"        => [
                    "nextEl"            => ".rtin-custom-nav-$rand .owl-next",
                    "prevEl"            => ".rtin-custom-nav-$rand .owl-prev",
                ],
                "loop"              => false,
                "autoplay"          => [
                    "delay" => 3000,
                    "disableOnInteraction"  => false,
                    "pauseOnMouseEnter"     => true
                ],
                "speed"             => 1000,
                "spaceBetween"      => 20,
                "breakpoints"       => [
                    0   => [
                        "slidesPerView" => 1
                    ],
                    500   => [
                        "slidesPerView" => 2
                    ],
                    1200 => [
                        "slidesPerView" => 3
                    ]
                ]
            ];
            return $slider_options;
        });

	}

	public static function instance() {
		if ( null == self::$instance ) {
			self::$instance = new self;
		}
		return self::$instance;
	}

	public function theme_support() {
		add_theme_support( 'rtcl' );
	}

    public function rtcl_merge_notice() {
        $rtcl_dir = '';

        if (defined('WP_PLUGIN_DIR')) {
            $rtcl_dir = WP_PLUGIN_DIR . '/classified-listing-pro/classified-listing-pro.php';
        }

        if (file_exists($rtcl_dir)) {
            $rtcl_info = get_plugin_data($rtcl_dir);
            $version = $rtcl_info['Version'];
            if (version_compare($version, '2.0.0', '<')) {
                $message = sprintf(__('You have to must update <strong>Classified Listing Pro</strong> plugin, otherwise functionality will not work properly.', 'classilist'));
                printf( '<div class="notice notice-error"><p>%1$s</p></div>', $message );
            }
        }

        $class = 'notice notice-warning';

        if (is_child_theme()) {
            $link = '<strong><a target="_blank" href="https://www.radiustheme.com/support/">support</a></strong>';
            $child_message = sprintf(__('If you face any issue after update, please switch to parent theme and contact with %s.', 'classilist'), $link);
            printf( '<div class="%1$s"><p>%2$s</p></div>', esc_attr( $class ), $child_message );
        }
    }

    public function archive_title( $title ) {
        if ( is_post_type_archive( 'rtcl_listing' ) || is_tax( 'rtcl_category' ) || is_tax( 'rtcl_location' ) ) {
            if ( is_tax( 'rtcl_category' ) || is_tax( 'rtcl_location' ) ) {
                $title = single_cat_title( '', false );
            } else {
                $id = Functions::get_page_id( 'listings' );
                $title = get_the_title( $id );
            }
        }
        return $title;
    }

	public function placeholder_img_url() {
		return URI_Helper::get_img( 'noimage-listing-thumb.jpg' );
	}

    public function listing_excerpt( $listing ) {
        if (!$listing) {
            return;
        }
        $listing->the_excerpt();
    }

	public function override_general_settings( $settings ){
		$settings['load_bootstrap'] = '';
		return $settings;
	}

	public function override_style_settings( $settings ){
		$prefix = Constants::$theme_prefix;
		$primary_color    = apply_filters( "{$prefix}_primary_color", RDTheme::$options['primary_color'] ); // #1aa78e
		$secondery_color  = apply_filters( "{$prefix}_secondery_color", RDTheme::$options['secondery_color'] ); // #fcaf01

		$args = array(
			'primary'           => $primary_color,
			'link'              => $primary_color,
			'link_hover'        => $secondery_color,
			'button'            => $primary_color,
			'button_hover'      => $secondery_color,
			'button_text'       => '#ffffff',
			'button_hover_text' => '#ffffff',
		);

		$settings = wp_parse_args( $args, $settings );
		
		return $settings;
	}

	// public function template_include( $template ){
	// 	if( Functions::is_account_page() ){
	// 		$new_template  = URI_Helper::get_custom_listing_template( 'listing-account', false );
	// 		$new_template = locate_template( array( $new_template ) );
	// 		return $new_template;
	// 	}

	// 	return $template;
	// }

    public function classilist_rtcl_store_time_options_rt_cb( $data ) {

        $format = isset(RDTheme::$options['time_format']) ? RDTheme::$options['time_format'] : true;

        if( $format == false) {
            $data['showMeridian'] = false;
        }

        return $data;
    }

	public function my_account_listing_contents( $listing ){
		if( Functions::is_account_page( 'listings' ) || ( function_exists('bp_is_user') && bp_is_user() ) ){
			URI_Helper::get_custom_listing_template( 'myaccount-contents', true, compact( 'listing' ) );
		}
	}

	public function fav_listing_delete_btn( $listing ){
		if( !Functions::is_account_page( 'favourites' ) ) return;
		?>
		<div class="rtin-action-btn">
			<a href="#" class="btn rtcl-delete-favourite-listing" data-id="<?php echo esc_attr( $listing->get_id() ); ?>"><?php esc_html_e( 'Remove from Favourites', 'classilist' ) ?></a>
		</div>
		<?php
	}

	public function top_ad_sign(){
		?>
		<div class="topad-sign"><i class="fa fa-trophy" aria-hidden="true"></i><?php esc_html_e( 'Top Ad', 'classilist' ); ?></div>
		<?php
	}

    public function classilist_listing_archive_grid( $col_class, $map ) {

        $col_class = isset(RDTheme::$options['grid_desktop_column']) ? 'col-xl-'.RDTheme::$options['grid_desktop_column'] : 'col-xxl-4 col-xl-4';
        $col_class .= isset(RDTheme::$options['grid_tablet_column']) ? ' col-md-'.RDTheme::$options['grid_tablet_column'] . ' col-sm-'.RDTheme::$options['grid_tablet_column'] : ' col-lg-6 col-md-6 col-sm-6';
        $col_class .= isset(RDTheme::$options['grid_mobile_column']) ? ' col-'.RDTheme::$options['grid_mobile_column'] : ' col-12';

        return $col_class;
    }

    public static function set_top_query_globally( $query ) {
        global $rtclTopListingIds;

        $rtclTopListingIds = [];
        $paginated         = ! $query->get( 'no_found_rows' );
        $listings          = (object) [
            'total'        => $paginated ? (int) $query->found_posts : count( $query->posts ),
            'total_pages'  => $paginated ? (int) $query->max_num_pages : 1,
            'per_page'     => (int) $query->get( 'posts_per_page' ),
            'current_page' => $paginated ? (int) max( 1, $query->get( 'paged', 1 ) ) : 1,
        ];

        Functions::setup_loop(
            [
                'is_shortcode' => true,
                'is_search'    => false,
                'is_paginated' => false,
                'as_top'       => true,
                'total'        => $listings->total,
                'total_pages'  => $listings->total_pages,
                'per_page'     => $listings->per_page,
                'current_page' => $listings->current_page
            ]
        );

        if ( Functions::get_loop_prop( 'total' ) ) {
            while ( $query->have_posts() ) : $query->the_post();
                $rtclTopListingIds[] = get_the_ID();
            endwhile;
            wp_reset_postdata();
        }

        Functions::reset_loop();

        if ( ! empty( $rtclTopListingIds ) ) {
            global $wp_query;
            $args             = $wp_query->query_vars;
            $existingExcluded = $wp_query->get( 'post__not_in' );
            if ( ! is_array( $existingExcluded ) ) {
                $existingExcluded = [];
            }
            $args['post__not_in'] = array_merge( $existingExcluded, $rtclTopListingIds );
            $wp_query             = new \WP_Query( $args );
        }
    }

	public static function listing_count_text( $post_num ) {
		if ( $post_num ) {
			if ( $post_num['total'] == 1 ) {
				$post_num_text = esc_html__( 'Showing 1 result', 'classilist' );
			}
			else {
				$post_num_text = sprintf( esc_html__( 'Showing %1$d–%2$d of %3$d results', 'classilist' ), $post_num['first'], $post_num['last'], $post_num['total'] );
			}
		}
		else {
			$post_num_text = esc_html__( 'Showing 0 result', 'classilist' );
		}
		return $post_num_text;
	}

	public static function listing_post_num( $rtcl_query ){

		$total = $rtcl_query->found_posts;
		$current = $rtcl_query->post_count;

		if ( $current ) {
			$posts_per_page = $rtcl_query->query_vars['posts_per_page'];
			$paged = !empty( $rtcl_query->query['paged'] ) ? $rtcl_query->query['paged'] : 1;
			$num_of_skipped_items = $posts_per_page * ($paged - 1);

			$first = $num_of_skipped_items + 1;
			$last = $num_of_skipped_items + $current;

			$result = array(
				'first' => $first,
				'last'  => $last,
				'total' => $total,
			);
		}
		else {
			$result = false;
		}

		return $result;
	}

	public static function listing_query( $view, $rtcl_query, $rtcl_top_query = false, $map = false ){
		$map = false;
		if ( $view == 'grid' ) { ?>
			<div class="row auto-clear">
				<?php
				$col_class =  $map ? 'col-xxl-6 col-xl-6 col-lg-6 col-md-6 col-sm-6 col-12' : 'col-xxl-4 col-xl-4 col-lg-6 col-md-6 col-sm-6 col-12';
                $col_class = apply_filters( 'classilist_listing_grid_col_class', $col_class, $map );

                if (Fns::is_enable_top_listings()) {
                    if ( is_object( $rtcl_top_query ) && $rtcl_top_query->have_posts() ) {
                        $top_listing = true;
                        while ( $rtcl_top_query->have_posts() ): $rtcl_top_query->the_post();?>
                            <div class="<?php echo esc_attr( $col_class );?>">
                                <?php Functions::get_template( 'custom/grid', compact( 'top_listing', 'map' ) );?>
                            </div>
                            <?php
                        endwhile;
                        wp_reset_postdata();
                    }
                }

				while ( $rtcl_query->have_posts() ): $rtcl_query->the_post();?>
					<div class="<?php echo esc_attr( $col_class );?>">
						<?php Functions::get_template( 'custom/grid', compact( 'map' ) ); ?>
					</div>
					<?php
				endwhile;
				wp_reset_postdata(); ?>
			</div>
			<?php
		}
		else {
			$layout  = NULL;
			$display = array();
			if ( $map ) {
				$layout = 'map';
				$display = array(
					'excerpt'  => false,
				);
			}

            if (Fns::is_enable_top_listings()) {
                if (is_object($rtcl_top_query) && $rtcl_top_query->have_posts()) {
                    $top_listing = true;
                    while ($rtcl_top_query->have_posts()) : $rtcl_top_query->the_post();
                        Functions::get_template('custom/list', compact('top_listing', 'map', 'layout', 'display'));
                    endwhile;
                    wp_reset_postdata();
                }
            }

			while ( $rtcl_query->have_posts() ) : $rtcl_query->the_post();
				Functions::get_template( 'custom/list', compact( 'map', 'layout', 'display' ) );
			endwhile; wp_reset_postdata();			
		}
	}

	public static function get_single_contact_address( $listing ){

		$listing_id = $listing->get_id();
		$listing_locations = $listing->get_locations();

		$render = $loc = '';

		$address = get_post_meta( $listing_id, 'address', true );
		$address = $address && Functions::get_option_item( 'rtcl_moderation_settings', 'display_options_detail', 'address', 'multi_checkbox' ) ? $address : '';

		$zipcode = get_post_meta( $listing_id, 'zipcode', true );
		$zipcode = $zipcode && Functions::get_option_item( 'rtcl_moderation_settings', 'display_options_detail', 'zipcode', 'multi_checkbox' ) ? $zipcode : '';

		$locations = array();
		if ( count( $listing_locations ) ) {
			foreach ( $listing_locations as $location ) {
				$locations[] = $location->name;
			}
			$locations = array_reverse( $locations );
			$loc = implode( ', ', $locations );
		}

		if ( $address ) {
			$render .= sprintf( '<div>%s</div>' , $address );
		}

		if ( $address && $loc && $zipcode ) {
			$render .= sprintf( '<div>%s, %s</div>' , $loc, $zipcode );
		}
		elseif ( $address && $loc ) {
			$render .= sprintf( '<div>%s</div>' , $loc );
		}
		elseif ( $zipcode ) {
			$render .= sprintf( '<div>%s</div>' , $zipcode );
		}

		return $render;
	}

	public static function the_phone( $phone = '', $whatsapp = '', $telegram = '' ) {

		$mobileClass = wp_is_mobile() ? " rtcl-mobile" : null;

		if ( $phone ) {
			$mobileClass = wp_is_mobile() ? " rtcl-mobile" : null;
			$phone_options = [
				'safe_phone'   => mb_substr($phone, 0, mb_strlen($phone) - 3) . apply_filters('rtcl_phone_number_placeholder', 'XXX'),
				'phone_hidden' => mb_substr($phone, -3)
			];
			?>
            <div class='item-number phone reveal-phone<?php echo esc_attr($mobileClass); ?>'
                 data-options="<?php echo $phone_options ? htmlspecialchars(wp_json_encode($phone_options)) : ''; ?>">
                <i class="fa fa-phone-alt"></i>
                <div class='numbers'>

					<?php echo esc_html($phone_options['safe_phone']); ?>
                </div>
                <small class='text-muted'><?php esc_html_e("(Show)","classilist") ?></small>
            </div>
        <?php } if ( $whatsapp && ! Functions::is_field_disabled( 'whatsapp_number' ) ) {
				$mobileClass = wp_is_mobile() ? " rtcl-mobile" : null;
				$whatsapp_options = [
					'safe_phone'   => mb_substr($whatsapp, 0, mb_strlen($whatsapp) - 3) . apply_filters('rtcl_phone_number_placeholder', 'XXX'),
					'phone_hidden' => mb_substr($whatsapp, -3)
				];
				?>
                <div class='item-number whatsapp reveal-phone<?php echo esc_attr($mobileClass); ?>'
                     data-options="<?php echo $whatsapp_options ? htmlspecialchars(wp_json_encode($whatsapp_options)) : ''; ?>">
<!--                    <i class="fab fa-whatsapp"></i>-->
                    <i class="fa-brands fa-square-whatsapp"></i>
                    <a target="_blank" href="https://api.whatsapp.com/send?phone=<?php echo esc_attr( $whatsapp ); ?>&text=<?php echo get_the_title(); ?>">
                        <div class='numbers'>
							<?php echo esc_html($whatsapp_options['safe_phone']); ?>
                        </div>
                    </a>
                    <small class='text-muted'><?php esc_html_e("(Show)","classilist") ?></small>
                </div>
		<?php } if ( $telegram ) { ?>
            <div class='item-number telegram'>
                <i class="rtcl-icon rtcl-icon-telegram mr-2"></i>
                <a class="rtcl-telegram-message" target="_blank" href="tg://resolve?domain=<?php echo esc_attr( $telegram ); ?>">
                    <span><?php esc_html_e( "Call on Telegram", "classilist" ) ?></span>
                </a>
            </div>
		<?php }
	}


	public static function get_listing_type( $listing ){

		$listing_types = Functions::get_listing_types();
		$listing_types = empty( $listing_types ) ? array() : $listing_types;

		$type = $listing->get_ad_type();

		if ( $type && !empty( $listing_types[$type] ) ) {
			$result = array(
				'label' => $listing_types[$type],
				'icon'  => 'fa-tags'				
			);
		}
		else {
			$result = false;
		}

		return $result;
	}

    public static function store_query() {
        global $post;

        $args = array(
            'post_type'      => rtcl()->post_type,
            'post_status'    => 'publish',
            'posts_per_page' => Functions::get_option_item( 'rtcl_general_settings', 'listings_per_page', 20 ),
            'author'         => get_post_meta( $post->ID, 'store_owner_id', true ),
            'paged'          => Pagination::get_page_number(),
        );

        $general_settings = Functions::get_option('rtcl_general_settings');
        $atts = array(
            'orderby' => !empty($general_settings['orderby']) ? $general_settings['orderby'] : 'date',
            'order'   => !empty($general_settings['order']) ? $general_settings['order'] : 'DESC',
        );

        $current_order = Pagination::get_listings_current_order($atts['orderby'] . '-' . $atts['order']);
        switch ($current_order) {
            case 'title-asc' :
                $args['orderby'] = 'title';
                $args['order'] = 'ASC';
                break;
            case 'title-desc' :
                $args['orderby'] = 'title';
                $args['order'] = 'DESC';
                break;
            case 'date-asc' :
                $args['orderby'] = 'date';
                $args['order'] = 'ASC';
                break;
            case 'date-desc' :
                $args['orderby'] = 'date';
                $args['order'] = 'DESC';
                break;
            case 'price-asc' :
                $args['meta_key'] = 'price';
                $args['orderby'] = 'meta_value_num';
                $args['order'] = 'ASC';
                break;
            case 'price-desc' :
                $args['meta_key'] = 'price';
                $args['orderby'] = 'meta_value_num';
                $args['order'] = 'DESC';
                break;
            case 'views-asc' :
                $args['meta_key'] = '_views';
                $args['orderby'] = 'meta_value_num';
                $args['order'] = 'ASC';
                break;
            case 'views-desc' :
                $args['meta_key'] = '_views';
                $args['orderby'] = 'meta_value_num';
                $args['order'] = 'DESC';
                break;
            case 'rand' :
                $args['orderby'] = 'rand';
                break;
        }

        return new \WP_Query( $args );
    }

    public function classilist_rtcl_stores_grid_columns_class() {
        return 'columns-6';
    }

    public function classilist_change_listing_time_format( $string ) {

        $time_format = get_option( 'date_format' ) . ' ' . get_option( 'time_format' );
        if( empty($time_format) ) {
            $time_format = $string;
        }
        return $time_format;
    }
	
	public static function form_builder_custom_group_field_check(  ) {
        global $listing;
		$form = $listing->getForm();
		if($form){
			$fields = $form->getFieldAsGroup( FBField::CUSTOM );
			$fields_available = false;
			if ( count( $fields ) ) {
				foreach ( $fields as $fieldName => $field ) {
					$field = new FBField( $field );
					$value = $field->getFormattedCustomFieldValue( $listing->get_id() );
					if (!empty($value)){
						return true;
					}
				}
				return $fields_available;
			}
		}
    }
}

Listing_Functions::instance();