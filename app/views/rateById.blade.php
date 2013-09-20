@extends('layout')

@section('title')
<title>Like My Cat</title>
@stop

@section('additionalHeadData')
<?php
function baseUrl(){
  return sprintf(
    "%s://%s",
    isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] != 'off' ? 'https' : 'http',
    $_SERVER['HTTP_HOST']
  );
}

$baseUrl = baseUrl();
?>
<meta property="og:url" content="{{ URL::route('get rate/{upload_id}', $currentUpload->id) }}" />
<meta property="og:image" name="fb_image_metatag" content="{{ URL::route('get uploads/{upload_id}/image', $currentUpload->id) }}" />
<meta property="og:title" content="Like My Cat" />
<meta property="og:description" content="{{{ $currentUpload->name }}}" />`
@stop

@section('customStyles')
.catName{
font-style:italic;
}
.catName:before{
content:"\"";
}
.catName:after{
content:"\"";
}
.btn-rating-bar{
margin-bottom: 10px;
}

.fb-comments, .fb-comments span, .fb-comments.fb_iframe_widget span iframe {
    width: 100% !important;
}

@stop

@section('startOfBody')
<div id="fb-root"></div>
<script>
  window.fbAsyncInit = function() {
    // init the FB JS SDK
    FB.init({
      appId      : '487690214655188',                        // App ID from the app dashboard
      channelUrl : '//www.likemycat.com/channel.php', // Channel file for x-domain comms
      status     : true,                                 // Check Facebook Login status
      xfbml      : true                                  // Look for social plugins on the page
    });

    // Additional initialization code such as adding Event Listeners goes here

     FB.Event.subscribe('comment.create',
         function(response) {
            //alert('You liked the URL: ' + response);
            //$("#comments").hide().fadeIn('fast');
            console.log('You liked the URL: ' + response);
 			}
  		);
  };

  // Load the SDK asynchronously
  (function(d, s, id){
     var js, fjs = d.getElementsByTagName(s)[0];
     if (d.getElementById(id)) {return;}
     js = d.createElement(s); js.id = id;
     js.src = "//connect.facebook.net/en_US/all.js";
     fjs.parentNode.insertBefore(js, fjs);
   }(document, 'script', 'facebook-jssdk'));
  </script>
@stop

@section('additionalScriptTags')
<script src="/assets/js/jquery.raty.min.js"></script>
<!--<script src="/assets/js/catRatings.js"></script>-->
<script>
  /* begin catratings static */
$(document).ready(function () {

	$(function () {
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
</script>
/* end catratings static */
@stop

@section('content')
<div class="row-fluid">
	<div class="span8 well" id="current_box" style="text-align: center;">
		<h2>Rate this cat!</h2>
		<div class="btn-toolbar btn-rating-bar visible-phone">
			<span id="star-phone"></span>
			<br/><br/>
			<div class="btn-group">
				<a href="{{ URL::route('get rate/{upload_id}', $nextUpload->id) }}"><button type="submit" name="skip" value="skip" class="btn btn-mini btn-primary getNextImage"><i class="icon-step-forward"></i> Skip Image</button></a>
				<button type="submit" name="report" value="report" class="btn btn-mini btn-danger flagImage"><i class="icon-minus-sign"></i> Inappropriate</button>
			</div>
		</div>
		<div class="btn-toolbar btn-rating-bar hidden-phone">
			<span id="star"></span>
			<br/><br/>
			<div class="btn-group">
				<a href="{{ URL::route('get rate/{upload_id}', $nextUpload->id) }}"><button type="submit" name="skip" value="skip" class="btn btn-mini btn-primary"><i class="icon-step-forward"></i> Skip Image</button></a>
				<button type="submit" name="report" value="report" class="btn btn-mini btn-danger flagImage"><i class="icon-minus-sign"></i> Inappropriate</button>
			</div>
		</div>
		<img id="img_upload_current" alt="Current Image" src="{{ URL::route('get uploads/{upload_id}/image', $currentUpload->id) }}" />
		<p class="lead" style="margin-top:.5cm;"><span id="cat_name" class="catName">{{{ $currentUpload->name }}}</span></p>
		<a href="{{ URL::route('get rate/{upload_id}', $currentUpload->id) }}" id="share_url"><i class="icon-share"></i> Share</a>
		<!--<div class="fb-like" data-href="https://www.daknag.me/rate" data-width="300" data-layout="button_count" data-show-faces="true" data-send="true"></div>-->
		<div id="like">
		</div>
		<hr/>
		<div id="comments">
			<!--<div class="fb-comments" data-href="https://www.likemycat.com/rate" data-width="400"></div>-->
		</div>
	</div>
	<div class="span2 well" style="text-align: center;">
		@if (isset($previousUpload))
		<div id="previous_div_box">
			<a class="getPrevImage" id="previous_link" href="{{ URL::route('get rate/{upload_id}', $previousUpload->id) }}"><h4>&laquo; Previous</h4>
				<img class="getPrevImage" id="img_upload_prev" alt="Previous Image" src="{{ URL::route('get uploads/{upload_id}/image/thumb', $previousUpload->id) }}" />
			</a>
			<div>
				<small>Rating: <span id="avg_rating_display">{{ sprintf("%.1f", $previousUpload->getAvgRating()) }}</span>
					<br/>
					(<span id="rating_count_display">{{ $previousUpload->getNumRatings() }}</span> votes)
				</small>
			</div>
		</div>
		@endif
		<div>
			<a class="getNextImage" href="{{ URL::route('get rate/{upload_id}', $nextUpload->id) }}" id="next_link"><h4>Next &raquo;</h4>
				<img class="getNextImage" id="img_upload_next" alt="Next Image" src="{{ URL::route('get uploads/{upload_id}/image/thumb', $nextUpload->id) }}" />
			</a>
		</div>
		<hr/>
		<div>
			<h5>Today's Top Cat:</h5>
			<a href="#" id="top_cat_link"><img id="img_top_cat" alt="Top Cat" src="/assets/img/placeholder_130.gif" /></a>
			<br/>
			<span class="catName" id="top_cat_name"></span>
			<br/>
			<small>Rating: <span id="top_cat_rating"></span></small>
		</div>
		<div>
			<h5>All Time Leader:</h5>
			<a href="" id="top_cat_ever_link"><img id="img_top_cat_ever" alt="Top Cat Ever" src="/assets/img/placeholder_130.gif"/></a>
			<br/>
			<span class="catName" id="top_cat_ever_name"></span>
			<br/>
			<small>Rating: <span id="top_cat_ever_rating"></span></small>
		</div>
		<hr/>
		<div>
			<h4><a href="{{ URL::to('uploads/top/view') }}">View Top Cats</a></h4>
		</div>
	</div>
</div>
@stop