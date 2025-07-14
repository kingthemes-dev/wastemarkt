<?php

namespace RtclFaq\Helpers;

/**
 * Installer Class
 */
class Installer {

	/**
	 * Activated hook callback
	 *
	 * @return void
	 */
	public static function activate(): void {

		if ( ! is_blog_installed() ) {
			return;
		}

		do_action( 'rtcl_flush_rewrite_rules' );
	}

	/**
	 * Deactivated hook callback
	 *
	 * @return void
	 */
	public static function deactivate(): void {
		do_action( 'rtcl_flush_rewrite_rules' );
	}
}
