<?php
/**
 * Security Check Class
 * Called on Security Check process
 *
 * @file The Security Check file
 * @package HMWP/Scan
 * @since 5.0.1
 */

defined( 'ABSPATH' ) || die( 'Cheatin\' uh?' );

class HMWP_Controllers_SecurityCheck extends HMWP_Classes_FrontController {
	/**
	 * The time when the security check was made
	 *
	 * @var bool|int Security check time
	 */
	public $securitycheck_time = false;
	/**
	 * All the tasks from the security check
	 *
	 * @var array Security Report
	 */
	public $report = array();
	public $risktasks = array();
	public $riskreport = array();

	/**
	 * Set private variable as null
	 *
	 * @var null
	 */
	private $html = null;
	private $headers = null;
	private $htmlerror = null;

	/**
	 * Initialize the Security Check
	 *
	 * @return void
	 * @throws Exception
	 */
	public function init() {

		// If it's not the Security Check, return
		if ( HMWP_Classes_Tools::getValue( 'page' ) <> 'hmwp_securitycheck' ) {
			return;
		}

		// Initiate security
		$this->initSecurity();

		// Add the Menu Tabs in variable
		if ( is_rtl() ) {
			HMWP_Classes_ObjController::getClass( 'HMWP_Classes_DisplayController' )->loadMedia( 'bootstrap.rtl' );
			HMWP_Classes_ObjController::getClass( 'HMWP_Classes_DisplayController' )->loadMedia( 'rtl' );
		} else {
			HMWP_Classes_ObjController::getClass( 'HMWP_Classes_DisplayController' )->loadMedia( 'bootstrap' );
		}

		HMWP_Classes_ObjController::getClass( 'HMWP_Classes_DisplayController' )->loadMedia( 'font-awesome' );
		HMWP_Classes_ObjController::getClass( 'HMWP_Classes_DisplayController' )->loadMedia( 'settings' );
		HMWP_Classes_ObjController::getClass( 'HMWP_Classes_DisplayController' )->loadMedia( 'alert' );

		if ( HMWP_Classes_Tools::getOption( 'hmwp_security_alert' ) ) {
			if ( $this->securitycheck_time = get_option( HMWP_SECURITY_CHECK_TIME ) ) {
				if ( time() - $this->securitycheck_time['timestamp'] > ( 3600 * 24 * 7 ) ) {
					HMWP_Classes_Error::setNotification( esc_html__( 'You should check your website every week to see if there are any security changes.', 'hide-my-wp' ) );
				}
			}
		}

		// Show source code analysed on Security Check
		if ( HMWP_Classes_Tools::getValue( 'hmwp_crawled' ) ) {
			HMWP_Classes_Error::setNotification( '<pre>' . htmlentities($this->getSourceCode()) . '</pre>' );
		}

		// Show connect for activation
		if ( ! HMWP_Classes_Tools::getOption( 'hmwp_token' ) ) {
			$this->show( 'Connect' );

			return;
		}

		$this->risktasks  = $this->getRiskTasks();
		$this->riskreport = $this->getRiskReport();

		// Show errors on top
		HMWP_Classes_ObjController::getClass( 'HMWP_Classes_Error' )->hookNotices();

		//Show connect for activation
		echo '<noscript><div class="alert-danger text-center py-3">' . sprintf( esc_html__( "Javascript is disabled on your browser! You need to activate the javascript in order to use %s plugin.", 'hide-my-wp' ), esc_html(HMWP_Classes_Tools::getOption( 'hmwp_plugin_name' )) ) . '</div></noscript>';
		$this->show( 'SecurityCheck' );
	}

	/**
	 * Initiate Security List
	 *
	 * @return array|mixed
	 */
	public function initSecurity() {
		$this->report = get_option( HMWP_SECURITY_CHECK );

		if ( ! empty( $this->report ) ) {
			if ( ! $tasks_ignored = get_option( HMWP_SECURITY_CHECK_IGNORE ) ) {
				$tasks_ignored = array();
			}
			$tasks = $this->getTasks();
			foreach ( $this->report as $function => &$row ) {
				if ( ! in_array( $function, $tasks_ignored ) ) {
					if ( isset( $tasks[ $function ] ) ) {
						if ( isset( $row['version'] ) && $function == 'checkWP' && isset( $tasks[ $function ]['solution'] ) ) {
							$tasks[ $function ]['solution'] = str_replace( '{version}', $row['version'], $tasks[ $function ]['solution'] );
						}
						$row = array_merge( $tasks[ $function ], $row );

						if ( ! HMWP_Classes_Tools::getOption( 'hmwp_token' ) || HMWP_Classes_Tools::getOption( 'hmwp_mode' ) == 'default' ) {
							if ( isset( $row['javascript'] ) && $row['javascript'] <> '' ) {
								$row['javascript'] = 'jQuery(\'#hmwp_security_mode_require_modal\').modal(\'show\')';
							}
						}
					}
				} else {
					unset( $this->report[ $function ] );
				}
			}
		}

		return $this->report;
	}

	/**
	 * Get the Risk Tasks for speedometer
	 *
	 * @return array
	 */
	public function getRiskTasks() {
		return array(
			'checkPHP',
			'checkXmlrpc',
			'checkUsersById',
			'checkRDS',
			'checkUploadsBrowsable',
			'checkConfig',
			'checkOldLogin',
			'checkOldPaths',
			'checkCommonPaths',
			'checkVersionDisplayed',
			'checkSSL',
			'checkDBDebug',
			'checkAdminUsers',
			'checkFirewall',
		);
	}

	/**
	 * Get the Risk Report for Daskboard Widget and speedometer
	 *
	 * @return array
	 */
	public function getRiskReport() {
		$riskreport = array();
		//get all the risk tasks
		$risktasks = $this->getRiskTasks();
		//initiate the security report
		$report = $this->initSecurity();

		if ( ! empty( $report ) ) {
			foreach ( $report as $function => $row ) {
				if ( in_array( $function, $risktasks ) ) {
					if ( ! $row['valid'] ) {
						//add the invalid tasks into risk report
						$riskreport[ $function ] = $row;
					}
				}
			}
		}

		// Return the risk report
		return $riskreport;
	}

	/**
	 * @return string|void
	 */
	public function getRiskErrorCount() {
		$tasks = $this->getRiskReport();
		if ( is_array( $tasks ) && count( $tasks ) > 0 ) {
			return '<span class="awaiting-mod">' . count( $tasks ) . '</span>';
		}
	}

	/**
	 * Get all the security tasks
	 *
	 * @return array
	 */
	public function getTasks() {
		return array(
			'checkPHP'              => array(
				'name'     => esc_html__( 'PHP Version', 'hide-my-wp' ),
				'value'    => false,
				'valid'    => false,
				'warning'  => false,
				'message'  => __( "Using an old version of PHP makes your site slow and prone to hacker attacks due to known vulnerabilities that exist in versions of PHP that are no longer maintained. <br /><br />You need <strong>PHP 7.4</strong> or higher for your website.", 'hide-my-wp' ),
				'solution' => esc_html__( "Email your hosting company and tell them you'd like to switch to a newer version of PHP or move your site to a better hosting company.", 'hide-my-wp' ),
			),
			'checkMysql'            => array(
				'name'     => esc_html__( 'Mysql Version', 'hide-my-wp' ),
				'value'    => false,
				'valid'    => false,
				'warning'  => false,
				'message'  => __( "Using an old version of MySQL makes your site slow and prone to hacker attacks due to known vulnerabilities that exist in versions of MySQL that are no longer maintained. <br /><br />You need <strong>Mysql 5.4</strong> or higher", 'hide-my-wp' ),
				'solution' => esc_html__( "Email your hosting company and tell them you'd like to switch to a newer version of MySQL or move your site to a better hosting company", 'hide-my-wp' ),
			),
			'checkWP'               => array(
				'name'     => esc_html__( 'WordPress Version', 'hide-my-wp' ),
				'value'    => false,
				'valid'    => false,
				'warning'  => false,
				'message'  => sprintf( __( "You should always update WordPress to the %slatest versions%s. These usually include the latest security fixes, and don't alter WP in any significant way. These should be applied as soon as WP releases them. <br /><br />When a new version of WordPress is available, you will receive an update message on your WordPress Admin screens. To update WordPress, click the link in this message.", 'hide-my-wp' ), '<a href="https://wordpress.org/download/" target="_blank">', '</a>' ),
				'solution' => esc_html__( "There is a newer version of WordPress available ({version}).", 'hide-my-wp' ),
			),
			'checkWPDebug'          => array(
				'name'       => esc_html__( 'WP Debug Mode', 'hide-my-wp' ),
				'value'      => false,
				'valid'      => false,
				'warning'    => false,
				'message'    => __( "Every good developer should turn on debugging before getting started on a new plugin or theme. In fact, the WordPress Codex 'highly recommends' that developers use WP_DEBUG. <br /><br />Unfortunately, many developers forget the debug mode, even when the website is live. Showing debug logs in the frontend will let hackers know a lot about your WordPress website.", 'hide-my-wp' ),
				'solution'   => __( "Disable WP_DEBUG for live websites in wp-config.php <code>define('WP_DEBUG', false);</code>", 'hide-my-wp' ),
				'javascript' => "jQuery(this).hmwp_fixConfig('WP_DEBUG',false);",
			),
			'checkDBDebug'          => array(
				'name'       => esc_html__( 'DB Debug Mode', 'hide-my-wp' ),
				'value'      => false,
				'valid'      => false,
				'warning'    => false,
				'message'    => esc_html__( "It's not safe to have Database Debug turned on. Make sure you don't use Database debug on live websites.", 'hide-my-wp' ),
				'solution'   => sprintf( __( "Turn off the debug plugins if your website is live. You can also add the option to hide the DB errors <code>global \x24wpdb; \x24wpdb->hide_errors();</code> in wp-config.php file", 'hide-my-wp' ), '<a href="' . HMWP_Classes_Tools::getSettingsUrl( 'hmwp_tweaks#tab=disable' ) . '" >', HMWP_Classes_Tools::getOption( 'hmwp_plugin_menu' ), '</a>' ),
				'javascript' => "jQuery(this).hmwp_fixSettings('hmwp_disable_debug',1);",
			),
			'checkScriptDebug'      => array(
				'name'       => esc_html__( 'Script Debug Mode', 'hide-my-wp' ),
				'value'      => false,
				'valid'      => false,
				'warning'    => false,
				'message'    => __( "Every good developer should turn on debugging before getting started on a new plugin or theme. In fact, the WordPress Codex 'highly recommends' that developers use SCRIPT_DEBUG. Unfortunately, many developers forget the debug mode even when the website is live. Showing debug logs in the frontend will let hackers know a lot about your WordPress website.", 'hide-my-wp' ),
				'solution'   => __( "Disable SCRIPT_DEBUG for live websites in wp-config.php <code>define('SCRIPT_DEBUG', false);</code>", 'hide-my-wp' ),
				'javascript' => "jQuery(this).hmwp_fixConfig('SCRIPT_DEBUG',false);",
			),
			'checkDisplayErrors'    => array(
				'name'     => esc_html__( 'display_errors PHP directive', 'hide-my-wp' ),
				'value'    => false,
				'valid'    => false,
				'warning'  => false,
				'message'  => esc_html__( "Displaying any kind of debug info in the frontend is extremely bad. If any PHP errors happen on your site they should be logged in a safe place and not displayed to visitors or potential attackers.", 'hide-my-wp' ),
				'solution' => __( "Edit wp-config.php and add <code>ini_set('display_errors', 0);</code> at the end of the file", 'hide-my-wp' ),
			),
			'checkSSL'              => array(
				'name'     => esc_html__( 'Backend under SSL', 'hide-my-wp' ),
				'value'    => false,
				'valid'    => false,
				'warning'  => false,
				'message'  => __( "SSL is an abbreviation used for Secure Sockets Layers, which are encryption protocols used on the internet to secure information exchange and provide certificate information.<br /><br />These certificates provide an assurance to the user about the identity of the website they are communicating with. SSL may also be called TLS or Transport Layer Security protocol. <br /><br />It's important to have a secure connection for the Admin Dashboard in WordPress.", 'hide-my-wp' ),
				'solution' => sprintf( esc_html__( "Learn how to set your website as %s. %sClick Here%s", 'hide-my-wp' ), '<strong>' . str_replace( 'http:', 'https:', home_url() ) . '</strong>', '<a href="' . esc_url( HMWP_Classes_Tools::getOption('hmwp_plugin_website') . '/kb/how-to-move-wordpress-from-http-to-https/' ) . '" target="_blank">', '</a>' ),
			),
			'checkAdminUsers'       => array(
				'name'       => esc_html__( "User 'admin' or 'administrator' as Administrator", 'hide-my-wp' ),
				'value'      => false,
				'valid'      => false,
				'warning'    => false,
				'message'    => __( "In the old days, the default WordPress admin username was 'admin' or 'administrator'. Since usernames make up half of the login credentials, this made it easier for hackers to launch brute-force attacks. <br /><br />Thankfully, WordPress has since changed this and now requires you to select a custom username at the time of installing WordPress.", 'hide-my-wp' ),
				'solution'   => esc_html__( "Change the user 'admin' or 'administrator' with another name to improve security.", 'hide-my-wp' ),
				'javascript' => "jQuery('#hmwp_fixadmin_modal').modal('show');",
			),
			'checkUserRegistration' => array(
				'name'     => esc_html__( "Spammers can easily signup", 'hide-my-wp' ),
				'value'    => false,
				'valid'    => false,
				'warning'  => false,
				'message'  => __( "You shouldn't let users subscribe to your blog if you don't have an e-commerce, membership, or guest posting website. You will end up with spam registrations, and your website will be filled with spammy content and comments. We recommend using the Brute Force protection on the registration form.", 'hide-my-wp' ),
				'solution' => sprintf( esc_html__( "Change the signup path from %s %s > Change Paths > Custom Register URL %s then activate Brute Force on Sign up from %s %s > Brute Force > Settings %s or uncheck the option %s > %s > %s", 'hide-my-wp' ), '<a href="' . HMWP_Classes_Tools::getSettingsUrl( 'hmwp_permalinks#tab=newlogin' ) . '" >', HMWP_Classes_Tools::getOption( 'hmwp_plugin_menu' ), '</a>', '<a href="' . HMWP_Classes_Tools::getSettingsUrl( 'hmwp_brute#tab=brute' ) . '" >', HMWP_Classes_Tools::getOption( 'hmwp_plugin_menu' ), '</a>', '<strong>' . esc_html__( 'Settings' ), esc_html__( 'General' ), esc_html__( 'Anyone can register' ) . '</strong>' )
			),
			'checkPluginsUpdates'   => array(
				'name'     => esc_html__( "Outdated Plugins", 'hide-my-wp' ),
				'value'    => false,
				'valid'    => false,
				'warning'  => false,
				'message'  => __( "WordPress and its plugins and themes are like any other software installed on your computer, and like any other application on your devices. Periodically, developers release updates which provide new features, or fix known bugs. <br /><br />These new features may not necessarily be something that you want. In fact, you may be perfectly satisfied with the functionality you currently have. Nevertheless, you are still likely to be concerned about bugs.<br /><br />Software bugs can come in many shapes and sizes. A bug could be very serious, such as preventing users from using a plugin, or it could be minor and only affect a certain part of a theme, for example. In some cases, bugs can cause serious security holes. <br /><br />Keeping plugins up to date is one of the most important and easiest ways to keep your site secure.", 'hide-my-wp' ),
				'solution' => esc_html__( "Go to the Dashboard > Plugins section and update all the plugins to the last version.", 'hide-my-wp' ),
			),
			'checkOldPlugins'       => array(
				'name'     => esc_html__( "Not Recent Updates Released", 'hide-my-wp' ),
				'value'    => false,
				'valid'    => false,
				'warning'  => false,
				'message'  => esc_html__( "Plugins that have not been updated in the last 12 months can have real security problems. Make sure you use updated plugins from WordPress Directory.", 'hide-my-wp' ),
				'solution' => esc_html__( "Go to the Dashboard > Plugins section and update all the plugins to the last version.", 'hide-my-wp' ),
			),
			'checkThemesUpdates'    => array(
				'name'     => esc_html__( "Outdated Themes", 'hide-my-wp' ),
				'value'    => false,
				'valid'    => false,
				'warning'  => false,
				'message'  => __( "WordPress and its plugins and themes are like any other software installed on your computer, and like any other application on your devices. Periodically developers release updates which provide new features or fix known bugs. <br /><br />New features may be something that you do not necessarily want. In fact, you may be perfectly satisfied with the functionality you currently have. Nevertheless, you may still be concerned about bugs.<br /><br />Software bugs can come in many shapes and sizes. A bug could be very serious, such as preventing users from using a plugin, or it could be a minor bug that only affects a certain part of a theme, for example. In some cases, bugs can even cause serious security holes.<br /><br />Keeping themes up to date is one of the most important and easiest ways to keep your site secure.", 'hide-my-wp' ),
				'solution' => esc_html__( "Go to the Dashboard > Appearance section and update all the themes to the last version.", 'hide-my-wp' ),
			),
			'checkDBPrefix'         => array(
				'name'       => esc_html__( "Database Prefix", 'hide-my-wp' ),
				'value'      => false,
				'valid'      => false,
				'warning'    => false,
				'message'    => __( "The WordPress database is like a brain for your entire WordPress site, because every single bit of information about your site is stored there, thus making it a hacker’s favorite target. <br /><br />Spammers and hackers run automated code for SQL injections.<br />Unfortunately, many people forget to change the database prefix when they install WordPress. <br />This makes it easier for hackers to plan a mass attack by targeting the default prefix <strong>wp_</strong>.", 'hide-my-wp' ),
				'solution'   => sprintf( esc_html__( "%s protects your website from most SQL injections but, if possible, use a custom prefix for database tables to avoid SQL injections. %sRead more%s", 'hide-my-wp' ), HMWP_Classes_Tools::getOption( 'hmwp_plugin_name' ), '<a href="' . esc_url( HMWP_Classes_Tools::getOption('hmwp_plugin_website') . '/kb/how-to-change-database-prefix-in-wordpress/' ) . '" target="_blank">', '</a>' ),
				'javascript' => "jQuery(this).hmwp_fixPrefix(true);",
			),
			'checkFilePermissions'  => array(
				'name'       => esc_html__( "File Permissions", 'hide-my-wp' ),
				'value'      => false,
				'valid'      => false,
				'warning'    => false,
				'message'    => __( "File permissions in WordPress play a critical role in website security. Properly configuring these permissions ensures that unauthorized users cannot gain access to sensitive files and data. <br />Incorrect permissions can inadvertently open your website to attacks, making it vulnerable. <br />As a WordPress administrator, understanding and correctly setting file permissions are essential for safeguarding your site against potential threats.", 'hide-my-wp' ),
				'solution'   => sprintf( esc_html__( "Even if the default paths are protected by %s after customization, we recommend setting the correct permissions for all directories and files on your website, use File Manager or FTP to check and change the permissions. %sRead more%s", 'hide-my-wp' ), HMWP_Classes_Tools::getOption( 'hmwp_plugin_name' ), '<a href="' . esc_url( HMWP_Classes_Tools::getOption('hmwp_plugin_website') . '/kb/how-to-change-file-permissions-in-wordpress/' ) . '" target="_blank">', '</a>' ),
				'javascript' => "jQuery('#hmwp_fixpermissions_modal').modal('show');",
			),
			'checkSaltKeys'         => array(
				'name'       => esc_html__( "Salts and Security Keys valid", 'hide-my-wp' ),
				'value'      => false,
				'valid'      => false,
				'warning'    => false,
				'message'    => __( "Security keys are used to ensure better encryption of information stored in the user's cookies and hashed passwords. <br /><br />These make your site more difficult to hack, access and crack by adding random elements to the password. You don't have to remember these keys. In fact, once you set them you'll never see them again. Therefore, there's no excuse for not setting them properly.", 'hide-my-wp' ),
				'solution'   => __( "Security keys are defined in wp-config.php as constants on lines. They should be as unique and as long as possible. <code>AUTH_KEY,SECURE_AUTH_KEY,LOGGED_IN_KEY,NONCE_KEY,AUTH_SALT,SECURE_AUTH_SALT,LOGGED_IN_SALT,NONCE_SALT</code>", 'hide-my-wp' ),
				'javascript' => "jQuery(this).hmwp_fixSalts(true);",
			),
			'checkSaltKeysAge'      => array(
				'name'       => esc_html__( "Security Keys Updated", 'hide-my-wp' ),
				'value'      => false,
				'valid'      => false,
				'warning'    => false,
				'message'    => esc_html__( "The security keys in wp-config.php should be renewed as often as possible.", 'hide-my-wp' ),
				'solution'   => sprintf( __( "You can generate %snew Keys from here%s <code>AUTH_KEY,SECURE_AUTH_KEY,LOGGED_IN_KEY,NONCE_KEY,AUTH_SALT,SECURE_AUTH_SALT,LOGGED_IN_SALT,NONCE_SALT</code>", 'hide-my-wp' ), '<a href="https://api.wordpress.org/secret-key/1.1/salt/" target="_blank">', '</a>' ),
				'javascript' => "jQuery(this).hmwp_fixSalts(true);",
			),
			'checkDbPassword'       => array(
				'name'     => esc_html__( "WordPress Database Password", 'hide-my-wp' ),
				'value'    => false,
				'valid'    => false,
				'warning'  => false,
				'message'  => __( "There is no such thing as an \"unimportant password\"! The same goes for your WordPress database password. <br />Although most servers are configured so that the database can't be accessed from other hosts (or from outside the local network), that doesn't mean your database password should be \"12345\" or no password at all.", 'hide-my-wp' ),
				'solution' => __( "Choose a proper database password, at least 8 characters long with a combination of letters, numbers and special characters. After you change it, set the new password in the wp-config.php file <code>define('DB_PASSWORD', 'NEW_DB_PASSWORD_GOES_HERE');</code>", 'hide-my-wp' ),
			),
			'checkCommonPaths'      => array(
				'name'     => sprintf( esc_html__( "%s is visible in source code", 'hide-my-wp' ), '/' . HMWP_Classes_Tools::getDefault( 'hmwp_wp-content_url' ) ),
				'value'    => false,
				'valid'    => false,
				'warning'  => false,
				'message'  => esc_html__( "It's important to rename common WordPress paths, such as wp-content and wp-includes to prevent hackers from knowing that you have a WordPress website.", 'hide-my-wp' ),
				'solution' => sprintf( esc_html__( "Change the wp-content, wp-includes and other common paths with %s %s > Change Paths%s", 'hide-my-wp' ), '<a href="' . HMWP_Classes_Tools::getSettingsUrl( 'hmwp_permalinks#tab=core' ) . '" >', HMWP_Classes_Tools::getOption( 'hmwp_plugin_menu' ), '</a>' ),
			),
			'checkOldPaths'         => array(
				'name'       => sprintf( esc_html__( "%s path is accessible", 'hide-my-wp' ), '/' . HMWP_Classes_Tools::getDefault( 'hmwp_wp-content_url' ) ),
				'value'      => false,
				'valid'      => false,
				'warning'    => false,
				'message'    => __( "It's important to hide the common WordPress paths to prevent attacks on vulnerable plugins and themes. <br /> Also, it's important to hide the names of plugins and themes to make it impossible for bots to detect them.", 'hide-my-wp' ),
				'solution'   => sprintf( esc_html__( "Switch on %s %s > Change Paths >  Hide WordPress Common Paths%s", 'hide-my-wp' ), '<a href="' . HMWP_Classes_Tools::getSettingsUrl( 'hmwp_permalinks#tab=core' ) . '" >', HMWP_Classes_Tools::getOption( 'hmwp_plugin_menu' ), '</a>' ),
				'javascript' => "jQuery(this).hmwp_fixSettings('hmwp_hide_oldpaths',1);",
			),
			'checkAdminPath'        => array(
				'name'     => sprintf( esc_html__( "%s is visible in source code", 'hide-my-wp' ), '/' . HMWP_Classes_Tools::getOption( 'hmwp_admin_url' ) ),
				'value'    => false,
				'valid'    => false,
				'warning'  => false,
				'message'  => sprintf( __( "Having the admin URL visible in the source code is awful because hackers will immediately know your secret admin path and start a Brute Force attack. The custom admin path should not appear in the ajax URL. <br /><br />Find solutions for %s how to hide the path from source code %s.", 'hide-my-wp' ), '<a href="' . esc_url( HMWP_Classes_Tools::getOption('hmwp_plugin_website') . '/kb/hide-wpadmin-and-wplogin-php-from-source-code/' ) . '" target="_blank">', '</a>' ),
				'solution' => sprintf( esc_html__( "Switch on %s %s > Change Paths > Hide wp-admin from ajax URL%s. Hide any reference to admin path from the installed plugins.", 'hide-my-wp' ), '<a href="' . HMWP_Classes_Tools::getSettingsUrl( 'hmwp_permalinks#tab=ajax' ) . '" >', HMWP_Classes_Tools::getOption( 'hmwp_plugin_menu' ), '</a>', '<strong>', '</strong>' ),
			),
			'checkLoginPath'        => array(
				'name'     => sprintf( esc_html__( "%s is visible in source code", 'hide-my-wp' ), '/' . HMWP_Classes_Tools::getOption( 'hmwp_login_url' ) ),
				'value'    => false,
				'valid'    => false,
				'warning'  => false,
				'message'  => sprintf( __( "Having the login URL visible in the source code is awful because hackers will immediately know your secret login path and start a Brute Force attack. <br /><br />The custom login path should be kept secret, and you should have Brute Force Protection activated for it. <br ><br />Find solutions for %s hiding the login path from source code here %s.", 'hide-my-wp' ), '<a href="' . esc_url( HMWP_Classes_Tools::getOption('hmwp_plugin_website') . '/kb/hide-wpadmin-and-wplogin-php-from-source-code/' ) . '" target="_blank">', '</a>' ),
				'solution' => sprintf( esc_html__( "%sHide the login path%s from theme menu or widget.", 'hide-my-wp' ), '<strong>', '</strong>' ),
			),
			'checkOldLogin'         => array(
				'name'     => sprintf( esc_html__( "%s path is accessible", 'hide-my-wp' ), '/' . HMWP_Classes_Tools::getDefault( 'hmwp_login_url' ) ),
				'value'    => false,
				'valid'    => false,
				'warning'  => false,
				'message'  => __( "If your site allows user logins, you need your login page to be easy to find for your users. You also need to do other things to protect against malicious login attempts. <br /><br />However, obscurity is a valid security layer when used as part of a comprehensive security strategy, and if you want to cut down on the number of malicious login attempts. Making your login page difficult to find is one way to do that.", 'hide-my-wp' ),
				'solution' => sprintf( esc_html__( "Change the wp-login from %s %s > Change Paths > Custom login URL%s and Switch on %s %s > Brute Force Protection%s", 'hide-my-wp' ), '<a href="' . HMWP_Classes_Tools::getSettingsUrl( 'hmwp_permalinks#tab=newlogin' ) . '" >', HMWP_Classes_Tools::getOption( 'hmwp_plugin_menu' ), '</a>', '<a href="' . HMWP_Classes_Tools::getSettingsUrl( 'hmwp_brute#tab=brute' ) . '" >', HMWP_Classes_Tools::getOption( 'hmwp_plugin_menu' ), '</a>' ),
			),
			'checkConfig'           => array(
				'name'       => esc_html__( "wp-config.php & wp-config-sample.php files are accessible", 'hide-my-wp' ),
				'value'      => false,
				'valid'      => false,
				'warning'    => false,
				'message'    => __( "One of the most important files in your WordPress installation is the wp-config.php file. <br />This file is located in the root directory of your WordPress installation and contains your website's base configuration details, such as database connection information.", 'hide-my-wp' ),
				'solution'   => sprintf( esc_html__( "Switch on %s %s > Change Paths > Hide WordPress Common Files%s", 'hide-my-wp' ), '<a href="' . HMWP_Classes_Tools::getSettingsUrl( 'hmwp_permalinks#tab=core' ) . '" >', HMWP_Classes_Tools::getOption( 'hmwp_plugin_menu' ), '</a>' ),
				'javascript' => "jQuery(this).hmwp_fixSettings('hmwp_hide_commonfiles',1);",
			),
			'checkReadme'           => array(
				'name'       => esc_html__( "readme.html file is accessible", 'hide-my-wp' ),
				'value'      => false,
				'valid'      => false,
				'warning'    => false,
				'message'    => esc_html__( "It's important to hide or remove the readme.html file because it contains WP version details.", 'hide-my-wp' ),
				'solution'   => sprintf( esc_html__( "Rename readme.html file or switch on %s %s > Change Paths > Hide WordPress Common Files%s", 'hide-my-wp' ), '<a href="' . HMWP_Classes_Tools::getSettingsUrl( 'hmwp_permalinks#tab=core' ) . '" >', HMWP_Classes_Tools::getOption( 'hmwp_plugin_menu' ), '</a>' ),
				'javascript' => "jQuery(this).hmwp_fixSettings('hmwp_hide_commonfiles',1);",
			),
			'checkInstall'          => array(
				'name'       => esc_html__( "install.php & upgrade.php files are accessible", 'hide-my-wp' ),
				'value'      => false,
				'valid'      => false,
				'warning'    => false,
				'message'    => __( "WordPress is well-known for its ease of installation. <br/>It's important to hide the wp-admin/install.php and wp-admin/upgrade.php files because there have already been a couple of security issues regarding these files.", 'hide-my-wp' ),
				'solution'   => sprintf( esc_html__( "Rename wp-admin/install.php & wp-admin/upgrade.php files or switch on %s %s > Change Paths > Hide WordPress Common Paths%s", 'hide-my-wp' ), '<a href="' . HMWP_Classes_Tools::getSettingsUrl( 'hmwp_permalinks#tab=core' ) . '" >', HMWP_Classes_Tools::getOption( 'hmwp_plugin_menu' ), '</a>' ),
				'javascript' => "jQuery(this).hmwp_fixSettings('hmwp_hide_commonfiles',1);",
			),
			'checkFirewall'         => array(
				'name'       => esc_html__( "Firewall against injections is loaded", 'hide-my-wp' ),
				'value'      => false,
				'valid'      => false,
				'warning'    => false,
				'message'    => __( "The most common way to hack a website is by accessing the domain and adding harmful queries in order to reveal information from files and database.<br /> These attacks are made on any website, WordPress or not, and if a call succeeds … it will probably be too late to save the website.", 'hide-my-wp' ),
				'solution'   => sprintf( esc_html__( "Activate the firewall and select the firewall strength that works for your website %s %s > Change Paths > Firewall & Headers %s", 'hide-my-wp' ), '<a href="' . HMWP_Classes_Tools::getSettingsUrl( 'hmwp_firewall' ) . '" >', HMWP_Classes_Tools::getOption( 'hmwp_plugin_menu' ), '</a>' ),
				'javascript' => "jQuery(this).hmwp_fixSettings('hmwp_sqlinjection',1);",
			),
			'checkVersionDisplayed' => array(
				'name'       => esc_html__( "Versions in Source Code", 'hide-my-wp' ),
				'value'      => false,
				'valid'      => false,
				'warning'    => false,
				'message'    => __( "WordPress, plugins and themes add their version info to the source code, so anyone can see it. <br /><br />Hackers can easily find a website with vulnerable version plugins or themes, and target these with Zero-Day Exploits.", 'hide-my-wp' ),
				'solution'   => sprintf( esc_html__( "Switch on %s %s > Tweaks > %s %s", 'hide-my-wp' ), '<a href="' . HMWP_Classes_Tools::getSettingsUrl( 'hmwp_tweaks#tab=hide' ) . '" >', HMWP_Classes_Tools::getOption( 'hmwp_plugin_menu' ), esc_html__( 'Hide Versions from Images, CSS and JS', 'hide-my-wp' ), '</a>' ),
				'javascript' => "jQuery(this).hmwp_fixSettings('hmwp_hide_version',1);",
			),
			'checkRegisterGlobals'  => array(
				'name'     => esc_html__( "PHP register_globals is on", 'hide-my-wp' ),
				'value'    => false,
				'valid'    => false,
				'warning'  => false,
				'message'  => esc_html__( "This is one of the biggest security issues you can have on your site! If your hosting company has this directive enabled by default, switch to another company immediately!", 'hide-my-wp' ),
				'solution' => __( "If you have access to php.ini file, set <code>register_globals = off</code> or contact the hosting company to set it off", 'hide-my-wp' ),
			),
			'checkExposedPHP'       => array(
				'name'     => esc_html__( "PHP expose_php is on", 'hide-my-wp' ),
				'value'    => false,
				'valid'    => false,
				'warning'  => false,
				'message'  => esc_html__( "Exposing the PHP version will make the job of attacking your site much easier.", 'hide-my-wp' ),
				'solution' => __( "If you have access to php.ini file, set <code>expose_php = off</code> or contact the hosting company to set it off", 'hide-my-wp' ),
			),
			'checkPHPSafe'          => array(
				'name'     => esc_html__( "PHP safe_mode is on", 'hide-my-wp' ),
				'value'    => false,
				'valid'    => false,
				'warning'  => false,
				'message'  => __( "PHP safe mode was one of the attempts to solve security problems of shared web hosting servers. <br /><br />It is still being used by some web hosting providers, however, nowadays this is regarded as improper. A systematic approach proves that it’s architecturally incorrect to try solving complex security issues at the PHP level, rather than at the web server and OS levels.<br /><br />Technically, safe mode is a PHP directive that restricts the way some built-in PHP functions operate. The main problem here is inconsistency. When turned on, PHP safe mode may prevent many legitimate PHP functions from working correctly. At the same time there exists a variety of methods to override safe mode limitations using PHP functions that aren’t restricted, so if a hacker has already got in – safe mode is useless.", 'hide-my-wp' ),
				'solution' => __( "If you have access to php.ini file, set <code>safe_mode = off</code> or contact the hosting company to set it off", 'hide-my-wp' ),
			),
			'checkAllowUrlInclude'  => array(
				'name'     => esc_html__( "PHP allow_url_include is on", 'hide-my-wp' ),
				'value'    => false,
				'valid'    => false,
				'warning'  => false,
				'message'  => __( "Having this PHP directive enabled will leave your site exposed to cross-site attacks (XSS). <br /><br />There's absolutely no valid reason to enable this directive, and using any PHP code that requires it is very risky.", 'hide-my-wp' ),
				'solution' => __( "If you have access to php.ini file, set <code>allow_url_include = off</code> or contact the hosting company to set it off", 'hide-my-wp' ),
			),
			'checkAdminEditor'      => array(
				'name'       => esc_html__( "Plugins/Themes editor disabled", 'hide-my-wp' ),
				'value'      => false,
				'valid'      => false,
				'warning'    => false,
				'message'    => __( "The plugins and themes file editor is a very convenient tool because it enables you to make quick changes without the need to use FTP. <br /><br />Unfortunately, it's also a security issue because it not only shows the PHP source code, it also enables attackers to inject malicious code into your site if they manage to gain access to admin.", 'hide-my-wp' ),
				'solution'   => __( "Disable DISALLOW_FILE_EDIT for live websites in wp-config.php <code>define('DISALLOW_FILE_EDIT', true);</code>", 'hide-my-wp' ),
				'javascript' => "jQuery(this).hmwp_fixConfig('DISALLOW_FILE_EDIT',true);",
			),
			'checkUploadsBrowsable' => array(
				'name'       => sprintf( esc_html__( "Folder %s is browsable", 'hide-my-wp' ), HMWP_Classes_Tools::getDefault( 'hmwp_upload_url' ) ),
				'value'      => false,
				'valid'      => false,
				'warning'    => false,
				'message'    => esc_html__( "Allowing anyone to view all files in the Uploads folder with a browser will allow them to easily download all your uploaded files. It's a security and a copyright issue.", 'hide-my-wp' ),
				'solution'   => sprintf( esc_html__( "Learn how to disable %sDirectory Browsing%s or switch on %s %s > Change Paths > Disable Directory Browsing%s", 'hide-my-wp' ), '<a href="https://www.netsparker.com/blog/web-security/disable-directory-listing-web-servers/">', '</a>', '<a href="' . HMWP_Classes_Tools::getSettingsUrl( 'hmwp_permalinks#tab=core' ) . '" >', HMWP_Classes_Tools::getOption( 'hmwp_plugin_menu' ), '</a>' ),
				'javascript' => "jQuery(this).hmwp_fixSettings('hmwp_disable_browsing',1);",
			),
			'checkWLW'              => array(
				'name'       => esc_html__( "Windows Live Writer is on", 'hide-my-wp' ),
				'value'      => false,
				'valid'      => false,
				'warning'    => false,
				'message'    => esc_html__( "If you're not using Windows Live Writer there's really no valid reason to have its link in the page header, because this tells the whole world you're using WordPress.", 'hide-my-wp' ),
				'solution'   => sprintf( esc_html__( "Switch on %s %s > Tweaks > Hide WLW Manifest scripts%s", 'hide-my-wp' ), '<a href="' . HMWP_Classes_Tools::getSettingsUrl( 'hmwp_tweaks#tab=hide' ) . '" >', HMWP_Classes_Tools::getOption( 'hmwp_plugin_menu' ), '</a>' ),
				'javascript' => "jQuery(this).hmwp_fixSettings('hmwp_disable_manifest',1);",
			),
			'checkXmlrpc'           => array(
				'name'       => esc_html__( "XML-RPC access is on", 'hide-my-wp' ),
				'value'      => false,
				'valid'      => false,
				'warning'    => false,
				'message'    => __( "WordPress XML-RPC is a specification that aims to standardize communications between different systems. It uses HTTP as the transport mechanism and XML as encoding mechanism to enable a wide range of data to be transmitted. <br /><br />The two biggest assets of the API are its extendibility and its security. XML-RPC authenticates using basic authentication. It sends the username and password with each request, which is a big no-no in security circles.", 'hide-my-wp' ),
				'solution'   => sprintf( esc_html__( "Switch on %s %s > Change Paths > Disable XML-RPC access%s", 'hide-my-wp' ), '<a href="' . HMWP_Classes_Tools::getSettingsUrl( 'hmwp_permalinks#tab=api' ) . '" >', HMWP_Classes_Tools::getOption( 'hmwp_plugin_menu' ), '</a>' ),
				'javascript' => "jQuery(this).hmwp_fixSettings('hmwp_disable_xmlrpc',1);",
			),
			'checkRDS'              => array(
				'name'       => esc_html__( "RDS is visible", 'hide-my-wp' ),
				'value'      => false,
				'valid'      => false,
				'warning'    => false,
				'message'    => __( "If you're not using any Really Simple Discovery services such as pingbacks, there's no need to advertise that endpoint (link) in the header. Please note that for most sites this is not a security issue because they \"want to be discovered\", but if you want to hide the fact that you're using WP, this is the way to go.", 'hide-my-wp' ),
				'solution'   => sprintf( esc_html__( "Switch on %s %s > Change Paths > Hide RSD Endpoint%s", 'hide-my-wp' ), '<a href="' . HMWP_Classes_Tools::getSettingsUrl( 'hmwp_permalinks#tab=api' ) . '" >', HMWP_Classes_Tools::getOption( 'hmwp_plugin_menu' ), '</a>' ),
				'javascript' => "jQuery(this).hmwp_fixSettings('hmwp_hide_rsd',1);",
			),
			'checkUsersById'        => array(
				'name'       => esc_html__( "Author URL by ID access", 'hide-my-wp' ),
				'value'      => false,
				'valid'      => false,
				'warning'    => false,
				'message'    => __( "Usernames (unlike passwords) are not secret. By knowing someone's username, you can't log in to their account. You also need the password. <br /><br />However, by knowing the username, you are one step closer to logging in using the username to brute-force the password, or to gain access in a similar way. <br /><br />That's why it's advisable to keep the list of usernames private, at least to some degree. By default, by accessing siteurl.com/?author={id} and looping through IDs from 1 you can get a list of usernames, because WP will redirect you to siteurl.com/author/user/ if the ID exists in the system.", 'hide-my-wp' ),
				'solution'   => sprintf( esc_html__( "Switch on %s %s > Change Paths > Hide Author ID URL%s", 'hide-my-wp' ), '<a href="' . HMWP_Classes_Tools::getSettingsUrl( 'hmwp_permalinks#tab=author' ) . '" >', HMWP_Classes_Tools::getOption( 'hmwp_plugin_menu' ), '</a>' ),
				'javascript' => "jQuery(this).hmwp_fixSettings('hmwp_hide_authors',1);",
			),
			'checkBlogDescription'  => array(
				'name'     => esc_html__( "Default WordPress Tagline", 'hide-my-wp' ),
				'value'    => false,
				'valid'    => false,
				'warning'  => false,
				'message'  => __( "The WordPress site tagline is a short phrase located under the site title, similar to a subtitle or advertising slogan. The goal of a tagline is to convey the essence of your site to visitors. <br /><br />If you don't change the default tagline it will be very easy to detect that your website was actually built with WordPress", 'hide-my-wp' ),
				'solution' => sprintf( esc_html__( "Change the Tagline in %s > %s > %s", 'hide-my-wp' ), '<strong>' . esc_html__( 'Settings' ), esc_html__( 'General' ), esc_html__( 'Tagline' ) . '</strong>' ),
			),

		);
	}

	/**
	 * Process the security check
	 */
	public function doSecurityCheck() {

		if ( ! $tasks_ignored = get_option( HMWP_SECURITY_CHECK_IGNORE ) ) {
			$tasks_ignored = array();
		}

		$tasks = $this->getTasks();
		foreach ( $tasks as $function => $task ) {
			if ( ! in_array( $function, $tasks_ignored ) ) {
				if ( $result = @call_user_func( array( $this, $function ) ) ) {
					$this->report[ $function ] = $result;
				}
			}
		}


		update_option( HMWP_SECURITY_CHECK, $this->report );
		update_option( HMWP_SECURITY_CHECK_TIME, array( 'timestamp' => current_time( 'timestamp', 1 ) ) );
	}

	/**
	 * Run the actions on submit
	 *
	 * @throws Exception
	 */
	public function action() {
		parent::action();

		if ( ! HMWP_Classes_Tools::userCan( HMWP_CAPABILITY ) ) {
			return;
		}

		switch ( HMWP_Classes_Tools::getValue( 'action' ) ) {
			case 'hmwp_securitycheck':

				$this->doSecurityCheck();

				if ( HMWP_Classes_Tools::isAjax() ) {
					wp_send_json_success( esc_html__( 'Done!', 'hide-my-wp' ) );
				}

				break;

			case 'hmwp_frontendcheck':

				$urls       = $error = array();
				$filesystem = HMWP_Classes_Tools::initFilesystem();

				//set hmwp_preview and not load the broken paths with WordPress rules
				$custom_logo_id = get_theme_mod( 'custom_logo' );
				if ( (int) $custom_logo_id > 0 ) {
					if ( $logo = wp_get_attachment_image_src( $custom_logo_id, 'full' ) ) {
						$image = $logo[0];

						if ( $filesystem->exists( str_replace( home_url( '/' ), ABSPATH, $image ) ) ) {
							$url    = $image . '?hmwp_preview=1';
							$url    = HMWP_Classes_ObjController::getClass( 'HMWP_Models_Rewrite' )->find_replace_url( $url );
							$urls[] = $url;
						}
					}
				}

				if ( empty( $urls ) ) {

					$image = _HMWP_ROOT_DIR_ . '/view/assets/img/logo.svg';
					if ( $filesystem->exists( str_replace( home_url( '/' ), ABSPATH, $image ) ) ) {
						$url    = _HMWP_URL_ . '/view/assets/img/logo.svg?hmwp_preview=1';
						$url    = HMWP_Classes_ObjController::getClass( 'HMWP_Models_Rewrite' )->find_replace_url( $url );
						$urls[] = $url;
					}

				}

				$url    = home_url( '/' ) . '?hmwp_preview=1';
				$url    = HMWP_Classes_ObjController::getClass( 'HMWP_Models_Rewrite' )->find_replace_url( $url );
				$urls[] = $url;

				if ( HMWP_Classes_Tools::getOption( 'hmwp_hideajax_admin' ) ) {
					$url = home_url( HMWP_Classes_Tools::getOption( 'hmwp_admin-ajax_url' ) ) . '?hmwp_preview=1';
				} else {
					$url = admin_url( HMWP_Classes_Tools::getOption( 'hmwp_admin-ajax_url' ) ) . '?hmwp_preview=1';
				}
				$url    = HMWP_Classes_ObjController::getClass( 'HMWP_Models_Rewrite' )->find_replace_url( $url );
				$urls[] = $url;


				$url    = home_url() . '/' . HMWP_Classes_Tools::getOption( 'hmwp_wp-json' );
				$urls[] = $url;


				foreach ( $urls as $url ) {

					if ( is_ssl() ) {
						$url = str_replace( 'http://', 'https://', $url );
					}

					$response = HMWP_Classes_Tools::hmwp_localcall( $url, array(
						'redirection' => 1,
						'cookies'     => false
					) );

					if ( ! is_wp_error( $response ) && in_array( wp_remote_retrieve_response_code( $response ), array(
							404,
							302,
							301
						) ) ) {
						$error[] = '<a href="' . $url . '" target="_blank" style="word-break: break-word;">' . str_replace( '?hmwp_preview=1', '', $url ) . '</a> (' . wp_remote_retrieve_response_code( $response ) . ' ' . wp_remote_retrieve_response_message( $response ) . ')';
					}
				}

				//Test new admin path. Send all cookies to admin path
				if ( HMWP_Classes_Tools::getDefault( 'hmwp_admin_url' ) <> HMWP_Classes_Tools::getOption( 'hmwp_admin_url' ) ) {

					$url = admin_url( 'admin.php' );
					$url = HMWP_Classes_ObjController::getClass( 'HMWP_Models_Rewrite' )->find_replace_url( $url );

					if ( is_ssl() ) {
						$url = str_replace( 'http://', 'https://', $url );
					}

					$response = HMWP_Classes_Tools::hmwp_localcall( $url, array(
						'redirection' => 1,
						'cookies'     => $_COOKIE
					) );

					if ( ! is_wp_error( $response ) && in_array( wp_remote_retrieve_response_code( $response ), array(
							404,
							302,
							301
						) ) ) {
						$error[] = '<a href="' . $url . '" target="_blank" style="word-break: break-word;">' . str_replace( '?hmwp_preview=1', '', $url ) . '</a> (' . wp_remote_retrieve_response_code( $response ) . ' ' . wp_remote_retrieve_response_message( $response ) . ')';

					}
				}

				if ( ! empty( $error ) && HMWP_Classes_Tools::isNginx() ) {
					$error[] = '<a href="' . esc_url( HMWP_Classes_Tools::getOption('hmwp_plugin_website') . '/kb/how-to-setup-hide-my-wp-on-nginx-server/' ) . '" target="_blank" style="word-break: break-word;line-height: 35px;font-weight: 700;">' . esc_html__( "Don't forget to reload the Nginx service.", 'hide-my-wp' ) . '</a>';
				}

				if ( HMWP_Classes_Tools::isAjax() ) {
					if ( empty( $error ) ) {
						$message   = array();
						$message[] = esc_html__( 'Great! The new paths are loading correctly.', 'hide-my-wp' );
						if ( HMWP_Classes_Tools::getOption( 'prevent_slow_loading' ) ) {
							$message[] = '<form id="hmwp_fixsettings_form" method="POST">
                                         ' . wp_nonce_field( 'hmwp_fixsettings', 'hmwp_nonce', false, false ) . '
                                         <input type="hidden" name="action" value="hmwp_fixsettings"/>
                                         
                                         <div class="col-sm-12 p-0 my-2 switch switch-xxs" style="font-size: 0.9rem;">
                                            <input type="checkbox" id="prevent_slow_loading" name="prevent_slow_loading" onChange="jQuery(this).hmwp_fixSettings(\'prevent_slow_loading\',0);" class="switch" ' . ( HMWP_Classes_Tools::getOption( 'prevent_slow_loading' ) ? 'checked="checked"' : '' ) . ' value="1"/>
                                            <label for="prevent_slow_loading">' . sprintf( esc_html__( "You can now turn off '%s' option.", 'hide-my-wp' ), __( 'Prevent Broken Website Layout', 'hide-my-wp' ) ) . '</label>
                                         </div>
                                       </form>';
						}
						if ( HMWP_Classes_Tools::isCachePlugin() && ! HMWP_Classes_Tools::getOption( 'hmwp_change_in_cache' ) ) {
							$message[] = '<form id="hmwp_fixsettings_form" method="POST">
                                         ' . wp_nonce_field( 'hmwp_fixsettings', 'hmwp_nonce', false, false ) . '
                                         <input type="hidden" name="action" value="hmwp_fixsettings"/>
                                         
                                         <div class="col-sm-12 p-0 my-2 switch switch-xxs" style="font-size: 0.9rem;">
                                            <input type="checkbox" id="hmwp_change_in_cache" name="hmwp_change_in_cache" onChange="jQuery(this).hmwp_fixSettings(\'hmwp_change_in_cache\',1);" class="switch" ' . ( HMWP_Classes_Tools::getOption( 'hmwp_change_in_cache' ) ? 'checked="checked"' : '' ) . ' value="1"/>
                                            <label for="hmwp_change_in_cache">' . sprintf( esc_html__( "You can now turn on '%s' option.", 'hide-my-wp' ), __( 'Change Paths in Cached Files', 'hide-my-wp' ) ) . '</label>
                                         </div>
                                       </form>';
						}

						wp_send_json_success( join( '', $message ) );
					} else {
						wp_send_json_error( esc_html__( 'Error! The new paths are not loading correctly. Clear all cache and try again.', 'hide-my-wp' ) . "<br /><br />" . join( '<br />', $error ) );
					}
				}

				break;

			case 'hmwp_fixsettings':
				//Initialize WordPress Filesystem
				$wp_filesystem = HMWP_Classes_ObjController::initFilesystem();

				$name  = HMWP_Classes_Tools::getValue( 'name' );
				$value = HMWP_Classes_Tools::getValue( 'value' );

				if ( HMWP_Classes_Tools::getIsset( 'name' ) && HMWP_Classes_Tools::getIsset( 'value' ) ) {
					if ( in_array( $name, array_keys( HMWP_Classes_Tools::$options ) ) ) {
						HMWP_Classes_Tools::saveOptions( $name, $value );
						//call it in case of rule change
						HMWP_Classes_ObjController::getClass( 'HMWP_Models_Settings' )->saveRules();

						if ( HMWP_Classes_Tools::isIIS() && HMWP_Classes_ObjController::getClass( 'HMWP_Models_Rules' )->isConfigWritable() ) {
							//Flush the changes for IIS server
							HMWP_Classes_ObjController::getClass( 'HMWP_Models_Rewrite' )->flushChanges();
						}

						//Hide the common WP Files that migth be visible to detectors
						if ( $name == 'hmwp_hide_commonfiles' ) {
							$wp_filesystem->delete( HMWP_Classes_Tools::getRootPath() . 'readme.html' );
							$wp_filesystem->delete( HMWP_Classes_Tools::getRootPath() . 'license.txt' );
							$wp_filesystem->delete( HMWP_Classes_Tools::getRootPath() . 'wp-config-sample.php' );
						}

						$message = esc_html__( 'Saved! You can run the test again.', 'hide-my-wp' );
						if ( HMWP_Classes_Tools::isNginx() || HMWP_Classes_Tools::isCloudPanel() ) {
							$message .= '<br />' . esc_html__( "Don't forget to reload the Nginx service.", 'hide-my-wp' ) . ' ' . '<strong><a href="' . esc_url( HMWP_Classes_Tools::getOption('hmwp_plugin_website') . '/kb/how-to-setup-hide-my-wp-on-nginx-server/' ) . '" target="_blank" style="color: red">' . esc_html__( "Learn How", 'hide-my-wp' ) . '</a></strong>';
						}

						if ( HMWP_Classes_Tools::isAjax() ) {
							wp_send_json_success( $message );
						}

						break;
					}
				}
				//refresh the security scan
				$this->doSecurityCheck();

				if ( HMWP_Classes_Tools::isAjax() ) {
					wp_send_json_error( esc_html__( 'Could not fix it. You need to change it manually.', 'hide-my-wp' ) );
				}

				break;
			case 'hmwp_fixconfig':

				$name  = HMWP_Classes_Tools::getValue( 'name' );
				$value = HMWP_Classes_Tools::getValue( 'value', null );

				if ( ! in_array( $name, array(
						'WP_DEBUG',
						'SCRIPT_DEBUG',
						'DISALLOW_FILE_EDIT'
					) ) || ! in_array( $value, array( 'true', 'false' ) ) ) {

					if ( HMWP_Classes_Tools::isAjax() ) {
						wp_send_json_error( esc_html__( 'Could not fix it. You need to change it manually.', 'hide-my-wp' ) );
					}
					break;
				}

				if ( $name && isset( $value ) ) {
					if ( $config_file = HMWP_Classes_Tools::getConfigFile() ) {

						/** @var HMWP_Models_Rules $rulesModel */
						$rulesModel = HMWP_Classes_ObjController::getClass( 'HMWP_Models_Rules' );

						$wp_filesystem = HMWP_Classes_ObjController::initFilesystem();
						if ( ! $rulesModel->isConfigWritable( $config_file ) ) {
							$current_permission = $wp_filesystem->getchmod( $config_file );
							$wp_filesystem->chmod( $config_file, 0644 );
						}

						if ( $rulesModel->isConfigWritable( $config_file ) ) {
							$find    = "define\s?\(\s?'$name'";
							$replace = "define('$name',$value);" . PHP_EOL;
							if ( $rulesModel->findReplace( $find, $replace, $config_file ) ) {

								if ( isset( $current_permission ) ) {
									$wp_filesystem->chmod( $config_file, octdec( $current_permission ) );
								}

								if ( HMWP_Classes_Tools::isAjax() ) {
									wp_send_json_success( esc_html__( 'Saved! You can run the test again.', 'hide-my-wp' ) );
								}
								break;
							}
						}

					}
				}
				//refresh the security scan
				$this->doSecurityCheck();

				if ( HMWP_Classes_Tools::isAjax() ) {
					wp_send_json_error( esc_html__( 'Could not fix it. You need to change it manually.', 'hide-my-wp' ) );
				}
				break;
			case 'hmwp_fixprefix':

				/** @var HMWP_Models_Prefix $prefixModel */ $prefixModel = HMWP_Classes_ObjController::getClass( 'HMWP_Models_Prefix' );

				//to change or undo the database prefix (true = change, false = undo)
				if ( HMWP_Classes_Tools::getValue( 'value' ) == 'true' ) {
					//Generate random database prefix
					$prefixModel->setPrefix( $prefixModel->generateValidateNewPrefix() );;
				}

				//run the process to change the prefix
				if ( $prefixModel->changePrefix() ) {
					//empty the cache
					HMWP_Classes_Tools::emptyCache();

					//Flush the rules in WordPress
					flush_rewrite_rules();

					//wait for config refresh
					sleep( 10 );

					//Force the recheck security notification
					delete_option( HMWP_SECURITY_CHECK );

					if ( HMWP_Classes_Tools::isAjax() ) {
						wp_send_json_success( esc_html__( 'Saved! You can run the test again.', 'hide-my-wp' ) );
					}
					break;

				}

				if ( HMWP_Classes_Tools::isAjax() ) {
					wp_send_json_error( esc_html__( 'Could not fix it. You need to change it manually.', 'hide-my-wp' ) );
				}

				break;
			case 'hmwp_fixpermissions':
				$value = HMWP_Classes_Tools::getValue( 'value' );

				/** @var HMWP_Models_Permissions $permissionModel */
				$permissionModel = HMWP_Classes_ObjController::getClass( 'HMWP_Models_Permissions' );

				//run the process to change the prefix
				if ( $permissionModel->changePermissions( $value ) ) {

					//refresh the security scan
					$this->doSecurityCheck();

					if ( HMWP_Classes_Tools::isAjax() ) {
						wp_send_json_success( esc_html__( 'Saved! You can run the test again.', 'hide-my-wp' ) );
					}
					break;
				}

				if ( HMWP_Classes_Tools::isAjax() ) {
					wp_send_json_error( esc_html__( 'Could not fix it. You need to change it manually.', 'hide-my-wp' ) );
				}
				break;
			case 'hmwp_fixsalts':

				/** @var HMWP_Models_Salts $saltsModel */ $saltsModel = HMWP_Classes_ObjController::getClass( 'HMWP_Models_Salts' );

				//run the process to change the prefix
				if ( $saltsModel->validateSalts() ) {
					if ( $saltsModel->generateSalts() ) {
						update_option( HMWP_SALT_CHANGED, array( 'timestamp' => current_time( 'timestamp', 1 ) ) );

						//Force the recheck security notification
						delete_option( HMWP_SECURITY_CHECK );

						if ( HMWP_Classes_Tools::isAjax() ) {
							wp_send_json_success( esc_html__( 'Saved! You can run the test again.', 'hide-my-wp' ) . '<script>location.href = "' . site_url() . '/' . HMWP_Classes_Tools::getOption( 'hmwp_login_url' ) . '?redirect_to=' . urlencode( site_url( 'wp-admin/admin.php?page=hmwp_securitycheck' ) ) . '"</script>' );
						}
						break;
					}
				}

				if ( HMWP_Classes_Tools::isAjax() ) {
					wp_send_json_error( esc_html__( 'Could not fix it. You need to change it manually.', 'hide-my-wp' ) );
				}
				break;
			case 'hmwp_fixadmin':
				global $wpdb;
				$username = HMWP_Classes_Tools::getValue( 'name' );

				if ( ! validate_username( $username ) ) {
					if ( HMWP_Classes_Tools::isAjax() ) {
						wp_send_json_error( esc_html__( 'Invalid username.', 'hide-my-wp' ) );
					}
					break;
				}

				if ( username_exists( $username ) ) {
					if ( HMWP_Classes_Tools::isAjax() ) {
						wp_send_json_error( esc_html__( 'A user already exists with that username.', 'hide-my-wp' ) );
					}
					break;
				}

				$admin = false;
				if ( username_exists( 'admin' ) ) {
					$admin = 'admin';
				} elseif ( username_exists( 'administrator' ) ) {
					$admin = 'administrator';
				}

				if ( $admin ) {
					// Query main user table
					$wpdb->query( $wpdb->prepare( "UPDATE `{$wpdb->users}` SET user_login = %s WHERE user_login = %s", $username, $admin ) );

					// Process sitemeta if we're in a multi-site situation
					if ( is_multisite() ) {
						$old_admins = $wpdb->get_var( "SELECT meta_value FROM `" . $wpdb->sitemeta . "` WHERE meta_key = 'site_admins'" );
						$new_admins = str_replace( strlen( $admin ) . ':"' . $admin . '"', strlen( $username ) . ':"' . $username . '"', $old_admins );
						$wpdb->query( $wpdb->prepare( "UPDATE `{$wpdb->sitemeta}` SET meta_value = %s WHERE meta_key = 'site_admins'", $new_admins ) );
					}
				}

				//Force the recheck security notification
				delete_option( HMWP_SECURITY_CHECK_TIME );

				if ( HMWP_Classes_Tools::isAjax() ) {
					wp_send_json_success( esc_html__( 'Saved! You can run the test again.', 'hide-my-wp' ) . '<script>location.href = "' . site_url() . '/' . HMWP_Classes_Tools::getOption( 'hmwp_login_url' ) . '?redirect_to=' . urlencode( site_url( 'wp-admin/admin.php?page=hmwp_securitycheck' ) ) . '"</script>' );
				}

				break;
			case 'hmwp_fixupgrade':

				if ( ! function_exists( 'get_plugins' ) ) {
					require_once ABSPATH . 'wp-admin/includes/plugin.php';
				}

				$all_plugins = get_plugins();

				include_once ABSPATH . 'wp-admin/includes/class-wp-upgrader.php';

				foreach ( $all_plugins as $plugin_slug => $value ) {
					$upgrader = new \Plugin_Upgrader( new \WP_Ajax_Upgrader_Skin() );
					$upgrader->upgrade( $plugin_slug );
				}

				if ( HMWP_Classes_Tools::isAjax() ) {
					wp_send_json_success( esc_html__( 'Saved! You can run the test again.', 'hide-my-wp' ) );
				}

				break;

			case 'hmwp_securityexclude':

				$name = HMWP_Classes_Tools::getValue( 'name' );
				if ( $name ) {
					if ( ! $tasks_ignored = get_option( HMWP_SECURITY_CHECK_IGNORE ) ) {
						$tasks_ignored = array();
					}

					$tasks_ignored[] = $name;
					$tasks_ignored   = array_unique( $tasks_ignored );
					update_option( HMWP_SECURITY_CHECK_IGNORE, $tasks_ignored );
				}

				if ( HMWP_Classes_Tools::isAjax() ) {
					wp_send_json_success( esc_html__( 'Saved! This task will be ignored on future tests.', 'hide-my-wp' ) );
				}

				break;
			case 'hmwp_resetexclude':

				update_option( HMWP_SECURITY_CHECK_IGNORE, array() );

				if ( HMWP_Classes_Tools::isAjax() ) {
					wp_send_json_success( esc_html__( 'Saved! You can run the test again.', 'hide-my-wp' ) );
				}

				break;
		}


	}

	/**
	 * Check PHP version
	 *
	 * @return array
	 */
	public function checkPHP() {
		$phpversion = phpversion();

		if ( $phpversion <> '' && strpos( $phpversion, '-' ) !== false ) {
			$phpversion = substr( $phpversion, 0, strpos( $phpversion, '-' ) );
		}

		return array(
			'value' => $phpversion,
			'valid' => ( version_compare( $phpversion, '7.4', '>=' ) ),
		);
	}

	/**
	 * Check if mysql is up-to-date
	 *
	 * @return array
	 */
	public function checkMysql() {
		global $wpdb;

		$mysql_version = $wpdb->db_version();

		return array(
			'value' => $mysql_version,
			'valid' => ( version_compare( $mysql_version, '8.0', '>' ) ),
		);

	}

	/**
	 * Check is WP_DEBUG is true
	 *
	 * @return array|bool
	 */
	public function checkWPDebug() {
		if ( defined( 'WP_DEBUG' ) ) {
			if ( defined( 'WP_DEBUG_DISPLAY' ) && ! WP_DEBUG_DISPLAY ) {
				return array(
					'value' => esc_html__( 'No' ),
					'valid' => true
				);
			} else {
				return array(
					'value' => ( WP_DEBUG ? esc_html__( 'Yes' ) : esc_html__( 'No' ) ),
					'valid' => ! WP_DEBUG,
				);
			}

		}

		return false;
	}

	/**
	 * Check if DB debugging is enabled
	 *
	 * @return array
	 */
	static function checkDbDebug() {
		global $wpdb;
		$show_errors = ( $wpdb->show_errors && ! HMWP_Classes_Tools::getOption( 'hmwp_disable_debug' ) );

		return array(
			'value' => ( $show_errors ? esc_html__( 'Yes' ) : esc_html__( 'No' ) ),
			'valid' => ! $show_errors,
		);

	}

	/**
	 * Check if global WP JS debugging is enabled
	 *
	 * @return array|bool
	 */
	static function checkScriptDebug() {
		if ( defined( 'SCRIPT_DEBUG' ) ) {
			return array(
				'value' => ( SCRIPT_DEBUG ? esc_html__( 'Yes' ) : esc_html__( 'No' ) ),
				'valid' => ! SCRIPT_DEBUG,
			);
		}

		return false;
	}

	/**
	 * Check if the backend is SSL or not
	 *
	 * @return array
	 */
	public function checkSSL() {
		return array(
			'value' => ( is_ssl() ? esc_html__( 'Yes' ) : esc_html__( 'No' ) ),
			'valid' => ( is_ssl() ),
		);
	}

	/**
	 * Check Admin User declared
	 *
	 * @return array
	 */
	public function checkAdminUsers() {
		if ( ! $admin = username_exists( 'admin' ) ) {
			$admin = username_exists( 'administrator' );
		}

		return array(
			'value' => ( ! empty( $admin ) ? esc_html__( 'Yes' ) : esc_html__( 'No' ) ),
			'valid' => ( empty( $admin ) ),
		);
	}

	/**
	 * Check WordPress version
	 *
	 * @return array|bool
	 */
	public function checkWP() {
		global $wp_version;
		$wp_lastversion = false;
		if ( isset( $wp_version ) ) {

			$url      = 'https://api.wordpress.org/core/version-check/1.7/';
			$response = HMWP_Classes_Tools::hmwp_localcall( $url, array( 'timeout' => 5 ) );

			$obj = json_decode( $response['body'] );
			if ( isset( $obj->offers[0] ) ) {
				$upgrade = $obj->offers[0];
				if ( isset( $upgrade->version ) ) {
					$wp_lastversion = $upgrade->version;
				}
			}

			if ( $wp_lastversion ) {
				return array(
					'value'   => $wp_version,
					'valid'   => version_compare( $wp_version, $wp_lastversion, '>=' ),
					'version' => $wp_lastversion,
				);
			}
		}

		return false;
	}

	/**
	 * Check if plugins are up-to-date
	 *
	 * @return array
	 */
	public function checkPluginsUpdates() {
		//Get the current update info
		$current = get_site_transient( 'update_plugins' );

		if ( ! is_object( $current ) ) {

			$current = new stdClass;

			set_site_transient( 'update_plugins', $current );

			// run the internal plugin update check
			wp_update_plugins();

			$current = get_site_transient( 'update_plugins' );
		}

		if ( isset( $current->response ) && is_array( $current->response ) ) {
			$plugin_update_cnt = count( $current->response );
		} else {
			$plugin_update_cnt = 0;
		}

		$plugins = array();
		foreach ( $current->response as $tmp ) {
			if ( isset( $tmp->slug ) ) {
				$plugins[] = $tmp->slug;
			}
		}

		return array(
			'value' => ( $plugin_update_cnt > 0 ? sprintf( esc_html__( '%s plugin(s) are outdated: %s', 'hide-my-wp' ), $plugin_update_cnt, '<br />' . '<span style="font-weight: normal; color: #dc3545!important">' . join( "<br />", $plugins ) . '</span>' ) : esc_html__( 'All plugins are up to date', 'hide-my-wp' ) ),
			'valid' => ( ! $plugin_update_cnt ),
		);

	}

	/**
	 * Check if themes are up-to-date
	 *
	 * @return array
	 */
	public function checkThemesUpdates() {
		$current          = get_site_transient( 'update_themes' );
		$themes           = array();
		$theme_update_cnt = 0;

		if ( ! is_object( $current ) ) {
			$current = new stdClass;
		}

		set_site_transient( 'update_themes', $current );
		wp_update_themes();

		$current = get_site_transient( 'update_themes' );

		if ( isset( $current->response ) && is_array( $current->response ) ) {
			$theme_update_cnt = count( $current->response );
		}

		foreach ( $current->response as $theme_name => $tmp ) {
			$themes[] = $theme_name;
		}

		return array(
			'value' => ( $theme_update_cnt > 0 ? sprintf( esc_html__( '%s theme(s) are outdated: %s', 'hide-my-wp' ), $theme_update_cnt, '<br />' . '<span style="font-weight: normal; color: #dc3545!important">' . join( "<br />", $themes ) . '</span>' ) : esc_html__( 'Themes are up to date', 'hide-my-wp' ) ),
			'valid' => ( ! $theme_update_cnt ),
		);

	}

	/**
	 * Check the old plugins from WordPress directory
	 *
	 * @return array
	 */
	public function checkOldPlugins() {
		global $hmwp_plugin_details;

		$hmwp_plugin_details = array();
		$bad                 = array();
		$active_plugins      = get_option( 'active_plugins', array() );

		foreach ( $active_plugins as $plugin_path ) {
			$plugin = explode( '/', $plugin_path );
			$plugin = @$plugin[0];
			if ( empty( $plugin ) || empty( $plugin_path ) ) {
				continue;
			}

			$response = HMWP_Classes_Tools::hmwp_localcall( 'https://api.wordpress.org/plugins/info/1.1/?action=plugin_information&request%5Bslug%5D=' . $plugin, array( 'timeout' => 5 ) );

			if ( ! is_wp_error( $response ) && wp_remote_retrieve_response_code( $response ) == 200 && wp_remote_retrieve_body( $response ) ) {
				$details = wp_remote_retrieve_body( $response );
				$details = json_decode( $details, true );
				if ( empty( $details ) ) {
					continue;
				}
				$hmwp_plugin_details[ $plugin_path ] = $details;
				$updated                             = strtotime( $details['last_updated'] );
				if ( $updated + 365 * DAY_IN_SECONDS < time() ) {
					$bad[ $plugin_path ] = true;
				}
			}
		} // foreach active plugin

		if ( ! empty( $bad ) ) {
			$plugins = get_plugins();
			foreach ( $bad as $plugin_path => $tmp ) {
				if ( $plugins[ $plugin_path ]['Name'] <> '' ) {
					$bad[ $plugin_path ] = $plugins[ $plugin_path ]['Name'];
				}
			}
		}

		return array(
			'value' => ( count( $bad ) > 0 ? sprintf( esc_html__( '%s plugin(s) have NOT been updated by their developers in the past 12 months: %s', 'hide-my-wp' ), count( $bad ), '<br />' . '<span style="font-weight: normal; color: #dc3545!important">' . join( "<br />", $bad ) . '</span>' ) : esc_html__( 'All plugins have been updated by their developers in the past 12 months', 'hide-my-wp' ) ),
			'valid' => empty( $bad ),
		);

	}

	/**
	 * Check incompatible plugins
	 *
	 * @return array
	 */
	public function checkIncompatiblePlugins() {
		global $hmwp_plugin_details, $wp_version;

		$bad = array();

		if ( empty( $hmwp_plugin_details ) ) {
			$this->checkOldPlugins();
		}

		foreach ( $hmwp_plugin_details as $plugin_path => $plugin ) {
			if ( version_compare( $wp_version, $plugin['tested'], '>' ) ) {
				$bad[ $plugin_path ] = $plugin;
			}
		} // foreach active plugins we have details on

		if ( ! empty( $bad ) ) {
			$plugins = get_plugins();
			foreach ( $bad as $plugin_path => $tmp ) {
				$bad[ $plugin_path ] = $plugins[ $plugin_path ]['Name'];
			}
		}

		return array(
			'value' => ( empty( $bad ) ? esc_html__( 'All plugins are compatible', 'hide-my-wp' ) : implode( '<br />', $bad ) ),
			'valid' => empty( $bad ),
		);

	}

	/**
	 * Check if version is displayed in source code
	 *
	 * @return array
	 */
	public function checkVersionDisplayed() {
		return array(
			'value' => ( HMWP_Classes_Tools::getOption( 'hmwp_hide_version' ) ? 'Removed' : 'Visible' ),
			'valid' => ( HMWP_Classes_Tools::getOption( 'hmwp_hide_version' ) ),
		);
	}

	/**
	 * Check if PHP is exposed
	 *
	 * @return array
	 */
	public function checkExposedPHP() {

		if ( ! isset( $this->html ) || $this->html == '' ) {
			$this->getSourceCode();
		}

		$check = false;
		if ( isset( $this->headers ) && ! empty( $this->headers ) ) {
			if ( isset( $this->headers['X-Powered-By'] ) && is_string( $this->headers['X-Powered-By'] ) && stripos( $this->headers['X-Powered-By'], 'PHP' ) !== false ) {
				$check = true;
			}
			if ( isset( $this->headers['server'] ) && is_string( $this->headers['server'] ) && stripos( $this->headers['server'], 'PHP' ) !== false ) {
				$check = true;
			}
		} else {
			$check = (bool) ini_get( 'expose_php' );
		}

		return array(
			'value' => ( $check ? esc_html__( 'Yes' ) : esc_html__( 'No' ) ),
			'valid' => ( ! $check ),
		);

	}

	/**
	 * Check Database Prefix
	 *
	 * @return array
	 */
	public function checkDBPrefix() {
		global $wpdb;

		if ( ( $wpdb->prefix === 'wp_' ) || ( $wpdb->prefix === 'wordpress_' ) || ( $wpdb->prefix === 'wp3_' ) ) {

			return array(
				'value' => $wpdb->prefix,
				'valid' => false,
			);

		} else {
			return array(
				'value'             => $wpdb->prefix,
				'valid'             => true,
				'javascript_custom' => "jQuery(this).hmwp_fixPrefix(false);",
				'javascript_button' => esc_html__( 'Reset', 'hide-my-wp' ),
			);
		}

	}

	/**
	 * Check Salt Keys
	 *
	 * @return array
	 */
	public function checkSaltKeys() {
		$bad_keys = array();

		$keys = array(
			'AUTH_KEY',
			'SECURE_AUTH_KEY',
			'LOGGED_IN_KEY',
			'NONCE_KEY',
			'AUTH_SALT',
			'SECURE_AUTH_SALT',
			'LOGGED_IN_SALT',
			'NONCE_SALT'
		);

		try {
			$constants = get_defined_constants();

			foreach ( $keys as $key ) {
				if ( ! in_array( $key, array_keys( $constants ) ) ) {
					$bad_keys[] = $key;
				} else {
					$constant = $constants[ $key ];
					if ( empty( $constant ) || trim( $constant ) == 'put your unique phrase here' || strlen( $constant ) < 50 ) {
						$bad_keys[] = $key;
					}
				}
			} // foreach

		} catch ( Exception $e ) {
		}

		return array(
			'value' => ( ! empty( $bad_keys ) ? implode( ', ', $bad_keys ) : esc_html__( 'Yes' ) ),
			'valid' => empty( $bad_keys ),
		);

	}

	/**
	 * Check if wp-config.php has the right chmod
	 *
	 * @return array|false
	 */
	public function checkSaltKeysAge() {
		$old  = 95;
		$diff = false;

		if ( $saltcheck_time = get_option( HMWP_SALT_CHANGED ) ) {
			if ( ( isset( $saltcheck_time['timestamp'] ) ) ) {
				$diff = ( time() - $saltcheck_time['timestamp'] );
			}
		} elseif ( $config_file = HMWP_Classes_Tools::getConfigFile() ) {
			$age = @filemtime( $config_file );

			if ( ! empty( $age ) ) {
				$diff = time() - $age;
			}
		}


		if ( $diff ) {
			return array(
				'value'             => ( ( $diff > ( DAY_IN_SECONDS * $old ) ) ? sprintf( esc_html__( '%s days since last update', 'hide-my-wp' ), $diff ) : esc_html__( 'Updated', 'hide-my-wp' ) ),
				'valid'             => ( $diff <= ( DAY_IN_SECONDS * $old ) ),
				'javascript_custom' => "jQuery(this).hmwp_fixSalts(true);",
				'javascript_button' => esc_html__( 'Renew', 'hide-my-wp' ),
			);
		}

		return false;
	}

	/**
	 * Check Database Password
	 *
	 * @return array
	 */
	public function checkDbPassword() {
		$password = DB_PASSWORD;

		if ( empty( $password ) ) {
			return array(
				'value' => esc_html__( 'Empty', 'hide-my-wp' ),
				'valid' => false,
			);
		} elseif ( strlen( $password ) < 6 ) {
			return array(
				'value' => sprintf( esc_html__( 'only %d chars', 'hide-my-wp' ), strlen( $password ) ),
				'valid' => false,
			);
		} elseif ( sizeof( count_chars( $password, 1 ) ) < 5 ) {
			return array(
				'value' => esc_html__( 'too simple', 'hide-my-wp' ),
				'valid' => false,
			);
		} else {
			return array(
				'value' => esc_html__( 'Good', 'hide-my-wp' ),
				'valid' => true,
			);
		}
	}

	/**
	 * Check if display_errors is off
	 *
	 * @return array
	 */
	public function checkDisplayErrors() {
		$check = ini_get( 'display_errors' );

		return array(
			'value' => $check,
			'valid' => ! (bool) $check,
		);
	}

	/**
	 * Compare WP Blog Url with WP Site Url
	 *
	 * @return array
	 */
	public function checkBlogSiteURL() {
		$siteurl = home_url();
		$wpurl   = site_url();

		return array(
			'value' => ( ( $siteurl == $wpurl ) ? esc_html__( 'Yes' ) : esc_html__( 'No' ) ),
			'valid' => ( $siteurl <> $wpurl ),
		);

	}

	/**
	 * Check if wp-config.php has the right chmod
	 *
	 * @return array|bool
	 */
	public function checkConfigChmod() {

		//Initialize WordPress Filesystem
		$wp_filesystem = HMWP_Classes_ObjController::initFilesystem();

		if ( $config_file = HMWP_Classes_Tools::getConfigFile() ) {
			if ( HMWP_Classes_Tools::isWindows() ) {

				return array(
					'value'    => ( ( $wp_filesystem->is_writable( $config_file ) ) ? esc_html__( 'Yes' ) : esc_html__( 'No' ) ),
					'valid'    => ( ! $wp_filesystem->is_writable( $config_file ) ),
					'solution' => sprintf( esc_html__( "Change the wp-config.php file permission to Read-Only using File Manager.", 'hide-my-wp' ), '<a href="https://wordpress.org/support/article/changing-file-permissions/" target="_blank">', '</a>', '<a href="https://wordpress.org/support/article/changing-file-permissions/" target="_blank">', '</a>' ),
				);
			} else {
				$chmod   = $wp_filesystem->getchmod( $config_file );
				$octmode = substr( sprintf( '%o', $chmod ), - 4 );

				return array(
					'value' => ( ( substr( $octmode, - 1 ) != 0 ) ? esc_html__( 'Yes' ) : esc_html__( 'No' ) ),
					'valid' => ( substr( $octmode, - 1 ) == 0 ),
				);
			}
		}

		return array(
			'value' => esc_html__( 'No' ),
			'valid' => true,
		);
	}

	/**
	 * Check wp-config.php file
	 *
	 * @return array
	 */
	public function checkConfig() {
		$url      = home_url( 'wp-config.php?hmwp_preview=1&rnd=' . wp_rand() );
		$response = wp_remote_head( $url, array( 'timeout' => 5, 'cookies' => false ) );

		$visible = false;
		if ( ! is_wp_error( $response ) ) {
			if ( wp_remote_retrieve_response_code( $response ) == 200 ) {
				$visible = true;
			}
		}

		$url      = home_url( 'wp-config-sample.php?hmwp_preview=1&rnd=' . wp_rand() );
		$response = wp_remote_head( $url, array( 'timeout' => 5, 'cookies' => false ) );

		if ( ! is_wp_error( $response ) ) {
			if ( wp_remote_retrieve_response_code( $response ) == 200 ) {
				$visible = true;
			}
		}

		//if the settings are already activated
		if ( HMWP_Classes_Tools::getOption( 'hmwp_hide_commonfiles' ) ) {
			return array( 'value' => esc_html__( 'No' ), 'valid' => true );
		}

		return array(
			'value' => ( $visible ? esc_html__( 'Yes' ) : esc_html__( 'No' ) ),
			'valid' => ( ! $visible ),
		);
	}

	/**
	 * Check readme.html file
	 *
	 * @return array
	 */
	public function checkReadme() {
		$url      = home_url( 'readme.html?hmwp_preview=1&rnd=' . wp_rand() );
		$response = wp_remote_head( $url, array( 'timeout' => 5, 'cookies' => false ) );

		$visible = false;
		if ( ! is_wp_error( $response ) ) {

			if ( wp_remote_retrieve_response_code( $response ) == 200 ) {
				$visible = true;
			}
		}

		//In case it's litespeed, the file is hidden
		if ( HMWP_Classes_Tools::isLitespeed() ) {
			return array( 'value' => esc_html__( 'No' ), 'valid' => true );

		}

		//if the settings are already activated
		if ( HMWP_Classes_Tools::getOption( 'hmwp_hide_commonfiles' ) ) {
			$files = HMWP_Classes_Tools::getOption( 'hmwp_hide_commonfiles_files' );
			if ( ! empty( $files ) && in_array( 'readme.html', $files ) ) {
				return array( 'value' => esc_html__( 'No' ), 'valid' => true );
			}
		}

		return array(
			'value' => ( $visible ? esc_html__( 'Yes' ) : esc_html__( 'No' ) ),
			'valid' => ( ! $visible ),
		);
	}


	/**
	 * Does WP install.php file exist?
	 *
	 * @return array
	 */
	public function checkInstall() {
		$url      = site_url() . '/wp-admin/install.php?hmwp_preview=1&rnd=' . wp_rand();
		$response = wp_remote_head( $url, array( 'timeout' => 10, 'cookies' => false ) );
		$visible  = false;

		if ( ! is_wp_error( $response ) ) {
			if ( wp_remote_retrieve_response_code( $response ) == 200 ) {
				$visible = true;
			}
		}

		//if the settings are already activated
		if ( HMWP_Classes_Tools::getOption( 'hmwp_hide_commonfiles' ) ) {
			$files = HMWP_Classes_Tools::getOption( 'hmwp_hide_commonfiles_files' );
			if ( ! empty( $files ) && in_array( 'install.php', $files ) ) {
				return array( 'value' => esc_html__( 'No' ), 'valid' => true );
			}
		}

		return array(
			'value' => ( $visible ? esc_html__( 'Yes' ) : esc_html__( 'No' ) ),
			'valid' => ( ! $visible ),
		);
	}

	/**
	 * Check if firewall is activated
	 *
	 * @return array
	 */
	public function checkFirewall() {
		return array(
			'value' => ( HMWP_Classes_Tools::getOption( 'hmwp_sqlinjection' ) ? esc_html__( 'Yes' ) : esc_html__( 'No' ) ),
			'valid' => ( HMWP_Classes_Tools::getOption( 'hmwp_sqlinjection' ) ),
		);
	}

	/**
	 * Check if register_globals is off
	 *
	 * @return array
	 */
	public function checkRegisterGlobals() {
		$check = (bool) ini_get( 'register' . '_globals' );

		return array(
			'value' => ( $check ? esc_html__( 'Yes' ) : esc_html__( 'No' ) ),
			'valid' => ( ! $check ),
		);

	}

	/**
	 * Check if safe mode is off
	 *
	 * @return array
	 */
	public function checkPHPSafe() {
		$check = (bool) ini_get( 'safe' . '_mode' );

		return array(
			'value' => ( $check ? esc_html__( 'Yes' ) : esc_html__( 'No' ) ),
			'valid' => ( ! $check ),
		);

	}

	/**
	 * Check if allow_url_include is off
	 *
	 * @return array
	 */
	public function checkAllowUrlInclude() {
		$check = (bool) ini_get( 'allow_url_include' );

		return array(
			'value' => ( $check ? esc_html__( 'Yes' ) : esc_html__( 'No' ) ),
			'valid' => ( ! $check ),
		);
	}

	/**
	 * Is theme/plugin editor disabled?
	 *
	 * @return array
	 */
	public function checkAdminEditor() {
		if ( defined( 'DISALLOW_FILE_EDIT' ) ) {
			return array(
				'value'           => ( DISALLOW_FILE_EDIT ? esc_html__( 'Yes' ) : esc_html__( 'No' ) ),
				'valid'           => DISALLOW_FILE_EDIT,
				'javascript_undo' => "jQuery(this).hmwp_fixConfig('DISALLOW_FILE_EDIT',false);",

			);
		} else {
			return array(
				'value' => esc_html__( 'Yes' ),
				'valid' => false,
			);
		}
	}


	/**
	 * Check if Upload Folder is browsable
	 *
	 * @return array|false
	 */
	public function checkUploadsBrowsable() {

		//if the settings are already activated
		if ( HMWP_Classes_Tools::getOption( 'hmwp_disable_browsing' ) ) {
			return array( 'value' => esc_html__( 'No' ), 'valid' => true );
		}

		$upload_dir = wp_upload_dir();

		if ( ! isset( $upload_dir['baseurl'] ) || $upload_dir['baseurl'] == '' ) {
			return false;
		}

		$args = array(
			'method'      => 'GET',
			'timeout'     => 5,
			'sslverify'   => false,
			'httpversion' => 1.0,
			'blocking'    => true,
			'headers'     => array(),
			'body'        => null,
			'cookies'     => array()
		);

		$response = HMWP_Classes_Tools::hmwp_localcall( rtrim( $upload_dir['baseurl'], '/' ) . '/?nocache=' . wp_rand(), $args );

		if ( is_wp_error( $response ) ) {
			$return = array(
				'value' => esc_html__( 'No' ),
				'valid' => true,
			);
		} elseif ( wp_remote_retrieve_response_code( $response ) == 200 && stripos( $response['body'], 'index' ) !== false ) {
			$return = array(
				'value' => esc_html__( 'Yes' ),
				'valid' => false,
			);
		} else {
			$return = array(
				'value' => esc_html__( 'No' ),
				'valid' => true,
			);
		}

		if ( ! HMWP_Classes_Tools::isApache() && ! HMWP_Classes_Tools::isNginx() && ! HMWP_Classes_Tools::isLitespeed() ) {
			$return['javascript'] = '';
		}

		return $return;
	}

	/**
	 * Check if Wondows Live Writer is not disabled
	 *
	 * @return array
	 */
	public function checkWLW() {
		$check = ( ! HMWP_Classes_Tools::getOption( 'hmwp_disable_manifest' ) );

		return array(
			'value' => ( $check ? esc_html__( 'Yes' ) : esc_html__( 'No' ) ),
			'valid' => ( ! $check ),
		);

	}

	/**
	 * Check if XML PRC
	 *
	 * @return array
	 */
	public function checkXmlrpc() {

		$visible = false;

		if ( ! HMWP_Classes_Tools::getOption( 'hmwp_disable_xmlrpc' ) ) {
			$url      = site_url() . '/xmlrpc.php?rnd=' . wp_rand();
			$response = wp_remote_head( $url, array( 'timeout' => 5, 'cookies' => false ) );

			if ( ! is_wp_error( $response ) ) {

				if ( wp_remote_retrieve_response_code( $response ) == 200 || wp_remote_retrieve_response_code( $response ) == 405 ) {
					$visible = true;
				}

			}
		}

		return array(
			'value' => ( $visible ? esc_html__( 'Yes' ) : esc_html__( 'No' ) ),
			'valid' => ( ! $visible ),
		);

	}

	/**
	 * Check if XML PRC
	 *
	 * @return array
	 */
	public function checkRDS() {
		$check = ( ! HMWP_Classes_Tools::getOption( 'hmwp_hide_rsd' ) );

		return array(
			'value' => ( $check ? esc_html__( 'Yes' ) : esc_html__( 'No' ) ),
			'valid' => ( ! $check ),
		);

	}

	/**
	 * Check if the WP MySQL user has too many permissions granted
	 *
	 * @return array
	 */
	static function checkMysqlPermissions() {
		global $wpdb;

		$grants = $wpdb->get_results( 'SHOW GRANTS', ARRAY_N );
		foreach ( $grants as $grant ) {
			if ( stripos( $grant[0], 'GRANT ALL PRIVILEGES' ) !== false ) {
				return array(
					'value' => esc_html__( 'Yes' ),
					'valid' => false,
				);
			}
		}

		return array(
			'value' => esc_html__( 'No' ),
			'valid' => true,
		);
	}

	/**
	 * Check if a user can be found by its ID
	 *
	 * @return array
	 */
	static function checkUsersById() {
		$users   = get_users( 'number=1' );
		$success = false;
		$url     = home_url() . '/?hmwp_preview=1&author=';

		foreach ( $users as $user ) {
			$response      = wp_remote_head( $url . $user->ID, array(
				'redirection' => 0,
				'timeout'     => 5,
				'cookies'     => false
			) );
			$response_code = wp_remote_retrieve_response_code( $response );

			if ( $response_code == 301 ) {
				$success = true;
			}
			break;
		} // foreach

		//If the option is on, the author is hidden
		if ( HMWP_Classes_Tools::getOption( 'hmwp_hide_authors' ) ) {
			$success = false;
		}

		return array(
			'value' => ( $success ? esc_html__( 'Yes' ) : esc_html__( 'No' ) ),
			'valid' => ( ! $success ),
		);
	}

	/**
	 * Check if XML PRC
	 *
	 * @return array
	 */
	public function checkOldPaths() {
		$visible  = false;
		$url      = site_url() . '/wp-content/?rnd=' . wp_rand();
		$response = wp_remote_head( $url, array( 'timeout' => 5, 'cookies' => false ) );

		if ( ! is_wp_error( $response ) ) {

			if ( wp_remote_retrieve_response_code( $response ) == 200 ) {
				$visible = true;
			}

		}

		if ( HMWP_Classes_Tools::getDefault( 'hmwp_wp-content_url' ) <> HMWP_Classes_Tools::getOption( 'hmwp_wp-content_url' ) && HMWP_Classes_Tools::getOption( 'hmwp_hide_oldpaths' ) ) {
			$visible = false;
		}

		return array(
			'value' => ( $visible ? esc_html__( 'Yes' ) : esc_html__( 'No' ) ),
			'valid' => ( ! $visible ),
		);

	}

	/**
	 * Check the Old paths in source code
	 *
	 * @return array|bool
	 */
	public function checkCommonPaths() {
		$visible = false;

		if ( ! isset( $this->html ) || $this->html == '' ) {
			if ( ! $this->getSourceCode() ) {
				return false;
			}
		}

		//if the wp-content path is changed in HMWP
		if ( HMWP_Classes_Tools::getDefault( 'hmwp_wp-content_url' ) <> HMWP_Classes_Tools::getOption( 'hmwp_wp-content_url' ) ) {
			//if the new path is visible in the source code, the paths are changed
			if ( strpos( $this->html, site_url( '/' . HMWP_Classes_Tools::getOption( 'hmwp_wp-content_url' ) . '/' ) ) ) {
				//the old paths are changed
				$visible = false;
			} else {
				//check if wp-content is visible in the source code
				$visible = strpos( $this->html, content_url() );
			}
		} else {
			//check if wp-content is visible in the source code
			$visible = strpos( $this->html, content_url() );
		}

		return array(
			'value' => ( $visible ? esc_html__( 'Yes' ) : esc_html__( 'No' ) ),
			'valid' => ( ! $visible ),
		);

	}

	/**
	 * Check the Login path in source code
	 *
	 * @return array|bool
	 */
	public function checkLoginPath() {
		if ( ! isset( $this->html ) || $this->html == '' ) {
			if ( ! $this->getSourceCode() ) {
				return false;
			}
		}

		if ( ! $found = strpos( $this->html, site_url( 'wp-login.php' ) ) ) {
			if ( ! HMWP_Classes_Tools::getOption( 'hmwp_bruteforce' ) ) {
				//If the custom login path is visible in the source code and Brute force is not activated
				$found = strpos( $this->html, site_url( '/' . HMWP_Classes_Tools::getOption( 'hmwp_login_url' ) . '/' ) );
			}
		}

		return array(
			'value' => ( $found ? esc_html__( 'Yes' ) : esc_html__( 'No' ) ),
			'valid' => ( ! $found ),
		);

	}

	/**
	 * Check the Admin path in source code
	 *
	 * @return array|bool
	 */
	public function checkAdminPath() {
		if ( ! isset( $this->html ) || $this->html == '' ) {
			if ( ! $this->getSourceCode() ) {
				return false;
			}
		}

		$found = strpos( $this->html, site_url( '/' . HMWP_Classes_Tools::getOption( 'hmwp_admin_url' ) . '/' ) );

		if ( HMWP_Classes_Tools::getDefault( 'hmwp_admin-ajax_url' ) == HMWP_Classes_Tools::getOption( 'hmwp_admin-ajax_url' ) ) {
			return array(
				'value'      => ( $found ? esc_html__( 'Yes' ) : esc_html__( 'No' ) ),
				'valid'      => ( ! $found ),
				'javascript' => "jQuery(this).hmwp_fixSettings('hmwp_hideajax_admin',1);jQuery(this).hmwp_fixSettings('hmwp_admin-ajax_url','ajax');",
			);
		}

		return array(
			'value'      => ( $found ? esc_html__( 'Yes' ) : esc_html__( 'No' ) ),
			'valid'      => ( ! $found ),
			'javascript' => "jQuery(this).hmwp_fixSettings('hmwp_hideajax_admin',1);",
		);

	}

	/**
	 * Check if wp-admin is accessible for visitors
	 *
	 * @return array
	 */
	public function checkOldLogin() {
		$url      = home_url() . '/wp-login.php?hmwp_preview=1&rnd=' . wp_rand();
		$response = HMWP_Classes_Tools::hmwp_localcall( $url, array( 'redirection' => 0, 'cookies' => false ) );

		$visible = false;
		if ( ! is_wp_error( $response ) ) {

			if ( wp_remote_retrieve_response_code( $response ) == 200 ) {
				$visible = true;
			}
		}

		if ( HMWP_Classes_Tools::getDefault( 'hmwp_login_url' ) <> HMWP_Classes_Tools::getOption( 'hmwp_login_url' ) && HMWP_Classes_Tools::getOption( 'hmwp_hide_login' ) ) {
			$visible = false;
		}

		return array(
			'value' => ( $visible ? esc_html__( 'Yes' ) : esc_html__( 'No' ) ),
			'valid' => ( ! $visible ),
		);
	}

	/**
	 * Check if anyone can register easily
	 *
	 * @return array
	 */
	public function checkUserRegistration() {
		$check = ( get_option( 'users_can_register' ) );
		if ( $check ) {
			$check = ( HMWP_Classes_Tools::getOption( 'hmwp_register_url' ) == '' || !HMWP_Classes_Tools::getOption('hmwp_bruteforce') || !HMWP_Classes_Tools::getOption('hmwp_bruteforce_register') );
		}

		return array(
			'value' => ( $check ? esc_html__( 'Yes' ) : esc_html__( 'No' ) ),
			'valid' => ( ! $check ),
		);
	}

	/**
	 * Check if the default website description is shown
	 *
	 * @return array
	 */
	public function checkBlogDescription() {
		$check = ( get_option( 'blogdescription' ) == esc_html__( 'Just another WordPress site' ) );

		return array(
			'value' => ( $check ? esc_html__( 'Yes' ) : esc_html__( 'No' ) ),
			'valid' => ( ! $check ),
		);
	}

	/**
	 * Check if file and directory permissions are correctly set
	 *
	 * @return array
	 * @throws Exception
	 */
	public function checkFilePermissions() {

		/** @var HMWP_Models_Permissions $permissionModel */
		$permissionModel = HMWP_Classes_ObjController::getClass( 'HMWP_Models_Permissions' );

		$invalid = $permissionModel->getInvalidPermissions();
		$values  = array();
		foreach ( $invalid as $row ) {
			$values[] = $row['display_path'] . ' (' . $row['display_permission'] . ')';
		}

		return array(
			'value' => ( ! empty( $values ) ? sprintf( esc_html__( "%s don't have the correct permission.", 'hide-my-wp' ), '<span style="font-weight: normal; color: #dc3545!important">' . join( '<br />', $values ) . '</span>' . '<br />' ) : esc_html__( 'All files have the correct permissions.', 'hide-my-wp' ) ),
			'valid' => ( empty( $values ) ),
		);
	}

	/**
	 * Get the homepage source code
	 *
	 * @return string
	 */
	public function getSourceCode() {
		if ( ! isset( $this->html ) && ! isset( $this->htmlerror ) ) {
			$url      = home_url() . '?hmwp_preview=1';
			$response = HMWP_Classes_Tools::hmwp_localcall( $url, array(
				'redirection' => 0,
				'timeout'     => 10,
				'cookies'     => false
			) );

			if ( ! is_wp_error( $response ) ) {

				if ( wp_remote_retrieve_response_code( $response ) == 200 ) {
					$this->html    = wp_remote_retrieve_body( $response );
					$this->headers = wp_remote_retrieve_headers( $response );
				} else {
					$this->htmlerror = true;
					$this->html      = false;
					$this->headers   = false;
				}
			} else {
				$this->htmlerror = true;
				$this->html      = false;
				$this->headers   = false;
			}
		}

		return $this->html;
	}
}
