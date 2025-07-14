;(function ($) {

    if ('firebase' === rtcl_verify.gateway && rtcl_verify.enable_firebase) {
        initFirebase();
        if ($('#rtcl-otp-recaptcha-container').length) {
            renderCaptcha();
        }
    }

    function initFirebase() {
        firebase.initializeApp(rtcl_verify.firebase);
    }

    function renderCaptcha() {
        // reCaptcha v3
        window.rtclOTPrecaptchaVerifier = new firebase.auth.RecaptchaVerifier('rtcl-otp-recaptcha-container', {
            'size': 'invisible',
            'callback': function (response) {
                onSignInSubmit();
            }
        });
        // Render reCaptcha
        rtclOTPrecaptchaVerifier.render();
    }

    $(document).ready(function () {
        var $wrapper = $('#rtcl-user-account, #rtcl-post-form'),
            $phone = $wrapper.find('.phone-row #rtcl-phone, .phone-row #rtcl-verified-phone'),
            otpStatus = true,
            innterval;

        if ($wrapper.find('#verify_otp, #verify_firebase_otp').length) {
            $wrapper.find('#verify_otp, #verify_firebase_otp').prop("disabled", true);
        }

        if (!$wrapper.find('.verified-phone-wrap').length) {
            $wrapper.find('input[type=submit]').prop("disabled", true);
        }

        // Set default country code
        if (!$phone.val()) {
            var selectedCountry = $('#rtcl-country-list li.selected');
            if (selectedCountry.length) {
                var countryCode = selectedCountry.data('country-code');
                $(".selected-country-flag > div").removeClass().addClass('flag-' + countryCode);
                $phone.val(selectedCountry.data('calling-code'));
            } else {
                $phone.val('+1');
            }
        }

        if ($phone.val() && $('.selected-country-flag ').hasClass('unverified')) {
            var selectedCountry = $('#rtcl-country-list li.selected');
            if (selectedCountry.length) {
                var countryCode = selectedCountry.data('country-code');
                $(".selected-country-flag > div").removeClass().addClass('flag-' + countryCode);
            }
        }

        $(document).mouseup(function (e) {
            var container = $("#rtcl-user-account .rtcl-country-wrapper, #rtcl-post-form .rtcl-country-wrapper");

            // if the target of the click isn't the container nor a descendant of the container
            if (!container.is(e.target) && container.has(e.target).length === 0) {
                container.hide();
            }
        });

        $('#rtcl-post-form')
            .on('click', '#rtcl-country-list li', function () {
                let $this = $(this),
                    $form = $this.closest('form'),
                    countryCode = $this.attr('class').replace('country-', '');

                $phone.val($this.data('calling-code'));
                $phone.focus();
                $form.find(".selected-country-flag > div").removeClass().addClass('flag-' + countryCode);
                $form.find('.rtcl-country-wrapper').hide();
                $form.find('#rtcl-search-country').val('');
                $form.find('#rtcl-country-list li').show();
            })

            .on('click', '.selected-country-flag.unverified', function () {
                var $this = $(this),
                    $form = $this.closest('form'),
                    $list = $form.find('#rtcl-country-list li');

                if ($list.length > 1) {
                    $form.find('.rtcl-country-wrapper').show();
                    $form.find('#rtcl-search-country').focus();
                }
            })

            .on('keyup', '#rtcl-search-country', function (e) {

                var _self = $(this),
                    parent = _self.parent('div'),
                    ul = parent.find('ul'),
                    lis = ul.find('li'),
                    text = _self.val();

                if (text) {
                    text = text.toLowerCase();
                    lis.each(function (e) {
                        var li = $(this),
                            liText = li.text().toLowerCase();
                        if (liText.indexOf(text) === -1) {
                            li.hide();
                        } else {
                            li.show();
                        }
                    });
                } else {
                    lis.show();
                }
            })

            .on('click', '#send_otp', function (e) {
                e.preventDefault();
                var $this = $(this),
                    $form = $this.closest('form'),
                    $phone_row = $form.find('.phone-row'),
                    phone = $phone_row.find('#rtcl-verified-phone').val();

                if (!phone.includes('+')) {
                    phone = '+' + phone;
                }

                $.ajax({
                    url: rtcl.ajaxurl,
                    type: "POST",
                    dataType: 'json',
                    data: {
                        action: 'rtcl_send_otp',
                        phone: phone,
                        __rtcl_wpnonce: rtcl.__rtcl_wpnonce
                    },
                    beforeSend: function () {
                        $('body').find('#toast-container').remove();
                        $form.rtclBlock();
                    },
                    success: function (res) {
                        if (res.status) {

                            if ('firebase' === rtcl_verify.gateway) {
                                firebase.auth().signInWithPhoneNumber(phone, window.rtclOTPrecaptchaVerifier).then(function (confirmationResult) {
                                    //s is in lowercase
                                    window.rtclOTPconfirmationResult = confirmationResult;
                                    toastr.success(res.msg);
                                }).catch(function (error) {
                                    clearInterval(innterval);
                                    $form.find('.counter').hide();
                                    $this.prop("disabled", false);
                                    $form.find('#rtcl-reg-otp').prop('readonly', true);
                                    $form.find('#verify_firebase_otp').prop("disabled", true);
                                    toastr.error(error.message);
                                });
                            }

                            $this.prop("disabled", true);
                            $form.find('#rtcl-reg-otp').prop('readonly', false);
                            $form.find('#verify_otp, #verify_firebase_otp').prop("disabled", false);
                            var count = rtcl_verify.expireTime ? parseInt(rtcl_verify.expireTime) : 100;
                            innterval = setInterval(function timerCounter() {
                                var count_html = count <= 9 ? '0' + count : count;
                                $form.find('.counter').show().html(count_html);
                                if (count === 0) {
                                    clearInterval(innterval);
                                    $this.html(rtcl_verify.resendText);
                                    $this.prop("disabled", false);
                                    $form.find('#rtcl-reg-otp').prop('readonly', true);
                                    $form.find('#verify_otp, #verify_firebase_otp').prop("disabled", true);
                                    $this.addClass('resend');
                                }
                                count--;
                                return timerCounter;
                            }(), 1000);
                            if ('firebase' !== rtcl_verify.gateway) {
                                toastr.success(res.msg);
                            }

                        } else {
                            toastr.error(res.msg);
                        }
                        $form.rtclUnblock();
                    },
                    error: function (err) {
                        toastr.error(err.msg);
                        $form.rtclUnblock();
                    }
                });

            })

            .on('click', '#verify_firebase_otp', function (e) {
                e.preventDefault();
                var $this = $(this),
                    $form = $this.closest('form'),
                    $phone_row = $form.find('.phone-row'),
                    phone = $phone_row.find('#rtcl-verified-phone').val(),
                    otp = $form.find('#rtcl-reg-otp').val();

                $form.rtclBlock();

                rtclOTPconfirmationResult.confirm(otp).then(function (result) {
                    $.ajax({
                        url: rtcl.ajaxurl,
                        type: "POST",
                        dataType: 'json',
                        data: {
                            action: 'rtcl_my_account_firebase_otp_verified',
                            phone: phone,
                            __rtcl_wpnonce: rtcl.__rtcl_wpnonce
                        },
                        beforeSend: function () {
                            $('body').find('#toast-container').remove();
                            $form.rtclBlock();
                        },
                        success: function (res) {
                            if (res.status) {
                                $form.find('.selected-country-flag').removeClass('unverified').addClass('verified');
                                clearInterval(innterval);
                                $form.find('.rtcl-phone-collapse').show();
                                $form.find('.rtcl-otp-verification').slideUp();
                                $form.find('.phone-row').addClass('collapsed-otp');
                                $form.find('.counter').remove();
                                $form.find('input[type=submit]').prop("disabled", false);
                                $form.find('#verify_firebase_otp, #send_otp').prop("disabled", true);
                                $form.find('#rtcl-reg-otp').prop("readonly", true);
                                $phone_row.find('#rtcl-verified-phone').prop("readonly", true);
                                toastr.success(res.msg);
                                window.location.reload();
                            } else {
                                toastr.error(res.msg);
                            }
                            $form.rtclUnblock();
                        },
                        error: function (err) {
                            toastr.error(err.msg);
                            $form.rtclUnblock();
                        }
                    });
                }).catch(function (error) {
                    toastr.error(error.message);
                });
            })

            .on('click', '#verify_otp', function (e) {
                e.preventDefault();
                var $this = $(this),
                    $form = $this.closest('form'),
                    $phone_row = $form.find('.phone-row'),
                    phone = $phone_row.find('#rtcl-verified-phone').val(),
                    otp = $form.find('#rtcl-reg-otp').val();

                $.ajax({
                    url: rtcl.ajaxurl,
                    type: "POST",
                    dataType: 'json',
                    data: {
                        action: 'rtcl_verify_otp',
                        phone: phone,
                        otp_code: otp,
                        __rtcl_wpnonce: rtcl.__rtcl_wpnonce
                    },
                    beforeSend: function () {
                        $('body').find('#toast-container').remove();
                        $form.rtclBlock();
                    },
                    success: function (res) {
                        if (res.status) {
                            $form.find('.selected-country-flag').removeClass('unverified').addClass('verified');
                            clearInterval(innterval);
                            $form.find('.rtcl-phone-collapse').show();
                            $form.find('.rtcl-otp-verification').slideUp();
                            $form.find('.phone-row').addClass('collapsed-otp');
                            $form.find('.counter').remove();
                            $form.find('input[type=submit]').prop("disabled", false);
                            $form.find('#verify_otp, #send_otp').prop("disabled", true);
                            $form.find('#rtcl-reg-otp').prop("readonly", true);
                            $phone_row.find('#rtcl-verified-phone').prop("readonly", true);
                            toastr.success(res.msg);
                            window.location.reload();
                        } else {
                            toastr.error(res.msg);
                        }
                        $form.rtclUnblock();
                    },
                    error: function (err) {
                        toastr.error(err.msg);
                        $form.rtclUnblock();
                    }
                });
            })

            .on('click', '.verified-phone-wrap .rtcl-change-verified-number', function (e) {
                var $this = $(this),
                    $form = $this.closest('form');

                $form.find('.verified-phone-wrap + .phone-row').slideToggle();
            });

        $('#rtcl-user-account')
            .on('click', '#rtcl-country-list li', function () {
                let $this = $(this),
                    countryCode = $this.attr('class').replace('country-', '');

                $phone.val($this.data('calling-code'));
                $phone.focus();
                $(".selected-country-flag > div").removeClass().addClass('flag-' + countryCode);
                $('#rtcl-user-account .rtcl-country-wrapper').hide();
                $('#rtcl-search-country').val('');
                $('#rtcl-user-account #rtcl-country-list li').show();
            })

            .on('click', '.selected-country-flag.unverified', function () {
                var $list = $('#rtcl-country-list li');
                if ($list.length > 1) {
                    $('#rtcl-user-account .rtcl-country-wrapper').show();
                    $('#rtcl-search-country').focus();
                }
            })

            .on('keyup', '#rtcl-search-country', function (e) {

                var _self = $(this),
                    parent = _self.parent('div'),
                    ul = parent.find('ul'),
                    lis = ul.find('li'),
                    text = _self.val();

                if (text) {
                    text = text.toLowerCase();
                    lis.each(function (e) {
                        var li = $(this),
                            liText = li.text().toLowerCase();
                        if (liText.indexOf(text) === -1) {
                            li.hide();
                        } else {
                            li.show();
                        }
                    });
                } else {
                    lis.show();
                }
            })

            .on('click', '#send_otp', function (e) {
                e.preventDefault();
                var $this = $(this),
                    $form = $this.closest('form#rtcl-user-account'),
                    $phone_row = $form.find('.phone-row'),
                    phone = $phone_row.find('#rtcl-verified-phone').val();

                if (!phone.includes('+')) {
                    phone = '+' + phone;
                }

                $.ajax({
                    url: rtcl.ajaxurl,
                    type: "POST",
                    dataType: 'json',
                    data: {
                        action: 'rtcl_send_otp',
                        phone: phone,
                        __rtcl_wpnonce: rtcl.__rtcl_wpnonce
                    },
                    beforeSend: function () {
                        $('body').find('#toast-container').remove();
                        $form.rtclBlock();
                    },
                    success: function (res) {
                        if (res.status) {

                            if ('firebase' === rtcl_verify.gateway) {
                                firebase.auth().signInWithPhoneNumber(phone, window.rtclOTPrecaptchaVerifier).then(function (confirmationResult) {
                                    //s is in lowercase
                                    window.rtclOTPconfirmationResult = confirmationResult;
                                    toastr.success(res.msg);
                                }).catch(function (error) {
                                    clearInterval(innterval);
                                    $form.find('.counter').hide();
                                    $this.prop("disabled", false);
                                    $form.find('#rtcl-reg-otp').prop('readonly', true);
                                    $form.find('#verify_firebase_otp').prop("disabled", true);
                                    toastr.error(error.message);
                                });
                            }

                            $this.prop("disabled", true);
                            $form.find('#rtcl-reg-otp').prop('readonly', false);
                            $form.find('#verify_otp, #verify_firebase_otp').prop("disabled", false);
                            var count = rtcl_verify.expireTime ? parseInt(rtcl_verify.expireTime) : 100;
                            innterval = setInterval(function timerCounter() {
                                var count_html = count <= 9 ? '0' + count : count;
                                $form.find('.counter').show().html(count_html);
                                if (count === 0) {
                                    clearInterval(innterval);
                                    $this.html(rtcl_verify.resendText);
                                    $this.prop("disabled", false);
                                    $form.find('#rtcl-reg-otp').prop('readonly', true);
                                    $form.find('#verify_otp, #verify_firebase_otp').prop("disabled", true);
                                    $this.addClass('resend');
                                }
                                count--;
                                return timerCounter;
                            }(), 1000);
                            if ('firebase' !== rtcl_verify.gateway) {
                                toastr.success(res.msg);
                            }

                        } else {
                            toastr.error(res.msg);
                        }
                        $form.rtclUnblock();
                    },
                    error: function (err) {
                        toastr.error(err.msg);
                        $form.rtclUnblock();
                    }
                });

            })

            .on('click', '#verify_firebase_otp', function (e) {
                e.preventDefault();
                var $this = $(this),
                    $form = $this.closest('form#rtcl-user-account'),
                    $phone_row = $form.find('.phone-row'),
                    phone = $phone_row.find('#rtcl-verified-phone').val(),
                    otp = $form.find('#rtcl-reg-otp').val();

                $form.rtclBlock();

                rtclOTPconfirmationResult.confirm(otp).then(function (result) {
                    $.ajax({
                        url: rtcl.ajaxurl,
                        type: "POST",
                        dataType: 'json',
                        data: {
                            action: 'rtcl_my_account_firebase_otp_verified',
                            phone: phone,
                            __rtcl_wpnonce: rtcl.__rtcl_wpnonce
                        },
                        beforeSend: function () {
                            $('body').find('#toast-container').remove();
                            $form.rtclBlock();
                        },
                        success: function (res) {
                            if (res.status) {
                                $form.find('.selected-country-flag').removeClass('unverified').addClass('verified');
                                clearInterval(innterval);
                                $form.find('.rtcl-phone-collapse').show();
                                $form.find('.rtcl-otp-verification').slideUp();
                                $form.find('.phone-row').addClass('collapsed-otp');
                                $form.find('.counter').remove();
                                $form.find('input[type=submit]').prop("disabled", false);
                                $form.find('#verify_firebase_otp, #send_otp').prop("disabled", true);
                                $form.find('#rtcl-reg-otp').prop("readonly", true);
                                $phone_row.find('#rtcl-phone').prop("readonly", true);
                                toastr.success(res.msg);
                                window.location.reload();
                            } else {
                                toastr.error(res.msg);
                            }
                            $form.rtclUnblock();
                        },
                        error: function (err) {
                            toastr.error(err.msg);
                            $form.rtclUnblock();
                        }
                    });
                }).catch(function (error) {
                    toastr.error(error.message);
                });
            })

            .on('click', '#verify_otp', function (e) {
                e.preventDefault();
                var $this = $(this),
                    $form = $this.closest('form#rtcl-user-account'),
                    $phone_row = $form.find('.phone-row'),
                    phone = $phone_row.find('#rtcl-verified-phone').val(),
                    otp = $form.find('#rtcl-reg-otp').val();

                $.ajax({
                    url: rtcl.ajaxurl,
                    type: "POST",
                    dataType: 'json',
                    data: {
                        action: 'rtcl_verify_otp',
                        phone: phone,
                        otp_code: otp,
                        __rtcl_wpnonce: rtcl.__rtcl_wpnonce
                    },
                    beforeSend: function () {
                        $('body').find('#toast-container').remove();
                        $form.rtclBlock();
                    },
                    success: function (res) {
                        if (res.status) {
                            $form.find('.selected-country-flag').removeClass('unverified').addClass('verified');
                            clearInterval(innterval);
                            $form.find('.rtcl-phone-collapse').show();
                            $form.find('.rtcl-otp-verification').slideUp();
                            $form.find('.phone-row').addClass('collapsed-otp');
                            $form.find('.counter').remove();
                            $form.find('input[type=submit]').prop("disabled", false);
                            $form.find('#verify_otp, #send_otp').prop("disabled", true);
                            $form.find('#rtcl-reg-otp').prop("readonly", true);
                            $phone_row.find('#rtcl-verified-phone').prop("readonly", true);
                            toastr.success(res.msg);
                            window.location.reload();
                        } else {
                            toastr.error(res.msg);
                        }
                        $form.rtclUnblock();
                    },
                    error: function (err) {
                        toastr.error(err.msg);
                        $form.rtclUnblock();
                    }
                });
            })

            .on('click', '.phone-row.collapsed-otp .rtcl-phone-collapse', function (e) {
                var $this = $(this),
                    $form = $this.closest('form#rtcl-user-account');

                $form.find('.rtcl-otp-verification').slideToggle();
            })

            .on('click', '.verified-phone-wrap .rtcl-change-verified-number', function (e) {
                var $this = $(this),
                    $form = $this.closest('form#rtcl-user-account');

                $form.find('.verified-phone-wrap + .phone-row').slideToggle();
            });
    });
}(jQuery));