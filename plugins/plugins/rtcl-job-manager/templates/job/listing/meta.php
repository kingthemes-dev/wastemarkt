<?php
/**
 * Listing meta
 *
 * @author     RadiusTheme
 * @package    classified-listing/templates
 * @version    1.0.0
 */

use Rtcl\Helpers\Functions;
use Rtcl\Services\FormBuilder\FBField;
use Rtcl\Services\FormBuilder\FBHelper;

if ( ! $listing ) {
	global $listing;
}

if ( empty( $listing ) ) {
	return;
}

if ( ! $listing->can_show_date() && ! $listing->can_show_user() && ! $listing->can_show_category() && ! $listing->can_show_location() && ! $listing->can_show_views() ) {
	return;
}


$form = $listing->getForm();
$fields  = $form->getFieldAsGroup( FBField::CUSTOM );
?>

<ul class="rtcl-listing-meta-data">

	<?php if ( $listing->can_show_date() ) : ?>
        <li class="updated"><i class="rtcl-icon rtcl-icon-clock"></i>&nbsp;<?php $listing->the_time(); ?></li>
	<?php endif; ?>

	<?php
	if ( $listing->has_category() && $listing->can_show_category() ) :
		$category = $listing->get_categories();
		$category = end( $category );
		?>
        <li class="rt-categories">
            <i class="rtcl-icon rtcl-icon-tags"></i>
            <a href="?category=<?php echo esc_attr( $category->term_id ); ?>"><?php echo esc_html( $category->name ); ?></a>
        </li>
	<?php endif; ?>
	<?php
	if ( $listing->has_location() && $listing->can_show_location() ) :
		$listing_location = $listing->get_location_ids();
		$location_id = end( $listing_location );
		$location = get_term( $location_id );
		?>
        <li class="rt-location">
            <i class="rtcl-icon rtcl-icon-location"></i>
            <a href="?location=<?php echo esc_attr( $location_id ) ?>"><?php echo esc_html( $location->name ) ?></a>
			<?php //echo $location_id $listing->the_locations( true, true );
			?>
        </li>
	<?php endif; ?>

	<?php

	//TemplateHooks::loop_item_listable_fields();

	if ( false && count( $fields ) ) {
		$fields = FBHelper::reOrderCustomField( $fields );
		foreach ( $fields as $index => $field ) {

			$container_class = $field['container_class'] ?? '';
			$field           = new FBField( $field );
			$fieldID         = $field->getField()['name'];

			if ( ! $field->isArchiveViewAble() ) {
				continue;
			}

			$value = $field->getFormattedCustomFieldValue( $listing->get_id() );

			if ( empty( $value ) ) {
				continue;
			}
			$icon     = $field->getIconData();
			$has_icon = ! empty( $icon['type'] ) && 'class' === $icon['type'] && ! empty( $icon['class'] );
			$container_class .= $has_icon ? ' has-icon' : '';

			?>
            <div class="rtcl-cfp-item col-12 rtcl-cfp-<?php echo esc_attr( $field->getElement() ); ?> <?php echo esc_attr( $container_class) ?>" data-name="<?php echo esc_attr( $field->getName() ); ?>" data-uuid="<?php echo esc_attr( $field->getUuid() ); ?>">
				<?php
				if ( $field->getElement() === 'url' ) {
					$nofollow = ! empty( $field->getNofollow() ) ? ' rel="nofollow"' : '';
					?>
                    <a href="<?php echo esc_url( $value ); ?>"
                       target="<?php echo esc_attr( $field->getTarget() ); ?>"<?php echo esc_html( $nofollow ); ?>><?php echo esc_html( $field->getLabel() ); ?></a>
					<?php
				} else {
					if ( ( ! empty( $icon['type'] ) && 'class' === $icon['type'] && ! empty( $icon['class'] ) ) || ! empty( $field->getLabel() ) ) {
						?>
                        <div class="rtcl-cfp-label-wrap">
							<?php
							if ( $has_icon ) {
								?>
                                <div class="rtcl-field-icon"><i class="<?php echo esc_attr( $icon['class'] ); ?>"></i></div>
								<?php
							}
							if ( ! empty( $field->getLabel() ) ) {
								?>
                                <div class='cfp-label'><?php echo esc_html( $field->getLabel() ); ?></div>
								<?php
							}
							?>
                        </div>
					<?php } ?>
                    <div class="cfp-value">
						<?php

							Functions::print_html( FBHelper::getFormattedFieldHtml( $value, $field ) );

						?>
                    </div>
				<?php } ?>
            </div>
			<?php
		}
	}
	?>
</ul>