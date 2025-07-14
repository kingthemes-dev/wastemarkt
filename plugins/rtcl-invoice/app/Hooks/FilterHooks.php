<?php

namespace RtclInvoice\Hooks;

use Rtcl\Helpers\Functions;

class FilterHooks {

	public static function init() {
		add_filter( 'rtcl_get_template', array( __CLASS__, 'get_template' ), 20, 3 );
	}

	public static function get_template( $located, $template_name, $args ) {
		if ( 'myaccount/payment-history' === $template_name ) {
			$located = Functions::locate_template( $template_name, '', rtclInvoice()->get_plugin_template_path() );
		}

		return $located;
	}

}