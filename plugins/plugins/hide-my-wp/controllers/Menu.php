<?php
/**
 * The Menu function
 * Loaded when the user is logged in
 *
 * @file The Menu file
 * @package HMWP/Menu
 * @since 4.0.0
 */

defined( 'ABSPATH' ) || die( 'Cheatin\' uh?' );

class HMWP_Controllers_Menu extends HMWP_Classes_FrontController {

	/**
	 * Hook the Admin load
	 *
	 * @throws Exception
	 * @since 4.0.0
	 */
	public function hookInit() {

		//On error or when plugin disabled.
		if ( defined( 'HMWP_DISABLE' ) && HMWP_DISABLE ) {
			return;
		}

		//Add the plugin menu in admin.
		if ( HMWP_Classes_Tools::userCan( 'manage_options' ) ) {

			//Check if updates.
			if ( get_transient( 'hmwp_update' ) ) {

				//Delete the redirect transient.
				delete_transient( 'hmwp_update' );

				HMWP_Classes_ObjController::getClass( 'HMWP_Classes_Tools' )->checkRewriteUpdate( array() );
			}

			//Check if activated.
			if ( get_transient( 'hmwp_activate' ) ) {

				//Delete the redirect transient.
				delete_transient( 'hmwp_activate' );

				//Initialize WordPress Filesystem.
				$wp_filesystem = HMWP_Classes_ObjController::initFilesystem();

				$package_file = _HMWP_ROOT_DIR_ . '/customize.json';
				if ( $wp_filesystem->exists( $package_file ) ) {
					if ( $customize = json_decode( $wp_filesystem->get_contents( $package_file ), true ) ) {
						//Save customization.
						HMWP_Classes_Tools::saveCustomization( $customize );
						//Remove file after customization.
						$wp_filesystem->delete( $package_file );
					}
				}

				//Make sure the plugin is loading first.
				HMWP_Classes_Tools::movePluginFirst();
			}

			//Show Dashboard Box.
			if ( ! is_multisite() ) {
				add_action( 'wp_dashboard_setup', array( $this, 'hookDashboardSetup' ) );
			}

			if ( strpos( HMWP_Classes_Tools::getValue( 'page' ), 'hmwp_' ) !== false ) {
				add_action( 'admin_enqueue_scripts', array( $this->model, 'fixEnqueueErrors' ), PHP_INT_MAX );
			}

			//Get the error count from security check.
			add_filter( 'hmwp_alert_count', array( HMWP_Classes_ObjController::getClass( 'HMWP_Controllers_SecurityCheck' ), "getRiskErrorCount" ) );

			//Change the plugin name on customization.
			if ( HMWP_Classes_Tools::getOption( 'hmwp_plugin_name' ) <> _HMWP_PLUGIN_FULL_NAME_ ) {

				$websites = array(
					'https://wpplugins.tips',
					'https://hidemywpghost.com',
					'https://wpghost.com',
					'https://hidemywp.com'
				);

				//Hook plugin details.
				add_filter( 'gettext', function( $string ) {

					//Change the plugin name in the plugins list.
					$string = str_ireplace( _HMWP_PLUGIN_AUTHOR_NAME_, HMWP_Classes_Tools::getOption( 'hmwp_plugin_name' ), $string );
					$string = str_ireplace( _HMWP_PLUGIN_FULL_NAME_, HMWP_Classes_Tools::getOption( 'hmwp_plugin_name' ), $string );

					//Return the changed text
					return str_replace( 'WPPlugins', HMWP_Classes_Tools::getOption( 'hmwp_plugin_name' ), $string );

				}, 11, 1 );

				//Hook plugin row metas.
				add_filter( 'plugin_row_meta', function( $plugin_meta ) use ( $websites ) {
					foreach ( $plugin_meta as $key => &$string ) {
						//Change the author URL.
						if( !in_array(HMWP_Classes_Tools::getOption( 'hmwp_plugin_website' ), $websites) ) {
							$string = str_ireplace( $websites, HMWP_Classes_Tools::getOption( 'hmwp_plugin_website' ), $string );
						}
						//Change the plugin details.
						if ( stripos( $string, 'plugin=' . dirname( HMWP_BASENAME ) ) !== false ) {
							//Unset the plugin meta is plugin found
							unset( $plugin_meta[ $key ] );
						}
					}

					return $plugin_meta;
				}, 11, 1 );


				if( ! in_array(HMWP_Classes_Tools::getOption( 'hmwp_plugin_website' ), $websites) ){
					add_filter('hmwp_getview', function ($view){
						$style = '<style>#hmwp_wrap .dashicons-editor-help,.hmwp_help{display: none !important;}</style>';
						return $style . $view;
					}, 11, 1);
				}
			} elseif ( strpos( HMWP_Classes_Tools::getValue( 'page' ), 'hmwp_' ) !== false && apply_filters('hmwp_showaccount', true) ) {
				add_filter('hmwp_getview', function ($view){
					$style = '<script>window.intercomSettings = {api_base: "https://api-iam.intercom.io",app_id: "y45xtsgw",};</script><script>(function(){var w=window;var ic=w.Intercom;if(typeof ic==="function"){ic(\'reattach_activator\');ic(\'update\',w.intercomSettings);}else{var d=document;var i=function(){i.c(arguments);};i.q=[];i.c=function(args){i.q.push(args);};w.Intercom=i;var l=function(){var s=d.createElement(\'script\');s.type=\'text/javascript\';s.async=true;s.src=\'https://widget.intercom.io/widget/y45xtsgw\';var x=d.getElementsByTagName(\'script\')[0];x.parentNode.insertBefore(s,x);};if(document.readyState===\'complete\'){l();}else if(w.attachEvent){w.attachEvent(\'onload\',l);}else{w.addEventListener(\'load\',l,false);}}})();</script>';
					return $style . $view;
				}, 11, 1);
			}


			//Hook the show account option in admin.
			if ( ! HMWP_Classes_Tools::getOption( 'hmwp_plugin_account_show' ) ) {
				add_filter( 'hmwp_showaccount', '__return_false' );
			}



		}

	}

	/**
	 * Creates the Setting menu in WordPress
	 *
	 * @throws Exception
	 * @since 4.0.0
	 */
	public function hookMenu() {

		//On error or when plugin disabled.
		if ( defined( 'HMWP_DISABLE' ) && HMWP_DISABLE ) {
			return;
		}

		if ( ! HMWP_Classes_Tools::isMultisites() ) {

			//If the capability hmwp_manage_settings exists.
			$this->model->addMenu( array(
				HMWP_Classes_Tools::getOption( 'hmwp_plugin_name' ),
				HMWP_Classes_Tools::getOption( 'hmwp_plugin_menu' ),
				HMWP_CAPABILITY,
				'hmwp_settings',
				array( HMWP_Classes_ObjController::getClass( 'HMWP_Controllers_Overview' ), 'init' ),
				HMWP_Classes_Tools::getOption( 'hmwp_plugin_icon' )
			) );

			/* add the admin menu */
			$tabs = $this->model->getMenu();
			foreach ( $tabs as $slug => $tab ) {
				if ( isset( $tab['parent'] ) && isset( $tab['name'] ) && isset( $tab['title'] ) && isset( $tab['capability'] ) ) {

					if ( isset( $tab['show'] ) && !$tab['show'] ) {
						$tab['parent'] = 'hmwp_none';
					}

					$this->model->addSubmenu( array(
						$tab['parent'],
						$tab['title'],
						$tab['name'],
						$tab['capability'],
						$slug,
						$tab['function'],
					) );
				}
			}

			//Update the external links in the menu
			global $submenu;
			if ( ! empty( $submenu['hmwp_settings'] ) ) {
				foreach ( $submenu['hmwp_settings'] as &$item ) {

					if ( isset( $tabs[ $item[2] ]['href'] ) && $tabs[ $item[2] ]['href'] !== false ) {
						if ( wp_parse_url( $tabs[ $item[2] ]['href'], PHP_URL_HOST ) !== wp_parse_url( home_url(), PHP_URL_HOST ) ) {
							$item[0] .= '<i class="dashicons dashicons-external" style="font-size:12px;vertical-align:-2px;height:10px;"></i>';
						}
						$item[2] = $tabs[ $item[2] ]['href'];
					}
				}
			}
		}
	}

	/**
	 * Load the dashboard widget
	 *
	 * @throws Exception
	 * @since 5.1.0
	 */
	public function hookDashboardSetup() {
		wp_add_dashboard_widget( 'hmwp_dashboard_widget', HMWP_Classes_Tools::getOption( 'hmwp_plugin_name' ), array(
				HMWP_Classes_ObjController::getClass( 'HMWP_Controllers_Widget' ),
				'dashboard'
			) );

		// Move our widget to top.
		global $wp_meta_boxes;

		$dashboard = $wp_meta_boxes['dashboard']['normal']['core'];
		$ours = array( 'hmwp_dashboard_widget' => $dashboard['hmwp_dashboard_widget'] );
		$wp_meta_boxes['dashboard']['normal']['core'] = array_merge( $ours, $dashboard );
	}


	/**
	 * Creates the Setting menu in Multisite WordPress
	 *
	 * @throws Exception
	 * @since 5.2.1
	 */
	public function hookMultisiteMenu() {

		//If the capability hmwp_manage_settings exists
		$this->model->addMenu( array(
			HMWP_Classes_Tools::getOption( 'hmwp_plugin_name' ),
			HMWP_Classes_Tools::getOption( 'hmwp_plugin_menu' ),
			HMWP_CAPABILITY,
			'hmwp_settings',
			null,
			HMWP_Classes_Tools::getOption( 'hmwp_plugin_icon' )
		) );

		/* add the admin menu */
		$tabs = $this->model->getMenu();
		foreach ( $tabs as $slug => $tab ) {

			if ( isset( $tab['show'] ) && !$tab['show'] ) {
				$tab['parent'] = 'hmwp_none';
			}

			$this->model->addSubmenu( array(
				$tab['parent'],
				$tab['title'],
				$tab['name'],
				$tab['capability'],
				$slug,
				$tab['function'],
			) );

		}

		//Update the external links in the menu
		global $submenu;
		if ( ! empty( $submenu['hmwp_settings'] ) ) {
			foreach ( $submenu['hmwp_settings'] as &$item ) {

				if ( isset( $tabs[ $item[2] ]['href'] ) && $tabs[ $item[2] ]['href'] !== false ) {
					if ( wp_parse_url( $tabs[ $item[2] ]['href'], PHP_URL_HOST ) !== wp_parse_url( home_url(), PHP_URL_HOST ) ) {
						$item[0] .= '<i class="dashicons dashicons-external" style="font-size:12px;vertical-align:-2px;height:10px;"></i>';
					}
					$item[2] = $tabs[ $item[2] ]['href'];
				}
			}
		}
	}
}
