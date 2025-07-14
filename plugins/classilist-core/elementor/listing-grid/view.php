<?php
/**
 * @author  RadiusTheme
 * @since   1.0
 * @version 1.4
 */

namespace radiustheme\ClassiList_Core;

use radiustheme\ClassiList\Helper;
use radiustheme\ClassiList\URI_Helper;

$col_class = "col-xl-{$data['col_xl']} col-lg-{$data['col_lg']} col-md-{$data['col_md']} col-sm-{$data['col_sm']} col-{$data['col_mobile']}" ;

$layout = 1;
$display = array(
	'cat'   => $data['cat_display'] ? true : false,
    'views'   => $data['views_display'] == 'yes' ? true : false,
    'fields'   => $data['field_display']==='yes' ? true : false,
    'label' => false,
    'type'  => $data['type_display'] == 'yes' ? true : false,
);

$query = $data['query'];
$temp = Helper::wp_set_temp_query( $query );
?>
<div class="rt-el-listing-grid">
	<?php if ( $query->have_posts() ) :?>
		<div class="row auto-clear">
			<?php while ( $query->have_posts() ) : $query->the_post();?>
				<div class="<?php echo esc_attr( $col_class );?>">
					<?php URI_Helper::get_template_part( 'classified-listing/custom/grid', compact( 'layout', 'display' ) );?>
				</div>
			<?php endwhile;?>
		</div>
	<?php endif;?>
	<?php Helper::wp_reset_temp_query( $temp );?>
</div>