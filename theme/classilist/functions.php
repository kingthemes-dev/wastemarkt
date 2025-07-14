<?php
/**
 * @author  RadiusTheme
 * @since   1.0
 * @version 2.2.16
 */

add_editor_style( 'style-editor.css' );

if ( !isset( $content_width ) ) {
	$content_width = 1240;
}
class ClassiList_Main {

	public $theme   = 'classilist';
	public $action  = 'classilist_theme_init';

	public function __construct() {
		add_action( 'after_setup_theme', array( $this, 'load_textdomain' ) );
		$this->includes();
		add_action( 'admin_notices', array( $this, 'plugin_update_notices' ) );
	}

	public function load_textdomain(){
		load_theme_textdomain( $this->theme, get_template_directory() . '/languages' );
	}

	public function includes(){
		do_action( $this->action );
		require_once get_template_directory() . '/inc/constants.php';
		require_once get_template_directory() . '/inc/uri-helper.php';
		require_once get_template_directory() . '/inc/includes.php';
	}

	public function plugin_update_notices() {
		$plugins = array();

		if ( defined( 'CLASSILIST_CORE' ) ) {
			if ( version_compare( CLASSILIST_CORE, '1.14', '<' ) ) {
				$plugins[] = 'ClassiList Core';
			}
		}

		if ( defined( 'RTCL_VERSION' ) ) {
			if ( version_compare( RTCL_VERSION, '2.3.7', '<' ) ) {
				$plugins[] = 'Classified Listing Pro';
			}
		}

		if ( defined( 'RTCL_STORE_VERSION' ) ) {
			if ( version_compare( RTCL_STORE_VERSION, '1.5.9', '<' ) ) {
				$plugins[] = 'Classified Listing Store';
			}
		}

		foreach ( $plugins as $plugin ) {
			$notice = '<div class="error"><p>' . sprintf( __( "Please update plugin <b><i>%s</b></i> to the latest version otherwise some functionalities will not work properly. You can update it from <a href='%s'>here</a>", 'classilist' ), $plugin, menu_page_url( 'classilist-install-plugins', false ) ) . '</p></div>';
			echo wp_kses_post( $notice );
		}
	}
}

new ClassiList_Main;