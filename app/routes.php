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
	return 'admin manages all uploads here';
});

Route::get('user/uploads', function(){
	return 'user manages their own uploads here';
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
 * uploads
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

/**
 * upload
 */
Route::post('upload', array('before' => 'csrf|upload', function(){
	Log::info('Entering route "' . Route::currentRouteName() . '"');
	$path = Input::file('photo')->getRealPath();
	$extension = strtolower(Input::file('photo')->getClientOriginalExtension());
	$mime = Input::file('photo')->getMimeType();

	//echo 'path: ' . $path;

	$orientation = 0;
	$exif = exif_read_data($path);
	if(!empty($exif['Orientation'])){
		$orientation = $exif['Orientation'];
	} elseif(isset($exif['COMPUTED']) && isset($exif['COMPUTED']['Orientation'])){
		$orientation = $exif['COMPUTED']['Orientation'];
	} elseif (isset($exif['IFD0']) && isset($exif['IFD0']['Orientation'])) {
		$orientation = $exif['IFD0']['Orientation'];
	}

	$uniqueFileName = uniqid();
	$uploadsDir = 'uploads/';
	$resizedFileName = $uniqueFileName . '.' . $extension;
	$thumbFileName = 't_' . $resizedFileName;

	$image = new SimpleImage();
	$image->load($path);
	$image->fixRotation($orientation);
	$image->resizeToWidth(600); 
	$image->save(base_path() . '/' . $uploadsDir . $resizedFileName);
	$image->resizeToWidth(130); 
	$image->save(base_path() . '/' . $uploadsDir . $thumbFileName);

	$upload = new Upload();
	$upload->user_id = Auth::user()->id; // will only work if user is logged in - FIXME
	$upload->upload_dir = base_path() . '/uploads/';
	$upload->file_name = $resizedFileName;
	$upload->thumb_name = $thumbFileName;
	$upload->mime_type = $mime;

	if(Input::has('image_name')){
		$upload->name = Input::get('image_name');
	}

	$upload->save();

	return Redirect::route('get upload')->with('info', 'File uploaded as id ' . $upload->id 
		. ' <a href="cat/' . $upload->id . '/image">click here to view</a>'); //todo: move this to view, NOT controller (here)
}));

Route::filter('upload', function(){
	Log::info('Applying filer \'upload\'');

	$sizeLimit = 60000000; // 6mb

	if (!Input::hasFile('photo')){
		return Redirect::route('get upload')->with('error', 'No file included with POST');
	}

	$allowedExts = array("gif", "jpeg", "jpg", "png");
	$allowedMimeTypes = array("image/gif", "image/jpeg", "image/jpg", "image/pjpeg", "image/x-png", "image/png");

	$size = Input::file('photo')->getSize();
	$mime = Input::file('photo')->getMimeType();
	$extension = strtolower(Input::file('photo')->getClientOriginalExtension());

	if($size > $sizeLimit){
		return Redirect::route('get upload')->with('error', 'File too large - 6 MB limit.');
	}

	if(!in_array($mime, $allowedMimeTypes) || !in_array($extension, $allowedExts)){
		return Redirect::route('get upload')->with('error', 'Invalid file type.');
	}

});

Route::get('upload', function(){
	Log::info('Entering route "' . Route::currentRouteName() . '"');
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

Route::get('login', function(){
	Log::info('Entering route "' . Route::currentRouteName() . '"');

	if(Auth::check()){
		// ?? http://bytes.com/topic/asp-classic/answers/53883-after-redirect-back-3rd-party-session-variables-lost
		//return Redirect::intended('/')->with('info', 'You are already logged in'); // info not coming forward - no info alert
		return Redirect::intended('rate')->with('info', 'You are already logged in'); // info comes forward, but not best practice
	} else {
		return View::make('login');
	}
});


/**
 * logout
 */
Route::any('logout', function(){
	Auth::logout();
	return Redirect::intended('/')->with('info', 'Logged out');;
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
	return View::make('signup');
});


/**
 * users
 */
Route::get('users', function()
{
	Log::info('Entering route "' . Route::currentRouteName() . '"');
	Log::info('Log message', array('context' => 'Other helpful information'));
    //return View::make('users');
	$users = User::all();

	return View::make('users')->with('users', $users);
});


/**
 * rate
 */
Route::get('rate', function()
{
	Log::info('Entering route "' . Route::currentRouteName() . '"');
	return View::make('rate')->with('user', Auth::user());
});


?>