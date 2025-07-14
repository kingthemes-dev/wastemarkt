;
(function ($) {
  $(document).ready(function () {
    var imgWrap = $('.rtcl-setting-image-wrap'),
      $logo = $('#rtcl_invoice-company_logo'),
      attachment_id = $logo.val(),
      $invoice_preview = $('.rtcl-invoice-preview');

    // Delete the preview image when "Remove Image" button clicked
    imgWrap.on('click', '.rtcl-remove-image', function (e) {
      e.preventDefault();
      var self = $(this),
        target = self.parents('.rtcl-setting-image-wrap');
      if (rtclSettingsImageConfirm) {
        var new_id = target.find('#rtcl_invoice-company_logo').val();
        if (attachment_id !== new_id) {
          var logo_src = target.find('.image-preview-wrapper img').attr('src');
          $invoice_preview.find('.invoice-logo-pdf').attr('src', logo_src);
        }
      }
    });

    // Change the preview image when "Add Image" button clicked
    imgWrap.on("rtclImageChangeEvent", ".rtcl-add-image", function (e, imageSrc) {
      $invoice_preview.find('.invoice-logo-pdf').attr('src', imageSrc);
    });

    // company name
    $('#rtcl_invoice-company_name').on('keyup', function () {
      var $self = $(this);
      $invoice_preview.find('.company-title').text($self.val());
    });

    // company address
    $('#rtcl_invoice-company_address').on('keyup', function () {
      var $self = $(this);
      $invoice_preview.find('.company-address').text($self.val());
    });

    // footer note
    $('#rtcl_invoice-footer_text').on('keyup', function () {
      var $self = $(this);
      $invoice_preview.find('.footer-note').text($self.val());
    });
  });
})(jQuery);
