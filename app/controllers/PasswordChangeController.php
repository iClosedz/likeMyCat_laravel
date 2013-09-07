<?php

class PasswordChangeController extends BaseController {

	public function __construct(){
        $this->beforeFilter('auth');
        $this->beforeFilter('csrf', array('on' => 'post'));
    }

	public function index(){
		return View::make('passwordChange')->with('user', Auth::user());
	}


	public function store(){
		$email = Auth::user()->email;
		$password = Input::get('current_password');
		$newPassword = Input::get('new_password');
		$newPasswordConfirm = Input::get('new_password_confirmation');

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
	}



}