<?php
/*
Plugin Name: ClassiList Core
Plugin URI: https://www.radiustheme.com
Description: ClassiList Core Plugin for ClassiList Theme
Version: 1.15
Author: RadiusTheme
Author URI: https://www.radiustheme.com
*/

if ( ! defined( 'ABSPATH' ) ) exit;

if ( ! defined( 'CLASSILIST_CORE' ) ) {
	$plugin_data = get_file_data( __FILE__,  array( 'version' => 'Version' ) );
	define( 'CLASSILIST_CORE',               $plugin_data['version'] );
	define( 'CLASSILIST_CORE_THEME_PREFIX',  'classilist' );
	define( 'CLASSILIST_CORE_BASE_URL', plugin_dir_url( __FILE__ ) );
	define( 'CLASSILIST_CORE_BASE_DIR', plugin_dir_path( __FILE__ ) );
	define( 'CLASSILIST_CORE_DEMO_CONTENT', plugin_dir_path( __FILE__ ) . 'demo-content/' );
}

class ClassiList_Core {

	public $plugin  = 'classilist-core';
	public $action  = 'classilist_theme_init';

	public function __construct() {
		$prefix = CLASSILIST_CORE_THEME_PREFIX;
		
		add_action( 'plugins_loaded',    array( $this, 'demo_importer' ), 15 );
		add_action( 'init',    array( $this, 'load_textdomain' ), 16 );
		add_action( 'after_setup_theme', array( $this, 'post_types' ), 17 );
		add_action( 'after_setup_theme', array( $this, 'elementor_widgets' ) );
	}

	public function demo_importer() {
		require_once 'demo-content/ocdi-demo-importer.php';
	}

	public function load_textdomain() {
		load_plugin_textdomain( $this->plugin , false, dirname( plugin_basename( __FILE__ ) ) . '/languages' ); 
	}

	public function post_types(){
		if ( !did_action( $this->action ) || ! defined( 'RT_FRAMEWORK_VERSION' ) ) {
			return;
		}
		// require_once 'inc/post-meta.php';
	}

	public function elementor_widgets(){
		if ( did_action( $this->action ) && did_action( 'elementor/loaded' ) ) {
			require_once 'elementor/init.php';
		}
	}
}

new ClassiList_Core;