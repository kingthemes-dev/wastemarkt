<?php
/**
 *
 * @author        RadiusTheme
 * @package       invoice/templates
 * @version       1.0.0
 *
 * @var $order_id
 */
?>

<div class="rtcl-payment-invoice">
	<?php do_action( 'rtcl_invoice_header', $order_id ); ?>
    <div style="margin: 40px 60px;">
		<?php do_action( 'rtcl_invoice_content', $order_id ); ?>
    </div>
	<?php do_action( 'rtcl_invoice_footer', $order_id ); ?>
</div>