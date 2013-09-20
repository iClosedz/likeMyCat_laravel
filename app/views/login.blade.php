@extends('layout')

@section('title')
<title>Login</title>
@stop

@section('customStyles')
	body {
		padding-top: 40px;
		padding-bottom: 40px;
		background-color: #f5f5f5;
	}

	.form-signin {
		max-width: 300px;
		padding: 19px 29px 29px;
		margin: 0 auto 20px;
		background-color: #fff;
		border: 1px solid #e5e5e5;
		-webkit-border-radius: 5px;
		-moz-border-radius: 5px;
		border-radius: 5px;
		-webkit-box-shadow: 0 1px 2px rgba(0, 0, 0, .05);
		-moz-box-shadow: 0 1px 2px rgba(0, 0, 0, .05);
		box-shadow: 0 1px 2px rgba(0, 0, 0, .05);
	}

	.form-signin .form-signin-heading,.form-signin .checkbox {
		margin-bottom: 10px;
	}

	.form-signin input[type="text"],.form-signin input[type="password"] {
		font-size: 16px;
		height: auto;
		margin-bottom: 15px;
		padding: 7px 9px;
	}
@stop

<?php if(!isset($username)) { $username = ''; }?>
@section('content')
<div>
	@if (Auth::guest())
		{{ Form::open(array('route' => 'post login', 'class'=>'form-signin')) }}
			<h2 class="form-signin-heading">Please sign in</h2>
			{{ Form::text('username', $username, array('class' => 'input-block-level', 'placeholder' => 'Email')) }}
			{{ Form::password('password', array('class' => 'input-block-level', 'placeholder' => 'Password')) }}
			@if (isset($error))
				<div class="alert alert-error">
           			<button type="button" class="close" data-dismiss="alert">&times;</button>
           			<strong>Invalid username or password!</strong> Please try again.
         		</div>
			@endif
			{{ Form::button('Sign In', array('class' => 'btn btn-large btn-primary', 'type' => 'submit')) }}
			{{ Form::button('Forgot Password', array('class' => 'btn btn-large btn-warn', 'type' => 'submit', 'name' => 'forgot', 'value' => 'forgot')) }}
			<hr/>
			<a href="{{ URL::to('signup') }}"><button class="btn btn-large btn-success" type="button">Create a new account</button></a>

		{{ Form::close() }}
	@else
		<h1>You're already logged in. Why are you here?</h1>
	@endif
</div>
@stop