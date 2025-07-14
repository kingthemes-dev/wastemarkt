<?php

use Rtcl\Helpers\Functions;
use RtclJobManager\Controller\JobForm;
use RtclJobManager\Controller\Scripts;
use RtclJobManager\Controller\JobArchive;
use RtclJobManager\Admin\Settings;
use RtclJobManager\Admin\AdminHooks;
use RtclJobManager\Helpers\Installer;
use RtclJobManager\Hooks\ActionHooks;
use RtclJobManager\Hooks\AjaxHooks;
use RtclJobManager\Hooks\FilterHooks;
use RtclJobManager\Hooks\TemplateHooks;
use RtclJobManager\Models\Dependencies;
use RtclJobManager\Widgets\JobFilter;

require_once RTCL_JOB_MANAGER_PATH . 'vendor/autoload.php';

/**
 * RtclJobManager class
 */
final class App {
	private static string $suffix;
	private $dependency;
	private static $singleton = false;

	/**
	 * Create an inaccessible constructor.
	 */
	private function __construct() {
		self::$suffix = ( defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ) ? '' : '.min';

		$this->dependency = Dependencies::getInstance();

		$this->init();
	}

	public function job_type_id() {
		return apply_filters( 'rtcl_job_manager_type', 'job' );
	}

	/**
	 * Fetch an instance of the class.
	 */
	final public static function getInstance() {
		if ( false === self::$singleton ) {
			self::$singleton = new self();
		}

		return self::$singleton;
	}

	/**
	 * Initialize necessary things
	 */
	protected function init(): void {
		$this->load_language();
		$this->hooks();
		$this->activation_hook();
	}


	public function load_language(): void {
		load_plugin_textdomain( 'rtcl-job-manager', false, trailingslashit( RTCL_JOB_MANAGER_PLUGIN_DIRNAME ) . 'languages' );
	}

	private function hooks(): void {
		if ( $this->dependency->check() ) {

			add_action( 'widgets_init', [ $this, 'custom_widgets' ] );

			if ( rtcl()->is_request( 'admin' ) ) {
				Settings::init();
				AdminHooks::init();
			}

			if ( rtcl()->is_request( 'ajax' ) ) {
				AjaxHooks::init();
			}

			$enable_job_manager = Functions::get_option_item( 'rtcl_job_manager_settings', 'job_manager_enable' );

			if ( 'yes' !== $enable_job_manager ) {
				return;
			}

			Scripts::init();
			ActionHooks::init();
			FilterHooks::init();
			TemplateHooks::init();
			JobForm::init();
			JobArchive::init();
			do_action( 'rtcl_job_manager_loaded', $this );
		}
	}

	public static function activation_hook() {
		register_activation_hook( RTCL_JOB_MANAGER_PLUGIN_FILE, [ Installer::class, 'activate' ] );
		register_deactivation_hook( RTCL_JOB_MANAGER_PLUGIN_FILE, [ Installer::class, 'deactivate' ] );
	}

	/**
	 * Widgets register
	 *
	 * @return void
	 */
	public function custom_widgets() {

		if ( ! is_registered_sidebar( 'rtcl-job-archive-sidebar' ) ) {
			register_sidebar(
				[
					'name'          => apply_filters( 'rtcl_job_archive_sidebar_title', esc_html__( 'Classified Listing - Job Archive Sidebar', 'rtcl-job-manager' ) ),
					'id'            => 'rtcl-job-archive-sidebar',
					'description'   => esc_html__( 'Add widgets on job archive page', 'rtcl-job-manager' ),
					'before_widget' => '<div class="widget rtcl-widget %2$s">',
					'after_widget'  => '</div>',
					'before_title'  => '<div class="rtcl-widget-heading"><h3>',
					'after_title'   => '</h3></div>',
				]
			);
		}

		register_widget( JobFilter::class );
	}

	/**
	 * @return string
	 */
	public function get_plugin_template_path() {
		return $this->plugin_path() . '/templates/';
	}

	/**
	 * Get the plugin path.
	 *
	 * @return string
	 */
	public function plugin_path() {
		return untrailingslashit( plugin_dir_path( RTCL_JOB_MANAGER_PLUGIN_FILE ) );
	}
}

function rtcl_job_manager() {
	return App::getInstance();
}

rtcl_job_manager();
