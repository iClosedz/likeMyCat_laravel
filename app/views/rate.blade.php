@extends('layout')

@section('title')
<title>Like My Cat</title>
@stop

@section('customStyles')
.btn-mini {
padding: 5px 9.5px;
}
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
@stop

@section('additionalScriptTags')
<script src="/assets/js/catRatings.js"></script>
@stop

@section('content')
<div class="row-fluid">
	<div class="span8" id="current_box" style="outline: black solid thin; text-align: center;">
		<h2>Rate this cat!</h2>
		<div class="btn-toolbar btn-rating-bar visible-phone">
			<div class="btn-group">
				<button name="rate_1" class="btn btn-mini btn-rate">1</button>
				<button name="rate_2" class="btn btn-mini btn-rate">2</button>
				<button name="rate_3" class="btn btn-mini btn-rate">3</button>
				<button name="rate_4" class="btn btn-mini btn-rate">4</button>
				<button name="rate_5" class="btn btn-mini btn-rate">5</button>
				<button name="rate_6" class="btn btn-mini btn-rate">6</button>
				<button name="rate_7" class="btn btn-mini btn-rate">7</button>
				<button name="rate_8" class="btn btn-mini btn-rate">8</button>
				<button name="rate_9" class="btn btn-mini btn-rate">9</button>
				<button name="rate_10" class="btn btn-mini btn-rate">10</button>
			</div>
			<div class="btn-group">
				<button type="submit" name="skip" value="skip" class="btn btn-mini btn-primary getNextImage"><i class="icon-step-forward"></i> Skip Image</button>
				<button type="submit" name="report" value="report" class="btn btn-mini btn-danger flagImage"><i class="icon-minus-sign"></i> Inappropriate</button>
			</div>
		</div>
		<div class="btn-toolbar btn-rating-bar hidden-phone">
			<div class="btn-group">
				<button name="rate_1" class="btn btn-rate">1</button>
				<button name="rate_2" class="btn btn-rate">2</button>
				<button name="rate_3" class="btn btn-rate">3</button>
				<button name="rate_4" class="btn btn-rate">4</button>
				<button name="rate_5" class="btn btn-rate">5</button>
				<button name="rate_6" class="btn btn-rate">6</button>
				<button name="rate_7" class="btn btn-rate">7</button>
				<button name="rate_8" class="btn btn-rate">8</button>
				<button name="rate_9" class="btn btn-rate">9</button>
				<button name="rate_10" class="btn btn-rate">10</button>
			</div>
			<div class="btn-group">
				<button type="submit" name="skip" value="skip" class="btn btn-mini btn-primary getNextImage"><i class="icon-step-forward"></i> Skip Image</button>
				<button type="submit" name="report" value="report" class="btn btn-mini btn-danger flagImage"><i class="icon-minus-sign"></i> Inappropriate</button>
			</div>
		</div>
		<img id="img_upload_current" alt="Current Image" src="/assets/img/placeholder_600.gif" />
		<p class="lead" style="margin-top:.5cm;"><span id="cat_name" class="catName"></span></p>
		<a href="#" id="share_url"><i class="icon-share"></i> Share</a>
	</div>
	<div class="span2" style="outline: black dotted thin; text-align: center; padding-bottom:.5cm;">
		<div id="previous_div_box" class="" style="visibility:hidden; display: none;">
			<a class="getPrevImage" id="previous_link" href="#"><h4>&laquo; Previous</h4></a>
			<img class="getPrevImage" id="img_upload_prev" alt="Previous Image" src="/assets/img/placeholder_130.gif" />
			<div>
				<small>Average rating: <span id="avg_rating_display">0.0</span>
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
			<img id="img_top_cat" alt="Top Cat" src="/assets/img/placeholder_130.gif" />
			<span class="catName" id="top_cat_name"></span>
			<br/>
			<small>Rating: <span id="top_cat_rating"></span></small>
		</div>
		<div>
			<h5>All Time Leader:</h5>
			<img id="img_top_cat_ever" alt="Top Cat Ever" src="/assets/img/placeholder_130.gif" />
			<span class="catName" id="top_cat_ever_name"></span>
			<br/>
			<small>Rating: <span id="top_cat_ever_rating"></span></small>
		</div>
	</div>
</div>
</div>
@stop