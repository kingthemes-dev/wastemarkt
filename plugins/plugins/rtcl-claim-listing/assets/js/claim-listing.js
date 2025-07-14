;
(function ($) {
  $(document).ready(function () {
    $(".rtcl-claim-document-wrap .rtcl-media-action").on('click', 'span.add', function () {
      var addBtn = $(this),
        documentFile = $("<input type='file' style='position:absolute;left:-9999px' />");
      $('body .rtcl-claim-listing-form').append(documentFile);
      if (navigator.userAgent.match(/MSIE ([0-9]+)\./)) {
        documentFile.trigger('change');
      } else {
        documentFile.trigger('click');
      }
      documentFile.on('change', function () {
        var fileItem = $(this),
          file_wrap = addBtn.parents(".rtcl-claim-document"),
          file_holder = $('.other-document', file_wrap),
          file = fileItem[0].files[0],
          max_file_size = parseInt(rtcl_claim.max_file_size);
        if (file.type === 'application/pdf') {
          if (file.size <= max_file_size) {
            file_wrap.addClass('has-file');
            file_wrap.removeClass('no-file');
            file_holder.html("<span class='rtcl-document-name'>" + file.name + "</span>" + "<span class='rtcl-doc-remove' title='" + rtcl_claim.remove_text + "'><i class='rtcl-icon rtcl-icon-cancel'></i></span>");
          } else {
            alert(rtcl_claim.error_file_size);
          }
        } else {
          alert(rtcl_claim.error_extension);
        }
      });
    });
    $(".rtcl-claim-document-wrap .other-document").on('click', 'span.rtcl-doc-remove', function () {
      var self = $(this),
        file_wrap = self.parents(".rtcl-claim-document"),
        file_holder = $('.other-document', file_wrap);
      if (confirm(rtcl_claim.confirm_text)) {
        file_wrap.addClass('no-file');
        file_wrap.removeClass('has-file');
        file_holder.html("");
        file_wrap.closest('.rtcl-claim-listing-form').find('input[type=file]').remove();
      }
    });
    $(document).on('submit', '.rtcl-claim-listing-form', function (e) {
      e.preventDefault();
      var $this = $(this),
        $wrapper = $this.closest('.rtcl-claim-listing-wrapper'),
        msgHolder = $("<div class='alert rtcl-response'></div>"),
        $form = $wrapper.find('form');
      var formData = new FormData(this);
      var fileItem = $this.find('input[type=file]');
      if (fileItem.length) {
        var file = fileItem[0].files[0];
        formData.append('document', file);
      }
      formData.append('rtcl_wpnonce', rtcl.__rtcl_wpnonce);
      formData.append('action', 'rtcl_claim_form_submit');
      $.ajax({
        url: rtcl_claim.ajax_url,
        data: formData,
        cache: false,
        contentType: false,
        processData: false,
        type: 'POST',
        beforeSend: function beforeSend() {
          $form.find('.alert.rtcl-response').remove();
          $form.find('button[type=submit]').prop("disabled", true);
          $form.rtclBlock();
        },
        success: function success(response) {
          $form.rtclUnblock();
          if (response.success) {
            msgHolder.removeClass('alert-danger').addClass('alert-success').html(response.message).appendTo($form);
          } else {
            $form.find('button[type=submit]').prop("disabled", false);
            msgHolder.removeClass('alert-success').addClass('alert-danger').html(response.message).appendTo($form);
          }
        },
        error: function error(jqXhr, json, errorThrown) {
          msgHolder.removeClass('alert-success').addClass('alert-danger').html(e.responseText).appendTo($form);
          $form.find('button[type=submit]').prop("disabled", false);
          $form.rtclUnblock();
        }
      });
    });
  });
})(jQuery);
