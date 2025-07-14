;
(function ($) {
  $(document).ready(function () {
    $('.rtcl-marketplace-payout-status-btn').on('click', 'button', function () {
      var $this = $(this),
        $wrapper = $this.closest('.rtcl-marketplace-payout-status-wrap'),
        payout_id = $this.attr('data-id'),
        status = $wrapper.find('#payout-status-dropdown').val();
      toastr.options.positionClass = 'toast-bottom-right';
      $.ajax({
        url: rtcl.ajaxurl,
        type: "POST",
        dataType: 'json',
        data: {
          action: 'rtcl_marketplace_update_payout_status',
          payout_id: payout_id,
          status: status,
          __rtcl_wpnonce: rtcl.__rtcl_wpnonce
        },
        beforeSend: function beforeSend() {
          //$('body').find('#toast-container').remove();
          $wrapper.rtclBlock();
        },
        success: function success(res) {
          if (res.success) {
            toastr.success(res.message);
          } else {
            toastr.error(res.message);
          }
          $wrapper.rtclUnblock();
        },
        error: function error(err) {
          toastr.error(err.msg);
          $wrapper.rtclUnblock();
        }
      });
    });
  });
})(jQuery);
