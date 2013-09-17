@extends('layout')

@section('title')
<title>View Top Uploads</title>
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
@stop

@section('content')
<div class="page-header">
  <h1>Top Cats <small>(Minimum 5 votes)</small></h1>
</div>
<?php 
	$topUploadCount = 1; 

	if(Input::has('page') && Input::get('page') > 0){
		$topUploadCount += ((Input::get('page')-1) * $paginateSize);
	}
?>
@foreach($uploads as $upload)
<div class="well">
	<div class="row upload-box" id="row_for_upload_{{ $upload->id }}" style="padding-bottom:1cm;">
		<div class="span3">
			<a href="{{ URL::to('rate') }}#{{$upload->id}}">
			<img src="/uploads/{{ $upload->id }}/image/thumb"/>
			</a>
		</div>
		<div class="span3">
			<small>
				@if ($topUploadCount === 1)
				<span class="badge badge-important">#{{ $topUploadCount++ }}</span>
				@elseif ($topUploadCount === 2)
				<span class="badge badge-warning">#{{ $topUploadCount++ }}</span>
				@elseif ($topUploadCount === 3)
				<span class="badge badge-success">#{{ $topUploadCount++ }}</span>
				@else
				<span class="badge badge-info">#{{ $topUploadCount++ }}</span>
				@endif
				<!--<strong>"{{{ $upload->name }}}"</strong>-->
				<span class="catName">{{{ $upload->name }}}</span>
				<br/>
				<strong>Average rating</strong>: {{ round($upload->getAvgRating(),1) }}
				<br/>
				<strong>Rated</strong>: {{ $upload->getNumRatings() }} times
				<br/>
				<strong>Share link</strong>: <a href="{{ URL::to('rate') }}#{{$upload->id}}">{{ URL::to('rate') }}#{{$upload->id}}</a>
			</small>
		</div>
	</div>
</div>
<br/>
@endforeach
<?php echo $uploads->links(); ?>
@stop