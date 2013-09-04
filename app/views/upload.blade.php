@extends('layout')

@section('title')
<title>Upload</title>
@stop

@section('additionalHeadData')
<link href="/assets/css/bootstrap-fileupload.min.css" rel="stylesheet"/>
@stop

@section('content')
	<h1>Cat Uploader</h1>
	<div class="fileupload fileupload-new" data-provides="fileupload">
		<div class="fileupload-preview thumbnail" style="width: 300px; height: 225px;"></div>
		<div>
			<form class="form-signin" method="post" enctype="multipart/form-data">
				<input type="hidden" name="_token" value="<?php echo csrf_token(); ?>">
				<div class="fileupload-exists">
					<input class="fileupload-exists" type="text" id="image_name" name="image_name" placeholder="Pet's Name? (optional)"/>
				</div>
				<span class="btn btn-file"> 
					<span class="fileupload-new">Select image</span> 
					<span class="fileupload-exists">Change</span> 
					<input type="file" name="photo"/>
				</span>
				<a href="#" class="btn fileupload-exists" data-dismiss="fileupload">Remove</a> 
				<input type="submit" name="submit" value="Upload!" class="btn fileupload-exists"/>
			</form>
		</div>
	</div>

	@if (Session::has('upload'))
	<div class="alert">
		<button type="button" class="close" data-dismiss="alert">&times;</button>
		<strong>{{{Session::get('upload')->name}}} uploaded!</strong> <a href="{{ URL::route('get cat/{upload_id}/image', array(Session::get('upload')->id)) }}">Click here</a> to view or share.
		<br/><br/>
		<a href="{{ URL::route('get cat/{upload_id}/image', array(Session::get('upload')->id)) }}">
			<img src="{{ URL::route('get cat/{upload_id}/image/thumb', array(Session::get('upload')->id)) }}"/>
		</a>
	</div>
	@endif

	@if (Auth::guest())
		<div>Uploading as guest</div>
	@endif

@stop

@section('additionalScriptTags')
	<script src="/assets/js/bootstrap-fileupload.min.js"></script>
@stop