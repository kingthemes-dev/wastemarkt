<?php

use Rtcl\Helpers\Functions;
use RtclInvoice\Helpers\Functions as InvoiceFunctions;

?>
<div class="wrap rtcl-invoice-admin-settings">
	<?php
	settings_errors();
	$this->show_messages();
	Functions::print_notices();
	?>
    <div class="rtcl-invoice-settings">
        <form method="post" action="">
			<?php
			do_action( 'rtcl_invoice_settings' );
			wp_nonce_field( 'rtcl-invoice' );
			submit_button();
			?>
        </form>
    </div>
    <div class="rtcl-invoice-preview">
		<?php InvoiceFunctions::show_preview(); ?>
    </div>
</div>