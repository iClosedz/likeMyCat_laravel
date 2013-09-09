var prevImage = new catImage(-1, -1, -1, -1, -1, -1);
var currentImage = new catImage(-1, -1, -1, -1, -1, -1);
var nextImage = new catImage(-1, -1, -1, -1, -1, -1);
var firstTimeLoad = true;

$(document).ready(function () {
   //console.log("The DOM is now loaded and can be manipulated.");
   getImageData();

   $(function () {
      $("button.btn-rate").click(function () {
         return rateImage($(this).text());
      });

      $('.getPrevImage').click(function () {
         loadPrevImage();
         return false;
      });

      $('.getNextImage').click(function () {
         getImageData();
         return false;
      });

      $('.flagImage').click(function () {
         flagImage(currentImage.upload_id);
         return false;
      });

   });
});

function flagImage(uploadId) {
   console.log('flaging imageId ' + currentImage.uploadId);

   $.get("/uploads/" + currentImage.uploadId + "/flag/", {})
      .done(function (data) {
         console.log('rating returned "' + data + '"');
         alert("Image flagged as inappropriate!");
         getImageData(); // get next set of images
      })
      .fail(function (data) {
         console.log("Failed: " + data);
      });
}

function catImage(uploadId, fileName, fileNameThumb, avgRating, numRatings, catName) {
   this.uploadId = uploadId;
   this.fileName = fileName;
   this.fileNameThumb = fileNameThumb;
   this.avgRating = avgRating;
   this.numRatings = numRatings;
   this.catName = catName;
}

function rateImage(rating) {
   console.log('rating imageId ' + currentImage.uploadId + ': ' + rating);

   $.get("/uploads/" + currentImage.uploadId + "/rate/" + rating, {})
      .done(function (data) {
         console.log('rating returned "' + data + '"');
         getImageData(); // get next set of images
      })
      .fail(function (data) {
         console.log("Failed: " + data);
      });
}

function updateImage(elementId, imageSrc) {
   console.log('updateImage(' + elementId + ', ' + imageSrc + ')');
   document.getElementById(elementId).src = imageSrc;
}

function updateCurrentCatNameDisplay() {
   var catName = (currentImage.catName == null) ? 'Cat' : currentImage.catName;
   document.getElementById('cat_name').innerHTML = catName;
}

function loadNextImage(data) {
   console.log('loadNextImage()');

   jQuery.extend(true, prevImage, currentImage);
   jQuery.extend(true, currentImage, nextImage);

   nextImage.uploadId = data['results'][0]['upload_id'];
   nextImage.fileName = data['results'][0]['file_name'];
   nextImage.fileNameThumb = data['results'][0]['file_name_thumb'];
   nextImage.avgRating = data['results'][0]['avg_rating'];
   nextImage.numRatings = data['results'][0]['num_ratings'];
   nextImage.catName = data['results'][0]['cat_name'];

   updateImage('img_upload_prev', prevImage.fileNameThumb);
   updateImage('img_upload_current', currentImage.fileName);
   updateImage('img_upload_next', nextImage.fileNameThumb);

   updatePrevRating();
   updateCurrentCatNameDisplay();
}

function loadPrevImage() {
   console.log('loadPrevImage()')
   var tmpImage = new catImage(-1, -1, -1, -1, -1, -1);
   jQuery.extend(true, tmpImage, prevImage);
   jQuery.extend(true, prevImage, currentImage);
   jQuery.extend(true, currentImage, tmpImage);

   updateImage('img_upload_prev', prevImage.fileNameThumb);
   updateImage('img_upload_current', currentImage.fileName);
   updatePrevRating();
   updateCurrentCatNameDisplay();
   setShareId(currentImage.uploadId);
}

function updatePrevRating() {
   var numRatings = (prevImage.numRatings == null) ? 0 : prevImage.numRatings;
   var avgRating = (prevImage.avgRating == null) ? '0.0' : parseFloat(prevImage.avgRating).toFixed(1);

   document.getElementById('avg_rating_display').innerHTML = avgRating;
   document.getElementById('rating_count_display').innerHTML = numRatings;
}

function getParameterByName(name) {
   name = name.replace(/[\[]/, "\\\[").replace(/[\]]/, "\\\]");
   var regex = new RegExp("[\\?&]" + name + "=([^&#]*)"),
      results = regex.exec(location.search);
   return results == null ? "" : decodeURIComponent(results[1].replace(/\+/g, " "));
}

function updateShareId(shareImageId) {
   document.location.search = 'share_image_id=' + shareImageId;
}

function isNumeric(input) {
   return (input - 0) == input && (input + '').replace(/^\s+|\s+$/g, "").length > 0;
}

function getShareId() {
   var shareImageId = '';
   if (window.location.hash.length > 1) {
      var tmpShareId = window.location.hash.substring(1);
      if (isNumeric(tmpShareId)) {
         shareImageId = tmpShareId;
      }
   }
   return shareImageId;
}

function setShareId(shareId) {
   console.log('setShareId(' + shareId + ')');
   window.location.hash = shareId;
   document.getElementById('share_url').href = 'https://www.likemycat.com/rate#' + shareId;
}

function getImageData() {
   console.log('getImageData (firstTimeLoad: ' + firstTimeLoad + ')');
   var shareImageId = '';
   if (firstTimeLoad) {
      shareImageId = getShareId();
      console.log('shareImageId: ' + shareImageId);
   }

   $.post("/rate/getUploads", {
      'how_many_results': (firstTimeLoad ? 2 : 1),
      'exclude_image_id': (firstTimeLoad ? -1 : nextImage.uploadId),
      'share_image_id': (shareImageId === '' ? -1 : shareImageId)
   })
      .done(function (data) {
         console.log('getUploadService.php returned "' + data + '"');
         if (firstTimeLoad) {
            currentImage.uploadId = data['results'][0]['upload_id'];
            currentImage.fileName = data['results'][0]['file_name'];
            currentImage.fileNameThumb = data['results'][0]['file_name_thumb'];
            currentImage.avgRating = data['results'][0]['avg_rating'];
            currentImage.numRatings = data['results'][0]['num_ratings'];
            currentImage.catName = data['results'][0]['cat_name'];

            nextImage.uploadId = data['results'][1]['upload_id'];
            nextImage.fileName = data['results'][1]['file_name'];
            nextImage.fileNameThumb = data['results'][1]['file_name_thumb'];
            nextImage.avgRating = data['results'][1]['avg_rating'];
            nextImage.numRatings = data['results'][1]['num_ratings'];
            nextImage.catName = data['results'][1]['cat_name'];

            updateImage('img_upload_current', currentImage.fileName);
            updateImage('img_upload_next', nextImage.fileNameThumb);
            updatePrevRating();
            updateCurrentCatNameDisplay();
            setShareId(currentImage.uploadId);
            firstTimeLoad = false;
         } else {
            if (prevImage.uploadId == -1) {
               document.getElementById('previous_div_box').style.display = '';
               document.getElementById('previous_div_box').style.visibility = 'visible';
            };
            loadNextImage(data);
            setShareId(currentImage.uploadId);
         }
      })
      .fail(function (data) {
         console.log("Failed: " + data.responseText);
      });
}