<?php

//namespace ImageUpload;
//use ImageUpload;

class ImageUploadController extends BaseController {

	const UPLOAD_SIZE_LIMIT = 60000000; // 6 mb

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

		if(Input::has('image_name')){
			$upload->name = Input::get('image_name');
		}

		$upload->save();

		return Redirect::route('get upload')->with('upload', $upload);
		//return View::make('upload')->with('user', Auth::user());
	}

	public function __construct(){

		// apply csrf filter
		$this->beforeFilter('csrf', array('on' => 'post'));

		// apply image upload filter
		$this->beforeFilter(function(){
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
		});
	}

}
?>