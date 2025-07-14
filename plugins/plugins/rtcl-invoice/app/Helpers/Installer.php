<?php

namespace RtclInvoice\Helpers;

class Installer {
	public static function activate() {
		do_action( 'rtcl_flush_rewrite_rules' );
	}

	public static function deactivate() {
		do_action( 'rtcl_flush_rewrite_rules' );
	}
}