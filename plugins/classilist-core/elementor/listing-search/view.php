<?php
/**
 * @author  RadiusTheme
 * @since   1.0
 * @version 1.0
 */

namespace radiustheme\ClassiList_Core;

use radiustheme\ClassiList\URI_Helper;

$keyword = isset( $_GET['q'] ) ? $_GET['q'] : '';
?>
<div class="rt-el-listing-search rtcl">
	<?php URI_Helper::get_custom_listing_template( 'listing-search' );?>
</div>