@extends('layout')

@section('title')
<title>Manage Uploads</title>
@stop

@section('content')
	<h1>Manage Uploads</h1>
	@foreach($uploads as $upload)
	<div>
    <div class="row upload-box" id="row_for_upload_215" style="padding-bottom:1cm;">
		<div class="span3">
			<a href="/cat/{{ $upload->id }}/image"><img src="/cat/{{ $upload->id }}/image/thumb"/></a>
		</div>
		<div class="span3">
			<small>
				<strong>"{{{ $upload->name }}}"</strong>
				<br/>
				<strong>Uploaded by: </strong> <a href="mailto:{{{ $upload->user->email }}}">{{{ $upload->user->email }}}</a>
				<br/>
				<strong>Average rating</strong>: {{ $upload->getAvgRating() }}
				<br/>
				<strong>Rated</strong>: {{ $upload->getNumRatings() }} times
				<br/>
				<strong>Share link</strong>: <a href="https://www.likemycat.com/rate.php#215">https://www.likemycat.com/rate.php#215</a>
			</small>
		</div>
		<div class="btn-group">
		<btn class="btn btn-warning" onclick="confirm('Are you sure you want to hide this image?') && hideUploadById(215);">Hide on site</btn>
		<btn class="btn btn-danger" onclick="confirm('Are you sure you want to delete this image?') && deleteUploadById(215);">Delete</btn>
		</div>
		</div>
		<br/>
	@endforeach
@stop