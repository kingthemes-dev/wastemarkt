<?php
/**
 * @author  RadiusTheme
 * @since   1.0
 * @version 1.0
 */

namespace radiustheme\ClassiList_Core;

$col_class = "col-xl-{$data['col_xl']} col-lg-{$data['col_lg']} col-md-{$data['col_md']} col-sm-{$data['col_sm']} col-{$data['col_mobile']}" ;
?>
<div class="rtcl-store-grid">
	<div class="row auto-clear">
		<?php foreach ( $data['stores'] as $store ): ?>
			<?php $count_html = sprintf( _nx( '%s ad', '%s ads', $store['count'], 'Number of Ads', 'classilist-core' ), number_format_i18n( $store['count'] ) );?>
			<div class="<?php echo esc_attr( $col_class )?>">
				<a class="rtcl-store-link" href="<?php echo esc_attr( $store['permalink'] );?>">
					<div class="store-thumb"><?php echo $store['logo']; ?></div>
                    <div class="item-content">
                        <h3 class="rtcl-store-title"><?php echo esc_html( $store['title'] );?></h3>
                        <div class="rtcl-store-meta"><span class="ads-count"><?php echo esc_html( $count_html );?></span></div>
                    </div>
				</a>
			</div>
		<?php endforeach; ?>
	</div>
</div>