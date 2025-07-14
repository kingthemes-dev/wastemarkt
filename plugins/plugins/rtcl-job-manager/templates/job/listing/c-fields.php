<?php
/**
 * @author        RadiusTheme
 * @package       classified-listing/templates/listing
 * @version       3.0.0
 *
 * @var Form $form
 * @var array $fields
 * @var int $listing_id
 */

//phpcs:disable
use Rtcl\Helpers\Functions;
use RtclJobManager\Helpers\Functions as JobFunction;
use Rtcl\Models\Form\Form;
use Rtcl\Services\FormBuilder\FBField;
use Rtcl\Services\FormBuilder\FBHelper;

if ( ! is_a( $form, Form::class ) ) {
	return;
}
global $rtclJobCurrency;
$fields  = $form->getFieldAsGroup( FBField::CUSTOM );
$listing = rtcl()->factory->get_listing( $listing_id );

$jobFlexibility = $jobType = $jobExperience = $jobVacancies = $jobTime = $jobCurrency = '';
if ( count( $fields ) ) {
	$fields = FBHelper::reOrderCustomField( $fields );


	echo "<div class='rtcl-job-information'>";

	foreach ( $fields as $index => $field ) {
		$field   = new FBField( $field );
		$value   = $field->getFormattedCustomFieldValue( $listing_id );
		$fieldID = $field->getField()['name']; //'rtcl-job-flexibility'

		if ( ! preg_match( '/company-(logo|name|tagline)/', $fieldID ) ) {
			continue;
		}

		if ( ! empty( $value ) ) {
			?>

            <div class="cfp-value <?php echo esc_attr( $fieldID ) ?>">
				<?php if ( $field->getElement() === 'file' ) {
					if ( ! empty( $value ) && is_array( $value ) ) {
						foreach ( $value as $file ) {
							if ( empty( $file['url'] ) || empty( $file['name'] ) ) {
								continue;
							}
							$ext = pathinfo( $file['url'], PATHINFO_EXTENSION );
							if ( in_array( $ext, [ 'jpg', 'jpeg', 'gif', 'png', 'bmp' ] ) ) {
								$class = 'cfp-image';
							} ?>
                            <div class="rtcl-file-item">
                                <img src="<?php echo esc_url( $file['url'] ); ?>"
                                     alt="<?php echo esc_attr__( 'Company Logo', 'rtcl-job-manager' ) ?>">
                            </div>
							<?php
						}
					}
				} else {
					Functions::print_html( $value );
				}
				?>
            </div>
			<?php
		}
	}
	echo "</div>";

	echo "<div class='job-core-info'>";
	if ( $listing->get_the_title() ) { ?>
        <div class="rtcl-cfp-item title">
            <div class="cfp-label">
                <i class="rtcl-icon-briefcase"></i>
                <span><?php echo esc_html__( 'Job Title', 'rtcl-job-manager' ); ?></span>:
            </div>
            <div class="cfp-value">
				<?php $listing->the_title(); ?>
            </div>
        </div>
		<?php
	}

	if ( $listing->get_price() ):
		$rtclJobCurrency = $jobCurrency;
		?>
        <div class="rtcl-cfp-item salary">
            <div class="cfp-label">
                <i class="rtcl-icon-money"></i>
                <span><?php echo esc_html__( 'Salary', 'rtcl-job-manager' ); ?></span>:
            </div>
            <div class="cfp-value">
				<?php echo $listing->get_price_html(); ?>
            </div>
        </div>
	<?php
	endif;

	if ( $listing->has_location() && $listing->can_show_location() ): ?>
        <div class="rtcl-cfp-item location">
            <div class="cfp-label">
                <i class="rtcl-icon-map-pin"></i>
                <span><?php echo esc_html__( 'Location', 'rtcl-job-manager' ); ?></span>:
            </div>
            <div class="cfp-value">
				<?php $listing->the_locations( true, true ); ?>

				<?php
				if ( ! $jobType && $jobFlexibility ) {
					echo " (" . esc_html( $jobFlexibility ) . ") ";
				}
				?>
            </div>
        </div>
	<?php endif; ?>
	<?php echo "</div>" ?>
    <hr>
	<?php

	//TODO: Custom Fields will go here----
	$job_submit_form = JobFunction::job_form_builder();

	if ( $job_submit_form ) {
		$external_form_key       = "job_external_link_{$job_submit_form}";
		$job_submission_form_key = "job_submission_{$job_submit_form}";
		$external_meta_id        = trim( Functions::get_option_item( 'rtcl_job_manager_settings', $external_form_key, '' ) );
		$job_submission_meta_id  = trim( Functions::get_option_item( 'rtcl_job_manager_settings', $job_submission_form_key, '' ) );
	} else {
		$external_meta_id       = 'rtcl-job-external-link';
		$job_submission_meta_id = 'rtcl-job-submission-form';
	}
	echo "<div class='extra-custom-fields row'>";
	foreach ( $fields as $index => $field ) {

		$container_class = $field['container_class'] ?? '';
		$field           = new FBField( $field );
		$fieldID         = $field->getField()['name'];

		if ( ! $field->isSingleViewAble() ) {
			continue;
		}

		if ( in_array( $fieldID, [ $external_meta_id, $job_submission_meta_id ] ) ) {
			continue;
		}
		if ( preg_match( '/company-(logo|name|tagline)/', $fieldID ) ) {
			continue;
		}

		$value = $field->getFormattedCustomFieldValue( $listing_id );

		if ( empty( $value ) ) {
			continue;
		}

		$icon            = $field->getIconData();
		$has_icon        = ! empty( $icon['type'] ) && 'class' === $icon['type'] && ! empty( $icon['class'] );
		$container_class .= $has_icon ? ' has-icon' : '';
		?>
        <div class="rtcl-cfp-item col-12 rtcl-cfp-<?php echo esc_attr( $field->getElement() ); ?> <?php echo esc_attr( $container_class ) ?>" data-name="<?php echo esc_attr( $field->getName() ); ?>" data-uuid="<?php echo esc_attr( $field->getUuid() ); ?>">
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
					if ( 'repeater' === $field->getElement() ) {
						$repeaterFields = $field->getData( 'fields', [] );
						if ( ! empty( $repeaterFields ) && is_array( $value ) ) {
							?>
                            <div class="rtcl-cfp-repeater-items">
								<?php
								foreach ( $value as $rValueIndex => $rValues ) {
									?>
                                    <div class="rtcl-cfp-repeater-item">
										<?php
										foreach ( $repeaterFields as $repeaterField ) {
											$rField = new FBField( $repeaterField );
											$rValue = 'file' === $rField->getElement() ? ( ! empty( $rValues[ $rField->getName() ] ) && is_array( $rValues[ $rField->getName() ] ) ? FBHelper::getFieldAttachmentFiles( $listing_id, $rField->getField(), $rValues[ $rField->getName() ], true ) : [] ) : ( $rValues[ $rField->getName() ] ?? '' );
											?>
                                            <div class="rtcl-cfp-repeater-field" data-name="<?php echo esc_attr( $field->getName() ); ?>" data-uuid="<?php echo esc_attr( $field->getUuid() ); ?>">
												<?php
												$rIcon = $rField->getIconData();
												if ( ( ! empty( $rIcon['type'] ) && 'class' === $rIcon['type'] && ! empty( $rIcon['class'] ) ) || ! empty( $rField->getLabel() ) ) {
													?>
                                                    <div class="rtcl-cfp-label-wrap">
														<?php
														if ( ! empty( $rIcon['type'] ) && 'class' === $rIcon['type'] && ! empty( $rIcon['class'] ) ) {
															?>
                                                            <div class="rtcl-field-icon"><i
                                                                        class="<?php echo esc_attr( $rIcon['class'] ); ?>"></i>
                                                            </div>
															<?php
														}
														if ( ! empty( $rField->getLabel() ) ) {
															?>
                                                            <div
                                                                    class='cfp-label'><?php echo esc_html( $rField->getLabel() ); ?></div>
															<?php
														}
														?>
                                                    </div>
												<?php } ?>
                                                <div class="cfp-value">
													<?php Functions::print_html( FBHelper::getFormattedFieldHtml( $rValue, $rField ) ); ?>
                                                </div>
                                            </div>
											<?php
										}
										?>
                                    </div>
									<?php
								}
								?>
                            </div>
							<?php
						}
					} else {
						Functions::print_html( FBHelper::getFormattedFieldHtml( $value, $field ) );
					}
					?>
                </div>
			<?php } ?>
        </div>
		<?php
	}
	echo "</div>";
};
