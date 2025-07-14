<?php
/**
 * @author  RadiusTheme
 * @since   1.0
 * @version 1.2
 */

namespace radiustheme\ClassiList_Core;

use Elementor\Controls_Manager;
use \WP_Query;

if ( ! defined( 'ABSPATH' ) ) exit;

class Listing_List extends Custom_Widget_Base {

	public function __construct( $data = [], $args = null ){
		$this->rt_name = __( 'Listing List', 'classilist-core' );
		$this->rt_base = 'rt-listing-list';
		parent::__construct( $data, $args );
	}

	public function rt_fields(){
		$terms  = get_terms( array( 'taxonomy' => 'rtcl_category', 'fields' => 'id=>name' ) );
		$category_dropdown = array( '0' => __( 'All Categories', 'classilist-core' ) );

		foreach ( $terms as $id => $name ) {
			$category_dropdown[$id] = $name;
		}

		$fields = array(
			array(
				'mode'    => 'section_start',
				'id'      => 'sec_general',
				'label'   => __( 'General', 'classilist-core' ),
			),
			array(
				'type'    => Controls_Manager::SELECT2,
				'id'      => 'type',
				'label'   => __( 'Items to Show', 'classilist-core' ),
				'options' => array(
					'all'      => __( 'All', 'classilist-core' ),
					'featured' => __( 'Featured', 'classilist-core' ),
					'new'      => __( 'New', 'classilist-core' ),
					'popular'  => __( 'Popular', 'classilist-core' ),
					'top'      => __( 'Top', 'classilist-core' ),
					'custom'   => __( 'Custom', 'classilist-core' ),
				),
				'default' => 'all',
			),
			array(
				'type'      => Controls_Manager::SELECT2,
				'id'        => 'cat',
				'label'     => __( 'Categories', 'classilist-core' ),
				'options'   => $category_dropdown,
				'default'   => '0',
				'conditions' => array( 
					'terms' => array(
						array(
							'name' => 'type',
							'operator' => '!==',
							'value' => 'custom',
						)
					)
				),
			),
			array(
				'type'        => Controls_Manager::SWITCHER,
				'id'          => 'auth_display',
				'label'       => __( 'Author Name Display', 'classilist-core' ),
				'label_on'    => __( 'On', 'classilist-core' ),
				'label_off'   => __( 'Off', 'classilist-core' ),
				'default'     => 'yes',
			),
			array(
				'type'        => Controls_Manager::SWITCHER,
				'id'          => 'cat_display',
				'label'       => __( 'Category Name Display', 'classilist-core' ),
				'label_on'    => __( 'On', 'classilist-core' ),
				'label_off'   => __( 'Off', 'classilist-core' ),
				'default'     => 'yes',
			),
            array(
                'type'        => Controls_Manager::SWITCHER,
                'id'          => 'field_display',
                'label'       => __( 'Show Custom Fields', 'classilist-core' ),
                'label_on'    => __( 'On', 'classilist-core' ),
                'label_off'   => __( 'Off', 'classilist-core' ),
                'default'     => 'yes',
            ),
			array(
				'type'       => Controls_Manager::NUMBER,
				'id'         => 'number',
				'label'      => __( 'Number of Items', 'classilist-core' ),
				'default'    => '8',
				'description' => __( 'Write -1 to show all', 'classilist-core' ),
				'conditions' => array( 
					'terms' => array(
						array(
							'name' => 'type',
							'operator' => '!==',
							'value' => 'custom',
						)
					)
				),
			),
			array(
				'type'        => Controls_Manager::TEXT,
				'id'          => 'ids',
				'label'       => __( "Listing ID's, seperated by commas", 'classilist-core' ),
				'default'     => '',
				'condition'   => array( 'type' => array( 'custom' ) ),
				'description' => __( "Put the comma seperated ID's here eg. 23,26,89", 'classilist-core' ),
			),
			array(
				'type'        => Controls_Manager::SWITCHER,
				'id'          => 'random',
				'label'       => __( 'Change items on every page load', 'classilist-core' ),
				'label_on'    => __( 'On', 'classilist-core' ),
				'label_off'   => __( 'Off', 'classilist-core' ),
				'default'     => 'yes',
				'conditions' => array( 
					'terms' => array(
						array(
							'name' => 'type',
							'operator' => '!==',
							'value' => 'custom',
						)
					)
				),
			),
			array(
				'type'      => Controls_Manager::SELECT2,
				'id'        => 'orderby',
				'label'     => __( 'Order By', 'classilist-core' ),
				'options'   => array(
					'date'   => __( 'Date (Recents comes first)', 'classilist-core' ),
					'title'  => __( 'Title', 'classilist-core' ),
				),
				'default'   => 'date',
				'conditions' => array( 
					'terms' => array(
						array(
							'name' => 'type',
							'operator' => '!==',
							'value' => 'custom',
						),
						array(
							'name' => 'random',
							'operator' => '!==',
							'value' => 'yes',
						)
					)
				),
			),
			array(
				'mode' => 'section_end',
			),
		);
		return $fields;
	}

	private function rt_build_query( $data ) {

		if ( $data['type'] != 'custom' ) {

			// Get plugin settings
			$settings = get_option( 'rtcl_moderation_settings' );
			$min_view = !empty( $settings['popular_listing_threshold'] ) ? (int) $settings['popular_listing_threshold'] : 500;
			$new_threshold = !empty( $settings['new_listing_threshold'] ) ? (int) $settings['new_listing_threshold'] : 3;

			// Post type
			$args = array(
				'post_type'      => 'rtcl_listing',
				'post_status'    => 'publish',
				'ignore_sticky_posts' => true,
				'posts_per_page' => $data['number'],
			);

			// Ordering
			if ( $data['random'] ) {
				$args['orderby'] = 'rand';
			}
			else {
				$args['orderby'] = $data['orderby'];
				if ( $data['orderby'] == 'title' ) {
					$args['order'] = 'ASC';
				}
			}

			// Taxonomy
			if ( !empty( $data['cat'] ) ) {
				$args['tax_query'] = array(
					array(
						'taxonomy' => 'rtcl_category',
						'field' => 'term_id',
						'terms' => $data['cat'],
					)
				);
			}

			// Date and Meta Query
			switch ( $data['type'] ) {
				case 'new':
					$args['date_query'] = array(
						array(
							'after' => $new_threshold . ' day ago',
						),
					);
					break;

				case 'featured':
					$args['meta_key'] = 'featured';
					$args['meta_value'] = '1';
					break;

				case 'top':
					$args['meta_key'] = '_top';
					$args['meta_value'] = '1';
					break;

				case 'popular':
					$args['meta_key'] = '_views';
					$args['meta_value'] = $min_view;
					$args['meta_compare'] = '>=';
					break;
			}
		}

		else {

			$posts = array_map( 'trim' , explode( ',', $data['ids'] ) );

			$args = array(
				'post_type'      => 'rtcl_listing',
				'ignore_sticky_posts' => true,
				'nopaging'       => true,
				'post__in'       => $posts,
				'orderby'        => 'post__in',
			);
		}

		return new WP_Query( $args );
	}	

	protected function render() {
		$data = $this->get_settings();

		$data['query'] = $this->rt_build_query( $data );

		$template = 'view';

		return $this->rt_template( $template, $data );
	}
}