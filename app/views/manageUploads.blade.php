@extends('layout')

@section('title')
<title>Manage Uploads</title>
@stop

@section('additionalScriptTags')
<script src="/assets/js/manageUploads.js"></script>
@stop

@section('content')
<h1>Manage Uploads</h1>
<hr/>
<!-- using eager loading ??? -->
@foreach($uploads as $upload)
<div>
	<div class="row upload-box" id="row_for_upload_{{ $upload->id }}" style="padding-bottom:1cm;">
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
				@if ($user->hasRole('admin'))
				<strong>Flagged as inappropriate</strong>: <span id="flagged_count_{{$upload->id}}">{{ $upload->flagged()->count() }}</span> times
				<br/>
				@endif
				<strong>Share link</strong>: <a href="{{ URL::to('rate') }}#{{$upload->id}}">{{ URL::to('rate') }}#{{$upload->id}}</a>
			</small>
		</div>
		<div class="btn-group">
			@if ($user->hasRole('admin'))
			<btn class="btn btn-primary clear-flaggings" uploadid="{{$upload->id}}">Clear Flagging</btn>
			@endif
			<btn class="btn btn-warning hide-image" uploadid="{{$upload->id}}">Hide on site</btn>
			<btn class="btn btn-danger delete-image" uploadid="{{$upload->id}}">Delete</btn>
		</div>
	</div>
	<br/>
	@endforeach
	@stop