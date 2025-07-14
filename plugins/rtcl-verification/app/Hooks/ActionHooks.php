<?php

namespace RtclVerification\Hooks;

use Rtcl\Controllers\Hooks\TemplateHooks;
use Rtcl\Helpers\Functions as RtclFunctions;
use RtclVerification\Helpers\Functions;
use RtclVerification\Services\TwilioGateway;

class ActionHooks {

	public static function init() {
		add_action( 'rtcl_register_form_phone_start', [ __CLASS__, 'country_list' ] );
		add_action( 'rtcl_register_form_phone_end', [ __CLASS__, 'add_otp_button' ] );
		add_action( 'rtcl_new_user_created', [ __CLASS__, 'add_user_phone_info' ], 10, 4 );
		add_action( 'wp_ajax_rtcl_send_otp', [ __CLASS__, 'rtcl_send_otp' ] );
		add_action( 'wp_ajax_nopriv_rtcl_send_otp', [ __CLASS__, 'rtcl_send_otp' ] );
		add_action( 'wp_ajax_rtcl_verify_otp', [ __CLASS__, 'verify_otp_code' ] );
		add_action( 'wp_ajax_nopriv_rtcl_verify_otp', [ __CLASS__, 'verify_otp_code' ] );
		// Firebase action after otp verified
		add_action( 'wp_ajax_rtcl_firebase_otp_verified', [ __CLASS__, 'after_firebase_otp_verified' ] );
		add_action( 'wp_ajax_nopriv_rtcl_firebase_otp_verified', [ __CLASS__, 'after_firebase_otp_verified' ] );
		add_action( 'wp_ajax_rtcl_my_account_firebase_otp_verified', [
			__CLASS__,
			'rtcl_my_account_firebase_otp_verified'
		] );
		add_action( 'delete_user', [ __CLASS__, 'remove_user_mobile_number' ] );
	}

	public static function remove_user_mobile_number( $user_id ) {
		global $wpdb;
		$phone_table = $wpdb->prefix . "rtcl_phone";

		$row = $wpdb->get_row(
			$wpdb->prepare( "SELECT * FROM {$phone_table} WHERE user_id = %d", $user_id )
		);

		if ( $row ) {
			$wpdb->delete( $phone_table, [ 'id' => $row->id ], [ '%d' ] );
		}
	}

	public static function country_list() {
		$defaultCountry  = RtclFunctions::get_option_item( 'rtcl_misc_settings', 'verification_default_country' );
		$countries       = rtcl()->countries->get_countries();
		$specificCountry = RtclFunctions::get_option_item( 'rtcl_misc_settings', 'verification_country_list', [], 'multiselect' );

		if ( ! empty( $specificCountry ) ) {
			$specificCountry = array_flip( $specificCountry );
			$countries       = array_intersect_key( $countries, $specificCountry );
		}

		if ( ! empty( $countries ) ) {
			?>
            <div class="rtcl-country-wrapper">
                <input type="text" id="rtcl-search-country" class="rtcl-form-control"/>
                <ul id="rtcl-country-list">
					<?php
					foreach ( $countries as $key => $name ) {
						if ( 'AX' == $key ) {
							continue;
						}
						$selected = $defaultCountry == $key ? 'selected' : '';
						$code     = rtcl()->countries->get_country_calling_code( $key );
						$key      = strtolower( $key );
						echo "<li class='country-$key $selected' data-country-code='$key' data-calling-code='$code'>$name $code</li>";
					}
					?>
                </ul>
            </div>
			<?php
		}
		?>
        <div class="selected-country-flag unverified">
            <div class="flag-us">
                <div class="arrow-sign"></div>
            </div>
        </div>
		<?php
	}

	public static function add_otp_button() {
		$gateway        = RtclFunctions::get_option_item( 'rtcl_misc_settings', 'verification_gateway', 'firebase' );
		$verifyButtonId = ( 'firebase' === $gateway ) ? 'verify_firebase_otp' : 'verify_otp';
		?>
        <span class="rtcl-phone-collapse"></span>
        <div class="rtcl-otp-verification">
            <button type="button" id="send_otp" class="btn otp-btn">
                <?php esc_html_e( 'Send OTP', 'rtcl-verification' ); ?>
            </button>
            <span class="counter"></span>
            <div id="rtcl-otp-recaptcha-container"></div>
            <div class="rtcl-form-group otp-row">
                <label for="rtcl-reg-otp" class="rtcl-field-label">
					<?php esc_html_e( 'OTP Code', 'rtcl-verification' ); ?> <strong class="rtcl-required">*</strong>
                </label>
                <div class="otp-field">
                    <input type="number" name="otp_code" id="rtcl-reg-otp"
                           value="<?php echo ( ! empty( $_POST['otp_code'] ) ) ? esc_attr( absint( $_POST['otp_code'] ) ) : ''; ?>"
                           class="rtcl-form-control" required/>
                    <button type="button" id="<?php echo esc_attr( $verifyButtonId ); ?>"
                            class="btn verify-btn"><?php esc_html_e( 'Verify OTP', 'rtcl-verification' ); ?></button>
                </div>
            </div>
        </div>
		<?php
	}

	/**
	 * Send OTP [rtcl_resend_verify_ajax_cb]
	 * Ajax callback
	 */
	public static function rtcl_send_otp() {
		if ( ! RtclFunctions::verify_nonce() ) {
			wp_send_json_error( [
				"message" => esc_html__( "Session Error!!", "rtcl-verification" )
			] );
		}

		$to       = _sanitize_text_fields( $_POST['phone'] );
		$response = Functions::send_otp( $to );

		wp_send_json( $response );
	}

	public static function verify_otp_code() {
		if ( ! RtclFunctions::verify_nonce() ) {
			wp_send_json_error( [
				"message" => esc_html__( "Session Error!!", "rtcl-verification" )
			] );
		}

		$phone_no = _sanitize_text_fields( $_POST['phone'] );
		$otp_code = absint( $_POST['otp_code'] );

		$response = Functions::verify_otp( $phone_no, $otp_code );

		wp_send_json( $response );

	}

	public static function after_firebase_otp_verified() {
		if ( ! RtclFunctions::verify_nonce() ) {
			wp_send_json_error( [
				"message" => esc_html__( "Session Error!!", "rtcl-verification" )
			] );
		}

		$phone_no = _sanitize_text_fields( $_POST['phone'] );
		$otp_code = absint( $_POST['otp_code'] );
		$uid      = _sanitize_text_fields( $_POST['uid'] );

		$response = Functions::save_firebase_otp( $phone_no, $otp_code, $uid );

		wp_send_json( $response );
	}

	public static function rtcl_my_account_firebase_otp_verified() {
		global $wpdb;

		if ( ! RtclFunctions::verify_nonce() ) {
			wp_send_json_error( [
				"message" => esc_html__( "Session Error!!", "rtcl-verification" )
			] );
		}

		$response = [
			'status' => false,
			'msg'    => esc_html__( 'Error message!', 'rtcl-verification' )
		];

		$phone_no = _sanitize_text_fields( $_POST['phone'] );

		$phone_table_name = $wpdb->prefix . "rtcl_phone";
		$user_id          = get_current_user_id();

		$data = [
			'phone'   => $phone_no,
			'user_id' => $user_id,
			'type'    => 'primary'
		];

		$checkUserExists = $wpdb->get_var( $wpdb->prepare( "SELECT ID FROM {$phone_table_name} WHERE user_id = %d", $user_id ) );

		$updated = false;

		if ( ! $checkUserExists ) {
			$wpdb->insert( $phone_table_name, $data );
		} else {
			$updated = $wpdb->update(
				$phone_table_name,
				[
					'phone'       => $phone_no,
					'verified_at' => current_time( 'mysql' ),
					'updated_at'  => current_time( 'mysql' ),
				],
				[
					'id' => $checkUserExists
				]
			);
		}

		if ( $wpdb->insert_id || $updated ) {
			update_user_meta( get_current_user_id(), '_rtcl_phone', $phone_no );
			$response = [
				'status' => true,
				'msg'    => esc_html__( 'Verified the number!', 'rtcl-verification' )
			];
		}

		wp_send_json( $response );
	}

	public static function __add_user_phone_info( $user_id ) {
		global $wpdb;

		if ( $user_id ) {
			$phone                    = _sanitize_text_fields( $_POST['phone'] );
			$otp                      = absint( $_POST['otp_code'] );
			$phone_verification_table = $wpdb->prefix . "rtcl_phone_verification";

			$results = $wpdb->get_results( $wpdb->prepare( "SELECT expired_at FROM {$phone_verification_table} WHERE phone = %s AND code = %d", [
				$phone,
				$otp
			] ) );

			$now = $expired_time = strtotime( current_time( 'mysql' ) );

			if ( is_array( $results ) && ! empty( $results ) ) {
				$row          = end( $results );
				$expired_time = strtotime( $row->expired_at );

				if ( $expired_time > $now ) {
					$deleted = $wpdb->query( $wpdb->prepare( "DELETE FROM {$phone_verification_table} WHERE phone = %s AND code = %d", $phone, $otp ) );

					if ( $deleted ) {
						$phone_table_name = $wpdb->prefix . "rtcl_phone";
						$data             = [
							'phone'   => $phone,
							'user_id' => $user_id,
							'type'    => 'primary'
						];
						$wpdb->insert( $phone_table_name, $data );
					}
				}
			}
		}
	}

	/**
	 * @param $user_id
	 * @param $new_user_data
	 * @param $password_generated
	 * @param $source
	 *
	 * @return void
	 */
	public static function add_user_phone_info( $user_id, $new_user_data, $password_generated, $source ) {
		if ( 'api_social_login' !== $source ) {
			Functions::add_user_verified_phone( $user_id );
		}
	}

}