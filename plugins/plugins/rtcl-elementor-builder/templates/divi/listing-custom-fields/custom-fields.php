<?php
/**
 * @author     RadiusTheme
 * @package    classified-listing/templates
 * @version    1.0.0
 *
 * @var Rtcl\Models\Listing $listing
 */

use Rtcl\Helpers\Functions;

// rtcl_show_new_line
$class  = ! empty( $instance['rtcl_dispaly_style'] ) ? $instance['rtcl_dispaly_style'] : '';
$class .= ! empty( $instance['rtcl_show_new_line'] ) ? ' label-new-line' : '';
$class .= ! empty( $instance['rtcl_show_list_item_separator'] ) ? ' item-separator' : '';
?>
<div class="rtcl el-single-addon custom-field-content-area <?php echo esc_attr( $class ); ?>">
	<?php
	if ( Functions::isEnableFb() ) {
		$listing->custom_fields();
	} else {
		$listing->the_custom_fields();
	}
	?>
</div>
