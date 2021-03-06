@extends('layout')

@section('title')
	<title>Sign Up</title>
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

@section('content')
<div>
	{{ Form::open(array('route' => 'post signup', 'class'=>'form-signin')) }}
		<h2 class="form-signin-heading">New User</h2>
		{{ Form::email('username', null, array('class' => 'input-block-level', 'placeholder' => 'Email')) }}
		{{ Form::password('password', array('class' => 'input-block-level', 'placeholder' => 'Password')) }}
		{{ Form::password('password', array('class' => 'input-block-level', 'placeholder' => 'Confirm Password')) }}
		{{ Form::button('Sign Up', array('class' => 'btn btn-large btn-primary', 'type' => 'submit')) }}
		<hr/>
		<div class="media">
	        <div class="media-body">
	          	<h5 class="media-heading">Why do you need my email address? Stalkers.  You're gonna sell it.  You're gonna sell every letter of it, aren't you?</h5>
	          	<small>
	            	<br/>
	            	<p>Wow, that escalated quickly.</p>  
	            	<p>
	              No, we don't need or want to sell your email address or any of the information you choose to provide on this site.  
	              We only need you to register with a REAL email address to keep the spammers and griefers at bay 
	              (you can go make a new one at <a href="http://www.gmail.com">gmail.com</a> right now if you're very worried).
	            	</p>
	          	</small>
	        </div>
	    </div>
	{{ Form::close() }}
</div>
@stop
