<?php

//namespace ImageUpload;
//use ImageUpload;

class ImageUploadController extends BaseController {

	const UPLOAD_SIZE_LIMIT = 60000000; // 6 mb

	function getUploads(){
		$howManyResults = 1;
		$excludeImageId = false;
		$shareImageId = false;

		if(Input::has('how_many_results')){
			$howManyResults = Input::get('how_many_results');
			if($howManyResults > 10){
				$howManyResults = 10;
			} elseif ($howManyResults < 1) {
				$howManyResults = 1;
			}
		}

		if(Input::has('exclude_image_id')){
			$excludeImageId = Input::get('exclude_image_id');
		}

		if(Input::has('share_image_id')){
			$shareImageId = Input::get('share_image_id');
		}

		if($shareImageId && $shareImageId > 0){
			Log::info('shareImageId: ' . $shareImageId);
			$firstUpload = Upload::find($shareImageId);
		} else {
			if($excludeImageId && $excludeImageId > 0){
				$firstUpload = Upload::with('user', 'ratings', 'guestRatings')->where('id', '!=', $excludeImageId)->orderBy(DB::raw('RANDOM()'))->firstOrFail();
			} else {
    			$firstUpload = Upload::take(1)->firstOrFail(); // fixme
    		}
    	}

    	$uploads[0]['upload_id'] = $firstUpload->id;
    	$uploads[0]['cat_name'] = htmlspecialchars(utf8_encode($firstUpload->name));
    	$uploads[0]['file_name'] = URL::route('get cat/{upload_id}/image', array($firstUpload->id));
    	$uploads[0]['file_name_thumb'] = URL::route('get cat/{upload_id}/image/thumb', array($firstUpload->id));
    	$uploads[0]['avg_rating'] = $firstUpload->getAvgRating();
    	$uploads[0]['num_ratings'] = $firstUpload->getNumRatings();

    	if($howManyResults > 1){

    		if(Auth::check()){
    			$additionalUploads = Upload::with('user', 'ratings', 'guestRatings')
                ->where('id', '!=', $firstUpload->id)
    			->whereRaw('"id" not in (select upload_id from "ratings" where user_id = ' . Auth::user()->id . ')')
    			->orderBy(DB::raw('RANDOM()'))
    			->take($howManyResults-1)
    			->get();
    		}

    		if(empty($additionalUploads)){
    		//Log::info('no additional uploads not yet rated - just grabbing random uploads now');
    			$additionalUploads = Upload::with('user', 'ratings', 'guestRatings')
                ->where('id', '!=', $firstUpload->id)
    			->orderBy(DB::raw('RANDOM()'))
    			->take($howManyResults-1)
    			->get();
    		} else if(count($additionalUploads) < $howManyResults-1){
                $additionalUploads = Upload::with('user', 'ratings', 'guestRatings')
                ->where('id', '!=', $firstUpload->id)
                ->orderBy(DB::raw('RANDOM()'))
                ->take(($howManyResults - 1) - count($additionalUploads))
                ->get();
            }

    		for($i = 0; $i < count($additionalUploads); $i++){
    			$upload = $additionalUploads[$i];
    			$uploads[$i+1]['upload_id'] = $upload->id;
    			$uploads[$i+1]['cat_name'] = htmlspecialchars(utf8_encode($upload->name));
    			$uploads[$i+1]['file_name'] = URL::route('get cat/{upload_id}/image', array($upload->id));
    			$uploads[$i+1]['file_name_thumb'] = URL::route('get cat/{upload_id}/image/thumb', array($upload->id));
    			$uploads[$i+1]['avg_rating'] = $upload->getAvgRating();
    			$uploads[$i+1]['num_ratings'] = $upload->getNumRatings();
    		}
    	}

    	return Response::json(array('success' => true, 'results' => $uploads));
    }

    function uploadImage(){
    	if(Auth::check()){
    		$uploadedAs = Auth::user()->id;
    	} else {
    		$uploadedAs = User::where('is_guest', '=', true)->firstOrFail()->id;
    	}

    	Log::info('uploadedAs: ' . $uploadedAs);

    	$path = Input::file('photo')->getRealPath();
    	$extension = strtolower(Input::file('photo')->getClientOriginalExtension());
    	$mime = Input::file('photo')->getMimeType();

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
    	$upload->user_id = $uploadedAs;
    	$upload->upload_dir = base_path() . '/uploads/';
    	$upload->file_name = $resizedFileName;
    	$upload->thumb_name = $thumbFileName;
    	$upload->mime_type = $mime;


    	if(Input::has('image_name') && strlen(trim(Input::get('image_name'))) > 0){
    		$upload->name = Input::get('image_name');
    	} else {
    		$upload->name = 'Kitty';
    	}

    	$result = $upload->save();

    	if($result){
    		return Redirect::route('get upload')->with('upload', $upload);
    	} else {
    		return Redirect::route('get upload')->with('error', 'Error uploading image');
    	}
    }

    public function __construct(){
		// apply csrf filter (only to uploadImage)
    	$this->beforeFilter('csrf', array('on' => 'post', 'only' => array('uploadImage'))); 

		// apply image upload filter (only to uploadImage)
    	$this->beforeFilter(function(){
    		Log::info('before filter for uploadImage');
    		if (!Input::hasFile('photo')){
    			return Redirect::route('get upload')->with('error', 'No file included with POST');
    		}

    		$allowedExts = array("gif", "jpeg", "jpg", "png");
    		$allowedMimeTypes = array("image/gif", "image/jpeg", "image/jpg", "image/pjpeg", "image/x-png", "image/png");

    		$size = Input::file('photo')->getSize();
    		$mime = Input::file('photo')->getMimeType();
    		$extension = strtolower(Input::file('photo')->getClientOriginalExtension());

    		if($size > ImageUploadController::UPLOAD_SIZE_LIMIT){
    			return Redirect::route('get upload')->with('error', 'File too large - 6 MB limit.');
    		}

    		if(!in_array($mime, $allowedMimeTypes) || !in_array($extension, $allowedExts)){
    			return Redirect::route('get upload')->with('error', 'Invalid file type.');
    		}
    	}, array('only' => array('uploadImage')));
    }

}
?>