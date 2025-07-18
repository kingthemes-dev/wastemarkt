/*
 * Callback that is executed when Google's reCaptcha script loads
 *
 */
pms_recaptcha_callback = function() {

    if( typeof window.pmsRecaptchaCallbackExecuted == "undefined" ){//see if we executed this before

        if( window.pms_recaptcha && window.pms_recaptcha.pms_recaptcha_target_forms && window.pms_recaptcha.pms_recaptcha_target_forms.length > 0 )
            jQuery(document).off('submit', window.pms_recaptcha.pms_recaptcha_target_forms );

        jQuery( jQuery( ".pms-recaptcha" ).closest("form") ).each(function() {

            this.addEventListener("submit", pms_initialize_recaptcha_v3 );

        });
        window.pmsRecaptchaCallbackExecuted = true; // we use this to make sure we only run the callback once
    }

};

async function pms_initialize_recaptcha_v3( event = null, current_form = null ){

    if( event ){
        event.preventDefault();
        event.stopPropagation();
    }
    
    let currentForm = this

    if( current_form != null && current_form && current_form[0] ){
        currentForm = current_form[0]
    }

    /* Call the disable_form_submit_button function before submitting the form */
    if (typeof disable_form_submit_button === "function") {
        disable_form_submit_button(currentForm);
    }

    return new Promise((resolve) => {
        grecaptcha.ready(async function() {

            var recaptcha_field = jQuery('.pms-recaptcha', currentForm )

            if( !recaptcha_field[0] || !recaptcha_field[0]['dataset'] || !recaptcha_field[0]['dataset']['sitekey'] ){
                if( event )
                    currentForm.submit();
                else
                    return true;
            }

            var sitekey = recaptcha_field[0]['dataset']['sitekey'];

            let token = await grecaptcha.execute(sitekey, {action: "submit"}).then(function(token) {

                let recaptchaResponse = currentForm.elements["g-recaptcha-response"];
                jQuery(recaptchaResponse).val(token); // Set the recaptcha response

                if( token === false ){
                    if( event )
                        return pmsRecaptchaInitializationError();
                    else
                        return false;
                }

                var submitForm = true

                /* don't submit form if PMS gateway is Stripe */
                if( jQuery(".pms_pay_gate[type=radio]").length > 0 ){
                    jQuery(".pms_pay_gate").each( function(){
                        if( jQuery(this).is(":checked") && !jQuery(this).is(":disabled") && ( jQuery(this).val() == "stripe_connect" || jQuery(this).val() == "stripe_intents" || jQuery(this).val() == "stripe" || jQuery(this).val() == "paypal_connect" ) )
                            submitForm = false
                    })
                } else if( jQuery(".pms_pay_gate[type=hidden]").length > 0 ) {

                    if( !jQuery(".pms_pay_gate[type=hidden]").is(":disabled") && ( jQuery(".pms_pay_gate[type=hidden]").val() == "stripe_connect" || jQuery(".pms_pay_gate[type=hidden]").val() == "stripe_intents" || jQuery(".pms_pay_gate[type=hidden]").val() == "stripe" || jQuery(".pms_pay_gate[type=hidden]").val() == "paypal_connect" ) )
                        submitForm = false
                }

                if( submitForm ){

                    /* make sure the data we use to identify the default wp-login form is also submitted */
                    if ( currentForm['wp-submit'] && currentForm['wp-submit'].value !== 0 ){
                        var input = document.createElement("input");
                            input.setAttribute('type', 'hidden');
                            input.setAttribute('name', 'wp-submit');
                            input.setAttribute('value', currentForm['wp-submit']['value'] ?? '' );

                        currentForm.appendChild(input);
                    }

                    currentForm.submit();
                } else {
                    // jQuery(document).trigger( "pms_v3_recaptcha_success", jQuery( ".pms-form-submit input[type=\'submit\']", recaptchaResponse.closest("form") ) );
                    jQuery(document).trigger( "pms_v3_recaptcha_success", currentForm.elements["pms_register"] );
                    // return true;
                }

                resolve( token );

            });

        });
    });

}

/* the callback function for when the captcha does not load propperly, maybe network problem or wrong keys  */
function pmsRecaptchaInitializationError(){

    window.pmsRecaptchaInitError = true;

    var nonce = jQuery( ".pms-recaptcha" )[0]['dataset']['nonce'];

    //add a captcha field so we do not just let the form submit if we do not have a captcha response
    jQuery( ".pms-recaptcha" ).after( '<input type="hidden" id="pms_recaptcha_load_error" name="pms_recaptcha_load_error" value=' + nonce + '/>' );

    /* make sure that if the invisible recaptcha did not load properly ( network error or wrong keys ) we can still submit the form */
    jQuery("input[type=\'submit\']", jQuery( ".pms-recaptcha" ).closest("form") ).on("click", function(e){
        jQuery(this).closest("form").submit();
    });

}

jQuery( window ).on( "load", function () {   
    pms_recaptcha_callback();
} );