<?php
/**
 * @author  RadiusTheme
 * @since   1.0
 * @version 1.0
 */

namespace radiustheme\ClassiList_Core;

use Elementor\Controls_Manager;
use \WP_Query;

if ( ! defined( 'ABSPATH' ) ) exit;

class Listing_Slider extends Custom_Widget_Base {

	public function __construct( $data = [], $args = null ){
		$this->rt_name = __( 'Listing Slider', 'classilist-core' );
		$this->rt_base = 'rt-listing-slider';
		$this->rt_translate = array(
			'cols'  => array(
				'1'  => __( '1 Col', 'classilist-core' ),
				'2'  => __( '2 Col', 'classilist-core' ),
				'3'  => __( '3 Col', 'classilist-core' ),
				'4'  => __( '4 Col', 'classilist-core' ),
				'5'  => __( '5 Col', 'classilist-core' ),
				'6'  => __( '6 Col', 'classilist-core' ),
			),
		);
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
				'default' => 'featured',
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
                'default'     => 'no',
            ),
            array(
                'type'        => Controls_Manager::SWITCHER,
                'id'          => 'views_display',
                'label'       => __( 'Show Views', 'classilist-core' ),
                'label_on'    => __( 'On', 'classilist-core' ),
                'label_off'   => __( 'Off', 'classilist-core' ),
                'default'     => 'no',
            ),
            array(
                'type'        => Controls_Manager::SWITCHER,
                'id'          => 'type_display',
                'label'       => __( 'Show Type', 'classilist-core' ),
                'label_on'    => __( 'On', 'classilist-core' ),
                'label_off'   => __( 'Off', 'classilist-core' ),
                'default'     => 'no',
            ),
			array(
				'type'       => Controls_Manager::NUMBER,
				'id'         => 'number',
				'label'      => __( 'Total Number of Items', 'classilist-core' ),
				'default'    => '5',
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
					'date'   => __( 'Date (Recents comes first', 'classilist-core' ),
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

			// Responsive Columns
			array(
				'mode'    => 'section_start',
				'id'      => 'sec_responsive',
				'label'   => __( 'Number of Responsive Columns', 'classilist-core' ),
			),
			array(
				'type'    => Controls_Manager::SELECT2,
				'id'      => 'col_lg',
				'label'   => __( 'Desktops: > 1199px', 'classilist-core' ),
				'options' => $this->rt_translate['cols'],
				'default' => '4',
			),
			array(
				'type'    => Controls_Manager::SELECT2,
				'id'      => 'col_md',
				'label'   => __( 'Desktops: > 991px', 'classilist-core' ),
				'options' => $this->rt_translate['cols'],
				'default' => '4',
			),
			array(
				'type'    => Controls_Manager::SELECT2,
				'id'      => 'col_sm',
				'label'   => __( 'Tablets: > 767px', 'classilist-core' ),
				'options' => $this->rt_translate['cols'],
				'default' => '3',
			),
			array(
				'type'    => Controls_Manager::SELECT2,
				'id'      => 'col_xs',
				'label'   => __( 'Phones: < 768px', 'classilist-core' ),
				'options' => $this->rt_translate['cols'],
				'default' => '2',
			),
			array(
				'type'    => Controls_Manager::SELECT2,
				'id'      => 'col_mobile',
				'label'   => __( 'Small Phones: < 480px', 'classilist-core' ),
				'options' => $this->rt_translate['cols'],
				'default' => '1',
			),
			array(
				'mode' => 'section_end',
			),

			// Slider options
			array(
				'mode'        => 'section_start',
				'id'          => 'sec_slider',
				'label'       => __( 'Slider Options', 'classilist-core' ),
			),
			array(
                'type'        => Controls_Manager::SWITCHER,
                'id'          => 'slider_navigation',
                'label'       => __( 'Navigation', 'classilist-core' ),
                'label_on'    => __( 'On', 'classilist-core' ),
                'label_off'   => __( 'Off', 'classilist-core' ),
                'default'     => 'yes',
                'description' => __( 'Enable or disable navigation. Default: On', 'classilist-core' ),
            ),
			array(
				'type'        => Controls_Manager::SWITCHER,
				'id'          => 'slider_autoplay',
				'label'       => __( 'Autoplay', 'classilist-core' ),
				'label_on'    => __( 'On', 'classilist-core' ),
				'label_off'   => __( 'Off', 'classilist-core' ),
				'default'     => 'yes',
				'description' => __( 'Enable or disable autoplay. Default: On', 'classilist-core' ),
			),
			array(
				'type'        => Controls_Manager::SWITCHER,
				'id'          => 'slider_stop_on_hover',
				'label'       => __( 'Stop on Hover', 'classilist-core' ),
				'label_on'    => __( 'On', 'classilist-core' ),
				'label_off'   => __( 'Off', 'classilist-core' ),
				'default'     => 'yes',
				'description' => __( 'Stop autoplay on mouse hover. Default: On', 'classilist-core' ),
				'condition'   => array( 'slider_autoplay' => 'yes' ),
			),
			array(
				'type'    => Controls_Manager::SELECT2,
				'id'      => 'slider_interval',
				'label'   => __( 'Autoplay Interval', 'classilist-core' ),
				'options' => array(
					'5000' => __( '5 Seconds', 'classilist-core' ),
					'4000' => __( '4 Seconds', 'classilist-core' ),
					'3000' => __( '3 Seconds', 'classilist-core' ),
					'2000' => __( '2 Seconds', 'classilist-core' ),
					'1000' => __( '1 Second',  'classilist-core' ),
				),
				'default' => '5000',
				'description' => __( 'Set any value for example 5 seconds to play it in every 5 seconds. Default: 5 Seconds', 'classilist-core' ),
				'condition'   => array( 'slider_autoplay' => 'yes' ),
			),
			array(
				'type'    => Controls_Manager::NUMBER,
				'id'      => 'slider_autoplay_speed',
				'label'   => __( 'Autoplay Slide Speed', 'classilist-core' ),
				'default' => 200,
				'description' => __( 'Slide speed in milliseconds. Default: 200', 'classilist-core' ),
				'condition'   => array( 'slider_autoplay' => 'yes' ),
			),
			array(
				'type'        => Controls_Manager::SWITCHER,
				'id'          => 'slider_loop',
				'label'       => __( 'Loop', 'classilist-core' ),
				'label_on'    => __( 'On', 'classilist-core' ),
				'label_off'   => __( 'Off', 'classilist-core' ),
				'default'     => 'yes',
				'description' => __( 'Loop to first item. Default: On', 'classilist-core' ),
			),
			array(
				'mode' => 'section_end',
			),

			// Style Tab
			array(
				'mode'    => 'section_start',
				'id'      => 'sec_style_color',
				'tab'     => Controls_Manager::TAB_STYLE,
				'label'   => __( 'Color', 'classilist-core' ),
			),
			array(
				'type'    => Controls_Manager::COLOR,
				'id'      => 'bgcolor',
				'label'   => __( 'Background', 'classilist-core' ),
				'selectors' => array( '{{WRAPPER}} .listing-grid-each .rtin-item' => 'background-color: {{VALUE}}' ),
			),
			array(
				'type'    => Controls_Manager::COLOR,
				'id'      => 'title_color',
				'label'   => __( 'Title', 'classilist-core' ),
				'selectors' => array( '{{WRAPPER}} .listing-grid-each .rtin-item .rtin-content .rtin-title a' => 'color: {{VALUE}}' ),
			),
			array(
				'type'    => Controls_Manager::COLOR,
				'id'      => 'meta_color',
				'label'   => __( 'Meta', 'classilist-core' ),
				'selectors' => array( '{{WRAPPER}} .listing-grid-each .rtin-item .rtin-content .rtin-meta li, {{WRAPPER}} .listing-grid-each .rtin-item .rtin-content .rtin-meta a' => 'color: {{VALUE}}' ),
			),
			array(
				'type'    => Controls_Manager::COLOR,
				'id'      => 'price_color',
				'label'   => __( 'Price', 'classilist-core' ),
				'selectors' => array( '{{WRAPPER}} .listing-grid-each .rtin-item .rtin-content .rtin-price .rtcl-price-amount' => 'color: {{VALUE}}' ),
			),
			array(
				'mode' => 'section_end',
			),
			array(
				'mode'    => 'section_start',
				'id'      => 'sec_style_type',
				'tab'     => Controls_Manager::TAB_STYLE,
				'label'   => __( 'Typography', 'classilist-core' ),
			),
			array(
				'mode'     => 'group',
				'type'     => \Elementor\Group_Control_Typography::get_type(),
				'id'       => 'title_typo',
				'label'    => __( 'Title', 'classilist-core' ),
				'selector' => '{{WRAPPER}} .listing-grid-each .rtin-item .rtin-content .rtin-title',
			),
			array(
				'mode'     => 'group',
				'type'     => \Elementor\Group_Control_Typography::get_type(),
				'id'       => 'meta_typo',
				'label'    => __( 'Meta', 'classilist-core' ),
				'selector' => '{{WRAPPER}} .listing-grid-each .rtin-item .rtin-content .rtin-meta li',
			),
			array(
				'mode'     => 'group',
				'type'     => \Elementor\Group_Control_Typography::get_type(),
				'id'       => 'price_typo',
				'label'    => __( 'Price', 'classilist-core' ),
				'selector' => '{{WRAPPER}} .listing-grid-each span.rtcl-price-amount',
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
				'post_status'    => 'publish',
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