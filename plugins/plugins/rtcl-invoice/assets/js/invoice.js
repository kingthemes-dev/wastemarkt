;
(function ($) {
  $(document).ready(function () {

    /*$(document).on('click', '.invoice-download-btn', function (e) {
        e.preventDefault();
         var $this = $(this),
            $row = $this.closest('tr'),
            order_id = $row.data('order-id');
         var data = {
            order_id: order_id,
            action: 'rtcl_invoice_download',
            __rtcl_wpnonce: rtcl.__rtcl_wpnonce,
        }
         $.ajax({
            url: rtcl.ajaxurl,
            data: data,
            type: 'POST',
            beforeSend: function () {
                $row.rtclBlock();
            },
            success: function (res) {
                $row.rtclUnblock();
            },
            error: function (e) {
                $row.rtclUnblock();
            }
        });
     });*/
  });
})(jQuery);
