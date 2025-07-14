<?php

namespace RtclFaq\Helpers;

use Rtcl\Helpers\Functions;

/**
 * Installer Class
 */
class Fns {

	public static function is_active_faq() {
		$listing_enable_faq = Functions::get_option_item( 'rtcl_moderation_settings', 'listing_enable_faq' ) ?? 'yes';
		if ( 'yes' == $listing_enable_faq ) {
			return true;
		}

		return false;
	}
}
