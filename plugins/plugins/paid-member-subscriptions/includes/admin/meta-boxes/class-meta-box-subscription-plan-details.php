<?php

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

Class PMS_Meta_Box_Subscription_Details extends PMS_Meta_Box {


    /*
     * Method to hook the output and save data methods
     *
     */
    public function init() {

        // Hook the output method to the parent's class action for output instead of overwriting the
        // output_content method
        add_action( 'pms_output_content_meta_box_' . $this->post_type . '_' . $this->id, array( $this, 'output' ) );

        // Hook the save_data method to the parent's class action for saving data instead of overwriting the
        // save_meta_box method
        add_action( 'pms_save_meta_box_' . $this->post_type, array( $this, 'save_data' ) );

        add_action( 'admin_notices', array( $this, 'admin_notices' ) );

    }


    /*
     * Method to output the HTML for this meta-box
     *
     */
    public function output( $post ) {

        $subscription_plan = pms_get_subscription_plan( $post );

        include_once 'views/view-meta-box-subscription-details.php';

    }


    function admin_notices() {

        if ( ! ( $errors = get_transient( 'pms_plan_metabox_errors' ) ) )
            return;

        $displayed_errors = array();
        $message          = '<div id="pms-plan-metabox-errors" class="error below-h2"><ul>';

        foreach ( $errors as $error ){
            if( !in_array( $error['code'], $displayed_errors ) ){
                $message .= '<li>' . esc_html( $error['message'] ) . '</li>';
                $displayed_errors[] = $error['code'];
            }
        }

        $message .= '</ul></div>';

        echo $message; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped

        delete_transient( 'pms_plan_metabox_errors' );

        remove_action( 'admin_notices', array( $this, 'admin_notices' ) );

    }

    /*
     * Method to validate the data and save it for this meta-box
     *
     */
    public function save_data( $post_id ) {

        if( empty( $_POST['post_ID'] ) )
            return;

        if( $post_id != $_POST['post_ID'] || get_post_type( $post_id ) != $this->post_type )
            return;

        // Update subscription plan description post meta
        if( isset( $_POST['pms_subscription_plan_description'] ) )
            update_post_meta( $post_id, 'pms_subscription_plan_description', wp_kses_post( $_POST['pms_subscription_plan_description'] ) );

        
        if( isset( $_POST['pms_subscription_plan_duration_unit'] ) ){

            $duration_units = array( 'day', 'week', 'month', 'year' );

            if( in_array( $_POST['pms_subscription_plan_duration_unit'], $duration_units ) )
                $duration_unit = sanitize_text_field( $_POST['pms_subscription_plan_duration_unit'] );
            else
                $duration_unit = 'day';

            update_post_meta( $post_id, 'pms_subscription_plan_duration_unit', $duration_unit );

        }

        // Update subscription plan duration meta data
        if( isset( $_POST['pms_subscription_plan_duration'] ) ) {

            $subscription_plan_duration = sanitize_text_field( $_POST['pms_subscription_plan_duration'] );

            // Check to see if entered value is a whole number, if not set the value to 0 (zero)
            if( ( function_exists( 'ctype_digit' ) && !ctype_digit( $subscription_plan_duration ) ) || ( (int)$subscription_plan_duration === 0 && strlen( $subscription_plan_duration ) > 1 ) )
                $subscription_plan_duration = 0;

            /**
             * Limit the maximum duration that can be set based on the duration unit that is selected
             * D = 90, W = 52, M = 24, Y = 5
             */
            if( !empty( $duration_unit ) ){
                if( $duration_unit == 'day' && $subscription_plan_duration > 90 )
                    add_settings_error( 'pms-plans-metabox', 'pms-plans-metabox-duration-days-error', 'Duration for the selected unit (day) can be set to a maximum of 90.', 'error' );
                else if( $duration_unit == 'week' && $subscription_plan_duration > 52 )
                    add_settings_error( 'pms-plans-metabox', 'pms-plans-metabox-duration-week-error', 'Duration for the selected unit (week) can be set to a maximum of 52.', 'error' );
                else if( $duration_unit == 'month' && $subscription_plan_duration > 24 )
                    add_settings_error( 'pms-plans-metabox', 'pms-plans-metabox-duration-month-error', 'Duration for the selected unit (month) can be set to a maximum of 24.', 'error' );
                else if( $duration_unit == 'year' && $subscription_plan_duration > 5 )
                    add_settings_error( 'pms-plans-metabox', 'pms-plans-metabox-duration-year-error', 'Duration for the selected unit (year) can be set to a maximum of 5.', 'error' );
                else {

                    update_post_meta( $post_id, 'pms_subscription_plan_duration', absint( $subscription_plan_duration ) );

                }
            }
            
        }

        // Update price post meta
        if( isset( $_POST['pms_subscription_plan_price'] ) ) {

            $subscription_plan_price = sanitize_text_field( $_POST['pms_subscription_plan_price'] );

            if( !is_numeric( $subscription_plan_price ) || !( $subscription_plan_price >= 0 ) )
                $subscription_plan_price = 0;

            update_post_meta( $post_id, 'pms_subscription_plan_price', (float)$subscription_plan_price );

        }


        // Update sign-up fee post meta
        if( isset( $_POST['pms_subscription_plan_sign_up_fee'] ) ) {

            $subscription_plan_sign_up_fee = sanitize_text_field( $_POST['pms_subscription_plan_sign_up_fee'] );

            if( !is_numeric( $subscription_plan_sign_up_fee ) || !( $subscription_plan_sign_up_fee >= 0 ) )
                $subscription_plan_sign_up_fee = 0;

            update_post_meta( $post_id, 'pms_subscription_plan_sign_up_fee', (float)$subscription_plan_sign_up_fee );

        }


        // Update subscription plan free trial duration meta data
        if( isset( $_POST['pms_subscription_plan_trial_duration'] ) && isset( $_POST['pms_subscription_plan_trial_duration_unit'] ) ) {

            // setup trial duration unit to be updated
            $trial_duration_units = array( 'day', 'week', 'month', 'year' );

            if( in_array( $_POST['pms_subscription_plan_trial_duration_unit'], $trial_duration_units ) )
                $trial_duration_unit = sanitize_text_field( $_POST['pms_subscription_plan_trial_duration_unit'] );
            else
                $trial_duration_unit = 'day';

            $subscription_plan_trial_duration = sanitize_text_field( $_POST['pms_subscription_plan_trial_duration'] );

            // Check to see if entered value is a whole number, if not set the value to 0 (zero)
            if( ( function_exists( 'ctype_digit' ) && !ctype_digit( $subscription_plan_trial_duration ) ) || ( (int)$subscription_plan_trial_duration === 0 && strlen( $subscription_plan_trial_duration ) > 1 ) )
                $subscription_plan_trial_duration = 0;

            /**
             * Limit the maximum duration that can be set based on the duration unit that is selected
             * D = 90, W = 52, M = 24, Y = 5
             */
            if( $trial_duration_unit == 'day' && $subscription_plan_trial_duration > 90 )
                add_settings_error( 'pms-plans-metabox', 'pms-plans-metabox-trial-days-error', 'Trial duration for the selected unit (day) can be set to a maximum of 90.', 'error' );
            else if( $trial_duration_unit == 'week' && $subscription_plan_trial_duration > 52 )
                add_settings_error( 'pms-plans-metabox', 'pms-plans-metabox-trial-week-error', 'Trial duration for the selected unit (week) can be set to a maximum of 52.', 'error' );
            else if( $trial_duration_unit == 'month' && $subscription_plan_trial_duration > 24 )
                add_settings_error( 'pms-plans-metabox', 'pms-plans-metabox-trial-month-error', 'Trial duration for the selected unit (month) can be set to a maximum of 24.', 'error' );
            else if( $trial_duration_unit == 'year' && $subscription_plan_trial_duration > 5 )
                add_settings_error( 'pms-plans-metabox', 'pms-plans-metabox-trial-year-error', 'Trial duration for the selected unit (year) can be set to a maximum of 5.', 'error' );
            else {

                update_post_meta( $post_id, 'pms_subscription_plan_trial_duration', absint( $subscription_plan_trial_duration ) );
                update_post_meta( $post_id, 'pms_subscription_plan_trial_duration_unit', $trial_duration_unit );

            }

        }

        // Handle Payment Installments meta data
        if( isset( $_POST['pms_subscription_plan_limit_payment_cycles'] ) && $_POST['pms_subscription_plan_limit_payment_cycles'] === 'yes' &&
            ( !isset( $_POST['pms_subscription_plan_fixed_membership'] ) || $_POST['pms_subscription_plan_fixed_membership'] != 'on' ) )
        {

            // Update subscription plan limit payment cycles
            update_post_meta( $post_id, 'pms_subscription_plan_limit_payment_cycles', sanitize_text_field( $_POST['pms_subscription_plan_limit_payment_cycles'] ) );

            // Update subscription plan number of payments
            if( !empty( $_POST['pms_subscription_plan_number_of_payments'] ) ) {
                update_post_meta( $post_id, 'pms_subscription_plan_number_of_payments', sanitize_text_field( $_POST['pms_subscription_plan_number_of_payments'] ) );
            }

            // Update subscription plan status after last cycle
            if( isset( $_POST['pms_subscription_plan_status_after_last_cycle'] ) ) {
                update_post_meta( $post_id, 'pms_subscription_plan_status_after_last_cycle', sanitize_text_field( $_POST['pms_subscription_plan_status_after_last_cycle'] ) );
            }

            // Update subscription plan expire after
            if( isset( $_POST['pms_subscription_plan_expire_after'] ) ) {
                update_post_meta( $post_id, 'pms_subscription_plan_expire_after', sanitize_text_field( $_POST['pms_subscription_plan_expire_after'] ) );
            }

            // Update subscription plan expire after unit
            if( isset( $_POST['pms_subscription_plan_expire_after_unit'] ) ) {
                update_post_meta( $post_id, 'pms_subscription_plan_expire_after_unit', sanitize_text_field( $_POST['pms_subscription_plan_expire_after_unit'] ) );
            }

            // Update the subscription plan recurring option to "Always renew automatically"
            if ( !isset( $_POST['pms_subscription_plan_recurring'] ) || $_POST['pms_subscription_plan_recurring'] !== '2' ) {
                update_post_meta( $post_id, 'pms_subscription_plan_recurring', '2' );
            }

        }
        else {

            // Update Payment Installments meta data to default values if Limit Payment Cycles option is disabled
            update_post_meta( $post_id, 'pms_subscription_plan_limit_payment_cycles', 'no' );
            update_post_meta( $post_id, 'pms_subscription_plan_number_of_payments', '' );
            update_post_meta( $post_id, 'pms_subscription_plan_status_after_last_cycle', '' );
            update_post_meta( $post_id, 'pms_subscription_plan_expire_after', '' );
            update_post_meta( $post_id, 'pms_subscription_plan_expire_after_unit', '' );

        }


        // Update subscription plan recurring if Payment Installments are disabled
        // - if Payment Installments are enabled, the option is updated above along with the Payment Installments meta
        if( isset( $_POST['pms_subscription_plan_recurring'] ) && ( !isset( $_POST['pms_subscription_plan_limit_payment_cycles'] ) || $_POST['pms_subscription_plan_limit_payment_cycles'] !== 'yes' ) ) {
            update_post_meta( $post_id, 'pms_subscription_plan_recurring', (int)$_POST['pms_subscription_plan_recurring'] );
        }


        // Update status post meta
        if( isset( $_POST['pms_subscription_plan_status'] ) ) {

            update_post_meta($post_id, 'pms_subscription_plan_status', sanitize_text_field( $_POST['pms_subscription_plan_status'] ) );

            $status = sanitize_text_field( $_POST['pms_subscription_plan_status'] );

            if ( ! wp_is_post_revision( $post_id ) ){

                // unhook this function so it doesn't loop infinitely
                remove_action('pms_save_meta_box_pms-subscription', array( $this, 'save_data' ));

                // Change the post status as the discount status
                $post = array(
                    'ID'            => $post_id,
                    'post_status'   => $status,
                );
                wp_update_post( $post );

                // re-hook this function
                add_action('pms_save_meta_box_pms-subscription', array( $this, 'save_data' ) );

            }
        }


        // Update the user role
        if( isset( $_POST['pms_subscription_plan_user_role'] ) ) {

            $current_role = get_post_meta( $post_id, 'pms_subscription_plan_user_role', true );

            $new_role   = sanitize_text_field( $_POST['pms_subscription_plan_user_role'] );
            $post_title = isset( $_POST['post_title'] ) ? sanitize_text_field( $_POST['post_title'] ) : '';

            // Create a new user role based on subscription plan
            if( $new_role == 'create-new' ) {

                $new_role = 'pms_subscription_plan_' . $post_id;
                add_role( $new_role, $post_title, array( 'read' => true ) );

                $role = get_role( $new_role );
                $role->add_cap( $new_role, true );
            }

            // Update all users user role if the value changes
            if( !empty($current_role) && $current_role != $new_role ) {

                // Get all members that are subscribed to the current subscription plan
                $members = pms_get_members( array( 'subscription_plan_id' => $post_id ) );

                foreach( $members as $member ) {

                    // Add new user role
                    pms_add_user_role( $member->user_id, $new_role );

                    // Remove old user role
                    if( count(array_keys( pms_get_user_roles_by_plan_ids($member->get_subscriptions_ids()), $current_role )) == 1 )
                        pms_remove_user_role( $member->user_id, $current_role );

                }

            }

            // Update the subscription plan default user role
            update_post_meta( $post_id, 'pms_subscription_plan_user_role', $new_role );

        }

        set_transient( 'pms_plan_metabox_errors', get_settings_errors(), 60 );

    }

}

function pms_init_subscription_plan_details_meta_box() {

    $pms_meta_box_subscription_details = new PMS_Meta_Box_Subscription_Details( 'pms_subscription_details', esc_html__( 'Subscription Plan Details', 'paid-member-subscriptions' ), 'pms-subscription', 'normal' );
    $pms_meta_box_subscription_details->init();

}
add_action( 'init', 'pms_init_subscription_plan_details_meta_box', 2 );

