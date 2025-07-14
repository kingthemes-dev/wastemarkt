<?php
/**
 * @author  RadiusTheme
 * @since   1.0
 * @version 1.0
 */

namespace radiustheme\ClassiList_Core;

use Elementor\Controls_Manager;

if ( ! defined( 'ABSPATH' ) ) exit;

class Listing_Store_Grid extends Custom_Widget_Base {

	public function __construct( $data = [], $args = null ){
		$this->rt_name = __( 'Listing Store Grid', 'classilist-core' );
		$this->rt_base = 'rt-listing-store-grid';
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
		$fields = array(
			array(
				'mode'    => 'section_start',
				'id'      => 'sec_general',
				'label'   => __( 'General', 'classilist-core' ),
			),
			array(
				'type'       => Controls_Manager::NUMBER,
				'id'         => 'number',
				'label'      => __( 'Number of Items', 'classilist-core' ),
				'default'    => '4',
				'description' => __( 'Write -1 to show all', 'classilist-core' ),
			),
			array(
				'type'    => Controls_Manager::SELECT2,
				'id'      => 'orderby',
				'label'   => __( 'Order By', 'classilist-core' ),
				'options' => array(
					'date'  => __( 'Date (Recents comes first)', 'classilist-core' ),
					'title' => __( 'Title', 'classilist-core' ),
				),
				'default' => 'date',
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

			// Style Tab
			array(
				'mode'    => 'section_start',
				'id'      => 'sec_style_color',
				'tab'     => Controls_Manager::TAB_STYLE,
				'label'   => __( 'Style', 'classilist-core' ),
			),
			array(
				'type'    => Controls_Manager::COLOR,
				'id'      => 'title_color',
				'label'   => __( 'Title', 'classilist-core' ),
				'selectors' => array( '{{WRAPPER}} .rtcl-store-title' => 'color: {{VALUE}}' ),
			),
			array(
				'type'    => Controls_Manager::COLOR,
				'id'      => 'counter_color',
				'label'   => __( 'Counter', 'classilist-core' ),
				'selectors' => array( '{{WRAPPER}} .ads-count' => 'color: {{VALUE}}' ),
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
				'selector' => '{{WRAPPER}} .rtcl-store-title',
			),
			array(
				'mode'     => 'group',
				'type'     => \Elementor\Group_Control_Typography::get_type(),
				'id'       => 'counter_typo',
				'label'    => __( 'Counter', 'classilist-core' ),
				'selector' => '{{WRAPPER}} .ads-count',
			),
			array(
				'mode' => 'section_end',
			),
		);
		return $fields;
	}

	private function rt_store_query( $data ) {
		$result = array();

		$args = array(
			'post_type'           => 'store',
			'post_status'         => 'publish',
			'ignore_sticky_posts' => true,
			'posts_per_page'      => $data['number'],
		);

		$args['orderby'] = $data['orderby'];
		if ( $data['orderby'] == 'title' ) {
			$args['order'] = 'ASC';
		}

		$items = get_posts( $args );

		foreach ( $items as $item ) {

			$store = new \RtclStore\Models\Store( $item->ID );

			$result[] = array(
				'logo'      => $store->get_the_logo(),
				'title'     => $store->get_the_title(),
				'permalink' => $store->get_the_permalink(),
				'count'     => $store->get_ad_count(),
			);
		}

		return $result;
	}

	protected function render() {
		$data = $this->get_settings();

		$data['stores'] = $this->rt_store_query( $data );

		$template = 'view';

		return $this->rt_template( $template, $data );
	}
}