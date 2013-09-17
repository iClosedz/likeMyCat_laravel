<?php

class UploadsController extends BaseController {

	function viewTopUploads(){

		$paginateSize = 10;
		$minimumNumRatings = 5;

        $uploads = Upload::with('ratings', 'guestRatings')
        	->whereRaw('((select count(rating) from ratings where ratings.upload_id = uploads.id) + (select count(rating) from ratings_guest where ratings_guest.upload_id = uploads.id)) >= ' . $minimumNumRatings)
            ->orderBy(DB::raw(
        	'cast(((select sum(rating) from ratings where ratings.upload_id = uploads.id) '
            . ' + (select sum(rating) from ratings_guest where ratings_guest.upload_id = uploads.id)) as float) '
            . ' / ((select count(rating) from ratings where ratings.upload_id = uploads.id) '
            . ' + (select count(rating) from ratings_guest where ratings_guest.upload_id = uploads.id))'
        	), 'desc')->paginate($paginateSize);

		return View::make('uploads.top')->with('uploads', $uploads)->with('paginateSize', $paginateSize); 
	}

	function getTopUploads($timespan){
		$topUploadId = 0;
		$topUploadRating = 0;

		if($timespan === 'ever'){
			$allUploads = Upload::with('ratings', 'guestRatings')->get();
		} else {
			$sinceWhen = date('Y-m-d', strtotime("-1 $timespan"));

			$allUploads = Upload::with(array(
				'ratings' => function($query) use($sinceWhen) { 
					$query->where('updated_at', '>', $sinceWhen);
				}, 
				'guestRatings' => function($query) use($sinceWhen) { 
					$query->where('created_at', '>', $sinceWhen);
				}))->get();
		}

		foreach ($allUploads as $u) {
			if($u->getAvgRating() > $topUploadRating){
				// 5 ratings minimum for all time rating
				if(($timespan === 'ever' && $u->getNumRatings() >= 5) || $timespan !== 'ever'){
					$topUploadId = $u->id;
					$topUploadRating = $u->getAvgRating();
				}
			}
		}

		$topRatedUpload = Upload::find($topUploadId);

		if(!empty($topRatedUpload)){
			return Response::json(array(
				'success' => true, 
				'results' => array(
					'upload_id' => $topRatedUpload->id, 
					'name' => $topRatedUpload->name,
					'rating' => $topUploadRating,
					'ratings' => $topRatedUpload->getNumRatings(),
					'timespan' => $timespan
					)));
		} else {
			return Response::json(array(
				'success' => false, 
				'results' => array(
					'message' => 'no data returned for criteria', 
					'timespan' => $timespan
					)));
		}
	}

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
}
