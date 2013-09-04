<?php if(!isset($user)){ $user=null; } ?>
<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="utf-8">
	@yield('title')

	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<meta name="description" content="">
	<meta name="author" content="">

	<!-- Le styles -->
	<link href="/assets/css/bootstrap.css" rel="stylesheet">
	<style type="text/css">
		body {
			padding-top: 60px;
			padding-bottom: 40px;
		}

		@yield('customStyles')
	</style>
	<link href="/assets/css/bootstrap-responsive.css" rel="stylesheet">

	<!-- HTML5 shim, for IE6-8 support of HTML5 elements -->
		<!--[if lt IE 9]>
		<script src="./assets/js/html5shiv.js"></script>
		<![endif]-->
		<link rel="apple-touch-icon-precomposed" sizes="144x144" href="./assets/ico/apple-touch-icon-144-precomposed.png">
		<link rel="apple-touch-icon-precomposed" sizes="114x114" href="./assets/ico/apple-touch-icon-114-precomposed.png">
		<link rel="apple-touch-icon-precomposed" sizes="72x72" href="./assets/ico/apple-touch-icon-72-precomposed.png">
		<link rel="apple-touch-icon-precomposed" href="./assets/ico/apple-touch-icon-57-precomposed.png">
		<link rel="shortcut icon" href="./assets/ico/favicon.png">
		@yield('additionalHeadData')
	</head>
	<body>
		<!--- start nav bar -->
		<div class="navbar navbar-inverse navbar-fixed-top">
			<div class="navbar-inner">
				<div class="container">
					<button type="button" class="btn btn-navbar" data-toggle="collapse" data-target=".nav-collapse">
						<span class="icon-bar"></span> <span class="icon-bar"></span> <span class="icon-bar"></span>
					</button>
					<a class="brand" href="{{ URL::to('rate') }}">Like My Cat?</a>
					<div class="nav-collapse collapse">
						<ul class="nav">
							<li><a href="{{ URL::to('rate') }}">Rate</a></li> 
							<li><a href="{{ URL::to('upload') }}">Upload</a></li>
							<li><a href="about">About</a></li> 
							<li><a href="contact">Contact</a></li> 
							@if (Auth::check())
							<li class="dropdown">
								<a href="#" class="dropdown-toggle" data-toggle="dropdown">{{{ $user->email }}}<b class="caret"></b></a>
								<ul class="dropdown-menu">
									<li><a href="{{ URL::to('logout') }}">Log Out</a></li>
									<li class="divider"></li>
									<li class="nav-header">Account</li>
									<li><a href="user/changePassword">Change Password</a></li>
									@if ($user->hasRole('uploader'))
									<li><a href="{{ URL::to('user/uploads') }}">Manage Your Uploads</a></li>
									@endif
									@if ($user->hasRole('admin'))
									<li class="divider"></li>
									<li class="nav-header">Administration</li>
									<li><a href="{{ URL::to('admin/uploads') }}">Manage All Uploads</a></li>
									<li><a href="adminUsers">User Administration</a></li>
									@endif
								</ul>
							</li>
							@else
							<li><a href="{{ URL::to('signup') }}">Sign Up</a></li> 
							@endif
						</ul>
						@if (Auth::guest())
							{{ Form::open(array('route' => 'post login', 'class'=>'navbar-form pull-right')) }}
							{{ Form::email('username', null, array('class' => 'span2', 'placeholder' => 'Email')) }}
							{{ Form::password('password', array('class' => 'span2', 'placeholder' => 'Password')) }}
							{{ Form::button('Sign In', array('class' => 'btn', 'type' => 'submit')) }}
							{{ Form::close() }}
						@endif
					</div>
				</div>
			</div>
		</div>
		<!--- end nav bar -->
		<div class="container">
			@if (Session::has('error'))
				<div class="alert alert-error">
					<button type="button" class="close" data-dismiss="alert">&times;</button>
			    	{{ trans(Session::get('error')) }}
			    </div>
			@endif
			@if (Session::has('success'))
			    <div class="alert alert-success">
					<button type="button" class="close" data-dismiss="alert">&times;</button>
			    	{{ trans(Session::get('success')) }}
			    </div>
			@endif
			@if (Session::has('info'))
			    <div class="alert alert-info">
					<button type="button" class="close" data-dismiss="alert">&times;</button>
			    	{{ trans(Session::get('info')) }}
			    </div>
			@endif
			<div class="container-fluid">
				@yield('content')
				<hr/>
				<footer>
					<p>&copy; Bot Enterprises 2013</p>
				</footer>
			</div>
		</div>
		<script src="//ajax.googleapis.com/ajax/libs/jquery/1.10.2/jquery.min.js"></script>
		<script src="/assets/js/bootstrap.min.js"></script>
		<script src="./assets/js/console.fix.js"></script>
		@yield('additionalScriptTags')
	</body>
	</html>