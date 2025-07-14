<?php

namespace RtclInvoice\Admin;

use Rtcl\Models\SettingsAPI;

class AdminSettings extends SettingsAPI {
	protected static $instance = null;

	public function __construct() {
		$this->plugin_id = 'rtcl_invoice';
		add_action( 'admin_init', [ $this, 'save' ] );
		add_action( 'admin_menu', [ $this, 'invoice_menu' ] );
		add_action( 'rtcl_invoice_settings', [ $this, 'setup_settings' ] );
	}

	/**
	 * @param bool $new
	 *
	 * @return AdminSettings|null
	 */
	public static function get_instance( $new = false ) {
		// If the single instance hasn't been set, set it now.
		if ( $new || null === self::$instance ) {
			self::$instance = new self;
		}

		return self::$instance;
	}

	public function invoice_menu() {
		add_menu_page(
			esc_html__( 'Classified Listing - PDF Invoices', 'rtcl-invoices' ),
			esc_html__( 'PDF Invoices', 'rtcl-invoices' ),
			'manage_rtcl_options',
			'rtcl-invoice',
			[ $this, 'invoice_admin_settings' ],
			RTCL_INVOICE_URL . '/assets/img/icon-20x20.png',
			6
		);
	}

	public function invoice_admin_settings() {
		include_once RTCL_INVOICE_PATH . '/views/html-admin-settings.php';
	}

	public function setup_settings() {
		$this->set_fields();

		$this->admin_options();
	}

	public function save() {
		if ( 'POST' !== $_SERVER['REQUEST_METHOD'] || ! isset( $_REQUEST['page'] ) ) {
			return;
		}

		if ( isset( $_REQUEST['page'] ) && 'rtcl-invoice' !== $_REQUEST['page'] ) {
			return;
		}

		if ( empty( $_REQUEST['_wpnonce'] ) || ! wp_verify_nonce( $_REQUEST['_wpnonce'], 'rtcl-invoice' ) ) {
			die( __( 'Action failed. Please refresh the page and retry.', 'rtcl-invoices' ) );
		}

		$this->set_fields();
		$this->process_admin_options();
		self::add_message( __( 'Your settings have been saved.', 'rtcl-invoices' ) );

		do_action( 'rtcl_invoice_settings_saved', $this->option, $this );
	}

	public function set_fields() {
		$fields = array(
			'company_logo'    => array(
				'title' => esc_html__( 'Company Logo', 'rtcl-invoices' ),
				'type'  => 'image',
				'label' => esc_html__( 'Select a company logo to show on invoice.', 'rtcl-invoices' )
			),
			'company_name'    => array(
				'title'       => esc_html__( 'Company Name', 'rtcl-invoices' ),
				'type'        => 'text',
				'default'     => get_option( 'blogname' ),
				'description' => esc_html__( 'Add company title here.', 'rtcl-invoices' )
			),
			'company_address' => array(
				'title'       => esc_html__( 'Company Address', 'rtcl-invoices' ),
				'type'        => 'textarea',
				'default'     => 'San Francisco, California',
				'description' => esc_html__( 'Add company address here.', 'rtcl-invoices' )
			),
			'footer_text'     => array(
				'title'       => esc_html__( 'Footer Note', 'rtcl-invoices' ),
				'type'        => 'textarea',
				'default'     => 'Thank you for the purchase!',
				'description' => esc_html__( 'Add terms & conditions, policies, etc.', 'rtcl-invoices' )
			),
		);

		$this->form_fields = apply_filters( 'rtcl_invoice_setting_fields', $fields );
	}

}