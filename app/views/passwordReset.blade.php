@extends('layout')

@section('title')
<title>Reset Password</title>
@stop

@section('content')
@if (Session::has('error'))
    {{ trans(Session::get('reason')) }}
@endif
<form method="post">
	<h2 class="form-signin-heading">Reset Password</h2>
	<input type="hidden" name="token" value="{{ $token }}">
	{{ Form::email('email', null, array('class' => 'input-block-level', 'placeholder' => 'Email')) }}
	{{ Form::password('password', array('class' => 'input-block-level', 'placeholder' => 'New Password')) }}
	{{ Form::password('password_confirmation', array('class' => 'input-block-level', 'placeholder' => 'Confirm New Password')) }}
	{{ Form::button('Update Password', array('class' => 'btn btn-large btn-primary', 'type' => 'submit')) }}
</form>
@stop
