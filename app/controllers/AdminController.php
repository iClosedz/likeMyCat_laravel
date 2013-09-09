<?php

class AdminController extends BaseController {

	protected $user;
	protected $userWithTrashed;

	protected $upload;
	protected $uploadWithTrashed;

	public function __construct(User $user, Upload $upload){
		//$this->beforeFilter('auth');
		$this->beforeFilter('csrf', array('on' => 'post'));
		$this->beforeFilter(function(){
			if(!Auth::check()){
				return Redirect::route('get login')->with('error', 'You must be logged in to access that page.');
			}

			if(!Auth::user()->hasRole(Role::getByRoleName('admin'))){
				return Redirect::route('get /')->with('error', 'You must be an admin to access this page!');
			}
		});

		$this->user = $user->all(); 
		$this->userWithTrashed = $user->withTrashed();

		$this->upload = $upload->all(); 
		$this->uploadWithTrashed = $upload->withTrashed();

	}

	function showFlaggedUploads($userId){
		return View::make('manageUploads')
			->with('user', Auth::user())
			->with('uploads', Upload::withTrashed()
				->with('user', 'ratings', 'guestRatings', 'flagged')
				->orderBy('id', 'DESC')
				->paginate(4)
				);
	}

	function showRoles($userId){
		$user = User::withTrashed()->find($userId);

		return View::make('admin.userRoles')
		->with('user', Auth::user())
		->with('userBeingManaged', $user);
	}

	function grantRole($userId, $roleId){
		$user = User::withTrashed()->find($userId);
		$role = Role::find($roleId);

		$status = $user->grantRole($role);
		$user->save();

		return Redirect::action('AdminController@showRoles', array($user->id))
		->with('success', 'Role updated');
	}

	function revokeRole($userId, $roleId){
		$user = User::withTrashed()->find($userId);
		$role = Role::find($roleId);

		if($user->id === Auth::user()->id && $role->id === Role::byRoleName('admin')->firstOrFail()->id){
			return Redirect::action('AdminController@showRoles', array($user->id))
			->with('error', 'Can\'t remove self from admin role');
		}

		$status = $user->revokeRole($role);
		$user->save();

		return Redirect::action('AdminController@showRoles', array($user->id))
		->with('success', 'Role updated');
	}

	function disableUser($userId){
		$user = User::find($userId);

		if($user->hasRole(Role::getByRoleName('admin'))){
			return Response::json(array(
				'success' => false, 
				'results' => array('user_id' => $user->id, 'message' => 'can\'t disable admin user')
				));
		}

		$status = $user->delete();
		return Response::json(array(
			'success' => $status, 
			'results' => array('user_id' => $user->id)
			));
	}

	function enableUser($userId){
		$user = User::withTrashed()->find($userId);
		$status = $user->restore();

		return Response::json(array(
			'success' => $status, 
			'results' => array('user_id' => $user->id)
			));
	}

	function showUsers(){
		return View::make('admin.users')
		->with('user', Auth::user())
		->with('users', User::withTrashed()
			->with('roles')
			->orderBy('id', 'DESC')
			->get()
			);
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

	function getImageIncDeleted($uploadId){
		$upload = Upload::withTrashed()->findOrFail($uploadId);
		$imagePath = $upload->upload_dir . $upload->file_name;
		$contents = file_get_contents($imagePath);

		$response = Response::make($contents, 200);
		$response->header('Content-Type', $upload->mime_type);

		return $response;
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