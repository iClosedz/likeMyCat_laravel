@extends('layout')

@section('title')
<title>Like My Cat</title>
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

@section('additionalScriptTags')
<script src="/assets/js/jquery.raty.min.js"></script>
<script src="/assets/js/catRatings.js"></script>
@stop

@section('startOfBody')
<div id="fb-root"></div>
<script>(function(d, s, id) {
  var js, fjs = d.getElementsByTagName(s)[0];
  if (d.getElementById(id)) return;
  js = d.createElement(s); js.id = id;
  js.src = "//connect.facebook.net/en_US/all.js#xfbml=1&appId=487690214655188&status=0";
  fjs.parentNode.insertBefore(js, fjs);
}(document, 'script', 'facebook-jssdk'));</script>
@stop

@section('content')
<div class="row-fluid">
	<div class="span8 well" id="current_box" style="text-align: center;">
		<h2>Rate this cat!</h2>
		<div class="btn-toolbar btn-rating-bar visible-phone">
			<span id="star-phone"></span>
			<br/><br/>
			<div class="btn-group">
				<button type="submit" name="skip" value="skip" class="btn btn-mini btn-primary getNextImage"><i class="icon-step-forward"></i> Skip Image</button>
				<button type="submit" name="report" value="report" class="btn btn-mini btn-danger flagImage"><i class="icon-minus-sign"></i> Inappropriate</button>
			</div>
		</div>
		<div class="btn-toolbar btn-rating-bar hidden-phone">
			<span id="star"></span>
			<br/><br/>
			<div class="btn-group">
				<button type="submit" name="skip" value="skip" class="btn btn-mini btn-primary getNextImage"><i class="icon-step-forward"></i> Skip Image</button>
				<button type="submit" name="report" value="report" class="btn btn-mini btn-danger flagImage"><i class="icon-minus-sign"></i> Inappropriate</button>
			</div>
		</div>
		<img id="img_upload_current" alt="Current Image" src="/assets/img/placeholder_600.gif" />
		<p class="lead" style="margin-top:.5cm;"><span id="cat_name" class="catName"></span></p>
		<a href="#" id="share_url"><i class="icon-share"></i> Share</a>
		<hr/>
		<div id="comments">
			<!--<div class="fb-comments" data-href="https://www.likemycat.com/rate" data-width="400"></div>-->
		</div>
	</div>
	<div class="span2 well" style="text-align: center;">
		<div id="previous_div_box" class="" style="visibility:hidden; display: none;">
			<a class="getPrevImage" id="previous_link" href="#"><h4>&laquo; Previous</h4></a>
			<img class="getPrevImage" id="img_upload_prev" alt="Previous Image" src="/assets/img/placeholder_130.gif" />
			<div>
				<small>Rating: <span id="avg_rating_display">0.0</span>
					<br/>
					(<span id="rating_count_display">0</span> votes)
				</small>
			</div>
		</div>
		<div>
			<a class="getNextImage" href="#" id="next_link"><h4>Next &raquo;</h4></a>
			<img class="getNextImage" id="img_upload_next" alt="Next Image" src="/assets/img/placeholder_130.gif" />
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