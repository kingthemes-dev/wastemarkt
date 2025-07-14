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

class Listing_Category_Box extends Custom_Widget_Base {

	public function __construct( $data = [], $args = null ){
		$this->rt_name = __( 'Listing Category Box', 'classilist-core' );
		$this->rt_base = 'rt-listing-cat-box';
		$this->rt_translate = array(
			'cols'  => array(
				'12' => __( '1 Col', 'classilist-core' ),
				'6'  => __( '2 Col', 'classilist-core' ),
				'4'  => __( '3 Col', 'classilist-core' ),
				'3'  => __( '4 Col', 'classilist-core' ),
				'2'  => __( '6 Col', 'classilist-core' ),
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
				'id'      => 'col_xl',
				'label'   => __( 'Desktops: >1199px', 'classilist-core' ),
				'options' => $this->rt_translate['cols'],
				'default' => '3',
			),
			array(
				'type'    => Controls_Manager::SELECT2,
				'id'      => 'col_lg',
				'label'   => __( 'Desktops: >991px', 'classilist-core' ),
				'options' => $this->rt_translate['cols'],
				'default' => '3',
			),
			array(
				'type'    => Controls_Manager::SELECT2,
				'id'      => 'col_md',
				'label'   => __( 'Tablets: >767px', 'classilist-core' ),
				'options' => $this->rt_translate['cols'],
				'default' => '4',
			),
			array(
				'type'    => Controls_Manager::SELECT2,
				'id'      => 'col_sm',
				'label'   => __( 'Phones: >575px', 'classilist-core' ),
				'options' => $this->rt_translate['cols'],
				'default' => '6',
			),
			array(
				'type'    => Controls_Manager::SELECT2,
				'id'      => 'col_mobile',
				'label'   => __( 'Small Phones: <576px', 'classilist-core' ),
				'options' => $this->rt_translate['cols'],
				'default' => '12',
			),
			array(
				'mode' => 'section_end',
			),
		);
		return $fields;
	}

	private function rt_sort_by_order( $a, $b ) {
		if ( $a['order'] == $b['order'] ) {
			return 0;
		}
		return ( $a['order'] < $b['order'] ) ? -1 : 1;
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

		$template = 'view';

		return $this->rt_template( $template, $data );
	}
}