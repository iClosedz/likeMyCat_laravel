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
		//Session::put('url.intended', URL::current());
		//return Redirect::guest('rate')->with('error', 'You must be an admin to access this page!');
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
		return Redirect::intended('get /')->with('info', 'You are already logged in');
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

	$user = null;
	//$roles = null;

	if(Auth::check()){
		//User::getUserByEmail('davidkey@gmail.com');
		$user = Auth::user();
		//$roles = $user->userRoles;
	}

	return View::make('rate')->with('user', $user);//->with('roles', $roles); // is passing roles needed?
});


?>