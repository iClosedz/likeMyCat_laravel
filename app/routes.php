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

Route::get('/', function()
{
	return View::make('hello');
});

/**
 * login
 */
Route::post('login', array('before' => 'csrf', function(){
	Log::info('Entering route "' . Route::currentRouteName() . '"');
	$email = trim(Input::get('username'));
	$password = Input::get('password');

	if (Auth::attempt(array('email' => $email, 'password' => $password))){
		//return 'auth successful';
		return Redirect::intended('rate')->with('success', 'Login Successful');
	} else {
		return 'auth faiiled';
	}

	//todo: figure out how to catch TokenMismatchException
}));

Route::get('login', function(){
	Log::info('Entering route "' . Route::currentRouteName() . '"');
	return 'not yet coded...';
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
 * logout
 */
Route::any('logout', function(){
	Auth::logout();
	return Redirect::intended('rate')->with('info', 'Logged out');;
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
	$roles = null;

	if(Auth::check()){
		//User::getUserByEmail('davidkey@gmail.com');
		$user = Auth::user();
		$roles = $user->userRoles;
	}

	return View::make('rate')->with('user', $user)->with('roles', $roles);
});


?>