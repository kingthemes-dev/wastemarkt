<?php

use Rtcl\Helpers\Functions;

class RtclSellerAdminActionHooks {

	public function __construct() {
		add_action( 'show_user_profile', [ $this, 'rtcl_seller_user_profile_fields' ] );
		add_action( 'edit_user_profile', [ $this, 'rtcl_seller_user_profile_fields' ] );
		// For own profile update
		add_action( 'personal_options_update', [ $this, 'rtcl_seller_save_user_profile_fields' ] );
		// For others user profile update
		add_action( 'edit_user_profile_update', [ $this, 'rtcl_seller_save_user_profile_fields' ] );
		// Custom column in user table
		add_action( 'manage_users_columns', [ $this, 'register_user_status_column' ] );
		add_action( 'manage_users_custom_column', [ $this, 'register_user_status_column_view' ], 10, 3 );
	}

	function rtcl_seller_user_profile_fields( $user ) {
		$user_id            = $user->ID;
		$photo_class        = rtcl_seller_verification_get_photo_id( $user_id ) ? '' : ' no-photo';
		$document_class     = rtcl_seller_verification_get_document_file_name( $user_id ) ? ' has-file' : ' no-file';
		$max_image_size     = Functions::formatBytes( Functions::get_max_upload(), 0 );
		$max_file_size      = Functions::formatBytes( rtcl_seller_verification_get_max_file_upload_size(), 0 );
		$allowed_image_type = implode( ', ', (array) Functions::get_option_item( 'rtcl_misc_settings', 'image_allowed_type', [
			'png',
			'jpeg',
			'jpg'
		] ) );
		$verified           = (int) get_user_meta( $user_id, 'rtcl_verified_seller', true );
		$nonceId            = wp_create_nonce( rtcl()->nonceText );
		?>
        <h2><?php esc_html_e( "Seller Documents", "rtcl-seller-verification" ); ?></h2>

        <table class="form-table rtcl-seller-documents-wrapper">
            <tr>
                <th><?php esc_html_e( "Photo Id", "rtcl-seller-verification" ); ?></th>
                <td>
                    <div class="rtcl-documents-photo-wrap">
                        <div class="rtcl-documents-photo<?php echo esc_attr( $photo_class ); ?>">
							<?php if ( ! $verified ): ?>
                                <div class="rtcl-media-action">
                                    <span class="rtcl-icon-plus add"><?php esc_html_e( "Add Photo", "rtcl-seller-verification" ); ?></span>
                                    <span class="rtcl-icon-trash remove"><?php esc_html_e( "Delete Photo", "rtcl-seller-verification" ) ?></span>
                                </div>
							<?php endif; ?>
                            <div class="photo">
								<?php rtcl_seller_verification_the_photo( $user_id ); ?>
                            </div>
                        </div>
                        <span class="description">
                            <?php
                            printf(
	                            esc_html__( "Maximum file size %s, Allowed image type (%s)", "rtcl-seller-verification" ),
	                            esc_html( $max_image_size ),
	                            esc_html( $allowed_image_type )
                            ) ?>
                        </span>
                    </div>
                </td>
            </tr>
            <tr>
                <th><?php esc_html_e( "Other Document", "rtcl-seller-verification" ); ?></th>
                <td>
                    <div class="rtcl-other-document-wrap">
                        <div class="rtcl-other-document<?php echo esc_attr( $document_class ); ?>">
							<?php if ( ! $verified ): ?>
                                <div class="rtcl-media-action">
                                <span class="document-upload-btn add">
                                    <i class="rtcl-icon-upload"></i> <?php esc_html_e( "Upload File", "rtcl-seller-verification" ); ?>
                                </span>
                                </div>
							<?php endif; ?>
                            <div class="other-document">
								<?php if ( rtcl_seller_verification_get_document_file_name( $user_id ) ): ?>
                                    <span class="rtcl-document-name"><?php rtcl_seller_verification_the_document_name( $user_id ); ?></span>
                                    <a href="#" data-id="<?php echo esc_attr( $user_id ); ?>"
                                       title="<?php esc_attr_e( "Download", "rtcl-seller-verification" ); ?>"
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
                        <span class="description">
                            <?php
                            printf(
	                            esc_html__( "Maximum file size %s, PDF & allowed image type (%s)", "rtcl-seller-verification" ),
	                            esc_html( $max_image_size ),
	                            esc_html( $allowed_image_type )
                            ); ?>
                        </span>
                    </div>
                </td>
            </tr>
			<?php if ( current_user_can( 'manage_options' ) ): ?>
                <tr>
                    <th><?php esc_html_e( "Verified", "rtcl-seller-verification" ); ?></th>
                    <td>
                        <label for="rtcl_verified_seller">
                            <input name="rtcl_verified_seller" type="checkbox" id="rtcl_verified_seller"
                                   value="1" <?php checked( $verified, 1, true ); ?>>
							<?php esc_html_e( "Verify the seller", "rtcl-seller-verification" ); ?>
                        </label>
                    </td>
                </tr>
			<?php endif; ?>
        </table>
		<?php
	}

	function rtcl_seller_save_user_profile_fields( $user_id ) {
		if ( empty( $_POST['_wpnonce'] ) || ! wp_verify_nonce( $_POST['_wpnonce'], 'update-user_' . $user_id ) ) {
			return;
		}

		if ( ! current_user_can( 'edit_user', $user_id ) ) {
			return false;
		}
		if ( isset( $_POST['rtcl_verified_seller'] ) && $_POST['rtcl_verified_seller'] ) {
			update_user_meta( $user_id, 'rtcl_verified_seller', 1 );
		} else {
			delete_user_meta( $user_id, 'rtcl_verified_seller' );
		}
	}

	function register_user_status_column( $columns ) {
		$columns['rtcl_seller_status'] = apply_filters( 'rtcl_sv_user_status_column_title', esc_html__( 'Document Status', 'rtcl-seller-verification' ) );

		return $columns;
	}

	function register_user_status_column_view( $value, $column_name, $user_id ) {
		$status = rtcl_sv_get_user_status( $user_id );

		if ( $column_name == 'rtcl_seller_status' ) {
			return rtcl_sv_get_user_status_title( $status );
		}

		return $value;
	}

}