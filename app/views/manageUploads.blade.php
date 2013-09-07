@extends('layout')

@section('title')
<title>Manage Uploads</title>
@stop

@section('additionalScriptTags')
<script src="/assets/js/manageUploads.js"></script>
@stop

@section('content')
@if (Route::getCurrentRoute()->getPath() === '/admin/uploads')
<h1>Admin - Manage All Uploads</h1>
@else
<h1>Manage Uploads</h1>
@endif
<hr/>
<!-- using eager loading ??? -->
@foreach($uploads as $upload)
<div>
	<div class="row upload-box" id="row_for_upload_{{ $upload->id }}" style="padding-bottom:1cm;">
		<div class="span3">
			<a href="/cat/{{ $upload->id }}/image">
				@if (Route::getCurrentRoute()->getPath() === '/admin/uploads')
				<img src="/admin/cat/{{ $upload->id }}/image/thumb"/>
				@else
				<img src="/cat/{{ $upload->id }}/image/thumb"/>
				@endif
			</a>
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
				@if (Route::getCurrentRoute()->getPath() === '/admin/uploads')
				<strong>Flagged as inappropriate</strong>: <span id="flagged_count_{{$upload->id}}">{{ $upload->getNumFlagged() }}</span> times
				<br/>
				@endif
				<strong>Share link</strong>: <a href="{{ URL::to('rate') }}#{{$upload->id}}">{{ URL::to('rate') }}#{{$upload->id}}</a>
			</small>
		</div>
		<div class="btn-group">
			@if (Route::getCurrentRoute()->getPath() === '/admin/uploads')
			<?php $isHidden = (!empty($upload->deleted_at)) ? 'true' : 'false'; ?>
			<btn class="btn btn-primary clear-flaggings" uploadid="{{$upload->id}}">Clear Flagging</btn>
				@if($isHidden === 'true')
				<btn class="btn btn-info hide-image" uploadid="{{$upload->id}}" hideafter="false" ishidden="{{ $isHidden }}">Restore</btn>
				@else
				<btn class="btn btn-warning hide-image" uploadid="{{$upload->id}}" hideafter="false" ishidden="{{ $isHidden }}">Hide</btn>
				@endif
				<btn class="btn btn-danger delete-image" uploadid="{{$upload->id}}">Delete</btn>
			@else
			<btn class="btn btn-danger hide-image" uploadid="{{$upload->id}}" hideafter="true">Delete</btn>
			@endif
			</div>
		</div>
		<br/>
		@endforeach
		<?php echo $uploads->links(); ?>
		@stop