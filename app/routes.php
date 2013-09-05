<?php

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It's a breeze. Simply tell Laravel the URIs it should respond to
| and give it the Closure to execute when that URI is requested.
|
*/

Route::get('/', function(){
	//return View::make('hello');
	return Redirect::route('get rate');
});

Route::get('admin/users', function(){
	return 'user admin page';
});

Route::get('admin/uploads', function(){
	return View::make('manageUploads')->with('user', Auth::user())->with('uploads', Upload::orderBy('id', 'DESC')->get());
});

Route::get('user/uploads', function(){
	return View::make('manageUploads')->with('user', Auth::user())->with('uploads', Auth::user()->uploads);
});

Route::get('admin/uploads/delete/{upload_id}', function(Upload $upload){
	$status = $upload->delete();

	return Response::json(array(
		'success' => $status, 
		'results' => array('upload_id' => $upload->id)
		));
})->where('upload_id', '[0-9]+');

Route::get('user/uploads/delete/{upload_id}', function(Upload $upload){
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

Route::get('user/uploads', function(){
	return View::make('manageUploads')->with('user', Auth::user())->with('uploads', Auth::user()->uploads);
});

/**
 * create and apply admin filter
 */
Route::filter('admin', function(){
	if(!Auth::check()){
		Session::put('url.intended', URL::current());
		return Redirect::route('get login')->with('info', 'You must be logged in to access that page.');
	}

	if(!Auth::user()->hasRole('admin')){
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
 * upload
 */
Route::post('upload', 'ImageUploadController@uploadImage');
Route::when('upload', 'csrf', array('post'));
Route::when('upload', 'filterUploadImage', array('post'));

//Route::when('upload', 'csrf|filterUploadImage', array('post'));

Route::get('upload', function(){
	return View::make('upload')->with('user', Auth::user());
});

/**
 * login
 */
Route::post('login', array('before' => 'csrf', function(){
	Log::info('Entering route "' . Route::currentRouteName() . '"');
	$email = trim(Input::get('username'));
	$password = Input::get('password');

	if (Auth::attempt(array('email' => $email, 'password' => $password))){
		return Redirect::intended('/')->with('success', 'Login Successful');
	} else {
		//Session::flash('error', 'Authentication failed'); // flash message since we're not redirecting
		return View::make('login')->with('username', $email)->with('error', true);
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
	$email = trim(Input::get('username'));
	$password = Input::get('password');

	if(User::getUserByEmail($email)){
		return Redirect::route('get signup')->with('error', 'User already exists');
	}

	// validate input
	$rules = array('username' => 'email', 'password' => array('required', 'min:6'));
	$validator = Validator::make(Input::all(), $rules);

	if ($validator->fails()){
    	Log::info('validator errors: ' . $validator); // will this work?
    	return Redirect::to('get signup')->withErrors($validator);
    }

    $user = new User;
    $user->email = $email;
    $user->password = Hash::make($password);
    $user->is_guest = 'false';
    $user->ip_address = ip2long(isset($_SERVER["REMOTE_ADDR"]) ? $_SERVER["REMOTE_ADDR"] : 1);
    $status = $user->save();

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

/* log all queries */
Event::listen("illuminate.query", function($query, $bindings, $time, $name){
	Log::debug($query."\n", $bindings);
});

?>