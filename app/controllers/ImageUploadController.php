<?php

//namespace ImageUpload;
//use ImageUpload;

/* treat errors as exceptions for this module */
function exception_error_handler($errno, $errstr, $errfile, $errline ) {
    throw new ErrorException($errstr, $errno, 0, $errfile, $errline);
}
set_error_handler("exception_error_handler");



class ImageUploadController extends BaseController {
    
    const UPLOAD_SIZE_LIMIT = 60000000; /* 6 mb */
    const SESSION_CACHE_SIZE = 40;

    function refreshSessionUploadCache() {
        Log::info("Refreshing session cache for session " . session_id());
        /* only pull uploads user hasn't yet rated */
        if (Auth::check()) {
            $uploadsBatch = Upload::with('user', 'ratings', 'guestRatings')->whereRaw('"id" not in (select upload_id from "ratings" where user_id = ' . Auth::user()->id . ')')->orderBy(DB::raw('RANDOM()'), '')->take(ImageUploadController::SESSION_CACHE_SIZE)->get()->toArray();
        } else {
            $uploadsBatch = Upload::with('user', 'ratings', 'guestRatings')->whereRaw('"id" not in (select upload_id from "ratings_guest" where session_id = \'' . session_id() . '\')')->orderBy(DB::raw('RANDOM()'), '')->take(ImageUploadController::SESSION_CACHE_SIZE)->get()->toArray();
        }
            
        /* if there aren't enough unrated to fill cache, grab some already rated uploads too */
        if (count($uploadsBatch) < ImageUploadController::SESSION_CACHE_SIZE) {
            $count = count($uploadsBatch);

            /* if there were any already retrived, only pull already-rated uploads so we don't get duplicates */
            if($count > 0) {
                 if (Auth::check()) {
                    $additionalUploads = Upload::with('user', 'ratings', 'guestRatings')
                        ->where('id', '<>', $uploadsBatch[$count-1]['id'])
                        ->whereRaw('"id" in (select upload_id from "ratings" where user_id = ' . Auth::user()->id . ')')
                        ->orderBy(DB::raw('RANDOM()', ''))
                        ->take(ImageUploadController::SESSION_CACHE_SIZE - count($uploadsBatch))
                        ->get()->toArray();
                } else {
                    $additionalUploads = Upload::with('user', 'ratings', 'guestRatings')
                        ->where('id', '<>', $uploadsBatch[$count-1]['id'])
                        ->whereRaw('"id" in (select upload_id from "ratings_guest" where session_id = \'' . session_id() . '\')')
                        ->orderBy(DB::raw('RANDOM()', ''))
                        ->take(ImageUploadController::SESSION_CACHE_SIZE - count($uploadsBatch))
                        ->get()->toArray();
                }
            } else {
                /* pull the full cache size amount with no where clause */
                $additionalUploads = Upload::with('user', 'ratings', 'guestRatings')
                    ->orderBy(DB::raw('RANDOM()', ''))
                    ->take(ImageUploadController::SESSION_CACHE_SIZE)
                    ->get()->toArray();
            }

            if(isset($uploadsBatch) && count($uploadsBatch) > 0){
                $uploadsBatch = array_merge($additionalUploads, $uploadsBatch);
            } else {
                $uploadsBatch = $additionalUploads;
            }
        }
                
        Session::set('uploadCache', $uploadsBatch);
        return true;
    }
    
    function getUploads() {
        $howManyResults = 1;
        $excludeImageId = false;
        $shareImageId = false;

        if (Input::has('how_many_results')) {
            $howManyResults = Input::get('how_many_results');
            if ($howManyResults > 10) {
                $howManyResults = 10;
            } elseif ($howManyResults < 1) {
                $howManyResults = 1;
            }
        }
        
        if (Input::has('exclude_image_id')) {
            $excludeImageId = Input::get('exclude_image_id');
        }
        
        if (Input::has('share_image_id')) {
            $shareImageId = Input::get('share_image_id');
        }

        if (!Session::has('uploadCache') || count(Session::get('uploadCache')) === 0) {
            $this->refreshSessionUploadCache();
        }

        $uploadsBatch = Session::get('uploadCache');

        if ($shareImageId && $shareImageId > 0) {
            $sharedUpload = Upload::find($shareImageId)->toArray();
            $uploadsBatch = array_merge($uploadsBatch, array($sharedUpload));
        }
    
        $firstPass = true;
        for ($i = 0; $i < $howManyResults; $i++) {
            if (!isset($uploadsBatch) || count($uploadsBatch) === 0) {
                $this->refreshSessionUploadCache();
                $uploadsBatch = Session::get('uploadCache');
            }

            $curCacheRow = count($uploadsBatch) - 1;

            if($uploadsBatch[$curCacheRow]['id'] == $excludeImageId){
                /* don't show excluded image */
                $i--;
            } elseif($i > 0 && $uploads[$i-1]['upload_id'] == $uploadsBatch[$curCacheRow]['id']){
                /* don't show same image twice in a row */
                $i--;
            } elseif (!$firstPass && isset($shareImageId) && $shareImageId > 0 && $uploadsBatch[$curCacheRow]['id'] == $shareImageId){
                /* don't show shared image again */
                $i--;
            } else {
                $uploads[$i]['upload_id'] = $uploadsBatch[$curCacheRow]['id'];
                $uploads[$i]['cat_name'] = htmlspecialchars(utf8_encode($uploadsBatch[$curCacheRow]['name']));
                $uploads[$i]['file_name'] = URL::route('get uploads/{upload_id}/image', array( $uploadsBatch[$curCacheRow]['id']));
                $uploads[$i]['file_name_thumb'] = URL::route('get uploads/{upload_id}/image/thumb', array($uploadsBatch[$curCacheRow]['id']));
                $uploads[$i]['avg_rating'] = $uploadsBatch[$curCacheRow]['avg_rating'];
                $uploads[$i]['num_ratings'] = $uploadsBatch[$curCacheRow]['num_rating'];
            }

            array_pop($uploadsBatch);
            $firstPass = false;
        }

        Session::set('uploadCache', $uploadsBatch);
        
        return Response::json(array(
            'success' => true,
            'results' => $uploads
        ));
    }
    
    function uploadImage() {
        if (Auth::check()) {
            $uploadedAs = Auth::user()->id;
        } else {
            $uploadedAs = User::where('is_guest', '=', true)->firstOrFail()->id;
        }
        
        Log::info('uploadImage() uploadedAs: ' . $uploadedAs);
        
        $path      = Input::file('photo')->getRealPath();
        $extension = strtolower(Input::file('photo')->getClientOriginalExtension());
        $mime      = Input::file('photo')->getMimeType();
        
        $orientation = 0;

        try{
            $exif        = exif_read_data($path);
            if (!empty($exif['Orientation'])) {
                $orientation = $exif['Orientation'];
            } elseif (isset($exif['COMPUTED']) && isset($exif['COMPUTED']['Orientation'])) {
                $orientation = $exif['COMPUTED']['Orientation'];
            } elseif (isset($exif['IFD0']) && isset($exif['IFD0']['Orientation'])) {
                $orientation = $exif['IFD0']['Orientation'];
            }
        } catch (ErrorException $e) {
            Log::error('Exception when attempting to read exif data: ' .  $e->getMessage());
        }
        
        $uniqueFileName  = uniqid();
        $uploadsDir      = 'uploads/';
        $resizedFileName = $uniqueFileName . '.' . $extension;
        $thumbFileName   = 't_' . $resizedFileName;
        
        $image = new SimpleImage();
        $image->load($path);
        $image->fixRotation($orientation);
        $image->resizeToWidth(600);
        $image->save(base_path() . '/' . $uploadsDir . $resizedFileName);
        $image->resizeToWidth(130);
        $image->save(base_path() . '/' . $uploadsDir . $thumbFileName);
        
        $upload             = new Upload();
        $upload->user_id    = $uploadedAs;
        $upload->upload_dir = base_path() . '/uploads/';
        $upload->file_name  = $resizedFileName;
        $upload->thumb_name = $thumbFileName;
        $upload->mime_type  = $mime;
        
        
        if (Input::has('image_name') && strlen(trim(Input::get('image_name'))) > 0) {
            $upload->name = Input::get('image_name');
        } else {
            $upload->name = 'Kitty';
        }
        
        $result = $upload->save();
        
        if ($result) {
            return Redirect::route('get uploader')->with('upload', $upload);
        } else {
            return Redirect::route('get uploader')->with('error', 'Error uploading image');
        }
    }
    
    public function __construct() {
        // apply csrf filter (only to uploadImage)
        $this->beforeFilter('csrf', array(
            'on' => 'post',
            'only' => array(
                'uploadImage'
            )
        ));
        
        // apply image upload filter (only to uploadImage)
        $this->beforeFilter(function() {
            Log::info('before filter for uploadImage');
            if (!Input::hasFile('photo')) {
                return Redirect::route('get uploader')->with('error', 'No file included with POST');
            }
            
            $allowedExts      = array(
                "gif",
                "jpeg",
                "jpg",
                "png"
            );
            $allowedMimeTypes = array(
                "image/gif",
                "image/jpeg",
                "image/jpg",
                "image/pjpeg",
                "image/x-png",
                "image/png"
            );
            
            $size      = Input::file('photo')->getSize();
            $mime      = Input::file('photo')->getMimeType();
            $extension = strtolower(Input::file('photo')->getClientOriginalExtension());
            
            if ($size > ImageUploadController::UPLOAD_SIZE_LIMIT) {
                return Redirect::route('get uploader')->with('error', 'File too large - 6 MB limit.');
            }
            
            if (!in_array($mime, $allowedMimeTypes) || !in_array($extension, $allowedExts)) {
                return Redirect::route('get uploader')->with('error', 'Invalid file type.');
            }
        }, array(
            'only' => array(
                'uploadImage'
            )
        ));
    }
    
}
?>
