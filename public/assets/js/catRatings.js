var prevImage = new catImage(-1, -1, -1, -1, -1, -1);
var currentImage = new catImage(-1, -1, -1, -1, -1, -1);
var nextImage = new catImage(-1, -1, -1, -1, -1, -1);
var firstTimeLoad = true;

$(document).ready(function () {
   //console.log("The DOM is now loaded and can be manipulated.");
   getImageData();
   getTodaysTopCat();
   getTopCatAllTime();

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


      $.fn.raty.defaults.path = '/assets/img';
      $('#star').raty({ 
         number: 10,
         size     : 24,
         starHalf : 'star-half-big.png',
         starOff  : 'star-off-big.png',
         starOn   : 'star-on-big.png',
         click: function(score, evt) {
            rateImage(score);
            $('#star').raty('cancel', false);
            return false;
         }
      });

      $('#star-phone').raty({ 
         number: 10,
         starHalf : 'star-half-big.png',
         starOff  : 'star-off-big.png',
         starOn   : 'star-on-big.png',
         click: function(score, evt) {
            /* delay on phone so user can see rating before submit */
            setTimeout(function() {
               rateImage(score);
               $('#star-phone').raty('cancel', false);
               return false;
            }, 800);
         }
      });
   });
});

function getBaseUrl(){
   if (!window.location.origin){
      window.location.origin = window.location.protocol+"//"+window.location.host;
   }

   if (!window.location.origin){
      return 'https://www.likemycat.com';
   }

  return window.location.origin;
}

function getTodaysTopCat(){
  $.get("/uploads/top/day", {})
  .done(function (data) {
   console.log('rating returned "' + data + '"');

   if(data['success'] === true){
      document.getElementById('top_cat_name').innerHTML = data['results']['name'];
      document.getElementById('top_cat_rating').innerHTML = parseFloat(data['results']['rating']).toFixed(1);
      document.getElementById('img_top_cat').src = '/uploads/' + data['results']['upload_id'] + '/image/thumb';
      $("#top_cat_link").attr("href", '/rate#' + data['results']['upload_id']);
      $("#top_cat_link").bind('click', function() {
         getImageData(data['results']['upload_id']);
         return false;
      });
   }
})
  .fail(function (data) {
   console.log("getTodaysTopCat failed: " + data);
});
}

function getTopCatAllTime(){
  $.get("/uploads/top/ever", {})
  .done(function (data) {
   console.log('rating returned "' + data + '"');

   if(data['success'] === true){
      document.getElementById('top_cat_ever_name').innerHTML = data['results']['name'];
      document.getElementById('top_cat_ever_rating').innerHTML = parseFloat(data['results']['rating']).toFixed(1);
      document.getElementById('img_top_cat_ever').src = '/uploads/' + data['results']['upload_id'] + '/image/thumb';
      $("#top_cat_ever_link").attr("href", '/rate#' + data['results']['upload_id']);
      $("#top_cat_ever_link").bind('click', function() {
         getImageData(data['results']['upload_id']);
         return false;
      });
   }

})
  .fail(function (data) {
   console.log("getTodaysTopCat failed: " + data);
});
}

function loadNextImageById(uploadId){
   console.log('loadNextImageById() ' + uploadId);
}

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
   var curUrl = getBaseUrl() + '/rate#' + shareId;
   window.location.hash = shareId;
   document.getElementById('share_url').href = curUrl;

   updateLikeUrl(curUrl);
   updateCommentsUrl(curUrl);
}

function updateCommentsUrl(url){
   console.log('updateCommentsUrl(' + url + ')');
   document.getElementById('comments').innerHTML='<div class="fb-comments" data-href="'+url+'" data-num-posts="10" data-width="400"></div>'; 
   FB.XFBML.parse(document.getElementById('comments'));
}

function updateLikeUrl(url){
   console.log('updateLikeUrl(' + url + ')');
   document.getElementById('like').innerHTML='<div class="fb-like" data-href="'+url+'" data-width="300" data-layout="button_count" data-show-faces="true" data-send="true"></div>'; 
   FB.XFBML.parse(document.getElementById('like'));
}

function getImageData(uploadId) {
   console.log('getImageData (firstTimeLoad: ' + firstTimeLoad + ')');
   var shareImageId = '';
   var hasUploadId = typeof uploadId !== 'undefined';
   
   if(hasUploadId){
      shareImageId = uploadId;
      console.log('uploadId: ' + uploadId);
   }
   else if (firstTimeLoad) {
      shareImageId = getShareId();
      console.log('shareImageId: ' + shareImageId);
   }

   $.post("/rate/getUploads", {
      'how_many_results': ((firstTimeLoad || hasUploadId) ? 2 : 1),
      'exclude_image_id': ((firstTimeLoad || hasUploadId) ? -1 : nextImage.uploadId),
      'share_image_id': (shareImageId === '' ? -1 : shareImageId)
   })
   .done(function (data) {
      console.log('getUploadService.php returned "' + data + '"');
      if (firstTimeLoad || hasUploadId) {
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