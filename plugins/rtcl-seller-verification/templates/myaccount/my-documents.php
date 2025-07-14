<?php

use Rtcl\Helpers\Functions;

$user_id            = get_current_user_id();
$photo_class        = rtcl_seller_verification_get_photo_id() ? '' : ' no-photo';
$document_class     = rtcl_seller_verification_get_document_file_name() ? ' has-file' : ' no-file';
$max_image_size     = Functions::formatBytes( Functions::get_max_upload(), 0 );
$max_file_size      = Functions::formatBytes( rtcl_seller_verification_get_max_file_upload_size(), 0 );
$allowed_image_type = implode( ', ', (array) Functions::get_option_item( 'rtcl_misc_settings', 'image_allowed_type', [
	'png',
	'jpeg',
	'jpg'
] ) );
$verified           = rtcl_sv_check_verified_user( $user_id );
$nonceId            = wp_create_nonce( rtcl()->nonceText );
?>
<div id="rtcl-documents-content-wrap" class="rtcl-documents-content-wrap rtcl-MyAccount-content-inner">
    <div class="rtcl-documents-content">
        <div id="rtcl-documents-media">
            <div class="rtcl-form-group">
                <label class="rtcl-field-label"><?php esc_html_e( "Photo ID", 'rtcl-seller-verification' ); ?></label>
                <div class="rtcl-document-wrap rtcl-documents-photo-wrap">
                    <div class="rtcl-document rtcl-documents-photo<?php echo esc_attr( $photo_class ); ?>">
						<?php if ( ! $verified ): ?>
                            <div class="rtcl-media-action">
                                <span class="rtcl-icon-plus add"><?php esc_html_e( "Add Photo", "rtcl-seller-verification" ); ?></span>
                                <span class="rtcl-icon-trash remove"><?php esc_html_e( "Delete Photo", "rtcl-seller-verification" ) ?></span>
                            </div>
						<?php endif; ?>
                        <div class="photo">
							<?php $user_id ? rtcl_seller_verification_the_photo() : null; ?>
                        </div>
                    </div>
					<?php if ( ! $verified ): ?>
                        <div class="rtcl-form-notice">
							<?php
							printf(
								esc_html__( "Maximum file size %s, Allowed image type (%s)", "rtcl-seller-verification" ),
								esc_html( $max_image_size ),
								esc_html( $allowed_image_type )
							) ?>
                        </div>
					<?php endif; ?>
                </div>
            </div>
            <div class="rtcl-form-group">
                <label class="rtcl-field-label"><?php esc_html_e( "Other Document", 'rtcl-seller-verification' ); ?></label>
                <div class="rtcl-document-wrap rtcl-other-document-wrap">
                    <div class="rtcl-document rtcl-other-document<?php echo esc_attr( $document_class ); ?>">
						<?php if ( ! $verified ): ?>
                            <div class="rtcl-media-action">
                                <span class="document-upload-btn add">
                                    <i class="rtcl-icon-upload"></i> <?php esc_html_e( "Upload File", "rtcl-seller-verification" ); ?>
                                </span>
                            </div>
						<?php endif; ?>
                        <div class="other-document">
							<?php if ( $user_id && rtcl_seller_verification_get_document_file_name() ): ?>
                                <span class="rtcl-document-name"><?php rtcl_seller_verification_the_document_name(); ?></span>
                                <a href="#" data-id="<?php echo esc_attr( $user_id ); ?>" title="<?php esc_attr_e( "Download", "rtcl-seller-verification" ); ?>"
                                   class="rtcl-doc-view">
                                    <i class="rtcl-icon rtcl-icon-download"></i>
                                </a>
								<?php if ( ! $verified ): ?>
                                    <span class="rtcl-doc-remove"
                                          title="<?php esc_attr_e( "Remove", "rtcl-seller-verification" ); ?>"><i
                                                class="rtcl-icon rtcl-icon-cancel"></i></span>
								<?php endif; ?>
							<?php endif; ?>
                        </div>
                    </div>
					<?php if ( ! $verified ): ?>
                        <div class="rtcl-form-notice">
							<?php
							printf(
								esc_html__( "Maximum file size %s, PDF & allowed image type (%s)", "rtcl-seller-verification" ),
								esc_html( $max_image_size ),
								esc_html( $allowed_image_type )
							); ?>
                        </div>
					<?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>