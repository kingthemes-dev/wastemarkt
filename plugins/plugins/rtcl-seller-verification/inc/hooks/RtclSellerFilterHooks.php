<?php

use Rtcl\Helpers\Functions;

class RtclSellerFilterHooks {

	public function __construct() {
		add_filter( 'rtcl_advanced_settings_options', [ $this, 'add_documents_settings' ] );
		add_filter( 'rtcl_account_menu_items', [ $this, 'add_my_documents_menu_item_at_account_menu' ] );
		add_filter( 'rtcl_account_menu_items', [ $this, 'store_menu_restriction' ], 99 );
		add_filter( 'rtcl_my_account_endpoint', [ $this, 'store_endpoint_restriction' ], 99 );
		add_filter( 'rtcl_my_account_endpoint', [ $this, 'add_my_account_documents_end_points' ] );
		add_filter( 'rtcl_author_meta_classess', [ $this, 'add_author_meta_class' ], 10, 2 );
		add_filter( 'rtcl_email_services', [ $this, 'add_seller_document_email_services' ], 10 );
		add_filter( 'rtcl_style_settings_options', [ $this, 'add_style_settings' ] );
		add_filter( 'rtcl_membership_settings_options', [ $this, 'add_membership_settings' ] );
		add_filter( 'rtcl_get_admin_email_notification_options', [ $this, 'add_email_settings' ] );
		add_filter( 'rtcl_licenses', [ $this, 'license' ], 15 );
	}

	public function add_documents_settings( $options ) {
		$position = array_search( 'myaccount_edit_account_endpoint', array_keys( $options ) );

		if ( $position > - 1 ) {
			$newOptions = [
				'myaccount_documents_endpoint' => [
					'title'   => esc_html__( 'My Documents', 'rtcl-seller-verification' ),
					'type'    => 'text',
					'default' => 'my-documents'
				]
			];
			Functions::array_insert( $options, $position, $newOptions );
		}

		return $options;
	}

	public function store_menu_restriction( $items ) {
		$verified = $this->check_restriction();

		if ( ! $verified ) {
			unset( $items['store'] );
		}

		return $items;
	}

	public function store_endpoint_restriction( $endpoints ) {
		$verified = $this->check_restriction();

		if ( ! $verified ) {
			unset( $endpoints['store'] );
			flush_rewrite_rules();
		}

		return $endpoints;
	}

	public function check_restriction() {
		$store_verified_option = Functions::get_option_item( 'rtcl_membership_settings', 'enable_store_only_verified_user', false, 'checkbox' );

		$verified = true;
		if ( $store_verified_option && is_user_logged_in() ) {
			$user_id = get_current_user_id();

			$verified = rtcl_sv_check_verified_user( $user_id );
		}

		return $verified;
	}

	public function add_my_documents_menu_item_at_account_menu( $items ) {
		$position = array_search( 'edit-account', array_keys( $items ) );

		if ( $position > - 1 ) {
			Functions::array_insert( $items, $position, [
				'my-documents' => apply_filters( 'rtcl_seller_myaccount_documents_title', esc_html__( 'My Documents', 'rtcl-seller-verification' ) )
			] );
		}

		return $items;
	}

	public function add_my_account_documents_end_points( $endpoints ) {
		$endpoints['my-documents'] = Functions::get_option_item( 'rtcl_advanced_settings', 'myaccount_documents_endpoint', 'my-documents' );

		return $endpoints;
	}

	public function add_seller_document_email_services( $services ) {
		$services['Seller_Document_Email'] = new RtclSellerDocumentEmail();

		return $services;
	}

	public function add_style_settings( $options ) {
		$options['sv_label_color'] = [
			'title' => esc_html__( 'Seller Verification Color', 'rtcl-seller-verification' ),
			'type'  => 'color',
		];

		return $options;
	}

	public function add_membership_settings( $options ) {
		$position = array_search( 'enable_store_only_membership', array_keys( $options ) );

		if ( $position > - 1 ) {
			$newOptions = [
				'enable_store_only_verified_user' => [
					'title'      => esc_html__( 'Store only for verified users', 'rtcl-seller-verification' ),
					'label'      => esc_html__( 'Enable', 'rtcl-seller-verification' ),
					'type'       => 'checkbox',
					'dependency' => [
						'rules' => [
							'#rtcl_membership_settings-enable_store' => [
								'type'  => 'equal',
								'value' => 'yes'
							]
						]
					]
				]
			];

			Functions::array_insert( $options, $position, $newOptions );
		}

		return $options;
	}

	public function add_email_settings( $options ) {
		$options['seller_photo_id_uploaded']      = esc_html__( 'Seller Verification - Photo ID Uploaded', 'rtcl-seller-verification' );
		$options['seller_business_file_uploaded'] = esc_html__( 'Seller Verification - Business Document Uploaded', 'rtcl-seller-verification' );

		return $options;
	}

	public function license( $licenses ) {
		$licenses[] = [
			'plugin_file' => RTCL_SELLER_FILE,
			'api_data'    => [
				'key_name'    => 'license_seller_verification_key',
				'status_name' => 'license_seller_verification_status',
				'action_name' => 'rtcl_seller_verification_manage_licensing',
				'product_id'  => 188545,
				'version'     => RTCL_SELLER_VERSION,
			],
			'settings'    => [
				'title' => esc_html__( 'Seller Verification license key', 'rtcl-seller-verification' ),
			],
		];

		return $licenses;
	}

}