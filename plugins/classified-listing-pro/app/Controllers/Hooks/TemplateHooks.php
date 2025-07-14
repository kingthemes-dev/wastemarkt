<?php

namespace RtclPro\Controllers\Hooks;

use Rtcl\Helpers\Functions;
use Rtcl\Helpers\Link;
use Rtcl\Models\Form\Form;
use Rtcl\Models\Listing;
use Rtcl\Services\FormBuilder\FBHelper;
use Rtcl\Widgets\AjaxFilter;
use Rtcl\Widgets\Filter;
use RtclPro\Helpers\Fns;
use RtclPro\Helpers\Options;
use RtclPro\Models\UserAuthentication;
use WP_Query;

class TemplateHooks {

	public static function init() {
		add_action( 'rtcl_after_listing_loop_item', [ __CLASS__, 'sold_out_banner' ] );
		add_action( 'rtcl_single_listing_content', [ __CLASS__, 'sold_out_banner' ], 20 );
		add_action( 'rtcl_my_listing_after_listing_thumb', [ __CLASS__, 'sold_out_banner' ] );

		/**
		 * Reviews
		 *
		 */
		add_action( 'rtcl_review_before', [ __CLASS__, 'review_display_gravatar' ], 10 );
		add_action( 'rtcl_review_meta', [ __CLASS__, 'review_display_meta' ], 10 );
		add_action( 'rtcl_review_after_meta', [ __CLASS__, 'review_display_rating' ], 10 );
		add_action( 'rtcl_review_comment_text', [ __CLASS__, 'review_display_comment_title' ], 10 );
		add_action( 'rtcl_review_comment_text', [ __CLASS__, 'review_display_comment_text' ], 20 );

		add_action( 'rtcl_listing_loop_action', [ __CLASS__, 'view_switcher' ], 30 );

		if ( Fns::is_enable_top_listings() ) {
			add_action( 'rtcl_listing_loop_prepend_data', [ __CLASS__, 'top_listing_items' ], 20 );
			add_action( 'rtcl_after_listing_loop', [ __CLASS__, 'remove_top_listing_items' ], 20 );
		}

		add_action( 'rtcl_listing_badges', [ __CLASS__, 'listing_popular_badge' ], 30 );
		add_action( 'rtcl_listing_badges', [ __CLASS__, 'listing_top_badge' ], 40 );
		add_action( 'rtcl_listing_badges', [ __CLASS__, 'listing_bump_up_badge' ], 50 );

		add_action( "rtcl_widget_filter_form", [ __CLASS__, 'widget_filter_form_rating_item' ], 40, 2 );
		add_action( "rtcl_widget_filter_form", [ __CLASS__, 'widget_filter_form_cf_item' ], 50, 2 );

		// Ajax Filter
		add_action( 'rtcl_widget_ajax_filter_render_directory', [ __CLASS__, 'ajax_filter_render_directory' ], 10, 3 );
		add_action( "rtcl_widget_ajax_filter_render_cf", [ __CLASS__, 'ajax_filter_render_cf_items' ], 10, 3 );
		add_action( "rtcl_widget_ajax_filter_render_rating", [ __CLASS__, 'ajax_filter_render_rating' ], 10, 3 );

		add_action( 'rtcl_account_chat_endpoint', [ __CLASS__, 'account_chat_endpoint' ] );

		if ( Fns::is_enable_quick_view() ) {
			/**
			 * Quick View
			 */
			add_action( 'rtcl_quick_view_gallery', [ __CLASS__, 'quick_view_gallery' ] );
			add_action( 'rtcl_quick_view_summary', [ __CLASS__, 'quick_view_summary_title' ] );
			add_action( 'rtcl_quick_view_summary', [ __CLASS__, 'quick_view_summary_label' ], 20 );
			add_action( 'rtcl_quick_view_summary', [ __CLASS__, 'quick_view_summary_meta' ], 30 );
			add_action( 'rtcl_quick_view_summary', [ __CLASS__, 'quick_view_summary_custom_fields' ], 40 );
			add_action( 'rtcl_quick_view_summary', [ __CLASS__, 'quick_view_summary_price' ], 50 );
			add_action( 'rtcl_listing_meta_buttons', [ __CLASS__, 'add_quick_view_button' ], 20 );
		}
		if ( Fns::is_enable_compare() ) {
			add_action( 'rtcl_listing_meta_buttons', [ __CLASS__, 'add_compare_button' ], 30 );
		}

		add_action( 'rtcl_listing_loop_item', [ __CLASS__, 'loop_item_listable_fields' ], 40 );

		add_action( 'rtcl_my_account_verify', [ __CLASS__, 'user_verification_action' ] );

		add_action( 'rtcl_my_listing_actions', [ __CLASS__, 'my_listing_mark_as_sold_button' ], 40 );

		add_action( 'rtcl_add_user_information', [
			__CLASS__,
			'add_chat_link'
		] );// TODO: need to remove in future version
		add_action( 'rtcl_listing_seller_information', [ __CLASS__, 'add_chat_link' ], 40 );
		add_action( 'rtcl_listing_seller_information', [ __CLASS__, 'add_user_online_status' ], 50 );

		if ( Fns::registered_user_only( 'listing_seller_information' ) && ! is_user_logged_in() ) {
			remove_all_actions( 'rtcl_listing_seller_information' );
			add_action( 'rtcl_listing_seller_information', [ __CLASS__, 'add_user_login_link' ], 1 );
		}
	}

	public static function add_user_login_link() {
		$redirect_to = add_query_arg( 'redirect_to', get_the_permalink(), Link::get_my_account_page_link() );
		?>
		<div class='list-group-item'>
			<?php echo wp_kses( sprintf( __( "Please <a href='%s'>login</a> to view the seller information.", "classified-listing-pro" ),
				esc_url( $redirect_to ) ), [ 'a' => [ 'href' => [] ] ] ); ?>
		</div>
		<?php
	}

	/**
	 * @param Listing $listing
	 */
	public static function my_listing_mark_as_sold_button( $listing ) {
		if ( is_a( $listing, Listing::class ) && Fns::is_enable_mark_as_sold() ) {
			$is_mark_as_sold = Fns::is_mark_as_sold( $listing->get_id() );
			$sold_text       = $is_mark_as_sold ? apply_filters( 'rtcl_mark_as_unsold_text', __( "Mark as unsold", "classified-listing-pro" ) )
				: apply_filters( 'rtcl_mark_as_sold_text', __( "Mark as sold", "classified-listing-pro" ) );
			?>
			<a data-id="<?php echo absint( $listing->get_id() ) ?>" href="javascript:;"
			   class="mark-as-sold<?php echo $is_mark_as_sold ? " sold" : "" ?>">
				<svg width="16" height="16" viewBox="0 0 16 16" fill="none" xmlns="http://www.w3.org/2000/svg">
					<path
						d="M11.4698 5.7851C11.5579 5.87333 11.6074 5.99294 11.6074 6.11765C11.6074 6.24235 11.5579 6.36196 11.4698 6.45019L7.07765 10.8424C6.98941 10.9305 6.86981 10.98 6.7451 10.98C6.62039 10.98 6.50079 10.9305 6.41255 10.8424L4.5302 8.96C4.44707 8.87079 4.40182 8.7528 4.40397 8.63089C4.40612 8.50897 4.45551 8.39265 4.54173 8.30643C4.62795 8.22021 4.74427 8.17082 4.86618 8.16867C4.9881 8.16652 5.10609 8.21177 5.1953 8.2949L6.7451 9.84392L10.8047 5.7851C10.8929 5.69697 11.0125 5.64747 11.1373 5.64747C11.262 5.64747 11.3816 5.69697 11.4698 5.7851ZM16 8C16 9.58225 15.5308 11.129 14.6518 12.4446C13.7727 13.7602 12.5233 14.7855 11.0615 15.391C9.59966 15.9965 7.99113 16.155 6.43928 15.8463C4.88743 15.5376 3.46197 14.7757 2.34315 13.6569C1.22433 12.538 0.462403 11.1126 0.153721 9.56072C-0.15496 8.00887 0.00346614 6.40034 0.608967 4.93853C1.21447 3.47672 2.23985 2.22729 3.55544 1.34824C4.87103 0.469192 6.41775 0 8 0C10.121 0.00249086 12.1544 0.846145 13.6541 2.3459C15.1539 3.84565 15.9975 5.87903 16 8ZM15.0588 8C15.0588 6.6039 14.6448 5.23914 13.8692 4.07833C13.0936 2.91751 11.9911 2.01276 10.7013 1.4785C9.41146 0.944232 7.99217 0.804443 6.62289 1.07681C5.25362 1.34918 3.99585 2.02146 3.00866 3.00866C2.02147 3.99585 1.34918 5.25361 1.07681 6.62289C0.804447 7.99217 0.944235 9.41146 1.4785 10.7013C2.01277 11.9911 2.91751 13.0936 4.07833 13.8692C5.23915 14.6448 6.6039 15.0588 8 15.0588C9.87148 15.0567 11.6657 14.3124 12.989 12.989C14.3124 11.6657 15.0567 9.87148 15.0588 8Z"
						fill="#646464"/>
				</svg>
				<span class="rtcl-tooltip"><?php echo esc_html( $sold_text ); ?></span>
			</a>
			<?php
		}
	}

	public static function user_verification_action() {
		global $wp;
		if ( isset( $wp->query_vars['verify'] ) ) {
			UserAuthentication::verify_if_valid();
		}
	}


	public static function loop_item_listable_fields() {
		global $listing;
		if ( empty( $listing ) ) {
			return;
		}
		if ( FBHelper::isEnabled() ) {
			$form = $listing->getForm();
			Functions::get_template( "listing/listable", [
				'form'       => $form,
				'listing_id' => $listing->get_id()
			], '', rtclPro()->get_plugin_template_path() );
		} else {
			$category_id = Functions::get_term_child_id_for_a_post( $listing->get_categories() );

			// Get custom fields
			$custom_field_ids = Functions::get_custom_field_ids( $category_id );

			$fields = [];
			if ( ! empty( $custom_field_ids ) ) {
				$args   = [
					'post_type'        => rtcl()->post_type_cf,
					'post_status'      => 'publish',
					'posts_per_page'   => - 1,
					'post__in'         => $custom_field_ids,
					'orderby'          => 'menu_order',
					'order'            => 'ASC',
					'suppress_filters' => false,
					'meta_query'       => [
						[
							'key'     => '_listable',
							'compare' => '=',
							'value'   => 1,
						]
					]
				];
				$args   = apply_filters( 'rtcl_loop_item_listable_fields', $args, $listing );
				$fields = get_posts( $args );
			}
			Functions::get_template( "listing/listable-fields", [
				'fields'     => $fields,
				'listing_id' => $listing->get_id()
			], '', rtclPro()->get_plugin_template_path() );
		}
	}


	/**
	 * @param Listing $listing
	 */
	public static function add_compare_button( $listing ) {
		if ( empty( rtcl()->session ) ) {
			rtcl()->initialize_session();
		}
		$compare_ids    = rtcl()->session->get( 'rtcl_compare_ids', [] );
		$selected_class = '';
		if ( is_array( $compare_ids ) && in_array( $listing->get_id(), $compare_ids ) ) {
			$selected_class = ' selected';
		}
		?>
		<div class="rtcl-compare rtcl-btn<?php echo esc_attr( $selected_class ); ?>"
			 data-tooltip="<?php esc_html_e( "Add to compare list", "classified-listing-pro" ) ?>"
			 data-listing_id="<?php echo absint( $listing->get_id() ) ?>"><i
				class="rtcl-icon rtcl-icon-exchange"></i></div>
		<?php
	}

	/**
	 * @param Listing $listing
	 */
	public static function add_quick_view_button( $listing ) {
		?>
		<div class="rtcl-quick-view rtcl-btn"
			 data-tooltip="<?php esc_html_e( "Quick view", "classified-listing-pro" ) ?>"
			 data-listing_id="<?php echo absint( $listing->get_id() ) ?>"><i
				class="rtcl-icon rtcl-icon-zoom-in"></i></div>
		<?php
	}

	/**
	 * @param Listing $listing
	 */
	static function quick_view_gallery( $listing ) {
		if ( ! $listing ) {
			return;
		}
		$listing->the_gallery();
	}

	/**
	 * @param Listing $listing
	 */
	static function quick_view_summary_title( $listing ) {
		if ( ! $listing ) {
			return;
		}
		printf( '<h3 class="rtcl-qv-title"><a href="%s">%s</a></h3>',
			$listing->get_the_permalink(),
			$listing->get_the_title()
		);
	}

	/**
	 * @param Listing $listing
	 */
	static function quick_view_summary_label( $listing ) {
		if ( ! $listing ) {
			return;
		}
		$global_listing = '';
		if ( isset( $GLOBALS['listing'] ) ) {
			$global_listing = $GLOBALS['listing'];
		}
		$GLOBALS['listing'] = $listing;
		$listing->the_badges();
		if ( $global_listing ) {
			$GLOBALS['listing'] = $global_listing;
		} else {
			unset( $GLOBALS['listing'] );
		}
	}

	/**
	 * @param Listing $listing
	 */
	static function quick_view_summary_meta( $listing ) {
		if ( ! $listing ) {
			return;
		}
		$global_listing = '';
		if ( isset( $GLOBALS['listing'] ) ) {
			$global_listing = $GLOBALS['listing'];
		}
		$GLOBALS['listing'] = $listing;
		$listing->the_meta();
		if ( $global_listing ) {
			$GLOBALS['listing'] = $global_listing;
		} else {
			unset( $GLOBALS['listing'] );
		}
	}

	/**
	 * @param Listing $listing
	 */
	static function quick_view_summary_price( $listing ) {
		if ( ! $listing ) {
			return;
		}
		echo sprintf( '<div class="rtcl-qv-price">%s</div>', $listing->get_price_html() );
	}

	/**
	 * @param Listing $listing
	 */
	static function quick_view_summary_custom_fields( $listing ) {
		if ( ! $listing ) {
			return;
		}
		$listing->the_custom_fields();
	}

	public static function account_chat_endpoint() {
		Functions::get_template( "myaccount/chat-conversation", '', null, rtclPro()->get_plugin_template_path() );
	}


	/**
	 * @param array      $itemData
	 * @param array      $filterData
	 * @param AjaxFilter $object
	 *
	 * @return void
	 */
	public static function ajax_filter_render_cf_items( $itemData, $filterData, $object ) {
		$cfFilters = Fns::get_ajax_filter_cs_items( $_GET, $filterData );
		$html      = '';
		if ( ! empty( $cfFilters ) ) {
			foreach ( $cfFilters as $cfFilter ) {
				if ( ! empty( $cfFilter['html'] ) ) {
					$html .= $cfFilter['html'];
				}
			}
		}
		?>
		<div class="rtcl-ajax-filter-cf-wrap"><?php Functions::print_html( $html, true ); ?></div>
		<?php
	}


	/**
	 * @param array      $itemData
	 * @param array      $filterData
	 * @param AjaxFilter $object
	 *
	 * @return void
	 */
	public static function ajax_filter_render_directory( $itemData, $filterData, $object ) {
		if ( ! FBHelper::isEnabled() ) {
			return;
		}
		$inputDirectory = ! empty( $_GET['directory'] ) ? ( is_numeric( $_GET['directory'] ) ? absint( $_GET['directory'] )
			: trim( sanitize_text_field( wp_unslash( $_GET['directory'] ) ) ) ) : '';
		$selectedValues = ! empty( $inputDirectory ) ? ( ! is_numeric( $inputDirectory ) ? explode( ',', $inputDirectory ) : [ $inputDirectory ] ) : [];
		$selectedValues = empty($selectedValues) && !empty($itemData['selected']) && is_array($itemData['selected']) ? array_filter(array_map('absint', $itemData['selected'])) : $selectedValues;
		$itemData['title'] = ! empty( $itemData['title'] ) ? $itemData['title'] : esc_html__( 'Ad Type', 'classified-listing-pro' );
		$fieldType         = ! empty( $itemData['type'] )
							 && in_array( $itemData['type'],
			[
				'checkbox',
				'radio',
				'select'
			] ) ? $itemData['type'] : 'checkbox';

		$hideAbleCount = ! empty( $filterData['hideAbleCount'] ) ? absint( $filterData['hideAbleCount'] ) : 6;

		$options = [
			'name'       => 'directory',
			'field_type' => $fieldType,
			'values'     => $selectedValues
		];

		$allForms   = Form::query()->select( 'id,title,`default`' )->where( 'status', 'publish' )->order_by( 'created_at', 'DESC' )->get();
		$field_html = '<div class="rtcl-ajax-filter-data">';
		$count      = 0;
		if ( ! empty( $allForms ) ) {
			if ( in_array( $fieldType, [ 'checkbox', 'radio' ] ) ) {
				foreach ( $allForms as $form ) {
					$count ++;
					$formTitle  = $form->title . ( $form->default === 1 ? __( 'Default', 'classified-listing-pro' ) : '' );
					$field_html .= sprintf( '<div class="rtcl-ajax-filter-data-item rtcl-filter-checkbox-item rtcl-filter-directory-%1$s%6$s">
															<div class="rtcl-ajax-filter-diiWrap">
																<input id="filters-directory-value-%1$s" name="%2$s" value="%1$s" type="%3$s" class="rtcl-filter-checkbox"%4$s />
																<label for="filters-directory-value-%1$s" class="rtcl-filter-checkbox-label">
																	<span class="rtcl-filter-checkbox-text">%5$s</span>
																</label>
															</div>
												</div>',
						esc_attr( $form->id ),
						$options['name'],
						$fieldType,
						in_array( $form->id, $selectedValues ) ? ' checked' : '',
						esc_html( $formTitle ),
						$count >= $hideAbleCount ? ' hideAble' : ''
					);
				}
			} elseif ( $fieldType == 'select' ) {
				$field_html .= '<select class="rtcl-filter-select-item" name="' . esc_attr( $options['name'] ) . '">';
				$field_html .= '<option value="">' . __( 'Select', 'classified-listing-pro' ) . '</option>';
				foreach ( $allForms as $form ) {
					$formTitle  = $form->title . ( $form->default === 1 ? __( 'Default', 'classified-listing-pro' ) : '' );
					$field_html .= sprintf( '<option value="%1$s"%2$s>%3$s</option>',
						esc_attr( $form->id ),
						in_array( $form->id, $selectedValues ) ? ' selected' : '',
						esc_html( $formTitle )
					);
				}
				$field_html .= '</select>';
			}
		}
		$field_html .= '</div>';
		if ( $fieldType !== 'select' && $count >= $hideAbleCount ) {
			$field_html .= '<div class="rtcl-more-less-btn">
									<div class="text more-text"><i class="rtcl-icon rtcl-icon-plus"></i>' . __( 'More', 'classified-listing-pro' ) . '</div>
									<div class="text less-text"><i class="rtcl-icon rtcl-icon-minus"></i>' . __( 'Less', 'classified-listing-pro' ) . '</div>
							</div>';
		}

		Functions::print_html( $object->render_filter_item( $itemData, $options, $field_html ), true );
	}


	/**
	 * @param array      $itemData
	 * @param array      $filterData
	 * @param AjaxFilter $object
	 *
	 * @return void
	 */
	public static function ajax_filter_render_rating( $itemData, $filterData, $object ) {
		if ( ! Functions::get_option_item( 'rtcl_moderation_settings', 'enable_review_rating', false, 'checkbox' ) ) {
			return;
		}
		$rating            = ! empty( $_GET['filter_rating'] ) && is_numeric( $_GET['filter_rating'] ) ? $_GET['filter_rating'] + 0 : 0;
		$itemData['title'] = ! empty( $itemData['title'] ) ? $itemData['title'] : esc_html__( 'Ratings', 'classified-listing-pro' );
		$ratingOptions     = apply_filters(
			'rtcl_ajax_filter_widget_rating_list',
			[
				'5.0' => esc_html__( '5.0', 'classified-listing-pro' ),
				'4.5' => esc_html__( '4.5 & up', 'classified-listing-pro' ),
				'4.0' => esc_html__( '4.0 & up', 'classified-listing-pro' ),
				'3.5' => esc_html__( '3.5 & up', 'classified-listing-pro' ),
				'3.0' => esc_html__( '3.0 & up', 'classified-listing-pro' ),
			]
		);

		$field_html = '<ul class="list-tree rtcl-filter-ratings">';
		foreach ( $ratingOptions as $key => $text ) {
			$key        = (float) $key;
			$width      = ( $key / 5 ) * 100;
			$field_html .= '<li class="rtcl-filter-ratings-item list-tree-item' . esc_attr( $rating == $key ? ' selected' : '' ) . '" data-id="'
						   . esc_attr( $key ) . '">
									<div class="star-rating"><span style="width:' . esc_attr( $width ) . '%"></span></div>
									<span class="rating-filter-label">' . esc_html( $text ) . '</span>
								</li>';
		}

		$field_html .= '</ul>';

		$options = [ 'name' => 'filter_rating', 'field_type' => 'number', 'allow_rest' => true ];
		Functions::print_html( $object->render_filter_item( $itemData, $options, $field_html ), true );
	}

	/**
	 * @param Filter $object
	 * @param array  $data
	 */
	public static function widget_filter_form_cf_item( $object, $data ) {
		if ( ! empty( $data['custom_field_filter'] ) ) {
			Functions::print_html( $data['custom_field_filter'], true );
		}
	}

	/**
	 * @param Filter $object
	 */
	static function widget_filter_form_rating_item( $object, $data ) {
		if ( ! empty( $data['rating_filter'] ) ) {
			Functions::print_html( $data['rating_filter'], true );
		}
	}

	/**
	 * @param Listing $listing
	 */
	static function listing_bump_up_badge( $listing ) {

		$display_option    = is_singular( rtcl()->post_type ) ? 'display_options_detail' : 'display_options';
		$can_show          = apply_filters( 'rtcl_listing_can_show_top_badge', true, $listing );
		$can_show_settings = Functions::get_option_item( 'rtcl_moderation_settings', $display_option, 'bump_up', 'multi_checkbox' );
		$can_show_settings = apply_filters( 'rtcl_listing_can_show_bump_up_badge_settings', $can_show_settings );
		if ( ! $can_show || ! $can_show_settings || ! get_post_meta( $listing->get_id(), '_bump_up', true ) ) {
			return;
		}
		$label = Functions::get_option_item( 'rtcl_moderation_settings', 'listing_bump_up_label' );
		$label = $label ?: esc_html__( "Bump Up", "classified-listing-pro" );
		echo '<span class="badge rtcl-badge-_bump_up">' . esc_html( $label ) . '</span>';
	}

	/**
	 * @param Listing $listing
	 */
	static function listing_top_badge( $listing ) {

		$display_option    = is_singular( rtcl()->post_type ) ? 'display_options_detail' : 'display_options';
		$can_show          = apply_filters( 'rtcl_listing_can_show_top_badge', true, $listing );
		$can_show_settings = Functions::get_option_item( 'rtcl_moderation_settings', $display_option, 'top', 'multi_checkbox' );
		$can_show_settings = apply_filters( 'rtcl_listing_can_show_top_badge_settings', $can_show_settings );
		if ( ! $can_show || ! $can_show_settings || ! get_post_meta( $listing->get_id(), '_top', true ) ) {
			return;
		}

		$label = Functions::get_option_item( 'rtcl_moderation_settings', 'listing_top_label' );
		$label = $label ?: esc_html__( "Top", "classified-listing-pro" );
		echo '<span class="badge rtcl-badge-_top">' . esc_html( $label ) . '</span>';
	}

	/**
	 * @param Listing $listing
	 */
	static function listing_popular_badge( $listing ) {

		$views             = absint( get_post_meta( $listing->get_id(), '_views', true ) );
		$popular_threshold = Functions::get_option_item( 'rtcl_moderation_settings', 'popular_listing_threshold', 0, 'number' );
		if ( $views >= $popular_threshold ) {
			$popular_label = Functions::get_option_item( 'rtcl_moderation_settings', 'popular_listing_label' );
			$popular_label = $popular_label ?: esc_html__( "Popular", "classified-listing-pro" );
			echo '<span class="badge rtcl-badge-popular popular-badge badge-success">' . esc_html( $popular_label ) . '</span>';
		}
	}

	/**
	 * @param null| WP_Query $query
	 */
	static function top_listing_items( $query = null ) {
		$query = ! empty( $query ) && is_a( $query, WP_Query::class ) ? $query : Fns::top_listings_query();

		$paginated = ! $query->get( 'no_found_rows' );
		$listings  = (object) [
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
		global $rtclTopListingIds;
		$rtclTopListingIds = [];
		if ( Functions::get_loop_prop( 'total' ) ) {
			while ( $query->have_posts() ) : $query->the_post();
				Functions::get_template_part( 'content', 'listing' );
				$rtclTopListingIds[]= get_the_ID();
			endwhile;
			wp_reset_postdata();
		}

		Functions::reset_loop();
		if(!empty($rtclTopListingIds)) {
			global $wp_query;
			$args = $wp_query->query_vars;
			$existingExcluded = $wp_query->get('post__not_in');
			if (!is_array($existingExcluded)) {
				$existingExcluded = [];
			}
			$args['post__not_in'] =array_merge($existingExcluded, $rtclTopListingIds) ;
			$wp_query = new WP_Query($args);
		}
	}
	
	static function remove_top_listing_items( ) {
		if(!empty($rtclTopListingIds) && is_array($rtclTopListingIds)) {
			global $wp_query;
			$args = $wp_query->query_vars;
			$existingExcluded = $wp_query->get('post__not_in');
			if (!empty($existingExcluded) && is_array($existingExcluded)) {
				$args['post__not_in'] =array_diff($existingExcluded, $rtclTopListingIds) ;
				$wp_query = new WP_Query($args);
			}
		}
	}


	/**
	 * Output the Listing view switcher
	 */
	static function view_switcher() {
		$views        = Options::get_listings_view_options();
		$default_view = Functions::get_option_item( 'rtcl_general_settings', 'default_view', 'list' );
		$default_view = apply_filters( 'rtcl_archive_default_view', $default_view );
		$current_view = ( ! empty( $_GET['view'] ) && array_key_exists( $_GET['view'], $views ) ) ? $_GET['view'] : $default_view;
		Functions::get_template(
			'listing/view-switcher',
			compact( 'views', 'current_view', 'default_view' ),
			'',
			rtclPro()->get_plugin_template_path()
		);
	}

	public static function add_chat_link( $listing ) {
		$chat_link_available = Fns::is_enable_chat() && is_a( $listing, Listing::class )
			&& ( ( is_user_logged_in() && $listing->get_author_id() !== get_current_user_id() )
				|| ! is_user_logged_in() ) ;
		$chat_link_available = apply_filters( 'rtcl_is_chat_link_available', $chat_link_available, $listing );
		if ( $chat_link_available ) {
			$chat_btn_class = [ 'rtcl-chat-link' ];
			$chat_url       = Link::get_my_account_page_link();
			if ( is_user_logged_in() ) {
				$chat_url = '#';
				array_push( $chat_btn_class, 'rtcl-contact-seller' );
			} else {
				array_push( $chat_btn_class, 'rtcl-no-contact-seller' );
			}
			?>
			<div class='rtcl-contact-seller rtcl-list-group-item'>
				<a class="<?php echo esc_attr( implode( ' ', $chat_btn_class ) ) ?>"
				   href="<?php echo esc_url( $chat_url ) ?>"
				   data-listing_id="<?php echo absint( $listing->get_id() ) ?>">
					<i class='rtcl-icon rtcl-icon-chat'> </i><?php esc_html_e( "Chat", "classified-listing-pro" ) ?>
				</a>
			</div>
		<?php }
	}

	public static function add_user_online_status( $listing ) {
		$status_text  = apply_filters( 'rtcl_user_offline_text', esc_html__( 'Offline Now', 'classified-listing-pro' ) );
		$status       = Fns::is_online( $listing->get_owner_id() );
		$status_class = $status ? 'online' : 'offline';
		if ( $status ) {
			$status_text = apply_filters( 'rtcl_user_online_text', esc_html__( 'Online Now', 'classified-listing-pro' ) );
		}
		?>
		<div class="rtcl-list-group-item rtcl-user-status <?php echo esc_attr( $status_class ); ?>">
			<span><?php echo esc_html( $status_text ); ?></span>
		</div>
		<?php
	}

	static function sold_out_banner() {
		global $listing;
		if ( $listing && Fns::is_enable_mark_as_sold() && Fns::is_mark_as_sold( $listing->get_id() ) ) {
			echo '<span class="rtcl-sold-out">' . apply_filters( 'rtcl_sold_out_banner_text', esc_html__( "Sold Out", 'classified-listing-pro' ) ) . '</span>';
		}
	}

	/**
	 * Display the review authors gravatar
	 *
	 * @param array $comment \WP_Comment.
	 *
	 * @return void
	 */
	public static function review_display_gravatar( $comment ) {
		$gravatar = get_avatar( $comment, apply_filters( 'rtcl_review_gravatar_size', '60' ) );
		echo apply_filters( 'rtcl_review_gravatar_image', $gravatar ); // Hook Added By Rashid
	}


	/**
	 * Display the review authors meta (name, verified owner, review date)
	 *
	 * @return void
	 */
	public static function review_display_meta() {
		Functions::get_template( 'listing/review-meta', [], '', rtclPro()->get_plugin_template_path() );
	}


	/**
	 * Display the reviewers star rating
	 *
	 * @return void
	 */
	public static function review_display_rating() {
		if ( post_type_supports( rtcl()->post_type, 'comments' ) ) {
			Functions::get_template( 'listing/review-rating', [], '', rtclPro()->get_plugin_template_path() );
		}
	}


	/**
	 * Display the review content.
	 */
	public static function review_display_comment_title( $comment ) {
		echo '<span class="rtcl-review__title">';
		echo esc_html( get_comment_meta( $comment->comment_ID, 'title', true ) );
		echo '</span>';
	}


	/**
	 * Display the review content.
	 */
	public static function review_display_comment_text() {
		echo '<div class="description">';
		comment_text();
		echo '</div>';
	}
}
