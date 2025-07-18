<?php
/**
 * Set the ajax action and call for WordPress
 *
 * @file The Actions file
 * @package HMWP/Action
 * @since 4.0.0
 */

defined( 'ABSPATH' ) || die( 'Cheatin\' uh?' );

class HMWP_Classes_Action extends HMWP_Classes_FrontController {

	/**
	 *
	 * All the registered actions
	 *
	 * @var array with all form and ajax actions
	 */
	var $actions = array();

	/**
	 * The hookAjax is loaded as custom hook in hookController class
	 *
	 * @return void
	 * @throws Exception
	 */
	public function hookInit() {
		if ( HMWP_Classes_Tools::isAjax() ) {
			$this->getActions( true );
		}
	}

	/**
	 * The hookSubmit is loaded when action si posted
	 *
	 * @return void
	 * @throws Exception
	 */
	function hookMenu() {
		/* Only if post */
		if ( ! HMWP_Classes_Tools::isAjax() ) {
			$this->getActions();
		}
	}

	/**
	 * Hook the Multisite Menu
	 *
	 * @throws Exception
	 */
	function hookMultisiteMenu() {
		/* Only if post */
		if ( ! HMWP_Classes_Tools::isAjax() ) {
			$this->getActions();
		}
	}

	/**
	 * Get the list with all the plugin actions
	 *
	 * @return array
	 * @since 6.1.1
	 */
	public function getActionsTable() {
		return array(
			array(
				"name"    => "HMWP_Controllers_Settings",
				"actions" => array(
					"action" => array(
						"hmwp_settings",
						"hmwp_tweakssettings",
						"hmwp_confirm",
						"hmwp_newpluginschange",
						"hmwp_abort",
						"hmwp_ignore_errors",
						"hmwp_restore_settings",
						"hmwp_manualrewrite",
						"hmwp_mappsettings",
						"hmwp_firewall",
						"hmwp_geo_download",
						"hmwp_advsettings",
						"hmwp_devsettings",
						"hmwp_devdownload",
						"hmwp_changepathsincache",
						"hmwp_savecachepath",
						"hmwp_backup",
						"hmwp_restore",
						"hmwp_rollback",
						"hmwp_preset",
						"hmwp_download_settings",
						"hmwp_advanced_install",
						"hmwp_pause_enable",
						"hmwp_pause_disable",
						"hmwp_update_product_name",
					)
				),
			),
			array(
				"name"    => "HMWP_Controllers_Overview",
				"actions" => array(
					"action" => array(
						"hmwp_feature_save"
					)
				),
			),
			array(
				"name"    => "HMWP_Controllers_SecurityCheck",
				"actions" => array(
					"action" => array(
						"hmwp_securitycheck",
						"hmwp_frontendcheck",
						"hmwp_fixsettings",
						"hmwp_fixconfig",
						"hmwp_fixprefix",
						"hmwp_fixpermissions",
						"hmwp_fixsalts",
						"hmwp_fixadmin",
						"hmwp_fixupgrade",
						"hmwp_securityexclude",
						"hmwp_resetexclude"
					)
				),
			),
			array(
				"name"    => "HMWP_Controllers_Brute",
				"actions" => array(
					"action" => array(
						"hmwp_brutesettings",
						"hmwp_google_enterprise",
						"hmwp_blockedips",
						"hmwp_deleteip",
						"hmwp_deleteallips"
					)
				),
			),
			array(
				"name"    => "HMWP_Controllers_Templogin",
				"actions" => array(
					"action" => array(
						"hmwp_temploginsettings",
						"hmwp_templogin_block",
						"hmwp_templogin_activate",
						"hmwp_templogin_delete",
						"hmwp_templogin_new",
						"hmwp_templogin_update",
					)
				),
			),
			array(
				"name"    => "HMWP_Controllers_Log",
				"actions" => array(
					"action" => array(
						"hmwp_logsettings"
					)
				),
			),
			array(
				"name"    => "HMWP_Controllers_Widget",
				"actions" => array(
					"action" => "hmwp_widget_securitycheck"
				),
			),
			array(
				"name"    => "HMWP_Controllers_Connect",
				"actions" => array(
					"action" => array(
						"hmwp_connect"
					)
				),
			),
			array(
				"name"    => "HMWP_Classes_Error",
				"actions" => array(
					"action" => array(
						"hmwp_ignoreerror"
					)
				),
			),
		);
	}


	/**
	 * Get all actions from config.json in core directory and add them in the WP
	 *
	 * @param  bool  $ajax
	 *
	 * @throws Exception
	 * @since 4.0.0
	 */
	public function getActions( $ajax = false ) {
		//Proceed only if logged in and in dashboard
		if ( ! is_admin() && ! is_network_admin() ) {
			return;
		}

		$this->actions = array();
		$action        = HMWP_Classes_Tools::getValue( 'action' );
		$nonce         = HMWP_Classes_Tools::getValue( 'hmwp_nonce' );

		if ( $action == '' || $nonce == '' ) {
			return;
		}

		//Get all the plugin actions
		$actions = $this->getActionsTable();

		foreach ( $actions as $block ) {
			//If there is a single action
			if ( isset( $block['actions']['action'] ) ) {

				//If there are more actions for the current block
				if ( ! is_array( $block['actions']['action'] ) ) {
					//Add the action in the actions array
					if ( $block['actions']['action'] == $action ) {
						$this->actions[] = array( 'class' => $block['name'] );
					}
				} else {
					//If there are more actions for the current block
					foreach ( $block['actions']['action'] as $value ) {
						//Add the actions in the actions array
						if ( $value == $action ) {
							$this->actions[] = array( 'class' => $block['name'] );
						}
					}
				}
			}
		}

		//Validate referer based on the call type
		if ( $ajax ) {
			check_ajax_referer( $action, 'hmwp_nonce' );
		} else {
			check_admin_referer( $action, 'hmwp_nonce' );
		}

		//Add the actions in WP.
		foreach ( $this->actions as $actions ) {
			HMWP_Classes_ObjController::getClass( $actions['class'] )->action();
		}
	}

}
