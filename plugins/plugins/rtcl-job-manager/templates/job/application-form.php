<?php
/**
 * Application Form
 *
 * @var $listing_id
 * @var $user_restrictions
 * @var $form_fields
 */

use Rtcl\Helpers\Functions;
?>

    <div id="rtcl-job-form-trigger"></div>
<?php if ( 'yes' == $user_restrictions && ! is_user_logged_in() ) : ?>
    <div class="modal fade rtcl-bs-modal rtcl-job-application-login" id="rtcl-job-login-modal" tabindex="-1"
         role="dialog">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
				<?php
				$args = [
					'message'     => '',
					'redirect_to' => '',
					'hidden'      => false,
				];
				Functions::get_template( 'myaccount/form-login', $args );
				?>
            </div>
        </div>
    </div>
<?php else : ?>

    <div class="rtcl-job-application-container">
        <form class="rtcl-job-application-form" id="rtcl-job-application-form" method="post" enctype="multipart/form-data">
            <div class="rtcl-form-group-wrap">

                <div class="rtcl-form-group">
                    <label for="rtcl-first-name" class="rtcl-field-label">
						<?php esc_html_e( 'First Name', 'rtcl-job-manager' ); ?>
                        <span class="require-star">*</span>
                    </label>
                    <div class="rtcl-field-col">
                        <input type="text" name="first_name" id="rtcl-first-name"
                               value="<?php echo esc_attr( $first_name ?? '' ); ?>"
                               class="rtcl-form-control form-control" required/>
                    </div>
                </div>
                <div class="rtcl-form-group">
                    <label for="rtcl-last-name" class="rtcl-field-label">
						<?php esc_html_e( 'Last Name', 'rtcl-job-manager' ); ?>
                    </label>
                    <div class="rtcl-field-col">
                        <input type="text" name="last_name" id="rtcl-last-name"
                               value="<?php echo esc_attr( $last_name ?? '' ); ?>"
                               class="rtcl-form-control form-control"/>
                    </div>
                </div>

				<?php if ( $form_fields['birth_date'] == 'yes' ) : ?>
                    <div class="rtcl-form-group">
                        <label for="rtcl-date-of-birth" class="rtcl-field-label">
							<?php esc_html_e( 'Date of Birth', 'rtcl-job-manager' ); ?>
                            <span class="require-star">*</span>
                        </label>
                        <div class="rtcl-field-col">
                            <input type="date" name="birth_date" id="rtcl-birth-date" placeholder="Birth Date"
                                   class="rtcl-form-control form-control" required>
                        </div>
                    </div>
				<?php endif; ?>
                <div class="rtcl-form-group">
                    <label for="rtcl-email" class="rtcl-field-label">
						<?php esc_html_e( 'E-mail', 'rtcl-job-manager' ); ?>
                        <span class="require-star">*</span>
                    </label>
                    <div class="rtcl-field-col">
                        <input type="email" name="email" id="rtcl-email" class="rtcl-form-control form-control"
                               value="<?php echo esc_attr( $user->user_email ?? '' ); ?>" required="required"/>
                    </div>
                </div>
				<?php if ( $form_fields['whatsup'] == 'yes' ) : ?>
                    <div class="rtcl-form-group">
                        <label for="rtcl-last-name" class="rtcl-field-label">
							<?php esc_html_e( 'Whatsapp number', 'rtcl-job-manager' ); ?>
                        </label>
                        <div class="rtcl-field-col">
                            <input type="text" name="whatsapp_number" id="rtcl-whatsapp-phone"
                                   value="<?php echo esc_attr( $whatsapp_number ?? '' ); ?>"
                                   class="rtcl-form-control form-control"/>
                        </div>
                    </div>
				<?php endif; ?>
				<?php if ( $form_fields['phone'] == 'yes' ) : ?>
                    <div class="rtcl-form-group">
                        <label for="rtcl-phone" class="rtcl-field-label">
							<?php esc_html_e( 'Phone', 'rtcl-job-manager' ); ?>
                        </label>
                        <div class="rtcl-field-col">
                            <input type='text' name='phone' id='rtcl-phone'
                                   value='<?php echo esc_attr( $phone ?? '' ); ?>'
                                   class='rtcl-form-control form-control'/>
                        </div>
                    </div>
				<?php endif; ?>

				<?php if ( $form_fields['website'] == 'yes' ) : ?>
                    <div class="rtcl-form-group">
                        <label for="rtcl-website" class="rtcl-field-label">
							<?php esc_html_e( 'Website', 'rtcl-job-manager' ); ?>
                        </label>
                        <div class="rtcl-field-col">
                            <input type="url" name="website" id="rtcl-website"
                                   value="<?php echo esc_attr( $website ?? '' ); ?>"
                                   class="rtcl-form-control form-control"/>
                            <p class="description small"><?php esc_html_e( 'e.g. https://example.com', 'rtcl-job-manager' ); ?></p>
                        </div>
                    </div>
				<?php endif; ?>


                <div class="rtcl-job-location-froup">
					<?php if ( $form_fields['location'] == 'yes' ) : ?>
						<?php do_action( 'rtcl_job_loaction' ); ?>
					<?php endif; ?>

					<?php if ( $form_fields['address'] == 'yes' ) : ?>
                        <div class="rtcl-form-group">
                            <label for="rtcl-address"
                                   class="rtcl-field-label"><?php esc_html_e( 'Address', 'rtcl-job-manager' ); ?></label>
                            <textarea name="address" rows="3" class="rtcl-map-field rtcl-form-control"
                                      id="rtcl-address"><?php echo esc_textarea( $address ?? '' ); ?></textarea>
                        </div>
					<?php endif; ?>
                </div>
				<?php if ( $form_fields['social'] == 'yes' ) : ?>
					<?php do_action( 'rtcl_job_social_profile' ); ?>
				<?php endif; ?>

				<?php if ( $form_fields['cv'] == 'yes' ) : ?>
                    <div class="rtcl-form-group">
                        <label for="rtcl-website" class="rtcl-field-label">
							<?php esc_html_e( 'Upload your CV', 'rtcl-job-manager' ); ?>
                            <span class="require-star">*</span>
                        </label>
                        <div class="rtcl-field-col">
                            <input type="file" name="resume" accept="application/pdf" class="rtcl-form-control form-control"
                                   required>
                            <p class="description small"><?php esc_html_e( 'Upload only PDF file', 'rtcl-job-manager' ); ?></p>
                        </div>
                    </div>
				<?php endif; ?>

				<?php if ( $form_fields['cover_letter'] == 'yes' ) : ?>
                    <div class="rtcl-form-group">
                        <label for="rtcl-website" class="rtcl-field-label">
							<?php esc_html_e( 'Cover Letter', 'rtcl-job-manager' ); ?>
                            <span class="require-star">*</span>
                        </label>
                        <div class="rtcl-field-col">
                        <textarea class="rtcl-form-control form-control" name="cover_letter" placeholder="Cover Letter"
                                  required></textarea>
                        </div>
                    </div>
				<?php endif; ?>

                <div class="rtcl-form-group">
                    <div class="rtcl-field-col">
                        <input type="submit" name="submit" class="btn"
                               value="<?php esc_attr_e( 'Submit', 'rtcl-job-manager' ); ?>"/>
                    </div>
                </div>

                <input type="hidden" name="listing_id" value="<?php echo esc_attr( $listing_id ); ?>">
            </div>

            <div class="rtcl-response"></div>
        </form>
    </div>
<?php endif; ?>