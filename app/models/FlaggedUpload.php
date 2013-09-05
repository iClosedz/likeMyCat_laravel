<?php

class FlaggedUpload extends Eloquent {

	/**
	 * The database table used by the model.
	 *
	 * @var string
	 */
	protected $table = 'flagged_uploads';

	public function upload(){
		return $this->belongsTo('Upload');
	}

}

?>