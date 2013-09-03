<!DOCTYPE html>
<html lang="en">
	<head>
		<meta charset="utf-8">
		@yield('title')

		<meta name="viewport" content="width=device-width, initial-scale=1.0">
		<meta name="description" content="">
		<meta name="author" content="">

		<!-- Le styles -->
		<link href="./assets/css/bootstrap.css" rel="stylesheet">
		<style type="text/css">
			body {
				padding-top: 60px;
				padding-bottom: 40px;
			}
			@yield('customStyles')
		</style>
		<link href="./assets/css/bootstrap-responsive.css" rel="stylesheet">

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
		  			<a class="brand" href="./rate">Like My Cat?</a>
		  				<div class="nav-collapse collapse">
		    				<ul class="nav">
						     <li><a href="./rate">Rate</a></li> 
						     <li><a href="./upload_form">Upload</a></li>
						     <li><a href="./about">About</a></li> 
						     <li><a href="./contact">Contact</a></li> 
							</ul>
						  <form class="navbar-form pull-right" method="post" action="./login">
						    <input class="span2" name="user_id" type="text" placeholder="Email"> 
						    <input class="span2" name="password" type="password" placeholder="Password">
						    <button type="submit" name="submit" class="btn">Sign in</button>
						  </form>
						</div>
				</div>
			</div>
		</div>
	<!--- end nav bar -->
		<div class="container">
		<!-- alerts go here -->
			<div class="container-fluid">
				@yield('content')
				<hr/>
				<footer>
					<p>&copy; Bot Enterprises 2013</p>
				</footer>
			</div>
		</div>
		<script src="//ajax.googleapis.com/ajax/libs/jquery/1.10.2/jquery.min.js"></script>
		<script src="./assets/js/bootstrap.min.js"></script>
		@yield('additionalScriptTags')
	</body>
</html>