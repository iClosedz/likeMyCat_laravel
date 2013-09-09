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

function restoreUploadById(uploadId) {
	console.log('restore upload ' + uploadId);

	$.get("/admin/uploads/restore/" + uploadId, {})
	.done(function (data) {
		console.log('restore returned "' + data + '"');
	})
	.fail(function (data) {
		alert("Failed to restore image: " + data);
	});
}

function hideUploadById(uploadId) {
	console.log('hide upload ' + uploadId);

	$.get("/admin/uploads/hide/" + uploadId, {})
	.done(function (data) {
		console.log('delete returned "' + data + '"');
	})
	.fail(function (data) {
		alert("Failed to delete image: " + data);
	});
}

function clearFlagging(uploadId) {
	console.log('clear flagging for upload ' + uploadId);

	var rowToRemove = $('#row_for_upload_' + uploadId);
	$.get("/uploads/" + uploadId + "/flag/clear", {})
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

	$.get("/admin/uploads/delete/" + uploadId, {})
	.done(function (data) {
		console.log('delete returned "' + data + '"');
		removeRowByUploadId(uploadId);
	})
	.fail(function (data) {
		alert("Failed to delete image: " + data);
	});
}