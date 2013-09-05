$(document).ready(function () {
   $(function () {
      $("button.btn-rate").click(function () {
         return rateImage($(this).text());
      });

      $('.clear-flaggings').click(function () {
         confirm('Are you sure you want to clear flaggings for this image?') && clearFlagging($(this).attr('uploadid'));
         return false;
      });

      $('.hide-image').click(function () {
         confirm('Are you sure you want to hide this image?') && hideUploadById($(this).attr('uploadid'));
         return false;
      });

      $('.delete-image').click(function () {
         confirm('Are you sure you want to delete this image?') && deleteUploadById($(this).attr('uploadid'));
         return false;
      });
   });
});

function hideUploadById(uploadId) {
   console.log('(not working yet) hide upload ' + uploadId);

   var rowToRemove = $('#row_for_upload_' + uploadId);
   rowToRemove.animate({
      "opacity": "0",
   }, {
      "complete": function () {
         rowToRemove.hide();
      }
   });
}

function clearFlagging(uploadId) {
   console.log('clear flagging for upload ' + uploadId);

   var rowToRemove = $('#row_for_upload_' + uploadId);
   $.get("/cat/" + uploadId + "/flag/clear", {})
      .done(function (data) {
         console.log('clearFlagging returned "' + data + '"');
         $('#flagged_count_' + uploadId).text('0');
      })
      .fail(function (data) {
         alert("Failed to clear flagging: " + data);
      });
}

function removeRowByUploadId(uploadId) {
   var rowToRemove = $('#row_for_upload_' + uploadId);
   rowToRemove.animate({
      "opacity": "0",
   }, {
      "complete": function () {
         rowToRemove.remove();
      }
   });
}

function deleteUploadById(uploadId) {
   console.log('deleting upload ' + uploadId);

   $.get("uploads/delete/" + uploadId, {})
      .done(function (data) {
         console.log('delete returned "' + data + '"');
         removeRowByUploadId(uploadId);
      })
      .fail(function (data) {
         alert("Failed to delete image: " + data);
      });
}