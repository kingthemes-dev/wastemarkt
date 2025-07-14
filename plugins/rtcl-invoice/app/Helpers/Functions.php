<?php

namespace RtclInvoice\Helpers;

use Dompdf\Dompdf;
use Rtcl\Helpers\Functions as RtclFunctions;

class Functions {

	public static $order_id;

	public static function get_pdf_download_file() {
		return RTCL_INVOICE_URL . '/views/invoice-download.php';
	}

	public static function generate_pdf( $order_id = 0 ) {
		self::$order_id = $order_id;

		$html = self::get_invoice_template();
		$html .= self::get_pdf_style();

		$filename = "invoice-" . self::$order_id . ".pdf";

		if ( class_exists( '\\Dompdf\\Dompdf' ) ) {
			$document = new Dompdf();
			$document->loadHtml( $html );
			$document->render();
			$document->stream( $filename );
		}

	}

	public static function get_invoice_template() {
		$html = '';

		$data = array(
			'order_id' => self::$order_id
		);

		ob_start();
		RtclFunctions::get_template( 'invoice/general', $data, '', rtclInvoice()->get_plugin_template_path() );
		$html .= ob_get_clean();

		return $html;
	}

	public static function get_pdf_style() {
		$rtcl_style_opt = RtclFunctions::get_option( "rtcl_style_settings" );
		$primary        = ! empty( $rtcl_style_opt['primary'] ) ? $rtcl_style_opt['primary'] : apply_filters( 'rtcl_invoice_default_color', '#007bff' );
		$style          = '
		<style rel="stylesheet">
			table {
				width: 100%;
				border-collapse: collapse;
			}
			.invoice-logo-pdf {
				max-width: 150px;
				margin-right: 20px;
			}
			.invoice-heading {
				background-color: ' . $primary . ';
				text-align: right;
				margin-top: 40px;
				line-height: 1;
			}
			.invoice-heading h3 {
				display: inline-block;
				font-size: 40px;
				background-color: #ffffff;
				color: ' . $primary . ';
				padding: 0 25px;
				margin: 0 60px 0 0;
				line-height: 1;
			}
			.invoice-pricing-info-table {
				margin-top: 40px;
			}
			.invoice-pricing-info-table table th,
			.invoice-pricing-info-table table td {
				padding: 10px;
				text-align: left;
			}
			.invoice-pricing-info-table table,
			.invoice-pricing-info-table table th,
			.invoice-pricing-info-table table td {
				border: 1px solid #dee2e6;
			}
			.invoice-total {
				margin-left: 50%;
				margin-top: 20px;
			}
			.invoice-total table {
				border: none;
			}
			.invoice-total th,
			.invoice-total td {
				padding: 5px;
			}
			.invoice-total th {
				text-align: right;
			}
			.invoice-total td {
				margin-left: 10px;
			}
		</style>
		';

		return $style;
	}

	public static function get_default_preview_settings() {
		return array(
			'company_name'    => get_option( 'blogname' ),
			'company_address' => 'San Francisco, California',
			'footer_text'     => 'Thank you for the purchase!',
		);
	}

	public static function get_invoice_settings() {
		$options = get_option( 'rtcl_invoice' );
		if ( empty( $options ) ) {
			$options = self::get_default_preview_settings();
		}

		return $options;
	}

	public static function file_get_contents_curl( $url ) {
		try {
			$ch = curl_init();
			// Check if initialization had gone wrong*
			if ( $ch === false ) {
				throw new Exception( 'failed to initialize' );
			}
			// Better to explicitly set URL
			curl_setopt( $ch, CURLOPT_URL, $url );
			// That needs to be set; content will spill to STDOUT otherwise
			curl_setopt( $ch, CURLOPT_RETURNTRANSFER, true );
			curl_setopt( $ch, CURLOPT_SSL_VERIFYPEER, false ); // ignore SSL verifying
			curl_setopt( $ch, CURLOPT_HEADER, 0 );
			$content = curl_exec( $ch );

			// Check the return value of curl_exec(), too
			if ( $content === false ) {
				throw new Exception( curl_error( $ch ), curl_errno( $ch ) );
			}

			// Check HTTP return code, too; might be something else than 200
			$httpReturnCode = curl_getinfo( $ch, CURLINFO_HTTP_CODE );

			/* Process $content */

			return $content;

		} catch ( Exception $e ) {

			trigger_error( sprintf(
				'Curl failed with error #%d: %s',
				$e->getCode(), $e->getMessage() ),
				E_USER_ERROR );

		} finally {
			// Close curl handle unless it failed to initialize
			if ( is_resource( $ch ) ) {
				curl_close( $ch );
			}
		}
	}

	public static function show_preview() {
		$args = array(
			'post_type'      => rtcl()->post_type_payment,
			'posts_per_page' => 1,
			'post_status'    => 'rtcl-completed',
			'orderby'        => 'id',
			'order'          => 'DESC',
		);

		$payments = new \WP_Query( $args );

		if ( $payments->have_posts() ) {
			while ( $payments->have_posts() ) {
				$payments->the_post();
				self::$order_id = get_the_ID();
			}
		}

		echo self::get_invoice_template();

	}

	public static function get_payment_invoice_status( $status ) {
		switch ( $status ) {
			case 'Processing':
			case 'Completed':
				$status = __( 'Paid', 'rtcl-invoices' );
				break;
			default:
				$status = __( 'Due', 'rtcl-invoices' );
		}

		return $status;
	}

}