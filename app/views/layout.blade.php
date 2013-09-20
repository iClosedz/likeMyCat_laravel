<?php if(!isset($user)){ $user=null; } else { $user = Auth::user(); /*Log::info('current user email: ' . $user->email);*/ } ?>
<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="utf-8">
	@yield('title')

	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<meta name="description" content="Like My Cat">
	<meta name="author" content="Bot Enterprises">

	<!-- Le styles -->
	<link href="/assets/css/bootstrap.css" rel="stylesheet">
	<style type="text/css">
		body {
			padding-top: 60px;
			padding-bottom: 40px;
		}
		
		@if (App::environment() !== 'production')
			#environment_warning {
				background: black;
				opacity: 0.5;
				position: fixed;
				top: 50px;
				left: 50px;
			    background-color: red;
			}
		@endif
		@yield('customStyles')
	</style>
	
	<link href="/assets/css/bootstrap-responsive.css" rel="stylesheet">
	<link href="//netdna.bootstrapcdn.com/font-awesome/3.2.1/css/font-awesome.css" rel="stylesheet">
	<link href="/assets/css/social-buttons.css" rel="stylesheet">



	<!-- HTML5 shim, for IE6-8 support of HTML5 elements -->
		<!--[if lt IE 9]>
		<script src="/assets/js/html5shiv.js"></script>
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
		<div class="navbar navbar-fixed-top">
			<div class="navbar-inner">
				<div class="container">
					<button type="button" class="btn btn-navbar" data-toggle="collapse" data-target=".nav-collapse">
						<span class="icon-bar"></span> <span class="icon-bar"></span> <span class="icon-bar"></span>
					</button>
					<a class="brand" href="{{ URL::to('rate') }}">Like My Cat?</a>
					<div class="nav-collapse collapse">
						<ul class="nav">
							<li><a href="{{ URL::to('rate') }}"><i class="icon-thumbs-up"></i> Rate</a></li> 
							<li><a href="{{ URL::to('uploader') }}"><i class="icon-upload"></i> Upload</a></li>
							<li><a href="{{ URL::to('about') }}"><i class="icon-info-sign"></i> About</a></li>
							<li><a href="{{ URL::to('contact') }}"><i class="icon-envelope"></i> Contact</a></li> 
							@if (Auth::check())
							<li class="divider-vertical"></li>
							<li class="dropdown">
								<a href="#" class="dropdown-toggle" data-toggle="dropdown"><i class="icon-user"></i> {{{ $user->email }}}<b class="caret"></b></a>
								<ul class="dropdown-menu">
									<li><a href="{{ URL::to('logout') }}">Log Out</a></li>
									<li class="divider"></li>
									<li class="nav-header">Account</li>
									<li><a href="{{ URL::to('password/change') }}">Change Password</a></li>
									@if ($user->hasRole(Role::getByRoleName('uploader')))
									<li><a href="{{ URL::to('user/uploads') }}">Manage Your Uploads</a></li>
									@endif
									@if ($user->hasRole(Role::getByRoleName('admin')))
									<li class="divider"></li>
									<li class="nav-header">Administration</li>
									<li><a href="{{ URL::to('admin/uploads') }}">Manage All Uploads</a></li>
									<li><a href="{{ URL::to('admin/users') }}">User Administration</a></li>
									@endif
								</ul>
							</li>
							@endif
						</ul>
						@if (Auth::guest())
							<!-- {{ Form::open(array('route' => 'post login', 'class'=>'navbar-form pull-right')) }} -->
							<!-- {{ Form::email('username', null, array('class' => 'span2', 'placeholder' => 'Email')) }} -->
							<!-- {{ Form::password('password', array('class' => 'span2', 'placeholder' => 'Password')) }} -->
							<!-- {{ Form::button('Sign In', array('class' => 'btn', 'type' => 'submit')) }} -->
							<!-- <a href="{{ URL::to('signup') }}"><button class="btn btn-primary" type="button">Sign Up</button></a> -->
							<!-- <a class="navbar-form pull-right" href="{{ URL::to('login/fb') }}"><img src="/assets/img/fb_connect.png"/></a> -->
							<!-- {{ Form::close() }} -->
							<span class="navbar-form pull-right">
								<a href="{{ URL::to('login') }}"><button class="btn">Log In</button></a>
								<a href="{{ URL::to('login/fb') }}"><button class="btn btn-facebook"><i class="icon-facebook"></i> | Connect with Facebook</button></a>
							</span>
						@endif
						@if (Auth::check())
							@if (!empty($user->photo))
								<span class="navbar-form pull-right">
									<img src="{{ $user->photo }}" class="img-polaroid" height="30" width="30"/>
								</span>
							@endif
						@endif
					</div>
				</div>
			</div>
		</div>
		<!--- end nav bar -->
		<div class="container">
			@if (App::environment() !== 'production')
				<div id="environment_warning"><h4>{{ App::environment() }}</h4></div>
			@endif
			@if (isset($errors))
			 	@foreach($errors->all() as $message)
	        		<div class="alert alert-error">
						<button type="button" class="close" data-dismiss="alert">&times;</button>
						{{ $message }}
				    </div>
	    		@endforeach
    		@endif

			@if (Session::has('error'))
				<div class="alert alert-error">
					<button type="button" class="close" data-dismiss="alert">&times;</button>
					@if (Session::has('reason'))
						{{ trans(Session::get('reason')) }}
					@else
						{{ Session::get('error') }}
					@endif
			    </div>
			@endif
			@if (Session::has('success'))
			    <div class="alert alert-success">
					<button type="button" class="close" data-dismiss="alert">&times;</button>
					@if (Session::has('reason'))
						{{ trans(Session::get('reason')) }}
					@else
						Success!
					@endif
				</div>
			@endif
			@if (Session::has('info'))
			    <div class="alert alert-info">
					<button type="button" class="close" data-dismiss="alert">&times;</button>
					@if (Session::has('reason'))
						{{ trans(Session::get('reason')) }}
					@else
						{{ Session::get('info') }}
					@endif	   
				</div>
			@endif
			@if (!empty($error))
			<div class="alert alert-error">
					<button type="button" class="close" data-dismiss="alert">&times;</button>
					{{ $error }}
			    </div>
			@endif
			@if (!empty($success))
			<div class="alert alert-success">
					<button type="button" class="close" data-dismiss="alert">&times;</button>
					{{ $success }}
			    </div>
			@endif
			@if (!empty($info))
			<div class="alert alert-info">
					<button type="button" class="close" data-dismiss="alert">&times;</button>
					{{ $info }}
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
		<script src="/assets/js/console.fix.js"></script>
		@yield('additionalScriptTags')
	</body>
	</html>