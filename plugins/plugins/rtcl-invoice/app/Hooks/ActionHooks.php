<?php

namespace RtclInvoice\Hooks;

use Rtcl\Helpers\Functions as RtclFunctions;
use Rtcl\Models\Payment;
use RtclInvoice\Helpers\Functions;

class ActionHooks {

	public static function init() {
		add_action( 'rtcl_invoice_header', array( __CLASS__, 'invoice_header_settings' ) );
		add_action( 'rtcl_invoice_content', array( __CLASS__, 'billing_info' ), 10 );
		add_action( 'rtcl_invoice_content', array( __CLASS__, 'pricing_info' ), 20 );
		add_action( 'rtcl_invoice_content', array( __CLASS__, 'invoice_total' ), 20 );
		add_action( 'rtcl_invoice_footer', array( __CLASS__, 'footer_note' ) );
		//add_action( 'wp_footer', array( Functions::class, 'get_pdf_style' ) );
	}

	public static function invoice_header_settings() {
		$settings = Functions::get_invoice_settings();
		?>
        <div style="margin: 0 60px;">
            <table>
                <tr>
                    <td style="border: none;">
						<?php
						if ( ! empty( $settings['company_logo'] ) ) {
							// used curl instead of file_get_contents()
							// $image_abs_url = get_attached_file( $settings['company_logo'] );
							// file_get_contents($image_abs_url);
							$image_url = wp_get_attachment_image_url( $settings['company_logo'], 'full' );
							if ( ! empty( $image_url ) ) {
								?>
                                <img class="invoice-logo-pdf"
                                     src="data:image;base64,<?php echo base64_encode( Functions::file_get_contents_curl( $image_url ) ); ?>"
                                     alt="Logo"/>
								<?php
							}
						}
						?>
                        <div style="display: inline-block; vertical-align: top">
							<?php if ( isset( $settings['company_name'] ) ): ?>
                                <h4 class="company-title"
                                    style="font-size: 24px; margin: 0 0 5px 0; line-height: 1"><?php echo esc_html( $settings['company_name'] ); ?></h4>
							<?php endif; ?>
							<?php if ( isset( $settings['company_address'] ) ): ?>
                                <span class="company-address"><?php echo esc_html( $settings['company_address'] ); ?></span>
							<?php endif; ?>
                        </div>
                    </td>
                </tr>
            </table>
        </div>
        <div class="invoice-heading">
            <h3>
				<?php esc_html_e( 'Invoice', 'rtcl-invoices' ); ?>
            </h3>
        </div>
		<?php
	}

	public static function billing_info( $order_id ) {
		$order = rtcl()->factory->get_order( $order_id );

		if ( is_a( $order, Payment::class ) ) {
			$full_name = $order->get_billing_full_name();
			?>
            <table style="border: none; margin: 0;">
                <tr>
                    <td style="width: 50%; padding: 0; border: none;">
                        <table style="border: none; margin: 0;">
                            <tr>
                                <td style="border: none;padding: 0;vertical-align: initial; width: 40px">
                                    <strong><?php echo( ! empty( $full_name ) ? __( 'To:', 'rtcl-invoices' ) : '' ); ?></strong>
                                </td>
                                <td style="border: none; padding: 0">
                                    <table style="border: none; margin: 0">
                                        <tr>
                                            <td style="border: none; padding: 0"><?php echo $order->get_billing_full_name(); ?></td>
                                        </tr>
                                        <tr>
                                            <td style="border: none; padding: 0"><?php echo $order->get_billing_address_1(); ?></td>
                                        </tr>
                                        <tr>
                                            <td style="border: none; padding: 0"><?php echo $order->get_billing_phone(); ?></td>
                                        </tr>
                                    </table>
                                </td>
                            </tr>
                        </table>
                    </td>
                    <td style="padding: 0; border: none;">
                        <table style="border: none; margin: 0;">
                            <tr>
                                <td style="border: none; padding: 0; text-align: right;"><?php esc_html_e( 'Order Number#', 'classified-listing' ); ?></td>
                                <td style="border: none; padding: 0 0 0 10px">
									<?php echo esc_html( $order->get_maybe_id() ); ?></td>
                            </tr>
                            <tr>
                                <td style="border: none; padding: 0; text-align: right;"><?php esc_html_e( 'Payment Method:', 'classified-listing' ); ?></td>
                                <td style="border: none; padding: 0 0 0 10px;">
									<?php echo $order->get_payment_method_title(); ?>
                                </td>
                            </tr>
                            <tr>
                                <td style="border: none; padding: 0; text-align: right;"><?php esc_html_e( 'Payment Status:', 'classified-listing' ); ?></td>
                                <td style="border: none; padding: 0 0 0 10px;">
									<?php
									$payment_status = RtclFunctions::get_status_i18n( $order->get_status() );
									echo esc_html( Functions::get_payment_invoice_status( $payment_status ) );
									?>
                                </td>
                            </tr>
                            <tr>
                                <td style="border: none; padding: 0; text-align: right;"><?php esc_html_e( 'Date:', 'classified-listing' ); ?></td>
                                <td style="border: none; padding: 0 0 0 10px">
									<?php echo date( 'd/m/Y', strtotime( $order->get_created_date() ) ); ?>
                                </td>
                            </tr>
                        </table>
                    </td>
                </tr>
            </table>
			<?php
		} else {
			?>
            <table style="border: none; margin: 0; min-height: 100px;">
                <tr>
                    <td><?php esc_html_e( 'No order found', 'rtcl-invoices' ); ?></td>
                </tr>
            </table>
			<?php
		}
	}

	public static function pricing_info( $order_id ) {

		$pricing_type = get_post_meta( $order_id, 'payment_type', true );
		if ( empty( $pricing_type ) ) {
			$pricing_type = __( 'Regular', 'rtcl-invoices' );
		}

		$order = rtcl()->factory->get_order( $order_id );

		if ( is_a( $order, Payment::class ) ) {
			$wc_order_id = $order->get_wc_id();

			if ( ! empty( $wc_order_id ) && class_exists( 'WooCommerce' ) ) {
				$order = wc_get_order( $wc_order_id );
				RtclFunctions::get_template( 'invoice/invoice-pricing-wc', compact( 'order', 'pricing_type' ), '', rtclInvoice()->get_plugin_template_path() );

				return;
			}

			RtclFunctions::get_template( 'invoice/invoice-pricing', compact( 'order', 'pricing_type' ), '', rtclInvoice()->get_plugin_template_path() );
		}
	}

	public static function invoice_total( $order_id ) {
		$order = rtcl()->factory->get_order( $order_id );

		if ( is_a( $order, Payment::class ) ) {
			$wc_order_id = $order->get_wc_id();

			if ( ! empty( $wc_order_id ) && class_exists( 'WooCommerce' ) ) {
				$order = wc_get_order( $wc_order_id );
				RtclFunctions::get_template( 'invoice/invoice-total-wc', compact( 'order' ), '', rtclInvoice()->get_plugin_template_path() );

				return;
			}

			RtclFunctions::get_template( 'invoice/invoice-total', compact( 'order' ), '', rtclInvoice()->get_plugin_template_path() );
		}
	}

	public static function footer_note() {
		$settings = Functions::get_invoice_settings();
		if ( is_array( $settings ) && isset( $settings['footer_text'] ) ) {
			?>
            <div style="border-top: 1px solid #dee2e6; padding-top: 10px;">
                <p class="footer-note" style="margin: 0 60px"><?php echo esc_html( $settings['footer_text'] ); ?></p>
            </div>
			<?php
		}
	}

}