<?php

namespace RtclInvoice\Admin;

use Rtcl\Helpers\Functions as RtclFunctions;

class AdminHooks {

	public static function init() {
		add_filter( 'rtcl_licenses', [ __CLASS__, 'license' ], 20 );
		add_action( 'in_admin_header', [ __CLASS__, 'remove_all_notices' ], 9999 );
		add_action( 'admin_footer', [ __CLASS__, 'notice_preview_styles' ] );
	}

	public static function license( $licenses ) {
		$licenses[] = [
			'plugin_file' => RTCL_INVOICE_PLUGIN_FILE,
			'api_data'    => [
				'key_name'    => 'invoice_license_key',
				'status_name' => 'invoice_license_status',
				'action_name' => 'rtcl_manage_invoice_licensing',
				'product_id'  => 206470, // set product id
				'version'     => RTCL_INVOICE_VERSION,
			],
			'settings'    => [
				'title' => esc_html__( 'Invoice addon license key', 'rtcl-invoices' ),
			],
		];

		return $licenses;
	}

	public static function remove_all_notices() {
		$screen = get_current_screen();
		if ( isset( $screen->base ) && 'toplevel_page_rtcl-invoice' == $screen->base ) {
			remove_all_actions( 'admin_notices' );
			remove_all_actions( 'all_admin_notices' );
		}
	}

	public static function notice_preview_styles() {
		$rtcl_style_opt = RtclFunctions::get_option( "rtcl_style_settings" );
		$primary        = ! empty( $rtcl_style_opt['primary'] ) ? $rtcl_style_opt['primary'] : apply_filters( 'rtcl_invoice_default_color', '#007bff' );
		?>
        <style>
            .invoice-heading {
                background-color: <?php echo esc_attr($primary); ?> !important;
            }

            .invoice-heading h3 {
                color: <?php echo esc_attr($primary); ?> !important;
            }
        </style>
		<?php
	}

}