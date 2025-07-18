<?php
/**
 * @author        RadiusTheme
 * @package       classified-listing/views/public
 * @version       1.0.0
 */

use Rtcl\Models\RtclCFGField;
use Rtcl\Models\Listing;

$listing = new Listing( $listing_id );
$items = array();
$urls  = array();

if ( $listing->can_show_category() ) {
	$categories = $listing->get_categories();
	$cats = $cat = '';
	foreach ( $categories as $category ) {
		$cat .= $category->name;
		$cats = array(
			'label' => esc_html__( 'Category', 'classilist' ),
			'value' => $cat,
		);
		$cat .=', ';
	}
	$items[] = $cats;
}

foreach ( $fields as $field ) {
	$field = new RtclCFGField( $field->ID );
	$value = $field->getFormattedCustomFieldValue( $listing_id );
	if ( ! empty( $value ) ) {
		if ( $field->getType() === 'url' ) {
			$nofollow = ! empty( $field->getNofollow() ) ? ' rel="nofollow"' : '';
			$urls[] = sprintf( ' <a href="%1$s" target="%2$s"%3$s>%4$s</a>', $value, $field->getTarget(), $nofollow, $field->getLabel() );
		}
		else {
			$items[] = array(
				'label' => $field->getLabel(),
				'value' => $value,
			);
		}
	}
}

if ( !$items[0] && !$urls ) {
	return;
}
?>
<div class="classilist-item-details">
	<ul>
		<?php foreach ( $items as $item ): ?>
			<li>
                <?php if (!empty($item['label'])){ ?>
				<span class="rtin-label"><?php echo esc_html( $item['label'] ); ?> : </span>
				<?php } if (!empty($item['label'])){ ?>
				<span class="rtin-title"><?php echo wp_kses_post( $item['value'] ); ?></span>
				<?php } ?>
			</li>
		<?php endforeach; ?>
		<?php foreach ( $urls as $url ): ?>
			<li>
				<span class="rtin-label"><?php echo wp_kses_post( $url ); ?></span>
			</li>
		<?php endforeach; ?>
	</ul>
</div>