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
			if($(this).attr('hideafter') === 'true'){
				if(confirm('Are you sure you want to remove this image?')){
					hideUploadById($(this).attr('uploadid'));
					removeRowByUploadId($(this).attr('uploadid'));
				}
			} else {
				if($(this).attr('ishidden') === 'false'){
					//confirm('Are you sure you want to remove this image?') && 
					hideUploadById($(this).attr('uploadid'));
					$(this).attr('ishidden', 'true');
					$(this).addClass('btn-info');
					$(this).removeClass('btn-warning');
					$(this).text('Restore');	
				} else {
					restoreUploadById($(this).attr('uploadid'));
					$(this).attr('ishidden', 'false');
					$(this).addClass('btn-warning');
					$(this).removeClass('btn-info');
					$(this).text('Hide');
				}
			}

			return false;
		});

		$('.delete-image').click(function () {
			confirm('Are you sure you want to delete this image?') && deleteUploadById($(this).attr('uploadid'));
			return false;
		});
	});
});


function hideUploadById(uploadId) {
	console.log('hide upload ' + uploadId);

	$.get("/user/uploads/hide/" + uploadId, {})
	.done(function (data) {
		console.log('delete returned "' + data + '"');
	})
	.fail(function (data) {
		alert("Failed to delete image: " + data);
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
