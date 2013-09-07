<?php

class UserController extends BaseController {

	public function __construct(){
		//$this->beforeFilter('auth');
		$this->beforeFilter('csrf', array('on' => 'post'));
		$this->beforeFilter(function(){
			if(!Auth::check()){
				Session::put('url.intended', URL::current());
				return Redirect::route('get login')->with('info', 'You must be logged in to access that page.');
			}
		});
	}

	public function showUploads(){
		return View::make('manageUploads')
		->with('user', Auth::user())
		->with('uploads', Auth::user()->uploads()
			->with('user', 'ratings', 'guestRatings')
			->orderBy('id', 'DESC')
			->paginate(4)
				//->get()
			);
	}

	function hideUpload(Upload $upload){
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
	}

}
