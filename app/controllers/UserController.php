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


	/**
	 * Display a listing of the resource.
	 *
	 * @return Response
	 */
	public function index()
	{
		//echo 'hi there'
		return 'in user controller index';
	}

	/**
	 * Show the form for creating a new resource.
	 *
	 * @return Response
	 */
	public function create()
	{
		return 'in user controller create';
	}

	/**
	 * Store a newly created resource in storage.
	 *
	 * @return Response
	 */
	public function store()
	{
		//
	}

	/**
	 * Display the specified resource.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function show($id)
	{
		//
	}

	/**
	 * Show the form for editing the specified resource.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function edit($id)
	{
		//
	}

	/**
	 * Update the specified resource in storage.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function update($id)
	{
		//
	}

	/**
	 * Remove the specified resource from storage.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function destroy($id)
	{
		//
	}

}
