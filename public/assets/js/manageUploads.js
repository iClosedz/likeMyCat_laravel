function hideUploadById(uploadId){
	console.log('(not working yet) hide upload ' + uploadId);
	var rowToRemove = $('#row_for_upload_' + uploadId);
	rowToRemove.animate({
		"opacity" : "0",
	},{
		"complete" : function() {
			rowToRemove.hide();
		}
	});
}

function removeRowByUploadId(uploadId){
	var rowToRemove = $('#row_for_upload_' + uploadId);
	rowToRemove.animate({
		"opacity" : "0",
	},{
		"complete" : function() {
			rowToRemove.remove();
		}
	});
}

function deleteUploadById(uploadId){
	console.log('deleting upload ' + uploadId);
	$.get("uploads/delete/" + uploadId, {} )
	.done(function(data) {
		console.log('delete returned "' + data + '"');
		removeRowByUploadId(uploadId);
	})
	.fail(function(data){
		alert("Failed to delete image: " + data);
	});
}