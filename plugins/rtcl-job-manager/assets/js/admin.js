;
(function ($) {
  window.rtToast = function (message) {
    var type = arguments.length > 1 && arguments[1] !== undefined ? arguments[1] : 'success';
    var toast = document.createElement('div');
    toast.className = 'rt-toast toast-' + type;
    toast.textContent = message;
    document.body.appendChild(toast);
    setTimeout(function () {
      toast.classList.add('toast-show');
    }, 10);
    setTimeout(function () {
      toast.classList.remove('toast-show');
      setTimeout(function () {
        document.body.removeChild(toast);
      }, 300);
    }, 3000);
  };
  $(document).ready(function () {
    $('.rtclModaPopup').each(function () {
      var $this = $(this);
      var closeBtn = $('.close', $this);
      $this.on('click', function (event) {
        if (!event.target.closest('.modalInner')) {
          $this.removeClass('open');
        }
      });
      closeBtn.on('click', function () {
        $this.removeClass('open');
      });
    });
    $('.rtcl-application-title').on('click', function (e) {
      e.preventDefault();
      var $this = $(this);
      var applicationId = $this.data('id');
      var applicationInfo = $this.data('info');
      var modal = $('.rtclModaPopup');
      var modalBody = $('.modal-body', modal);
      modal.addClass('open');
      $.ajax({
        url: rtclJobManager.ajaxurl,
        type: 'POST',
        data: {
          action: 'load_application_info',
          appId: applicationId,
          info: applicationInfo
        },
        beforeSend: function beforeSend() {
          modal.addClass('loading');
          modalBody.html("<span class='spinner'></span>");
        },
        success: function success(response) {
          modal.removeClass('loading');
          modalBody.html(response);
        },
        error: function error(e) {
          console.log(e);
          modal.removeClass('loading');
          modalBody.html("Something is wrong...");
        }
      });
    });

    //   $('select[name="rtcl-job-status"]').change();

    $('body').on('change', 'select[name="rtcl-job-status"]', function (e) {
      var status = $(this).val();
      var prevVal = $(this).prev().html();
      if (!status) {
        return false;
      }
      var applicationId = $(this).data('application-id'); // If you need to pass the post ID or other identifier

      $.ajax({
        url: rtclJobManager.ajaxurl,
        type: 'POST',
        data: {
          action: 'update_job_status',
          status: status,
          applicationId: applicationId // Pass the ID of the post or job application
        },
        success: function success(response) {
          if (response.success) {
            rtToast('Status updated successfully!');
            var resData = {
              value: response.data.value,
              prevVal: prevVal,
              id: applicationId
            };
            $(document).trigger('rtcl_job_status_change', resData);
          } else {
            alert('Error updating status.');
          }
        }
      });
    });
    $(document).on('rtcl_job_status_change', function (e, data) {
      var dataId = data.id;
      var value = data.value;
      var prevVal = data.prevVal;
      $('.rtclJobStatusLabel[data-id="' + dataId + '"]').removeClass(prevVal).addClass(value).html(value);
    });
  });
})(jQuery);
