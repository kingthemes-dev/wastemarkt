<?php
/**
 * @author  RadiusTheme
 * @since   1.0
 * @version 1.4
 */

namespace radiustheme\ClassiList_Core;

use radiustheme\ClassiList\Helper;
use radiustheme\ClassiList\URI_Helper;

$col_class = "col-12 col-lg-6 " ;

$layout = 'alt';
$display = array(
	'auth'   => $data['auth_display'] ? true : false,
	'cat'   => $data['cat_display'] ? true : false,
    'fields'   => $data['field_display']==='yes' ? true : false,
    'label' => false,
);

$query = $data['query'];
$temp = Helper::wp_set_temp_query( $query );
?>
<div class="rt-el-listing-list">
	<?php if ( $query->have_posts() ) :?>
		<div class="row auto-clear rtcl">
			<?php while ( $query->have_posts() ) : $query->the_post();?>
				<div class="<?php echo esc_attr( $col_class );?>">
					<?php URI_Helper::get_template_part( 'classified-listing/custom/list', compact( 'layout', 'display' ) );?>
				</div>
			<?php endwhile;?>
		</div>
	<?php endif;?>
	<?php Helper::wp_reset_temp_query( $temp );?>
</div>