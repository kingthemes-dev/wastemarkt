var frame;
;
(function ($) {
  $(document).ready(function () {
    $('#rtcl-marketplace-download').sortable({
      axis: 'y',
      // Allow vertical sorting only
      handle: '.rtcl-sort-download',
      items: 'tr',
      cursor: 'move',
      scrollSensitivity: 40,
      forcePlaceholderSize: true,
      helper: 'clone',
      opacity: 0.65
    });
    $('body').on('click', '#addRowButton', function (e) {
      e.preventDefault();
      var newRow = "\n            <tr>\n                <td class=\"rtcl-sort-download\">\u2630</td>\n                <td class=\"file_name\" width=\"30%\">\n                    <input type=\"text\" class=\"input_text\" placeholder=\"File name\" name=\"_rtcl_file_names[]\" value=\"\" required/>\n                </td>\n                <td class=\"file_url\">\n                    <input type=\"text\" class=\"input_text file_url\" placeholder=\"http://\" name=\"_rtcl_file_urls[]\" value=\"\" required/>\n                </td>\n                <td class=\"file_url_choose\" width=\"1%\"><a href=\"#\" class=\"button upload_file_button\">Choose file</a><input class=\"upload-file-button\" type=\"file\" name=\"rtcl_marketplace_upload_file\" accept=\"".concat(rtclMarketPlace.allow_format, "\"></td>\n                <td width=\"1%\"><a href=\"#\" class=\"rtcl-remove-download\">Delete</a></td>\n            </tr>");
      $('#rtcl-marketplace-download').append(newRow);
    });
    $(document).on('click', '.rtcl-remove-download', function () {
      if (confirm('Do you want to delete this item?')) {
        //$(this).closest('tr').remove();
        $(this).closest('tr').fadeOut(500, function () {
          $(this).remove();
        });
      }
      return false;
    });
    $('body').on('blur', '.rtcl-marketplace-downloadable-meta input.file_url', function () {
      var checkFileName = $(this).closest('tr').find('.file_name input');
      if (checkFileName.val() === '') {
        $(this).closest('tr').addClass('empty-field');
        checkFileName.focus();
      } else {
        $(this).closest('tr').removeClass('empty-field');
      }
    });
    $('body').on('change', '.file_name input', function () {
      if ($(this).val()) {
        $(this).closest('tr').removeClass('empty-field');
      }
    });
    function rtclToggleCheckbox(checkboxSelector, elementSelector) {
      function toggleElement(status) {
        if ($(checkboxSelector).is(':checked')) {
          $(elementSelector).slideDown();
        } else {
          if (status) {
            $(elementSelector).hide();
          } else {
            $(elementSelector).slideUp();
          }
        }
      }

      // Initial check on page load
      toggleElement(true);

      // Event listener for checkbox change
      $(checkboxSelector).change(function () {
        toggleElement();
      });
    }
    rtclToggleCheckbox('#_rtcl_enable_download', '.rtcl-download-file-wrapper');
    rtclToggleCheckbox('#_rtcl_manage_stock', '.rtcl-marketplace-wrap');
    $(document).on("click", ".upload_file_button", function (event) {
      event.preventDefault();
      var file_path_field = $(this).closest('tr').find('td.file_url input');
      var frame = wp.media({
        title: "Choose File",
        button: {
          text: "Insert File"
        },
        multiple: false // Set to true if you want to allow multiple file uploads
      });
      frame.on('select', function () {
        var selection = frame.state().get('selection').first().toJSON();
        var fileSize = selection.filesizeInBytes;
        var maxSize = rtclMarketPlace.max_file_size * 1024 || 1048576; // 1MB in bytes

        if (fileSize > maxSize) {
          alert("Your file size is ".concat(Math.ceil(fileSize / 1024), " KB, which exceeds the maximum upload limit of ").concat(rtclMarketPlace.max_file_size, " KB."));
          frame.open();
          return false;
        }
        var file_path = selection.url;
        file_path_field.val(file_path).trigger('change');
      });
      frame.open();
      return false;
    });
    $(document).on('change', '.upload-file-button', function () {
      var fileInput = $(this)[0];
      var file = fileInput.files[0];
      var parentRow = $(this).closest('tr');
      var fileSize = file.size;
      var maxSize = rtclMarketPlace.max_file_size * 1024 || 1048576; // 1MB in bytes

      if (fileSize > maxSize) {
        alert("Your file size is ".concat(Math.ceil(fileSize / 1024), " KB, which exceeds the maximum upload limit of ").concat(rtclMarketPlace.max_file_size, " KB."));
        return false;
      }
      var reader = new FileReader();
      reader.onload = function (e) {
        var binaryData = e.target.result;
        var base64String = window.btoa(binaryData);
        $.ajax({
          url: rtclMarketPlace.ajaxurl,
          type: 'POST',
          data: {
            action: 'handle_ajax_file_upload',
            nonce: rtclMarketPlace.nonce,
            file: base64String,
            filename: file.name,
            filetype: file.type
          },
          beforeSend: function beforeSend() {
            parentRow.addClass('loading');
          },
          success: function success(response) {
            if (response.success) {
              parentRow.find('.file_url').val(response.data.url);
            } else {
              alert('File upload failed: ' + response.data.error);
            }
            parentRow.removeClass('loading');
          },
          error: function error(xhr, status, _error) {
            alert('An error occurred: ' + _error);
            parentRow.removeClass('loading');
          }
        });
      };
      reader.readAsBinaryString(file);
    });
  });
})(jQuery);
