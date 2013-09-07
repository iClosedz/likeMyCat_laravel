<?php

class UploadsController extends BaseController {

	function getImage(Upload $upload){
		$imagePath = $upload->upload_dir . $upload->file_name;
		$contents = file_get_contents($imagePath);

		$response = Response::make($contents, 200);
		$response->header('Content-Type', $upload->mime_type);

		return $response;
	}

	function getImageThumb(Upload $upload){
		$imagePath = $upload->upload_dir . $upload->thumb_name;
		$contents = file_get_contents($imagePath);

		$response = Response::make($contents, 200);
		$response->header('Content-Type', $upload->mime_type);

		return $response;
	}

	function setRating(Upload $upload, $inRating){
		if(Auth::check()){
			$rating = Auth::user()->ratings()->where('upload_id', '=', $upload->id)->first();

			if(empty($rating)){
				$rating = new Rating();
				$rating->user_id = Auth::user()->id;
				$rating->upload_id = $upload->id;
			}

			$rating->rating = $inRating;
			$rating->save();

			//return 'rating saved';
		} else {
			$rating = RatingGuest::where('upload_id', '=', $upload->id)->where('session_id', '=', session_id())->first();
			if(empty($rating)){
				$rating = new RatingGuest();
				$rating->session_id = session_id();
				$rating->upload_id = $upload->id;
			}

			$rating->rating = $inRating;
			$rating->ip_address = ip2long(isset($_SERVER["REMOTE_ADDR"]) ? $_SERVER["REMOTE_ADDR"] : 1);
			$rating->save();

			//return 'rated as guest';
		}

		return Response::json(array(
			'success' => true, 
			'results' => array(
				'upload_id' => $upload->id, 
				'rating' => $rating->rating,
				'as_guest' => Auth::guest())
			));
	}

	function flagUpload(Upload $upload){
		$myIpAddress = ip2long(isset($_SERVER["REMOTE_ADDR"]) ? $_SERVER["REMOTE_ADDR"] : 1);

		$flaggedUpload = FlaggedUpload::where('upload_id', '=', $upload->id)
		->where('ip_address', '=', $myIpAddress)
		->first();

		if(empty($flaggedUpload)){
			$flaggedUpload = new FlaggedUpload();
			$flaggedUpload->upload_id = $upload->id;
			$flaggedUpload->ip_address = $myIpAddress;
		} else {
			$flaggedUpload->touch();
		}

		$result = $flaggedUpload->save();
		
		return Response::json(array(
			'success' => ($result != false), 
			'results' => array(
				'upload_id' => $upload->id, 
				'flagged_id' => $flaggedUpload->id
				)));
	}

	function clearFlags($uploadId){
		if(Auth::user()->hasRole(Role::getByRoleName('admin'))) {
			$result = FlaggedUpload::where('upload_id', '=', $uploadId)->delete();
		} else {
			$result = false;
		}

		return Response::json(array(
			'success' => ($result != false), 
			'results' => array(
				'upload_id' => $uploadId
				)));
	}
}
