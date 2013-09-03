@extends('layout')

@section('title')
	<title>Sign Up</title>
@stop

@section('content')
	{{ Form::open(array('route' => 'post signup', 'class'=>'form-signin')) }}
		<h2 class="form-signin-heading">New User</h2>
		{{ Form::email('username', null, array('class' => 'input-block-level', 'placeholder' => 'Email')) }}
		{{ Form::password('password', array('class' => 'input-block-level', 'placeholder' => 'Password')) }}
		{{ Form::password('password', array('class' => 'input-block-level', 'placeholder' => 'Confirm Password')) }}
		{{ Form::button('Sign Up', array('class' => 'btn btn-large btn-primary', 'type' => 'submit')) }}
	{{ Form::close() }}
@stop
