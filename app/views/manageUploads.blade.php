@extends('layout')

@section('title')
<title>Manage Uploads</title>
@stop

@section('additionalScriptTags')
<script src="/assets/js/manageUploads.js"></script>
@stop

@section('content')
@if (strpos(Route::getCurrentRoute()->getPath(), '/admin/') === 0)
	@if (Route::getCurrentRoute()->getPath() === '/admin/uploads/flagged')
	<h1>Admin - Manage Flagged Uploads</h1>
	@else
	<h1>Admin - Manage All Uploads</h1>
	@endif
@else
	<h1>Manage Uploads</h1>
@endif
<hr/>
<div class="btn-toolbar" style="text-align: center;">
  <div class="btn-group">
  	@if (Route::getCurrentRoute()->getPath() === '/admin/uploads')
  	<a class="btn disabled" href="/admin/uploads"><i class="icon-list"></i> All</a>
  	@else
  	<a class="btn" href="/admin/uploads"><i class="icon-list"></i> All</a>
  	@endif
  	@if (Route::getCurrentRoute()->getPath() === '/admin/uploads/flagged')
  	<a class="btn disabled" href="/admin/uploads/flagged"><i class="icon-flag"></i> Flagged</a>
  	@else
  	<a class="btn" href="/admin/uploads/flagged"><i class="icon-flag"></i> Flagged</a>
  	@endif
  	@if (Route::getCurrentRoute()->getPath() === '/admin/uploads/hidden')
  	<a class="btn disabled" href="/admin/uploads/hidden"><i class="icon-minus-sign"></i> Hidden</a>
  	@else
  	<a class="btn" href="/admin/uploads/hidden"><i class="icon-minus-sign"></i> Hidden</a>
  	@endif
  </div>
</div>
<br/>
@foreach($uploads as $upload)
<div>
	<div class="row upload-box" id="row_for_upload_{{ $upload->id }}" style="padding-bottom:1cm;">
		<div class="span3">
			@if (strpos(Route::getCurrentRoute()->getPath(), '/admin/') === 0)
			<a href="/admin/uploads/{{ $upload->id }}/image">
			<img src="/admin/uploads/{{ $upload->id }}/image/thumb"/>
			@else
			<a href="/uploads/{{ $upload->id }}/image">
			<img src="/uploads/{{ $upload->id }}/image/thumb"/>
			@endif
			</a>
		</div>
		<div class="span3">
			<small>
				<strong>"{{{ $upload->name }}}"</strong>
				<br/>
				<strong>Uploaded by: </strong> <a href="mailto:{{{ $upload->user->email }}}">{{{ $upload->user->email }}}</a>
				<br/>
				<strong>Average rating</strong>: {{ round($upload->getAvgRating(),1) }}
				<br/>
				<strong>Rated</strong>: {{ $upload->getNumRatings() }} times
				<br/>
				@if (strpos(Route::getCurrentRoute()->getPath(), '/admin/') === 0)
				<strong>Flagged as inappropriate</strong>: <span id="flagged_count_{{$upload->id}}">{{ $upload->getNumFlagged() }}</span> times
				<br/>
				@endif
				<strong>Share link</strong>: <a href="{{ URL::to('rate') }}#{{$upload->id}}">{{ URL::to('rate') }}#{{$upload->id}}</a>
			</small>
		</div>
		<div class="btn-group">
			@if (strpos(Route::getCurrentRoute()->getPath(), '/admin/') === 0)
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