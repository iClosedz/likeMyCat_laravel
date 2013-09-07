<?php

class AdminController extends BaseController {

	public function __construct(){
		$this->beforeFilter('auth');
		$this->beforeFilter('csrf', array('on' => 'post'));
		$this->beforeFilter(function(){
			Log::info('before filter for AdminController');
			if(!Auth::check()){
				Session::put('url.intended', URL::current());
				return Redirect::route('get login')->with('info', 'You must be logged in to access that page.');
			}

			if(!Auth::user()->hasRole(Role::getByRoleName('admin'))){
				return Redirect::route('get /')->with('error', 'You must be an admin to access this page!');
			}
		});
	}

	function showUsers(){
		return 'user admin page';
	}

	function showUploads(){
		return View::make('manageUploads')
			->with('user', Auth::user())
			->with('uploads', Upload::withTrashed()
				->with('user', 'ratings', 'guestRatings', 'flagged')
				->orderBy('id', 'DESC')
				->paginate(4)
				);
	}

	function deleteUploadById($uploadId){
		$upload = Upload::withTrashed()->findOrFail($uploadId);
		$status = $upload->forceDelete();

		return Response::json(array(
			'success' => $status, 
			'results' => array('upload_id' => $upload->id)
			));
	}

	function hideUpload(Upload $upload){
		$status = $upload->delete();

		return Response::json(array(
			'success' => $status, 
			'results' => array('upload_id' => $upload->id)
			));
	}

	function restoreUploadById($uploadId){
		$upload = Upload::withTrashed()->findOrFail($uploadId);
		$status = $upload->restore();

		return Response::json(array(
			'success' => $status, 
			'results' => array('upload_id' => $upload->id)
			));
	}

	function getThumbIncDeleted($uploadId){
		$upload = Upload::withTrashed()->findOrFail($uploadId);
		$imagePath = $upload->upload_dir . $upload->thumb_name;
		$contents = file_get_contents($imagePath);

		$response = Response::make($contents, 200);
		$response->header('Content-Type', $upload->mime_type);

		return $response;
	}

}