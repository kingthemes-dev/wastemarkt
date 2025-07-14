<?php
/**
 * @author  RadiusTheme
 * @since   1.0
 * @version 1.0
 */

namespace radiustheme\ClassiList;

use Rtcl\Helpers\Functions;

if ( !class_exists( 'Rtcl' ) || !RDTheme::$has_header_search  ) {
	return;
}
?>
<div class="header-listing-search">
	<div class="container">
		<div class="header-listing-inner">
			<?php URI_Helper::get_custom_listing_template( 'listing-search' );?>
		</div>
	</div>
</div>