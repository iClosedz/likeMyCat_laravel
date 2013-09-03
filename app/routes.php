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

Route::post('login', array('before' => 'csrf', function(){
	Log::info('Entering route "' . Route::currentRouteName() . '"');
	$email = trim(Input::get('username'));
	$password = Input::get('password');

	if (Auth::attempt(array('email' => $email, 'password' => $password))){
		//return 'auth successful';
		return Redirect::intended('rate');
	} else {
		return 'auth faiiled';
	}

	//todo: figure out how to catch TokenMismatchException
}));

Route::get('login', function(){
	Log::info('Entering route "' . Route::currentRouteName() . '"');
	return 'not yet coded...';
});

Route::any('logout', function(){
	Auth::logout();
	return Redirect::intended('rate');
});


Route::get('users', function()
{
	Log::info('Entering route "' . Route::currentRouteName() . '"');
	Log::info('Log message', array('context' => 'Other helpful information'));
    //return View::make('users');
	$users = User::all();

	return View::make('users')->with('users', $users);
});

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