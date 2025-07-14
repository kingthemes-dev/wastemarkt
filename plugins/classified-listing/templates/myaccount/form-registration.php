<?php
/**
 * Login Form
 *
 * @package classified-listing/Templates
 * @version 1.0.0
 * @since   1.5.20
 */


use Rtcl\Helpers\Functions;
use Rtcl\Helpers\Link;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}
?>
<?php do_action( 'rtcl_before_user_registration_form' ); ?>
<div class="rtcl-user-registration-wrapper" id="rtcl-user-registration-wrapper">

	<?php Functions::print_notices(); ?>

	<?php if ( Functions::is_registration_enabled() && Functions::is_registration_page_separate() ): ?>
		<div class="rtcl-registration-form-wrap">

			<h2><?php esc_html_e( 'Registration', 'classified-listing' ); ?></h2>

			<form id="rtcl-register-form" class="form-horizontal" method="post">

				<?php do_action( 'rtcl_register_form_start' ); ?>

				<div class="rtcl-form-group">
					<label for="rtcl-reg-username" class="rtcl-field-label">
						<?php esc_html_e( 'Username', 'classified-listing' ); ?>
						<strong class="rtcl-required">*</strong>
					</label>
					<input type="text" name="username"
						   value="<?php echo ( ! empty( $_POST['username'] ) ) ? esc_attr( wp_unslash( $_POST['username'] ) ) : ''; ?>"
						   autocomplete="username" id="rtcl-reg-username" class="rtcl-form-control" required/>
					<span
						class="help-block"><?php esc_html_e( 'Username cannot be changed.', 'classified-listing' ); ?></span>
				</div>
				
				<?php do_action( 'rtcl_register_form_before_email' ); ?>
				
				<div class="rtcl-form-group">
					<label for="rtcl-reg-email" class="rtcl-field-label">
						<?php esc_html_e( 'Email address', 'classified-listing' ); ?>
						<strong class="rtcl-required">*</strong>
					</label>
					<input type="email" name="email"
						   value="<?php echo ( ! empty( $_POST['email'] ) ) ? esc_attr( wp_unslash( $_POST['email'] ) ) : ''; ?>"
						   autocomplete="email" id="rtcl-reg-email" class="rtcl-form-control" required/>
				</div>
				
				<?php do_action( 'rtcl_register_form_after_email' ); ?>
				
				<div class="rtcl-form-group">
					<label for="rtcl-reg-password" class="rtcl-field-label">
						<?php esc_html_e( 'Password', 'classified-listing' ); ?>
						<strong class="rtcl-required">*</strong>
					</label>
					<input type="password" name="password" id="rtcl-reg-password" autocomplete="new-password"
						   class="rtcl-form-control rtcl-password" required/>
				</div>

				<div class="rtcl-form-group">
					<label for="rtcl-reg-confirm-password" class="rtcl-field-label">
						<?php esc_html_e( 'Confirm Password', 'classified-listing' ); ?>
						<strong class="rtcl-required">*</strong>
					</label>
					<div class="confirm-password-wrap">
						<input type="password" name="pass2" id="rtcl-reg-confirm-password" class="rtcl-form-control"
							   autocomplete="off"
							   data-rule-equalTo="#rtcl-reg-password"
							   data-msg-equalTo="<?php esc_attr_e( 'Password does not match.', 'classified-listing' ); ?>"
							   required/>
						<span class="rtcl-checkmark"></span>
					</div>
				</div>

				<?php do_action( 'rtcl_register_form' ); ?>

				<div class="rtcl-form-group rtcl-form-group-no-margin-bottom">
					<div id="rtcl-registration-g-recaptcha"></div>
					<div id="rtcl-registration-g-recaptcha-message"></div>
					<input type="submit" name="rtcl-register" class="rtcl-btn rtcl-btn-primary"
						   value="<?php esc_attr_e( 'Register', 'classified-listing' ); ?>"/>
					<p class="login-link"><?php esc_html_e( 'Already have an account? Please login', 'classified-listing' ); ?>
						<a
							href="<?php echo esc_url( Link::get_my_account_page_link() ); ?>"><?php esc_html_e( 'Here', 'classified-listing' ); ?></a>
					</p>
				</div>
				<?php do_action( 'rtcl_register_form_end' ); ?>
			</form>
		</div>
	<?php endif; ?>
</div>
<?php do_action( 'rtcl_after_user_registration_form' ); ?>
