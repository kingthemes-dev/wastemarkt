<?php

namespace RtclVerification\Hooks;

use Rtcl\Controllers\Hooks\TemplateHooks;
use Rtcl\Helpers\Functions as RtclFunctions;
use Rtcl\Helpers\Link;
use RtclVerification\Helpers\Functions;
use WP_Error;

class FilterHooks {

	public static function init() {
		add_filter( 'rtcl_edit_account_phone_field', [ __CLASS__, 'modify_phone_field' ], 10, 2 );
		add_filter( 'rtcl_licenses', [ __CLASS__, 'license' ], 15 );
		add_filter( 'rtcl_registration_errors', [ __CLASS__, 'add_phone_validation' ], 20, 6 );
		if ( ! is_admin() ) {
			add_filter( 'rtcl_listing_form_contact_tpl_attributes', [ __CLASS__, 'rtcl_listing_form_restriction' ] );
			add_filter( 'rtcl_verification_listing_form_phone_field', [
				__CLASS__,
				'rtcl_verification_listing_form_phone_field'
			], 10, 2 );
		}
	}

	public static function rtcl_verification_listing_form_phone_field( $html, $phone ) {
		$disable        = RtclFunctions::get_option_item( 'rtcl_misc_settings', 'verification_post_restriction', false );
		$gateway        = RtclFunctions::get_option_item( 'rtcl_misc_settings', 'verification_gateway', 'firebase' );
		$verifyButtonId = ( 'firebase' === $gateway ) ? 'verify_firebase_otp' : 'verify_otp';

		if ( 'yes' == $disable && is_user_logged_in() ) {
			$used = Functions::number_is_used( $phone );
			if ( $used ) {
				$html = "<div class='verified-phone-wrap'><span class='verified-phone'>$phone</span>";
				$html .= "<input type='hidden' name='phone' id='rtcl-phone' value='{$phone}' />";
				$html .= '<span class="rtcl-change-verified-number"><i class="rtcl-icon rtcl-icon-pencil-squared"></i></span></div>';
				// Change Field
				$html .= "<div class='phone-row'>
						<span class='rtcl-phone-collapse'></span>
						<input type='text' name='verified-phone' value='' id='rtcl-verified-phone' class='form-control verification-form-control' required>
						<div class='rtcl-otp-verification'><button type='button' id='send_otp' class='btn btn-primary otp-btn'>" . __( 'Send OTP', 'rtcl-verification' ) . "</button>";
				$html .= '<span class="counter"></span>';
				$html .= '<div id="rtcl-otp-recaptcha-container"></div>';
				$html .= '<div class="form-group otp-row">
		            <label for="rtcl-reg-otp" class="control-label">' .
				         esc_html__( 'OTP Code', 'rtcl-verification' ) . '<strong class="rtcl-required">*</strong>
		            </label>
		            <div class="otp-field">
		                <input type="number" name="otp_code" id="rtcl-reg-otp" value="" class="form-control" required/>
		                <button type="button" id="' . esc_attr( $verifyButtonId ) . '"
		                        class="btn btn-primary verify-btn">' . esc_html__( 'Verify OTP', 'rtcl-verification' ) . '</button>
		            </div>
		            </div>
		        </div>';
				ob_start();
				ActionHooks::country_list();
				$html .= ob_get_clean();
				$html .= "</div>";
			} else {
				$html = "<div class='phone-row'>
						<span class='rtcl-phone-collapse'></span>
						<input type='text' name='verified-phone' value='{$phone}' id='rtcl-verified-phone' class='form-control verification-form-control' required>";
				$html .= "<div class='rtcl-otp-verification'><button type='button' id='send_otp' class='btn btn-primary otp-btn'>" . __( 'Send OTP', 'rtcl-verification' ) . "</button>";
				$html .= '<span class="counter"></span>';
				$html .= '<div id="rtcl-otp-recaptcha-container"></div>';
				$html .= '<div class="form-group otp-row">
		            <label for="rtcl-reg-otp" class="control-label">' .
				         esc_html__( 'OTP Code', 'rtcl-verification' ) . '<strong class="rtcl-required">*</strong>
		            </label>
		            <div class="otp-field">
		                <input type="number" name="otp_code" id="rtcl-reg-otp" value="" class="form-control" required/>
		                <button type="button" id="' . esc_attr( $verifyButtonId ) . '"
		                        class="btn btn-primary verify-btn">' . esc_html__( 'Verify OTP', 'rtcl-verification' ) . '</button>
		            </div>
		            </div>
		        </div>';
				ob_start();
				ActionHooks::country_list();
				$html .= ob_get_clean();
				$html .= "</div>";
			}
		}

		return $html;

	}

	public static function add_phone_validation( $errors, $username, $email, $args, $REQUEST, $source ) {
		if ( apply_filters( 'rtcl_registration_phone_validation', true, $source ) ) {
			if ( empty( $errors ) ) {
				$errors = new WP_Error();
			}
			global $wpdb;
			$phone                         = ! empty( $args['phone'] ) ? $args['phone'] : ( ! empty( $REQUEST['phone'] ) ? $REQUEST['phone'] : '' );
			$phone_verification_table_name = $wpdb->prefix . "rtcl_phone_verification";
			$hasPhone                      = $wpdb->get_row( $wpdb->prepare( "SELECT * FROM {$phone_verification_table_name} WHERE phone = %s", $phone ) );
			if ( $hasPhone ) {
				if ( ! $hasPhone->verified ) {
					$errors->add( 'rtcl-registration-phone-not-verified', esc_html__( 'Phone number not verified.', 'rtcl-verification' ) );
				}
			} else {
				$errors->add( 'rtcl-registration-phone-exist', esc_html__( 'Phone number not exist.', 'rtcl-verification' ) );
			}

		}

		return $errors;
	}

	public static function modify_phone_field( $html, $phone ) {
		$html           = '';
		$gateway        = RtclFunctions::get_option_item( 'rtcl_misc_settings', 'verification_gateway', 'firebase' );
		$verifyButtonId = ( 'firebase' === $gateway ) ? 'verify_firebase_otp' : 'verify_otp';

		$used = Functions::number_is_used( $phone );

		if ( $used ) {
			$html = "<div class='verified-phone-wrap'><span class='verified-phone'>$phone</span>";
			$html .= "<input type='hidden' name='phone' id='rtcl-phone' value='{$phone}' />";
			$html .= '<span class="rtcl-change-verified-number"><i class="rtcl-icon rtcl-icon-pencil-squared"></i></span></div>';
			// Change Field
			$html .= "<div class='phone-row'>
						<span class='rtcl-phone-collapse'></span>
						<input type='text' name='verified-phone' value='' id='rtcl-verified-phone' class='rtcl-form-control verification-form-control' required>
						<div class='rtcl-otp-verification'><button type='button' id='send_otp' class='btn btn-primary otp-btn'>" . __( 'Send OTP', 'rtcl-verification' ) . "</button>";
			$html .= '<span class="counter"></span>';
			$html .= '<div id="rtcl-otp-recaptcha-container"></div>';
			$html .= '<div class="rtcl-form-group otp-row">
		            <label for="rtcl-reg-otp" class="rtcl-field-label">' .
			         esc_html__( 'OTP Code', 'rtcl-verification' ) . '<strong class="rtcl-required">*</strong>
		            </label>
		            <div class="otp-field">
		                <input type="number" name="otp_code" id="rtcl-reg-otp" value="" class="rtcl-form-control" required/>
		                <button type="button" id="' . esc_attr( $verifyButtonId ) . '"
		                        class="btn btn-primary verify-btn">' . esc_html__( 'Verify OTP', 'rtcl-verification' ) . '</button>
		            </div>
		            </div>
		        </div>';
			ob_start();
			ActionHooks::country_list();
			$html .= ob_get_clean();
			$html .= "</div>";
		} else {
			$html = "<div class='phone-row'>
						<span class='rtcl-phone-collapse'></span>
						<input type='text' name='verified-phone' value='{$phone}' id='rtcl-verified-phone' class='rtcl-form-control verification-form-control' required>
						<div class='rtcl-otp-verification'><button type='button' id='send_otp' class='btn btn-primary otp-btn'>" . __( 'Send OTP', 'rtcl-verification' ) . "</button>";
			$html .= '<span class="counter"></span>';
			$html .= '<div id="rtcl-otp-recaptcha-container"></div>';
			$html .= '<div class="rtcl-form-group otp-row">
		            <label for="rtcl-reg-otp" class="rtcl-field-label">' .
			         esc_html__( 'OTP Code', 'rtcl-verification' ) . '<strong class="rtcl-required">*</strong>
		            </label>
		            <div class="otp-field">
		                <input type="number" name="otp_code" id="rtcl-reg-otp" value="" class="rtcl-form-control" required/>
		                <button type="button" id="' . esc_attr( $verifyButtonId ) . '"
		                        class="btn btn-primary verify-btn">' . esc_html__( 'Verify OTP', 'rtcl-verification' ) . '</button>
		            </div>
		            </div>
		        </div>';
			ob_start();
			ActionHooks::country_list();
			$html .= ob_get_clean();
			$html .= "</div>";
		}

		return $html;
	}

	public static function license( $licenses ) {
		$licenses[] = [
			'plugin_file' => RTCL_VERIFICATION_PLUGIN_FILE,
			'api_data'    => [
				'key_name'    => 'verification_license_key',
				'status_name' => 'verification_license_status',
				'action_name' => 'rtcl_manage_verification_licensing',
				'product_id'  => 177916,
				'version'     => RTCL_VERIFICATION_VERSION,
			],
			'settings'    => [
				'title' => esc_html__( 'Verification addon license key', 'rtcl-verification' ),
			],
		];

		return $licenses;
	}

	public static function rtcl_listing_form_restriction( $data ) {
		$disable = RtclFunctions::get_option_item( 'rtcl_misc_settings', 'verification_post_restriction', false );

		if ( is_array( $data ) && isset( $data['phone'] ) && is_user_logged_in() ) {
			if ( ! empty( $data['phone'] ) ) {
				$verified = Functions::number_is_verified( $data['phone'] );
				if ( ! $verified ) {
					if ( is_user_logged_in() ) {
						$user_phone = get_user_meta( get_current_user_id(), '_rtcl_phone', true );
						$verified   = Functions::number_is_verified( $user_phone );
						if ( $verified ) {
							$data['phone'] = $user_phone;
						} else {
							if ( 'yes' == $disable ) {
								remove_action( "rtcl_listing_form_end", [
									TemplateHooks::class,
									'listing_form_submit_button'
								], 50 );
								add_action( 'rtcl_listing_form_end', [ __CLASS__, 'phone_not_verified_warning' ], 50 );
							}
							add_action( 'rtcl_listing_form_phone_warning', function () {
								?>
                                <p class="rtcl-phone-desc alert-warning">
									<?php echo apply_filters( 'rtcl_verify_unverified_number_warning_text', sprintf( __( 'Please, verify the phone number.', 'rtcl-verification' ) ) ); ?>
                                </p>
								<?php
							} );
						}
					}
				} else {
					add_action( 'rtcl_listing_form_phone_warning', function () {
						?>
                        <p class="rtcl-phone-desc alert-success small"><?php esc_html_e( 'Verified number!', 'rtcl-verification' ); ?></p>
						<?php
					} );
				}
			} else {
				if ( 'yes' == $disable ) {
					remove_action( "rtcl_listing_form_end", [
						TemplateHooks::class,
						'listing_form_submit_button'
					], 50 );
					add_action( 'rtcl_listing_form_end', [ __CLASS__, 'phone_not_verified_warning' ], 50 );
				}
				add_action( 'rtcl_listing_form_phone_warning', function () {
					?>
                    <p class="rtcl-phone-desc alert-warning"><?php echo apply_filters( 'rtcl_verify_empty_phone_warning_text', sprintf( __( 'Please, add a verified phone number', 'rtcl-verification' ) ) ); ?></p>
					<?php
				} );
			}
		}

		return $data;
	}

	public static function phone_not_verified_warning() {
		?>
        <p class="alert alert-danger"><?php echo apply_filters( 'rtcl_verify_btn_warning_text', __( 'You have to verify phone number to post ad.', 'rtcl-verification' ) ); ?></p>
		<?php
	}

}