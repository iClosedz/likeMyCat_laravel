<?php

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
*/

Route::post('password/change', array('before' => 'auth|csrf', function(){
	Log::info("entering password/change");
	$email = Auth::user()->email;
	$password = Input::get('current_password');
	$newPassword = Input::get('new_password');
	$newPasswordConfirm = Input::get('new_password_confirmation');

	Log::info("Email: $email -- password: $password --- newPassword: $newPassword");

	if (Auth::attempt(array('email' => $email, 'password' => $password))){

		// validate input
		$rules = array(
			'username' => 'email', 
			'new_password' => array('required', 'min:6', 'same:new_password_confirmation')
			);
		$validator = Validator::make(Input::all(), $rules);
		if ($validator->fails()){
			return Redirect::to('password/change')->withErrors($validator);
		}

		Auth::user()->password = Hash::make($newPassword);
		Auth::user()->save();
		return Redirect::to('rate')->with('success', 'Password changed!');
	} else {
		return View::make('passwordChange')->with('user', Auth::user())->with('username', $email)
		->with('error', 'Current password incorrect');
	}

}));

Route::get('password/change', function(){
	return View::make('passwordChange')->with('user', Auth::user());
});

Route::post('password/reset/{token}', function(){
	$credentials = array('email' => Input::get('email'));

	return Password::reset($credentials, function($user, $password){
		$user->password = Hash::make($password);
		$user->save();
		Auth::loginUsingId($user->id);
		return Redirect::route('get rate')->with('success', 'Password changed!');
        //return Redirect::to('home');
	});
});

Route::get('password/reset/{token}', function($token){
	return View::make('passwordReset')->with('token', $token)->with('user', Auth::user());
});

Route::post('password/remind', function(){ // should be POST
	$email = trim(Input::get('email'));
	$credentials = array('email' => $email);

    //return Password::remind($credentials);
	return Password::remind($credentials, function($message, $user){
		$message->from('admin@likemycat.com', 'LikeMyCat Admin');
		$message->subject('Your Password Reminder');
	});
});

Route::get('password/remind', function(){ // should be POST
	return View::make('passwordRemind')->with('user', Auth::user());
});

Route::get('/', function(){
	return Redirect::route('get rate');
});

Route::get('admin/users', function(){
	return 'user admin page';
});

Route::get('admin/uploads', function(){
	return View::make('manageUploads')
	->with('user', Auth::user())
	->with('uploads', Upload::withTrashed()
		->with('user', 'ratings', 'guestRatings', 'flagged')
		->orderBy('id', 'DESC')
		->paginate(4)
		//->get()
		);
});

Route::get('user/uploads', function(){
	return View::make('manageUploads')
	->with('user', Auth::user())
	->with('uploads', Auth::user()->uploads()
		->with('user', 'ratings', 'guestRatings')
		->orderBy('id', 'DESC')
		->paginate(4)
		//->get()
		);
});

Route::get('admin/cat/{upload_id_inc_deleted}/image/thumb', function($uploadId){
	$upload = Upload::withTrashed()->findOrFail($uploadId);
	$imagePath = $upload->upload_dir . $upload->thumb_name;
	$contents = file_get_contents($imagePath);

	$response = Response::make($contents, 200);
	$response->header('Content-Type', $upload->mime_type);

	return $response;
})
->where('upload_id_inc_deleted', '[0-9]+');

Route::get('admin/uploads/delete/{delete_upload_id}', function($uploadId){
	Log::info('Entering route "' . Route::currentRouteName() . '"');

	$upload = Upload::withTrashed()->findOrFail($uploadId);
	$status = $upload->forceDelete();

	return Response::json(array(
		'success' => $status, 
		'results' => array('upload_id' => $upload->id)
		));
})->where('delete_upload_id', '[0-9]+');

Route::get('admin/uploads/hide/{upload_id}', function(Upload $upload){
	$status = $upload->delete();

	return Response::json(array(
		'success' => $status, 
		'results' => array('upload_id' => $upload->id)
		));
	
})->where('upload_id', '[0-9]+');

Route::get('admin/uploads/restore/{restore_upload_id}', function($uploadId){
	$upload = Upload::withTrashed()->findOrFail($uploadId);
	$status = $upload->restore();

	return Response::json(array(
		'success' => $status, 
		'results' => array('upload_id' => $upload->id)
		));
	
})->where('upload_id', '[0-9]+');

Route::get('user/uploads/hide/{upload_id}', function(Upload $upload){
	if(Auth::user()->id === $upload->user_id){
		$status = $upload->delete();

		return Response::json(array(
			'success' => $status, 
			'results' => array('upload_id' => $upload->id)
			));
	} else {
		return Response::json(array(
			'success' => false, 
			'results' => 'you don\'t own this image'
			));
	}
})->where('upload_id', '[0-9]+');

/**
 * create and apply admin filter
 */
Route::filter('admin', function(){
	if(!Auth::check()){
		Session::put('url.intended', URL::current());
		return Redirect::route('get login')->with('info', 'You must be logged in to access that page.');
	}

	if(!Auth::user()->hasRole(Role::getByRoleName('admin'))){
		return Redirect::route('get /')->with('error', 'You must be an admin to access this page!');
	}

});

Route::when('admin/*', 'admin');

/**
 * create and apply user filter
 */
Route::filter('user', function(){
	if(!Auth::check()){
		Session::put('url.intended', URL::current());
		return Redirect::route('get login')->with('info', 'You must be logged in to access that page.');
	}
});

Route::when('user/*', 'user');

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