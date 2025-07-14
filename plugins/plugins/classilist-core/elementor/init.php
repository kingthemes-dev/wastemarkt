<?php
/**
 * @author  RadiusTheme
 * @since   1.0
 * @version 1.3
 */

namespace radiustheme\ClassiList_Core;

use Elementor\Plugin;

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

class Custom_Widget_Init {

	public function __construct() {
		add_action( 'elementor/widgets/register',     array( $this, 'init' ) );
		add_action( 'elementor/editor/after_enqueue_styles', array( $this, 'editor_style' ) );
		add_action( 'elementor/elements/categories_registered', array( $this, 'widget_categoty' ) );
	}

	public function editor_style() {
		$img = plugins_url( 'icon.png', __FILE__ );
		wp_add_inline_style( 'elementor-editor', '.elementor-element .icon .rdtheme-el-custom {content: url( '.$img.');width: 28px;}' );
		wp_add_inline_style( 'elementor-editor', '.select2-container--default .select2-selection--single {min-width: 126px !important; min-height: 30px !important;}' );
	}

	public function init() {
		require_once __DIR__ . '/base.php';

		// Widgets -- dirname=>classname /@dev
		$widgets1 = array(
			'title'       => 'Title',
			'info-box'    => 'Info_Box',
			'cta-1'       => 'CTA_1',
			'cta-2'       => 'CTA_2',
			'accordian'   => 'Accordian',
			'google-map'  => 'Google_Map',
			'text-button' => 'Text_Button',
		);

		$widgets2 = array();
		if ( class_exists( 'Rtcl' ) ) {

			$widgets2 = array(
				'listing-search'            => 'Listing_Search',
				'listing-grid'              => 'Listing_Grid',
				'listing-list'              => 'Listing_List',
				'listing-slider'            => 'Listing_Slider',
				'listing-category-box'      => 'Listing_Category_Box',
				'listing-category-slider'   => 'Listing_Category_Slider',
			);

            if ( class_exists( 'RtclStore' ) ) {
                $widgets2 += array(
                    'listing-store-grid' => 'Listing_Store_Grid',
                );
            }
		}

		$widgets = array_merge( $widgets1, $widgets2 );
		$widgets = apply_filters( 'classilist_core_elementor_widgets', $widgets );

		foreach ( $widgets as $dirname => $class ) {
			$template_name = DIRECTORY_SEPARATOR . 'elementor-custom' . DIRECTORY_SEPARATOR . $dirname . DIRECTORY_SEPARATOR . 'class.php';
			if ( file_exists( STYLESHEETPATH . $template_name ) ) {
				$file = STYLESHEETPATH . $template_name;
			}
			elseif ( file_exists( TEMPLATEPATH . $template_name ) ) {
				$file = TEMPLATEPATH . $template_name;
			}
			else {
				$file = __DIR__ . DIRECTORY_SEPARATOR . $dirname . DIRECTORY_SEPARATOR . 'class.php';
			}

			require_once $file;
			
			$classname = __NAMESPACE__ . '\\' . $class;
			Plugin::instance()->widgets_manager->register( new $classname );
		}
	}

	public function widget_categoty( $class ) {
		$id         = CLASSILIST_CORE_THEME_PREFIX . '-widgets'; // Category /@dev
		$properties = array(
			'title' => __( 'RadiusTheme Elements', 'classilist-core' ),
		);

		Plugin::$instance->elements_manager->add_category( $id, $properties );
	}
}

new Custom_Widget_Init();