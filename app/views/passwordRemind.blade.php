@extends('layout')

@section('title')
	<title>Forgot Password</title>
@stop

@section('content')
	{{ Form::open(array('route' => 'post password/remind', 'class'=>'form-signin')) }}
		<h2 class="form-signin-heading">Forgot Password</h2>
		{{ Form::email('email', null, array('class' => 'input-block-level', 'placeholder' => 'Email')) }}
		{{ Form::button('Send Reminder', array('class' => 'btn btn-large btn-primary', 'type' => 'submit')) }}
	{{ Form::close() }}
@stop
