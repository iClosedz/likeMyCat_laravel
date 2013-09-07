@extends('layout')

@section('title')
<title>Change Password</title>
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
	{{ Form::open(array('class'=>'form-signin')) }}
	<h2 class="form-signin-heading">Change Password</h2>
	{{ Form::password('current_password', array('class' => 'input-block-level', 'placeholder' => 'Current Password')) }}
	{{ Form::password('new_password', array('class' => 'input-block-level', 'placeholder' => 'New Password')) }}
	{{ Form::password('new_password_confirmation', array('class' => 'input-block-level', 'placeholder' => 'Confirm New Password')) }}
	{{ Form::button('Update Password', array('class' => 'btn btn-large btn-primary', 'type' => 'submit')) }}
</form>
@stop
