<?php
/**
 * @author  RadiusTheme
 * @since   1.0
 * @version 1.0
 */

namespace radiustheme\ClassiList_Core;

use Elementor\Controls_Manager;

if ( ! defined( 'ABSPATH' ) ) exit;

class CTA_2 extends Custom_Widget_Base {

	public function __construct( $data = [], $args = null ){
		$this->rt_name = __( 'Call to Action 2', 'classilist-core' );
		$this->rt_base = 'rt-cta-2';
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
				'type'    => Controls_Manager::SELECT2,
				'id'      => 'theme',
				'label'   => __( 'Theme', 'classilist-core' ),
				'options' => array(
					'light' => __( 'Light Background', 'classilist-core' ),
					'dark'  => __( 'Dark Background', 'classilist-core' ),
				),
				'default' => 'light',
			),
			array(
				'type'    => Controls_Manager::TEXTAREA,
				'id'      => 'title',
				'label'   => __( 'Title', 'classilist-core' ),
				'default' => 'Lorem Ipsum Title',
			),
			array(
				'type'    => Controls_Manager::TEXTAREA,
				'id'      => 'subtitle',
				'label'   => __( 'Subtitle', 'classilist-core' ),
				'default' => 'Lorem ipsum dolor sit amet, consectetur adipiscing elit sed do eiusmod tempor incididunt ut labore et dolore magna aliqu Utnim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat',
			),
			array(
				'mode' => 'section_end',
			),
			array(
				'mode'    => 'section_start',
				'id'      => 'sec_btn1',
				'label'   => __( 'Button 1', 'classilist-core' ),
			),
			array(
				'type'    => Controls_Manager::TEXT,
				'id'      => 'btntext1',
				'label'   => __( 'Button Text', 'classilist-core' ),
				'default' => '',
			),
			array(
				'type'    => Controls_Manager::ICONS,
				'id'      => 'btnicon1',
				'label'   => esc_html__( 'Icon', 'evacon-core' ),
				'default' => [
					'value' => 'far fa-smile',
					'library' => 'fa-solid',
				],
			),
			array(
				'type'    => Controls_Manager::URL,
				'id'      => 'btnurl1',
				'label'   => __( 'Button URL', 'classilist-core' ),
				'placeholder' => 'https://your-link.com',
			),
			array(
				'mode' => 'section_end',
			),
			array(
				'mode'    => 'section_start',
				'id'      => 'sec_btn2',
				'label'   => __( 'Button 2', 'classilist-core' ),
			),
			array(
				'type'    => Controls_Manager::TEXT,
				'id'      => 'btntext2',
				'label'   => __( 'Button Text', 'classilist-core' ),
				'default' => '',
			),
			array(
				'type'    => Controls_Manager::ICONS,
				'id'      => 'btnicon2',
				'label'   => esc_html__( 'Icon', 'evacon-core' ),
				'default' => [
					'value' => 'far fa-smile',
					'library' => 'fa-solid',
				],
			),
			array(
				'type'    => Controls_Manager::URL,
				'id'      => 'btnurl2',
				'label'   => __( 'Button URL', 'classilist-core' ),
				'placeholder' => 'https://your-link.com',
			),
			array(
				'mode' => 'section_end',
			),
		);
		return $fields;
	}

	protected function render() {
		$data = $this->get_settings();

		$template = 'view';

		return $this->rt_template( $template, $data );
	}
}