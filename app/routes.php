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

Route::post('login', function(){
	if (Auth::attempt(array('email' => $email, 'password' => $password))){
		return 'auth successful';//Redirect::intended('dashboard');
	} else {
		return 'auth faiiled';
	}
});

/*
Route::any('user', 'UserController@index');
Route::post('login', 'LoginController@index');
Route::get('login', function(){
	//todo: redirect to login view
	return 'requires post data...';
});

*/
/*
Route::post('login', function(){
	Log::info('Entering \'login\' route.');

	$username = trim(Input::get('user_id'));
	$user = User::getUserByEmail($username);
	if(!isset($user) || $user == false){
		return 'no user with that username found';
	}

	$password = Input::get('password');
	//$password = Hash::make($password);
	//$user->password = $password;
	//$user->save();

	if (Hash::check($password, $user->password)){
		return 'passwords matched for ' . $username . '!';
	} else {
		return 'invalid password';
	}

	//return 'Username: ' . Input::get('user_id') . 'password: ' . Input::get('password');
});
*/

Route::get('users', function()
{
	Log::info('Entering \'users\' route.');
	Log::info('Log message', array('context' => 'Other helpful information'));
    //return View::make('users');
	$users = User::all();

	return View::make('users')->with('users', $users);
});

Route::get('rate', function()
{
	Log::info('Entering \'rate\' route.');
    //return View::make('users');
    //$users = User::find(3);
    //$user = User::where('email', '=', 'davidkey@gmail.com')->firstOrFail();
    //$users = User::byEmail('davidkey@gmail.com')->get();
    //$userId = $users[0]->getKey();
    //$user = User::find($userId);
	$user = User::getUserByEmail('davidkey@gmail.com');

    $roles = $user->userRoles;//->role;

/*
    foreach ($roles as $role) {
		Log::info('role: ' . $role->role->role_name);
	}
	*/

	return View::make('rate')->with('user', $user)->with('roles', $roles);
});


?>