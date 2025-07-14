;
(function ($) {
  $(document).ready(function () {
    $("#rtcl-payout-send-request").on("click", function (e) {
      e.preventDefault();
      var $this = $(this),
        $wrapper = $this.closest('.rtcl-payout-history-wrap'),
        user_id = $this.attr('data-id');
      $.ajax({
        url: rtcl.ajaxurl,
        type: "POST",
        dataType: 'json',
        data: {
          action: 'rtcl_marketplace_payout_request',
          user_id: user_id,
          __rtcl_wpnonce: rtcl.__rtcl_wpnonce
        },
        beforeSend: function beforeSend() {
          $('body').find('#toast-container').remove();
          $wrapper.rtclBlock();
        },
        success: function success(res) {
          if (res.success) {
            toastr.success(res.message);
            setTimeout(function () {
              location.reload();
            }, 300);
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
    $("form.marketplace-order-note-form").on("submit", function (e) {
      e.preventDefault();
      var $form = $(this),
        post_id = $form.find('input[name="post_id"]').val(),
        user_id = $form.find('input[name="user_id"]').val(),
        note = $form.find('textarea[name="note"]').val(),
        note_type = $form.find('select[name="note_type"]').val();
      console.log(post_id);
      $.ajax({
        url: rtcl.ajaxurl,
        type: "POST",
        dataType: 'json',
        data: {
          action: 'rtcl_marketplace_add_order_note',
          post_id: post_id,
          user_id: user_id,
          note: note,
          note_type: note_type,
          __rtcl_wpnonce: rtcl.__rtcl_wpnonce
        },
        beforeSend: function beforeSend() {
          $('body').find('#toast-container').remove();
          $form.rtclBlock();
        },
        success: function success(res) {
          if (res.success) {
            toastr.success(res.message);
            setTimeout(function () {
              location.reload();
            }, 300);
          } else {
            toastr.error(res.message);
          }
          $form.rtclUnblock();
        },
        error: function error(err) {
          toastr.error(err.msg);
          $form.rtclUnblock();
        }
      });
    });
  });
})(jQuery);
