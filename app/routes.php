<?php

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
*/

/* Root route */
Route::get('/', function(){
	return Redirect::route('get rate');
});

/* Binding upload_id to Upload model */
Route::model('upload_id', 'Upload'); 

/**
 * Route groups
 */
Route::group(array('prefix' => 'admin'), function(){
	Route::get('uploads', 'AdminController@showUploads');
	Route::get('uploads/delete/{delete_upload_id}', 'AdminController@deleteUploadById')->where('delete_upload_id', '[0-9]+');
	Route::get('uploads/hide/{upload_id}', 'AdminController@hideUpload')->where('upload_id', '[0-9]+');
	Route::get('uploads/restore/{restore_upload_id}', 'AdminController@restoreUploadById')->where('restore_upload_id', '[0-9]+');
	Route::get('uploads/{upload_id_inc_deleted}/image', 'AdminController@getImageIncDeleted')->where('upload_id_inc_deleted', '[0-9]+');
	Route::get('uploads/{upload_id_inc_deleted}/image/thumb', 'AdminController@getThumbIncDeleted')->where('upload_id_inc_deleted', '[0-9]+');
	Route::get('users', 'AdminController@showUsers');
	Route::get('users/{user_id}/disable', 'AdminController@disableUser')->where('user_id', '[0-9]+');
	Route::get('users/{user_id}/enable', 'AdminController@enableUser')->where('user_id', '[0-9]+');
	Route::get('users/{user_id}/roles', 'AdminController@showRoles')->where('user_id', '[0-9]+');
	Route::get('users/{user_id}/roles/{role_id}/grant', 'AdminController@grantRole')->where('user_id', '[0-9]+')->where('role_id', '[0-9]+');
	Route::get('users/{user_id}/roles/{role_id}/revoke', 'AdminController@revokeRole')->where('user_id', '[0-9]+')->where('role_id', '[0-9]+');

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


Route::group(array('prefix' => 'uploads'), function(){
	Route::get('{upload_id}/image', 'UploadsController@getImage')->where('upload_id', '[0-9]+');
	Route::get('{upload_id}/image/thumb', 'UploadsController@getImageThumb')->where('upload_id', '[0-9]+');
	Route::get('{upload_id}/rate/{rating}', 'UploadsController@setRating')->where('upload_id', '[0-9]+')->where('rating', '[1-9]|10');
	Route::get('{upload_id}/flag', 'UploadsController@flagUpload')->where('upload_id', '[0-9]+');
	Route::get('{flagged_upload_id}/flag/clear', 'UploadsController@clearFlags')->where('flagged_upload_id', '[0-9]+');

});


/**
 * uploader
 */

Route::post('uploader', 'ImageUploadController@uploadImage');
Route::get('uploader', function(){
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
 * about / contact / anger
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