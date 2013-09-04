@extends('layout')

@section('title')
<title>Upload</title>
@stop

@section('additionalHeadData')
<link href="/assets/css/bootstrap-fileupload.min.css" rel="stylesheet">
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
					<input type="file" name="photo">
				</span>
				<a href="#" class="btn fileupload-exists" data-dismiss="fileupload">Remove</a> 
				<input type="submit" name="submit" value="Upload!" class="btn fileupload-exists"/>
			</form>
		</div>
	</div>
	@if (Auth::check())
		<p>Authorized user</p>
	@else
		<p>Uploading as guest</p>
	@endif

@stop

@section('additionalScriptTags')
	<script src="/assets/js/bootstrap-fileupload.min.js"></script>
@stop