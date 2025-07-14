<?php

/**
 * Helpers class.
 */

namespace RtclElb\Helpers;

use Rtcl\Helpers\Functions;
use Rtcl\Models\Form\Form;
use Rtcl\Models\Listing;
use Rtcl\Services\FormBuilder\FBField;
use Rtcl\Services\FormBuilder\FBHelper;
use RtclElb\Traits\ELTempleateBuilderTraits;
use Rtcl\Traits\Addons\ListingItem;

/**
 * Helpers class.
 */
class Fns {

	/**
	 * @var array
	 */
	private static $cache = [];
	/*
	 * Template builder related traits.
	 */
	use ELTempleateBuilderTraits;

	use ListingItem;

	/**
	 * Classes instatiation.
	 *
	 * @param array $classes classes to init
	 *
	 * @return void
	 */
	public static function instances( array $classes ) {
		if ( empty( $classes ) ) {
			return;
		}

		foreach ( $classes as $class ) {
			$service = $class::getInstance();
			if ( method_exists( $service, 'init' ) ) {
				$service->init();
			}
		}
	}
	/**
	 * Is Active Store.
	 *
	 * @return bool
	 */
	public static function is_has_store() {
		if ( class_exists( 'RtclStore' ) ) {
			return true;
		}
		return false;
	}

	/**
	 * @return mixed|null
	 */
	public static function get_all_fb_form() {
		if ( ! Functions::isEnableFb() ) {
			return [];
		}
		$cache_key = 'rtcl_get_all_fb_form';
		if ( isset( self::$cache[ $cache_key ] ) && ! empty( self::$cache[ $cache_key ] ) ) {
			return self::$cache[ $cache_key ];
		}
		$rawForms = Form::query()
			->where( 'status', 'publish' )
			->order_by( 'created_at', 'DESC' )
			->get();
		if ( ! empty( $rawForms ) ) {
			foreach ( $rawForms as $raw_form ) {
				$_form = apply_filters( 'rtcl_fb_form', $raw_form );
				if ( is_a( $_form, Form::class ) ) {
					$forms[] = [ 'defaultValues' => FBHelper::getFormDefaultData( $_form ) ] + $_form->toArray();
				}
			}
		}
		$forms                     = apply_filters( 'rtcl_get_all_fb_form', $forms );
		self::$cache[ $cache_key ] = $forms;
		return $forms;
	}

	/**
	 * @return mixed|null
	 */
	public static function get_all_fb_form_as_list() {
		$rawForms = self::get_all_fb_form();
		$list     = [];
		if ( ! empty( $rawForms ) && is_array( $rawForms ) ) {
			foreach ( $rawForms as $raw_form ) {
				$list[ $raw_form['id'] ] = $raw_form['title'];
			}
		}
		return apply_filters( 'rtcl_get_all_fb_form_as_list', $list );
	}

	/**
	 * Is Active Store.
	 *
	 * @return bool
	 */
	public static function last_store_id() {
		if ( ! self::is_has_store() ) {
			return 0;
		}
		if ( is_singular( rtclStore()->post_type ) ) {
			return get_the_ID();
		}
		global $wpdb;
		$cache_key = 'rtcl_last_store_id';
		$_post_id  = wp_cache_get( $cache_key );
		if ( false === $_post_id || 'publish' !== get_post_status( $_post_id ) ) {
			$_post_id = $wpdb->get_var(
				$wpdb->prepare( "SELECT MAX(ID) FROM {$wpdb->prefix}posts WHERE post_type =  %s AND post_status = %s", rtclStore()->post_type, 'publish' )
			);
			wp_cache_set( $cache_key, $_post_id );
		}

		return $_post_id;
	}


	public static function rtcl_block_print_header_style( $name, $content ) {
		?>
		<style id="<?php echo esc_attr( $name ); ?>">
			<?php
			echo wp_specialchars_decode( trim( $content ) );
			?>
		</style>
		<?php
	}


	/**
	 * rtcl Template Part Content.
	 *
	 * @param array  $attributes Attributes.
	 * @param string $template_part_id Template Part ID.
	 * @param string $area Area.
	 *
	 * @return string
	 */
	public static function rtcl_template_part_content( $attributes, &$template_part_id, &$area ) {
		$content = '';

		if (
			isset( $attributes['slug'] ) &&
			isset( $attributes['theme'] ) &&
			wp_get_theme()->get_stylesheet() === $attributes['theme']
		) {
			$template_part_id    = $attributes['theme'] . '//' . $attributes['slug'];
			$template_part_query = new \WP_Query(
				[
					'post_type'      => 'wp_template_part',
					'post_status'    => 'publish',
					'post_name__in'  => [ $attributes['slug'] ],
					'tax_query'      => array( //phpcs:ignore
						[
							'taxonomy' => 'wp_theme',
							'field'    => 'slug',
							'terms'    => $attributes['theme'],
						],
					),
					'posts_per_page' => 1,
					'no_found_rows'  => true,
				]
			);

			$template_part_post = $template_part_query->have_posts() ? $template_part_query->next_post() : null;

			if ( $template_part_post ) {
				// A published post might already exist if this template part was customized elsewhere
				// or if it's part of a customized template.
				$content    = $template_part_post->post_content;
				$area_terms = get_the_terms( $template_part_post, 'wp_template_part_area' );
				if ( ! is_wp_error( $area_terms ) && false !== $area_terms ) {
					$area = $area_terms[0]->name;
				}
				/**
				 * Fires when a block template part is loaded from a template post stored in the database.
				 *
				 * @since 5.9.0
				 *
				 * @param string  $template_part_id   The requested template part namespaced to the theme.
				 * @param array   $attributes         The block attributes.
				 * @param WP_Post $template_part_post The template part post object.
				 * @param string  $content            The template part content.
				 */
				do_action( 'render_block_core_template_part_post', $template_part_id, $attributes, $template_part_post, $content );
			}
		}

		return $content;
	}

	public static function rtcl_child_template( $base, $slug ) {
		$is_child_theme = get_template_directory() !== get_stylesheet_directory();
		$template_exist = file_exists( get_stylesheet_directory() . '/' . $base . '/' . $slug . '.html' );

		return $is_child_theme && $template_exist;
	}


	public static function rtcl_last_post_id() {
		global $wpdb;
		$cache_key = 'rtcl_last_post_id';
		$_post_id  = get_transient( $cache_key );

		if ( false === $_post_id || 'publish' !== get_post_status( $_post_id ) ) {
			delete_transient( $cache_key );
			$_post_id = $wpdb->get_var(
				$wpdb->prepare( "SELECT MAX(ID) FROM {$wpdb->prefix}posts WHERE post_type =  %s AND post_status = %s", rtcl()->post_type, 'publish' )
			);
			set_transient( $cache_key, $_post_id, 12 * HOUR_IN_SECONDS );
		}

		return $_post_id;
	}

	public static function rtcl_get_api_key() {
		return get_option( 'rtcl_rest_api_key', null );
	}

	public static function rtcl_get_restapi_allow() {
		$restApiAllow = '';
		$settings     = get_option( 'rtcl_tools_settings', null );
		if ( ! empty( $settings ) ) {
			$restApiAllow = isset( $settings['allow_rest_api'] ) ? $settings['allow_rest_api'] : '';
		}
		return $restApiAllow;
	}

	public static function get_block_wrapper_class( $settings = [], $class_name = '' ) {
		$wrap_class = '';

		if ( isset( $settings['blockId'] ) ) {
			$wrap_class .= $settings['blockId'];
		}
		$wrap_class .= ' rtcl-block-frontend';

		if ( isset( $settings['mainWrapShowHide'] ) ) {
			$wrap_class .= $settings['mainWrapShowHide']['lg'] ? ' rtrb-hide-desktop' : '';
			$wrap_class .= $settings['mainWrapShowHide']['md'] ? ' rtrb-hide-tablet' : '';
			$wrap_class .= $settings['mainWrapShowHide']['sm'] ? ' rtrb-hide-mobile' : '';
		}
		if ( ! empty( $class_name ) ) {
			$wrap_class .= ' ' . $class_name;
		}

		return $wrap_class;
	}

	/**
	 * @param array $value form value.
	 * @param array $options form options.
	 * @return array
	 */
	private static function fb_options_value_set( $value, $options ) {
		if ( empty( $value ) ) {
			return [];
		}
		$newVal = [];
		foreach ( $options as $option ) {
			if ( ! empty( $option['value'] ) && in_array( $option['value'], $value, true ) ) {
				$opt = [
					'label' => $option['label'] ?? '',
					'icon' => $option['icon_class'] ?? ''
				];
				$newVal[] = $opt;
			}
		}
		return $newVal;
	}

	/**
	 * @param $value
	 * @param $options
	 * @return void
	 */
	private static function fb_repeater_value_set( $value, $repeaterFields, $field, $listing_id ) {
		$generated_data = [];
		if ( empty( $value ) ) {
			return [];
		}
		if ( empty( $repeaterFields ) || ! is_array( $value ) ) {
			return [];
		}
		foreach ( $value as $rValueIndex => $rValues ) {
			foreach ( $repeaterFields as $repeaterField ) {
				$val            = [];
				$rField         = new FBField( $repeaterField );
				$rValue         = 'file' === $rField->getElement() ? ( ! empty( $rValues[ $rField->getName() ] ) && is_array( $rValues[ $rField->getName() ] ) ? FBHelper::getFieldAttachmentFiles( $listing_id, $rField->getField(), $rValues[ $rField->getName() ], true ) : [] ) : ( $rValues[ $rField->getName() ] ?? '' );
				$val['icon']    = $rField->getIconData();
				$val['element'] = $rField->getElement();
				$val['label']   = $rField->getLabel();
				if ( $rField->getElement() === 'color_picker' ) {
					$val = $rValue;
				} elseif ( in_array( $rField->getElement(), [ 'select', 'radio', 'checkbox' ], true ) ) {
					if ( $rField->getElement() === 'checkbox' ) {
						$val['value'] = self::fb_options_value_set( $rValue, $rField->getOptions() );
					} else {
						$val['value'] = $rValue;
					}
				} else {
					$val['value'] = $rValue;
				}
				$generated_data[] = $val;
			}
		}
		return apply_filters( 'rtcl_fb_data_repeater_value', $generated_data, $value, $repeaterFields, $field, $listing_id );
	}
	/**
	 * Set style controls
	 *
	 * @param string $type field type.
	 * @return array
	 */
	public static function formBuilderData( $theListing, $type = FBField::CUSTOM ) {
		
		$cache_key = 'rtcl-fb-data-' . $theListing->get_id() . '-' . $type;
		if ( isset( self::$cache[ $cache_key ] ) && ! empty( self::$cache[ $cache_key ] ) ) {
			return self::$cache[ $cache_key ];
		}
		$data   = [];
		$form   = $theListing->getForm();
		if (!empty($form)) {
			$fields = $form->getFieldAsGroup( $type );
			if ( count( $fields ) ) {
				$fields = FBHelper::reOrderCustomField( $fields );
				foreach ( $fields as $index => $field ) {
					$field = new FBField( $field );
					$value = $field->getFormattedCustomFieldValue( $theListing->get_id() );
					switch ( $field->getElement() ) {
						case 'checkbox':
							$value = self::fb_options_value_set( $value, $field->getOptions() );
							break;
						case 'repeater':
							$repeaterFields = $field->getData( 'fields', [] );
							$value = self::fb_repeater_value_set( $value, $repeaterFields, $field, $theListing->get_id() );
							break;
						default:
					}

					$populated = [
						'element' => $field->getElement(),
						// 'field'   => $field, // If need Then will use it.
						'label'   => ! empty( $field->getLabel() ) ? $field->getLabel() : $field->getName() . '( Label Is Empty )',
						'icon'    => $field->getIconData(),
						'value'   => $value,
					];

					$data[ $field->getName() ] = $populated;
				}
			}
			self::$cache[ $cache_key ] = $data;
			if ( ! empty( $data ) ) {
				return $data;
			}
		}
	}


	public static function get_plugin_template_path(){
		return RTCL_ELEMENTOR_ADDONS_PLUGIN_PATH .'templates/';
	}

	public static function is_enable_compare() {
		return Functions::get_option_item( 'rtcl_general_settings', 'enable_compare', false, 'checkbox' );
	}

	public static function is_enable_quick_view() {
		return Functions::get_option_item( 'rtcl_general_settings', 'enable_quick_view', false, 'checkbox' );
	}
	public static function get_listing_taxonomy( $parent = 'all', $taxonomy = '' ) {
		$args = [
			'taxonomy'   => rtcl()->category,
			'fields'     => 'id=>name',
			'hide_empty' => true,
		];

		if ( ! empty( $taxonomy ) ) {
			$args['taxonomy'] = sanitize_text_field( $taxonomy );
		}

		if ( 'parent' === $parent ) {
			$args['parent'] = 0;
		}

		$terms = get_terms( $args );

		$category_dropdown = [];

		foreach ( $terms as $id => $name ) {
			$category_dropdown[ $id ] = html_entity_decode( $name );
		}

		return $category_dropdown;
	}

	public static function get_image_sizes_select() {

		global $_wp_additional_image_sizes;

		$intermediate_image_sizes = get_intermediate_image_sizes();

		$image_sizes = array();
		foreach ( $intermediate_image_sizes as $size ) {
			if ( isset( $_wp_additional_image_sizes[ $size ] ) ) {
				$image_sizes[ $size ] = array(
					'width'  => $_wp_additional_image_sizes[ $size ]['width'],
					'height' => $_wp_additional_image_sizes[ $size ]['height']
				);
			} else {
				$image_sizes[ $size ] = array(
					'width'  => intval( get_option( "{$size}_size_w" ) ),
					'height' => intval( get_option( "{$size}_size_h" ) )
				);
			}
		}

		$sizes_arr = [];
		foreach ( $image_sizes as $key => $value ) {
			$sizes_arr[ $key ] = ucwords( strtolower( preg_replace( '/[-_]/', ' ', $key ) ) ) . " - {$value['width']} x {$value['height']}";
		}

		$sizes_arr['full'] = __( 'Full Size', 'rtcl-elementor-builder' );

		return $sizes_arr;
	}

	public static function get_order_options() {
		$order_by = [
			'title' => esc_html__( 'Title', 'rtcl-elementor-builder' ),
			'date'  => esc_html__( 'Date', 'rtcl-elementor-builder' ),
			'ID'    => esc_html__( 'ID', 'rtcl-elementor-builder' ),
			'price' => esc_html__( 'Price', 'rtcl-elementor-builder' ),
			'views' => esc_html__( 'Views', 'rtcl-elementor-builder' ),
			'none'  => esc_html__( 'None', 'rtcl-elementor-builder' ),
		];

		return apply_filters( 'rtcl_divi_listing_order_by', $order_by );
	}

	public static function divi_get_user_selected_terms( $category_includes, $taxonomy = 'rtcl_category' ) {
		// available categories.
		$available_cat = self::get_listing_taxonomy( 'parent', $taxonomy );

		ksort( $available_cat );

		$includes_keys = array_filter(
			$category_includes,
			function ( $cat ) {
				if ( $cat === 'on' ) {
					return $cat;
				}
			}
		);

		$available_terms = array_keys( $available_cat );
		$selected_terms  = array();

		foreach ( $includes_keys as $key => $value ) {
			array_push( $selected_terms, $available_terms[ $key ] );
		}

		return $selected_terms;
	}

	public static function is_divi_plugin_active()
	{
		return defined( 'ET_BUILDER_PLUGIN_VERSION' );
	}

	public static function get_the_title()
	{
		$title_text = '';
		if ( is_archive() ) {
			$title_text = Functions::page_title( false );
		} elseif ( is_single() ) {
			$_id        = Fns::get_prepared_listing_id();
			$listing    = new Listing( $_id );
			$title_text = $listing->get_the_title();
		}
		return $title_text;
	}
}
