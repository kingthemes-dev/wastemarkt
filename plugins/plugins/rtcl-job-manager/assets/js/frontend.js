;
(function ($) {
  $(document).ready(function () {
    $.validator.addMethod('minAge', function (value, element, min) {
      var inputDate = new Date(value);
      var today = new Date();
      var age = today.getFullYear() - inputDate.getFullYear();
      var monthDiff = today.getMonth() - inputDate.getMonth();
      if (monthDiff < 0 || monthDiff === 0 && today.getDate() < inputDate.getDate()) {
        age--;
      }
      return age >= min;
    });
    $.validator.addMethod('maxAge', function (value, element, max) {
      var inputDate = new Date(value);
      var today = new Date();
      var age = today.getFullYear() - inputDate.getFullYear();
      var monthDiff = today.getMonth() - inputDate.getMonth();
      if (monthDiff < 0 || monthDiff === 0 && today.getDate() < inputDate.getDate()) {
        age--;
      }
      return age <= max;
    }, 'You must be at most 40 years old.');
    console.log(rtclJobManager.validation);
    $('#rtcl-job-application-form').validate({
      rules: {
        first_name: {
          required: true
        },
        birth_date: {
          required: true,
          date: true,
          minAge: 15,
          // Custom rule for minimum age
          maxAge: 45 // Custom rule for minimum age
        },
        email: {
          required: true,
          email: true
        },
        phone: {
          required: true,
          pattern: /^(\+?\d{1,4}[-\s]?)?(\(?\d{3}\)?[-\s]?)?\d{3}[-\s]?\d{4}$/
          //phoneUS: true, // Assumes US phone format, adjust regex for other formats
        },
        website: {
          url: true
        },
        resume: {
          required: true,
          extension: 'pdf|PDF' // Only allows .pdf files
        }
      },
      messages: {
        first_name: {
          required: rtclJobManager.validation.first_name.required // 'Please enter your name',
        },
        birth_date: {
          required: rtclJobManager.validation.birth_date.required,
          minAge: rtclJobManager.validation.birth_date.minAge,
          maxAge: rtclJobManager.validation.birth_date.maxAge
        },
        email: {
          required: rtclJobManager.validation.email.required,
          email: rtclJobManager.validation.email.email
        },
        phone: {
          required: rtclJobManager.validation.phone.required,
          pattern: rtclJobManager.validation.phone.pattern
          //phoneUS: "Please enter a valid phone number",
        },
        website: {
          url: rtclJobManager.validation.website.url
        },
        resume: {
          required: rtclJobManager.validation.resume.required,
          extension: rtclJobManager.validation.resume.extension
        }
      },
      submitHandler: function submitHandler(form, e) {
        //form.submit();
        e.preventDefault();
        var formWrapper = $(form);
        var targetBtn = formWrapper.find('input[type=submit]');
        var responseHolder = formWrapper.find('.rtcl-response');
        var msgHolder = $('<div class=\'alert\'></div>');
        var formData = new FormData(form);
        var formInnerGroup = formWrapper.find('.rtcl-form-group-wrap');
        formData.append('action', 'rtcl_job_submission');
        formData.append('nonce', rtclJobManager.nonce);
        $.ajax({
          url: rtclJobManager.ajaxurl,
          type: 'POST',
          data: formData,
          processData: false,
          contentType: false,
          beforeSend: function beforeSend() {
            responseHolder.addClass('rtcl-loading');
            targetBtn.prop('disabled', true);
            responseHolder.html('');
            $('<span class="rtcl-icon-spinner animate-spin"></span>').insertAfter(targetBtn);
          },
          success: function success(response) {
            targetBtn.prop('disabled', false).next('.rtcl-icon-spinner').remove();
            formWrapper.removeClass('rtcl-loading');
            formWrapper.closest('.rtcl-job-application-container').addClass('submit-successfully');
            if (response.success) {
              formWrapper[0].reset();
              msgHolder.removeClass('alert-danger').addClass('alert-success').html(response.data.message).appendTo(responseHolder);
              formInnerGroup.slideUp();
            } else {
              msgHolder.removeClass('alert-success').addClass('alert-danger').html(response.data.error).appendTo(responseHolder);
              console.log(response.data.error);
            }
          },
          error: function error(e) {
            console.log(e);
            msgHolder.removeClass('alert-success').addClass('alert-danger').html(e.responseText).appendTo(responseHolder);
            targetBtn.prop('disabled', false).next('.rtcl-icon-spinner').remove();
            formWrapper.removeClass('rtcl-loading');
          }
        });
      }
    });
    $('#rtcl-job-apply-btn').on('click', function (e) {
      e.preventDefault();
      var jobForm = $('.rtcl-job-application-container');
      var targetForm = $('#rtcl-job-form-trigger');
      var topOffset = targetForm.offset().top - 100;
      $('html, body').animate({
        scrollTop: topOffset
      }, 800);
      jobForm.slideDown();
    });
    $('#rtcl-job-location').on('change', function (e) {
      var $this = $(this);
      var parentEl = $this.parent();
      var subLocation = $this.next('.sub-location');
      var locationId = $this.val();
      $.ajax({
        url: rtclJobManager.ajaxurl,
        type: 'POST',
        data: {
          action: 'rtclJobLocationChange',
          location: locationId
        },
        beforeSend: function beforeSend() {
          parentEl.addClass('loading');
          subLocation.html('');
        },
        success: function success(response) {
          parentEl.removeClass('loading');
          if (response) {
            subLocation.html(response);
            console.log(response);
          }
        },
        error: function error(e) {
          parentEl.removeClass('loading');
          console.log(e);
        }
      });
    });
  });
})(jQuery);
