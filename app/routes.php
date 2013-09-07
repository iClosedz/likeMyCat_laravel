<?php

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
*/

Route::get('/', function(){
	return Redirect::route('get rate');
});

/**
 * Route groups
 */
Route::group(array('prefix' => 'admin'), function(){
	Route::get('uploads', 'AdminController@showUploads');
	Route::get('uploads/delete/{delete_upload_id}', 'AdminController@deleteUploadById')->where('delete_upload_id', '[0-9]+');
	Route::get('uploads/hide/{upload_id}', 'AdminController@hideUpload')->where('upload_id', '[0-9]+');
	Route::get('uploads/restore/{restore_upload_id}', 'AdminController@restoreUploadById')->where('restore_upload_id', '[0-9]+');
	Route::get('cat/{upload_id_inc_deleted}/image/thumb', 'AdminController@getThumbIncDeleted')->where('upload_id_inc_deleted', '[0-9]+');
	Route::get('users', 'AdminController@showUsers');
});

Route::group(array('prefix' => 'user'), function(){
	Route::get('uploads', 'UserController@showUploads');
	Route::get('uploads/hide/{upload_id}', 'UserController@hideUpload')->where('upload_id', '[0-9]+');
});

Route::group(array('prefix' => 'password'), function()
{
	Route::resource('change', 'PasswordChangeController', array('only' => array('index', 'store')));

	Route::get('reset/{token}', function($token){
		return View::make('passwordReset')->with('token', $token)->with('user', Auth::user());
	});

	Route::post('remind', function(){
		$email = trim(Input::get('email'));
		$credentials = array('email' => $email);

		return Password::remind($credentials, function($message, $user){
			$message->from('admin@likemycat.com', 'LikeMyCat Admin');
			$message->subject('Your Password Reminder');
		});
	});

	Route::get('remind', function(){ 
		return View::make('passwordRemind')->with('user', Auth::user());
	});
});




/**
 * cat/{upload_id}/
 */
Route::model('upload_id', 'Upload'); // Binding A Parameter To A Model

Route::get('cat/{upload_id}/image', function(Upload $upload){
	$imagePath = $upload->upload_dir . $upload->file_name;
	$contents = file_get_contents($imagePath);

	$response = Response::make($contents, 200);
	$response->header('Content-Type', $upload->mime_type);

	return $response;
})
->where('upload_id', '[0-9]+');

Route::get('cat/{upload_id}/image/thumb', function(Upload $upload){
	$imagePath = $upload->upload_dir . $upload->thumb_name;
	$contents = file_get_contents($imagePath);

	$response = Response::make($contents, 200);
	$response->header('Content-Type', $upload->mime_type);

	return $response;
})
->where('upload_id', '[0-9]+');

// should be post
Route::get('cat/{upload_id}/rate/{rating}', function(Upload $upload, $inRating){

	if(Auth::check()){
		$rating = Auth::user()->ratings()->where('upload_id', '=', $upload->id)->first();

		if(empty($rating)){
			$rating = new Rating();
			$rating->user_id = Auth::user()->id;
			$rating->upload_id = $upload->id;
		}

		$rating->rating = $inRating;
		$rating->save();

		//return 'rating saved';
	} else {
		$rating = RatingGuest::where('upload_id', '=', $upload->id)->where('session_id', '=', session_id())->first();
		if(empty($rating)){
			$rating = new RatingGuest();
			$rating->session_id = session_id();
			$rating->upload_id = $upload->id;
		}

		$rating->rating = $inRating;
		$rating->ip_address = ip2long(isset($_SERVER["REMOTE_ADDR"]) ? $_SERVER["REMOTE_ADDR"] : 1);
		$rating->save();

		//return 'rated as guest';
	}

	return Response::json(array(
		'success' => true, 
		'results' => array(
			'upload_id' => $upload->id, 
			'rating' => $rating->rating,
			'as_guest' => Auth::guest())
		));
})
->where('upload_id', '[0-9]+')->where('rating', '[1-9]|10');

/**
 * flagging
 */
Route::get('cat/{upload_id}/flag', function(Upload $upload){
	Log::info('Entering route "' . Route::currentRouteName() . '"');

	$myIpAddress = ip2long(isset($_SERVER["REMOTE_ADDR"]) ? $_SERVER["REMOTE_ADDR"] : 1);

	$flaggedUpload = FlaggedUpload::where('upload_id', '=', $upload->id)
	->where('ip_address', '=', $myIpAddress)
	->first();

	if(empty($flaggedUpload)){
		$flaggedUpload = new FlaggedUpload();
		$flaggedUpload->upload_id = $upload->id;
		$flaggedUpload->ip_address = $myIpAddress;
	} else {
		$flaggedUpload->touch();
	}

	$result = $flaggedUpload->save();
	
	return Response::json(array(
		'success' => ($result != false), 
		'results' => array(
			'upload_id' => $upload->id, 
			'flagged_id' => $flaggedUpload->id
			)));
})
->where('upload_id', '[0-9]+');

Route::get('cat/{upload_id}/flag/clear', array('before' => 'auth', function(Upload $upload){
	Log::info('Entering route "' . Route::currentRouteName() . '"');

	if(Auth::user()->hasRole(Role::getByRoleName('admin'))) {
		$result = FlaggedUpload::where('upload_id', '=', $upload->id)->delete();
	} else {
		$result = false;
	}
	
	return Response::json(array(
		'success' => ($result != false), 
		'results' => array(
			'upload_id' => $upload->id
			)));
}))
->where('upload_id', '[0-9]+');

/**
 * upload
 */

// filters are handled inside ImageUploadController constructor
Route::post('upload', 'ImageUploadController@uploadImage');

Route::get('upload', function(){
	return View::make('upload')->with('user', Auth::user());
});

/**
 * login
 */
Route::post('login', array('before' => 'csrf', function(){
	Log::info('Entering route "' . Route::currentRouteName() . '"');

	if(Input::has('forgot')){
		return Redirect::route('get password/remind');
	}

	$email = strtolower(trim(Input::get('username')));
	$password = Input::get('password');

	if (Auth::attempt(array('email' => $email, 'password' => $password))){
		return Redirect::intended('/')->with('success', 'Login Successful');
	} else {
		return View::make('login')->with('username', $email)->with('error', 'Authentication failed');
	}
	//todo: figure out how to catch TokenMismatchException
}));

Route::get('login', array('before' => 'guest', function(){
	Log::info('Entering route "' . Route::currentRouteName() . '"');
	return View::make('login');
}));


/**
 * logout
 */
Route::any('logout', function(){
	Auth::logout();
	return Redirect::intended('/')->with('info', 'Logged out');;
});

/**
 * about
 */
Route::any('about', function(){
	return View::make('about')->with('user', Auth::user());
});

Route::any('contact', function(){
	return View::make('contact')->with('user', Auth::user());
});

Route::any('anger', function(){
	return View::make('anger')->with('user', Auth::user());
});

/**
 * signup
 */
Route::post('signup', array('before' => 'csrf', function(){
	Log::info('Entering route "' . Route::currentRouteName() . '"');
	$email = strtolower(trim(Input::get('username')));
	$password = Input::get('password');

	if(User::getUserByEmail($email)){
		return Redirect::route('get signup')->with('error', 'User already exists');
	}

	// validate input
	$rules = array('username' => 'email', 'password' => array('required', 'min:6'));
	$validator = Validator::make(Input::all(), $rules);

	if ($validator->fails()){
		return Redirect::to('signup')->withErrors($validator);
	}

	$user = new User;
	$user->email = $email;
	$user->password = Hash::make($password);
	$user->is_guest = 'false';
	$user->ip_address = ip2long(isset($_SERVER["REMOTE_ADDR"]) ? $_SERVER["REMOTE_ADDR"] : 1);
	$status = $user->save();

    //add user to default role(s)
	$user->grantRole(Role::getByRoleName('user'))->grantRole(Role::getByRoleName('uploader'));

	if($status){
		Auth::loginUsingId($user->id);
		return Redirect::route('get rate')->with('success', 'Sign up successful!');
	} else {
		return Redirect::route('get signup')->with('error', 'Error creating new user');
	}

}));

Route::get('signup', function(){
	Log::info('Entering route "' . Route::currentRouteName() . '"');
	Auth::logout();
	return View::make('signup');
});


/**
 * rate
 */
Route::get('rate', function(){
	Log::info('Entering route "' . Route::currentRouteName() . '"');
	return View::make('rate')->with('user', Auth::user());
});

Route::any('rate/getUploads', 'ImageUploadController@getUploads');

/**
 * environment settings
 */
if(App::environment() === 'dev'){
	/* log all queries */
	Event::listen("illuminate.query", function($query, $bindings, $time, $name){
		Log::debug($query."\n", $bindings);
	});

	/* don't actually send emails */
	Mail::pretend();
}

?>