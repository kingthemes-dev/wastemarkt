<?php

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * Hooks to the different forms and adds extra fields, required by different processes,
 * at the bottom of each form
 *
 */
function pms_add_form_extra_fields( $atts = array() ) {

    /**
     * Retrieve the name of the current action; Useful to filter extra fields per form
     *
     */
    $hook 	   = current_action();
    $form_name = '';

    switch ($hook) {
        case 'pms_register_form_bottom' :

			if( isset( $atts['plans_position'] ) && $atts['plans_position'] == 'top' )
				return;

            $form_name = 'register';
            break;
        case 'pms_register_form_before_fields' :

			if( !isset( $atts['plans_position'] ) || $atts['plans_position'] != 'top' )
				return;

            $form_name = 'register';
            break;
        case 'pms_get_output_payment_gateways' :
            $form_name = 'register';
            break;
        case 'pms_new_subscription_form_bottom' :
            $form_name = 'new_subscription';
            break;
        case 'pms_upgrade_subscription_form_bottom' :
            $form_name = 'upgrade_subscription';
            break;
        case 'pms_renew_subscription_form_bottom' :
            $form_name = 'renew_subscription';
            break;
        case 'pms_retry_payment_form_bottom' :
            $form_name = 'retry_payment';
            break;
        case 'pms_edit_profile_form_after_fields' :
            $form_name = 'edit_profile';
            break;
        case 'pms_change_subscription_form_bottom' :
            $form_name = 'change_subscription';
			break;
        case 'pms_update_payment_method_form_bottom' :

			// per gateway form location for the update payment method form
			$form_name = 'update_payment_method';

			if( isset( $_GET['pms-action'] ) && $_GET['pms-action'] == 'update_payment_method' && !empty( $_GET['subscription_id'] ) ){
				$member_subscription = pms_get_member_subscription( absint( $_GET['subscription_id'] ) );

				if( !empty( $member_subscription->payment_gateway ) ){

					// there's no update payment method for the older Stripe gateway but we would still like these users to be able to update,
					// so we send them to the current active Stripe gateway
					if( $member_subscription->payment_gateway == 'stripe' ){
						$form_name = 'update_payment_method_' . pms_get_active_stripe_gateway();
					} else
						$form_name = 'update_payment_method_' . $member_subscription->payment_gateway;

				}
			}

            break;
    }

    /**
     * Filter the form name
     *
     * @param string $form_name
     * @param string $hook
     *
     */
    $form_name = apply_filters( 'pms_form_extra_fields_form_name', $form_name, $hook );


    /**
     * Dynamic hook to set extra form field sections
     *
     */
    $form_sections = apply_filters( 'pms_extra_form_sections', array(), $form_name );

    if( empty( $form_sections ) )
        return;

    /**
     * Dynamic hook to set extra form fields
     *
     */
    $form_fields = apply_filters( 'pms_extra_form_fields', array(), $form_name );

    if( empty( $form_fields ) )
        return;

    $processed_sections = array();

    // Go through each section and output the attached fields
    // Sections with the same `name` will be skipped
    foreach( $form_sections as $section ) {

        if( empty( $section['name'] ) || in_array( $section['name'], $processed_sections ) )
            continue;

        // Set section element tag type
        $section_element = ( ! empty( $section['element'] ) ? $section['element'] : 'div' );

        // Opening element tag of the section
        echo '<' . esc_attr( $section_element ) . ' ' . ( ! empty( $section['id'] ) ? 'id="' . esc_attr( $section['id'] ) . '"' : '' ) . ' class="pms-field-section ' . ( ! empty( $section['class'] ) ? esc_attr( $section['class'] ) : '' ) . '">';

        // Output each field
        foreach( $form_fields as $field ) {

            if( $field['section'] == $section['name'] )
                pms_output_form_field( $field );

        }

        // Closing element tag of each section
        echo '</' . esc_attr( $section_element ) . '>';

        $processed_sections[] = $section['name'];

    }

}

add_action( 'pms_register_form_bottom', 'pms_add_form_extra_fields', 50 );
add_action( 'pms_register_form_before_fields', 'pms_add_form_extra_fields', 50 );
add_action( 'pms_new_subscription_form_bottom', 'pms_add_form_extra_fields', 50 );
add_action( 'pms_upgrade_subscription_form_bottom', 'pms_add_form_extra_fields', 50 );
add_action( 'pms_renew_subscription_form_bottom', 'pms_add_form_extra_fields', 50 );
add_action( 'pms_retry_payment_form_bottom', 'pms_add_form_extra_fields', 50 );
add_action( 'pms_edit_profile_form_after_fields', 'pms_add_form_extra_fields', 50 );
add_action( 'pms_change_subscription_form_bottom', 'pms_add_form_extra_fields', 50 );
add_action( 'pms_update_payment_method_form_bottom', 'pms_add_form_extra_fields', 50 );


/**
 * Display GDPR checkbox for logged in users
 *
 */

function pms_show_gdpr_checkbox_for_logged_in_users(){

    $hook = current_action();
    $gdpr_settings = pms_get_gdpr_settings();

    if ( empty( $gdpr_settings ) ) {
        return;
    }

    $is_logged_in = is_user_logged_in();
    $gdpr_enabled = !empty( $gdpr_settings['gdpr_checkbox'] ) && $gdpr_settings['gdpr_checkbox'] === 'enabled';
    $gdpr_logged_in_enabled = !empty( $gdpr_settings['gdpr_logged_in_users'] ) && $gdpr_settings['gdpr_logged_in_users'] === 'enabled';

    // Return early if GDPR is not enabled at all
    if ( !$gdpr_enabled && !$gdpr_logged_in_enabled ) return;

    // Return early if user is logged in but their checkbox is not enabled
    if ( $is_logged_in && !$gdpr_logged_in_enabled ) return;


    $field_id = $is_logged_in ? 'pms_user_consent_logged_in' : 'pms_user_consent';
    $field_name = $is_logged_in ? 'user_consent_logged_in' : 'user_consent';
    $field_errors = pms_errors()->get_error_messages($field_name);

    // Only show for registration hook if user is not logged in
    if ( !$is_logged_in && $hook !== 'pms_register_form_after_fields' ) return;

    ?>
    <li style="list-style-type: none;" class="pms-field pms-gdpr-field <?php echo ( !empty($field_errors) ? 'pms-field-error' : '' ); ?>">
        <label for="<?php echo esc_attr( $field_id ); ?>">
            <input id="<?php echo esc_attr( $field_id ); ?>" name="<?php echo esc_attr( $field_name ); ?>" type="checkbox" value="1">
            <?php
            echo isset( $gdpr_settings['gdpr_checkbox_text'] ) ? wp_kses_post(str_replace('{{privacy_policy}}', get_the_privacy_policy_link(), pms_icl_t('plugin paid-member-subscriptions', 'gdpr_checkbox_text', $gdpr_settings['gdpr_checkbox_text']))) : esc_html__('I allow the website to collect and store the data I submit through this form. *', 'paid-member-subscriptions');
            ?>
        </label>
        <?php pms_display_field_errors($field_errors); ?>
    </li>
    <?php
}

add_action( 'pms_new_subscription_form_bottom', 'pms_show_gdpr_checkbox_for_logged_in_users', 60 );
add_action( 'pms_renew_subscription_form_bottom', 'pms_show_gdpr_checkbox_for_logged_in_users', 60 );
add_action( 'pms_change_subscription_form_bottom', 'pms_show_gdpr_checkbox_for_logged_in_users', 60 );
add_action( 'pms_register_form_after_fields', 'pms_show_gdpr_checkbox_for_logged_in_users', 60 );


/**
 * Returns the output of a form field, given a set of parameters for the field
 *
 * @param array $field
 *
 * @return string
 *
 */
function pms_output_form_field( $field = array() ) {

    if( empty( $field['type'] ) )
        return;


    /**
     * If the field has custom content output it
     *
     */
    if( has_action( 'pms_output_form_field_' . $field['type'] ) ) {

    	/**
	     * Action hook to dynamically add custom content for the field
	     * This way one can overwrite the default output of a field
	     *
	     * @param string $field_inner_output
	     * @param array  $field
	     *
	     */
    	do_action( 'pms_output_form_field_' . $field['type'], $field );

    /**
     * If the field does not have custom content output the default field HTML
     *
     */
    } else {

    	// Determine field wrapper element tag
	    $field_element_wrapper = ( ! empty( $field['element_wrapper'] ) ? $field['element_wrapper'] : 'div' );

	    // Opening element tag of the field
	    echo '<' . esc_attr( $field_element_wrapper ) . ' class="cozmoslabs-form-field-wrapper pms-field pms-field-type-' . esc_attr( $field['type'] ) . ' ' . ( ! empty( $field['required'] ) ? 'pms-field-required' : '' ) . ' ' . ( ! empty( $field['wrapper_class'] ) ? esc_attr( $field['wrapper_class'] ) : '' ) . '">';

	    // Field label
	    if( ! empty( $field['label'] ) ) {

	    	echo '<label ' . ( ! empty( $field['name'] ) ? 'for="' . esc_attr( $field['name'] ) . '"' : '' ) . ' class="cozmoslabs-form-field-label">';

	    		echo esc_attr( $field['label'] );

	    		// Required asterix
	    		echo ( ! empty( $field['required'] ) ? '<span class="pms-field-required-asterix">*</span>' : '' );

	    	echo '</label>';

	    }

        /**
         * Action hook to dynamically add the actual input HTML for the field
         *
         * @param array $field
         *
         */
        do_action( 'pms_output_form_field_inner_' . $field['type'], $field );

	    // Field description
	    if( ! empty( $field['description'] ) )
	        echo '<p class="pms-field-description cozmoslabs-description cozmoslabs-description-align-right">' . esc_attr( $field['description'] ) . '</p>';

	    // Field errors
	    if( ! empty( $field['name'] ) ) {

	    	$errors = pms_errors()->get_error_messages( $field['name'] );

	    	if( ! empty( $errors ) )
	    		pms_display_field_errors( $errors );

	    }

	    // Closing element tag of each section
	    echo '</' . esc_attr( $field_element_wrapper ) . '>';

    }

}


/**
 * Outputs the "heading" type form field
 *
 * @param array $field
 *
 */
function pms_output_form_field_heading( $field = array() ) {

	if( $field['type'] != 'heading' )
		return;

	if( empty( $field['default'] ) )
		return;

	// Determine field wrapper element tag
    $field_element_wrapper = ( ! empty( $field['element_wrapper'] ) ? $field['element_wrapper'] : 'div' );

    // Opening element tag of the field
    $output  = '<' . esc_attr( $field_element_wrapper ) . ' class="pms-field pms-field-type-' . esc_attr( $field['type'] ) . ' ' . ( ! empty( $field['wrapper_class'] ) ? esc_attr( $field['wrapper_class'] ) : '' ) . '">';

    $output .= wp_kses_post( $field['default'] );

    // Closing element tag of each section
    $output .= '</' . esc_attr( $field_element_wrapper ) . '>';

    echo $output; //phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped

}
add_action( 'pms_output_form_field_heading', 'pms_output_form_field_heading' );


/**
 * Outputs the "checkbox_single" type form field
 *
 * @param array $field
 *
 */
function pms_output_form_field_checkbox_single( $field = array() ) {

	if( $field['type'] != 'checkbox_single' )
		return;

	if( empty( $field['name'] ) )
		return;

	// Determine field wrapper element tag
    $field_element_wrapper = ( ! empty( $field['element_wrapper'] ) ? $field['element_wrapper'] : 'div' );

    // Opening element tag of the field
    $output  = '<' . esc_attr( $field_element_wrapper ) . ' class="pms-field pms-field-type-' . esc_attr( $field['type'] ) . ' ' . ( ! empty( $field['wrapper_class'] ) ? esc_attr( $field['wrapper_class'] ) : '' ) . '">';

    // Set value
	$value = ( !empty( $field['value'] ) ? $field['value'] : ( !empty( $field['default'] ) ? $field['default'] : '' ) );

    $output .= '<input type="checkbox" id="' . esc_attr( $field['name'] ) . '" name="' . esc_attr( $field['name'] ) . '" value="1" ' . ( ! empty( $value ) ? 'checked' : '' ) . ' />';

    $output .= '<label for="' . esc_attr( $field['name'] ) . '">';

    	$output .= ( ! empty( $field['label'] ) ? $field['label'] : '' );
    	$output .= ( ! empty( $field['required'] ) ? '<span class="pms-field-required-asterix">*</span>' : '' );

    $output .= '</label>';

    // Field description
    if( ! empty( $field['description'] ) )
        $output .= '<p class="pms-field-description">' . esc_attr( $field['description'] ) . '</p>';

    // Output errors
    $errors = pms_errors()->get_error_messages( $field['name'] );

	if( ! empty( $errors ) )
		$output .= pms_display_field_errors( $errors, true );

    // Closing element tag of each section
    $output .= '</' . esc_attr( $field_element_wrapper ) . '>';

    echo $output; //phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped

}
add_action( 'pms_output_form_field_checkbox_single', 'pms_output_form_field_checkbox_single' );


/**
 * Outputs the "card_expiration_date" type form field
 *
 * This is a complex field, made from two select fields, one for the credit card expiration month
 * and one for the credit card expiration year. This has been added to facilitate the easy addition
 * of the field by the payment gateways.
 *
 * It is a more rigid field, with less customization than other fields
 *
 */
function pms_output_form_field_card_expiration_date( $field = array() ) {

	if( $field['type'] != 'card_expiration_date' )
		return;

	// Determine field wrapper element tag
    $field_element_wrapper = ( ! empty( $field['element_wrapper'] ) ? $field['element_wrapper'] : 'div' );

    // Opening element tag of the field
    $output  = '<' . esc_attr( $field_element_wrapper ) . ' class="pms-field pms-field-type-' . esc_attr( $field['type'] ) . ' ' . ( ! empty( $field['wrapper_class'] ) ? esc_attr( $field['wrapper_class'] ) : '' ) . '">';

    // Field label
    if( ! empty( $field['label'] ) ) {

    	$output .= '<label for="pms_card_exp_month">';

    		$output .= esc_attr( $field['label'] );

    		// Required asterix
    		$output .= ( ! empty( $field['required'] ) ? '<span class="pms-field-required-asterix">*</span>' : '' );

    	$output .= '</label>';

    }

    // Card expiration month
    $output .= '<select id="pms_card_exp_month" name="pms_card_exp_month">';

		for( $i = 1; $i <= 12; $i++ )
	        $output .= '<option value="' . $i .'">' . $i . '</option>';

    $output .= '</select>';

    // Separator between the two selects
    $output .= '<span class="pms_expiration_date_separator"> / </span>';

    // Card expiration year
    $output .= '<select id="pms_card_exp_year" name="pms_card_exp_year">';

    	$year = date( 'Y' );

        for( $i = $year; $i <= $year + 15; $i++ )
            $output .= '<option value="' . $i . '">' . $i . '</option>';

    $output .= '</select>';


    // Field description
    if( ! empty( $field['description'] ) )
        $output .= '<p class="pms-field-description">' . esc_attr( $field['description'] ) . '</p>';

    // Output errors
    $errors = pms_errors()->get_error_messages( 'pms_card_exp_date' );

	if( ! empty( $errors ) )
		$output .= pms_display_field_errors( $errors, true );

    // Closing element tag of each section
    $output .= '</' . esc_attr( $field_element_wrapper ) . '>';

    echo $output; //phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped

}
add_action( 'pms_output_form_field_card_expiration_date', 'pms_output_form_field_card_expiration_date' );


/**
 * Outputs the inner field content of the "text" type form field
 *
 * @param array $field
 *
 */
function pms_output_form_field_inner_text( $field = array() ) {

	if( $field['type'] != 'text' )
		return;

	if( empty( $field['name'] ) )
		return;

	// Set value
	$value = ( !empty( $field['value'] ) ? $field['value'] : ( !empty( $field['default'] ) ? $field['default'] : '' ) );

	// Field output
	$output = '<input type="text" id="' . esc_attr( $field['name'] ) . '" name="' . esc_attr( $field['name'] ) . '" value="' . esc_attr( $value ) . '" />';

	echo $output; //phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped

}
add_action( 'pms_output_form_field_inner_text', 'pms_output_form_field_inner_text', 10, 2 );


/**
 * Outputs the inner field content of the "select" type form field
 *
 * @param array $field
 *
 */
function pms_output_form_field_inner_select( $field = array() ) {

	if( $field['type'] != 'select' )
		return;

	if( empty( $field['name'] ) )
		return;

	// Set value
	$value = ( !empty( $field['value'] ) ? $field['value'] : ( !empty( $field['default'] ) ? $field['default'] : '' ) );

	// Field output
	$output  = '<select id="' . esc_attr( $field['name'] ) . '" name="' . esc_attr( $field['name'] ) . '">';

		if( ! empty( $field['options'] ) ) {

			foreach( $field['options'] as $option_value => $option_label )
				$output .= '<option value="' . esc_attr( $option_value ) . '" ' . ( $value == $option_value ? 'selected' : '' ) . '>' . esc_attr( $option_label ) . '</option>';

		}

	$output .= '</select>';

	echo $output; //phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped

}
add_action( 'pms_output_form_field_inner_select', 'pms_output_form_field_inner_select', 10, 2 );


/**
 * Outputs the inner field content of the "checkbox" type form field
 *
 * @param array $field
 *
 */
function pms_output_form_field_inner_checkbox( $field = array() ) {

	if( $field['type'] != 'checkbox' )
		return;

	if( empty( $field['name'] ) )
		return;

	// Set values
	$values = ( !empty( $field['value'] ) ? $field['value'] : ( !empty( $field['default'] ) ? $field['default'] : array() ) );
	$output = '';

	// Output each checkbox
	if( ! empty( $field['options'] ) ) {

		foreach( $field['options'] as $option_value => $option_label ) {

			$output .= '<label>';
			$output .= '<input type="checkbox" name="' . esc_attr( $field['name'] ) . '[]" value="' . esc_attr( $option_value ) . '" ' . ( in_array( $option_value, $values ) ? 'checked' : '' ) . ' />';
			$output .= esc_attr( $option_label );
			$output .= '</label>';

		}

	}

	echo $output; //phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped

}
add_action( 'pms_output_form_field_inner_checkbox', 'pms_output_form_field_inner_checkbox', 10, 2 );


/**
 * Outputs the inner field content of the "radio" type form field
 *
 * @param array $field
 *
 */
function pms_output_form_field_inner_radio( $field = array() ) {

	if( $field['type'] != 'radio' )
		return;

	if( empty( $field['name'] ) )
		return;

	// Set value
	$value  = ( !empty( $field['value'] ) ? $field['value'] : ( !empty( $field['default'] ) ? $field['default'] : '' ) );
	$output = '';

	// Output each radio
	if( ! empty( $field['options'] ) ) {

		foreach( $field['options'] as $option_value => $option_label ) {

			$output .= '<label>';
			$output .= '<input type="radio" name="' . esc_attr( $field['name'] ) . '" value="' . esc_attr( $option_value ) . '" ' . ( $value == $option_value ? 'checked' : '' ) . ' />';
			$output .= esc_attr( $option_label );
			$output .= '</label>';

		}

	}

	echo $output; //phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped

}
add_action( 'pms_output_form_field_inner_radio', 'pms_output_form_field_inner_radio', 10, 2 );

/**
 * Outputs and empty wrapper with the given id.
 * We use this to mount the Stripe credit card form.
 */
function pms_output_form_field_empty( $field = array() ) {

    if( $field['type'] != 'empty' )
        return;

    $id = $field['id'] ? $field['id'] : '';

    $output = '<div id="'. esc_attr( $id ) .'"></div>';

    echo $output; //phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped

}
add_action( 'pms_output_form_field_empty', 'pms_output_form_field_empty', 10, 2 );

/**
 * Outputs the Select and Input fields used for a field with States
 */
add_action( 'pms_output_form_field_inner_select_state', 'pms_output_form_field_select_state', 10, 2 );
function pms_output_form_field_select_state( $field = array() ) {

    if( $field['type'] != 'select_state' )
        return;

    if( empty( $field['name'] ) )
        return;

    $value = ( !empty( $field['value'] ) ? $field['value'] : ( !empty( $field['default'] ) ? $field['default'] : '' ) );

    $output  = '<select id="' . esc_attr( $field['name'] ) . '" name="' . esc_attr( $field['name'] ) . '" class="pms-billing-state__select"></select>';
    $output .= '<input type="text" id="' . esc_attr( $field['name'] ) . '" class="pms-billing-state__input" name="' . esc_attr( $field['name'] ) . '" value="' . esc_attr( $value ) . '" />';

    echo $output; //phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
}

/**
 * Add honeypot field to avoid spambot attacks
 */
$pms_misc_settings = get_option( 'pms_misc_settings', array() );

if( isset( $pms_misc_settings, $pms_misc_settings['honeypot-field'] ) && $pms_misc_settings['honeypot-field'] == 1 ){

    add_filter( 'pms_extra_form_sections', 'pms_honeypot_section' );
    add_filter( 'pms_extra_form_fields', 'pms_honeypot_field', 999 );
    add_action( 'pms_register_form_validation', 'pms_validate_honeypot_field' );

}
function pms_honeypot_section( $sections ) {

    $sections['security'] = array(
        'name'    => 'security',
        'element' => 'ul',
        'class'	  => ''
    );

    return $sections;

}

function pms_honeypot_field( $fields ){
    // Adding a field type "text"
    $fields['beehive'] = array(
        'section'         => 'security',
        'type'            => 'text',
        'name'            => 'beehive',
        'label'           => 'Custom Field',
        'element_wrapper' => 'li',
        'wrapper_class'   => 'beehive',
    );

    return $fields;
}

function pms_validate_honeypot_field(){

    if( !empty( $_POST['beehive'] ) )
        pms_errors()->add( 'beehive', __( 'Are you sure ? Try again.', 'paid-member-subscriptions' ) );

}

/*
 * Example on how to register a form section
 *
 * Please do not uncomment this function as it will add sections to your form
 *
 *
function pms_example_register_form_sections( $sections ) {

	$sections['example_section'] = array(
		'name'    => 'example_section',
		'element' => 'ul',
		'class'	  => 'extra_class'
	);

	return $sections;

}
add_filter( 'pms_extra_form_sections', 'pms_example_register_form_sections' );

/*
 * Example of how to register different form fields
 *
 * Please do not uncomment this function as it will add fields to your form
 *
 *
function pms_example_register_form_fields( $fields ) {

	// Adding a field type "heading"
	$fields['my_first_heading_field'] = array(
		'section' 		  => 'example_section',
		'type' 			  => 'heading',
		'default' 		  => '<h3>Section heading</h3>',
		'element_wrapper' => 'li'
	);

	// Adding a field type "text"
	$fields['my_first_text_field'] = array(
		'section' 		  => 'example_section',
		'type' 			  => 'text',
		'name' 			  => 'my_first_text_field',
		'default' 		  => 'This is the default text',
		'value' 		  => 'This is the value of the field, which will overwrite the default text',
		'label' 		  => 'My text field label',
		'description' 	  => 'Description for text field',
		'element_wrapper' => 'li',
		'required'		  => 1
	);

	// Adding a field type "checkbox_single"
	$fields['my_first_checkbox_single_field'] = array(
		'section' 		  => 'example_section',
		'type' 			  => 'checkbox_single',
		'name' 			  => 'my_first_checkbox_single_field',
		'label' 		  => 'My first checkbox single label',
		'description' 	  => 'Description for checkbox single field',
		'element_wrapper' => 'li',
		'required'		  => 1
	);

	// Adding a field type "checkbox"
	$fields['my_first_checkbox_field'] = array(
		'section' 		  => 'example_section',
		'type' 			  => 'checkbox',
		'name' 			  => 'my_first_checkbox_field',
		'default' 		  => array( 'option_1' ),
		'label' 		  => 'My checkbox field label',
		'options' 		  => array(
			'option_1' => 'Option 1',
			'option_2' => 'Option 2',
			'option_3' => 'Option 3'
		),
		'description' 	  => 'Description for checkbox field',
		'element_wrapper' => 'li'
	);

	// Adding a field type "select"
	$fields['my_first_select_field'] = array(
		'section' 		  => 'example_section',
		'type' 			  => 'select',
		'name' 			  => 'my_first_select_field',
		'default' 		  => 'option_2',
		'label' 		  => 'My select field label',
		'options' 		  => array(
			'option_1' => 'Option 1',
			'option_2' => 'Option 2',
			'option_3' => 'Option 3'
		),
		'description' 	  => 'Description for select field',
		'element_wrapper' => 'li'
	);

	// Adding a field type "radio"
	$fields['my_first_radio_field'] = array(
		'section' 		  => 'example_section',
		'type' 			  => 'radio',
		'name' 			  => 'my_first_radio_field',
		'default' 		  => 'option_1',
		'label' 		  => 'My radio field label',
		'options' 		  => array(
			'option_1' => 'Option 1',
			'option_2' => 'Option 2',
			'option_3' => 'Option 3'
		),
		'description' 	  => 'Description for radio field',
		'element_wrapper' => 'li'
	);

	return $fields;

}
add_filter( 'pms_extra_form_fields', 'pms_example_register_form_fields' );
*/
