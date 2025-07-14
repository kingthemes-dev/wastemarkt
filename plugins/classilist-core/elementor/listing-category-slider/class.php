<?php
/**
 * @author  RadiusTheme
 * @since   1.0
 * @version 1.0
 */

namespace radiustheme\ClassiList_Core;

use Elementor\Controls_Manager;
use Rtcl\Helpers\Link;

if ( ! defined( 'ABSPATH' ) ) exit;

class Listing_Category_Slider extends Custom_Widget_Base {

	public function __construct( $data = [], $args = null ){
		$this->rt_name = __( 'Listing Category Slider', 'classilist-core' );
		$this->rt_base = 'rt-listing-cat-slider';
        $this->rt_translate = array(
            'cols'  => array(
                '1'  => __( '1 Col', 'classilist-core' ),
                '2'  => __( '2 Col', 'classilist-core' ),
                '3'  => __( '3 Col', 'classilist-core' ),
                '4'  => __( '4 Col', 'classilist-core' ),
                '5'  => __( '5 Col', 'classilist-core' ),
                '6'  => __( '6 Col', 'classilist-core' ),
                '7'  => __( '7 Col', 'classilist-core' ),
                '8'  => __( '8 Col', 'classilist-core' ),
            ),
        );
		parent::__construct( $data, $args );
	}

	public function rt_fields(){
		$terms  = get_terms( array( 'taxonomy' => 'rtcl_category', 'fields' => 'id=>name','parent' => 0, 'hide_empty' => false ) );
		$category_dropdown = array();

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
				'id'      => 'cats',
				'label'   => __( 'Categories', 'classilist-core' ),
				'options' => $category_dropdown,
				'multiple' => true,
				'description' => __( 'Start typing category names. If empty then all parent categories will be displayed', 'classilist-core' ),
			),
			array(
				'type'        => Controls_Manager::SWITCHER,
				'id'          => 'hide_empty',
				'label'       => __( 'Hide Empty', 'classilist-core' ),
				'label_on'    => __( 'On', 'classilist-core' ),
				'label_off'   => __( 'Off', 'classilist-core' ),
				'default'     => 'yes',
				'description' => __( 'Hide Categories that has no listings. Default: On', 'classilist-core' ),
			),
			array(
				'type'        => Controls_Manager::SWITCHER,
				'id'          => 'icon',
				'label'       => __( 'Icon', 'classilist-core' ),
				'label_on'    => __( 'On', 'classilist-core' ),
				'label_off'   => __( 'Off', 'classilist-core' ),
				'default'     => 'yes',
				'description' => __( 'Show or Hide Category Icons/Images. Default: On', 'classilist-core' ),
			),
			array(
				'type'        => Controls_Manager::SWITCHER,
				'id'          => 'count',
				'label'       => __( 'Listing Counts', 'classilist-core' ),
				'label_on'    => __( 'On', 'classilist-core' ),
				'label_off'   => __( 'Off', 'classilist-core' ),
				'default'     => 'yes',
				'description' => __( 'Show or Hide Listing Counts. Default: On', 'classilist-core' ),
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
                'default' => '6',
            ),
            array(
                'type'    => Controls_Manager::SELECT2,
                'id'      => 'col_md',
                'label'   => __( 'Desktops: > 991px', 'classilist-core' ),
                'options' => $this->rt_translate['cols'],
                'default' => '5',
            ),
            array(
                'type'    => Controls_Manager::SELECT2,
                'id'      => 'col_sm',
                'label'   => __( 'Tablets: > 767px', 'classilist-core' ),
                'options' => $this->rt_translate['cols'],
                'default' => '4',
            ),
            array(
                'type'    => Controls_Manager::SELECT2,
                'id'      => 'col_xs',
                'label'   => __( 'Phones: < 768px', 'classilist-core' ),
                'options' => $this->rt_translate['cols'],
                'default' => '3',
            ),
            array(
                'type'    => Controls_Manager::SELECT2,
                'id'      => 'col_mobile',
                'label'   => __( 'Small Phones: < 480px', 'classilist-core' ),
                'options' => $this->rt_translate['cols'],
                'default' => '2',
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
                'id'      => 'title_color',
                'label'   => __( 'Title', 'classilist-core' ),
                'selectors' => array( '{{WRAPPER}} .rtin-item .rtin-title' => 'color: {{VALUE}}' ),
            ),
            array(
                'type'    => Controls_Manager::COLOR,
                'id'      => 'counter_color',
                'label'   => __( 'Counter', 'classilist-core' ),
                'selectors' => array( '{{WRAPPER}} .rtin-item .rtin-count' => 'color: {{VALUE}}' ),
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
                'selector' => '{{WRAPPER}} .rtin-item .rtin-title',
            ),
            array(
                'mode'     => 'group',
                'type'     => \Elementor\Group_Control_Typography::get_type(),
                'id'       => 'counter_typo',
                'label'    => __( 'Counter', 'classilist-core' ),
                'selector' => '{{WRAPPER}} .rtin-item .rtin-count',
            ),
            array(
                'mode' => 'section_end',
            ),
        );
		return $fields;
	}

	private function rt_sort_by_order( $a, $b ) {
		//return $a['order'] < $b['order'] ? false : true;
		if ($a['order'] == $b['order']) {
			return 0;
		}
		return ($a['order'] < $b['order']) ? -1 : 1;
	}

	private function rt_term_post_count( $term_id ){

		$args = array(
			'nopaging'            => true,
			'fields'              => 'ids',
			'post_type'           => 'rtcl_listing',
			'ignore_sticky_posts' => 1,
			'suppress_filters'    => false,
			'tax_query' => array(
				array(
					'taxonomy' => 'rtcl_category',
					'field'    => 'term_id',
					'terms'    => $term_id,
				)
			)
		);

		$posts = get_posts( $args );
		return count( $posts );
	}

	public function rt_results( $data ) {

		$results = array();

		$args = array(
			'taxonomy'   => 'rtcl_category',
			'parent'     => 0,
			'include'    => $data['cats'] ? $data['cats'] : array(),
			'hide_empty' => $data['hide_empty'] ? true : false,
		);

		$terms  = get_terms( $args );

		foreach ( $terms as $term ) {

			$order = get_term_meta( $term->term_id, '_rtcl_order', true );
			$image = get_term_meta( $term->term_id, '_rtcl_image', true );
			$icon  = get_term_meta( $term->term_id, '_rtcl_icon', true );

			if ( $image ) {
				$image = wp_get_attachment_image_src( $image );
				$image = $image[0];
				$icon_html = sprintf( '<img src="%s" alt="%s" />', $image, $term->name );
			}
			elseif ( $icon ) {
				$icon_html = sprintf( '<span class="rtcl-icon rtcl-icon-%s"></span>', $icon );
			}
			else {
				$icon_html = '';
			}

			$count = $this->rt_term_post_count( $term->term_id );


			if ( $data['hide_empty'] && $count < 1 ) {
				continue;
			}

			$results[] = array(
				'name'         => $term->name,
				'order'        => (int) $order,
				'permalink'    => Link::get_category_page_link( $term ),
				'count'        => $count,
				'icon_html'    => $icon_html,
			);
		}

		usort( $results, array( $this, 'rt_sort_by_order' ) );

		return $results;
	}

	protected function render() {
		$data = $this->get_settings();
		$data['rt_results'] = $this->rt_results( $data );
        $count = count( $data['rt_results'] );

		$template = 'view';

		return $this->rt_template( $template, $data );
	}
}