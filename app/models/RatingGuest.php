<?php

class RatingGuest extends Eloquent {

	/**
	 * The database table used by the model.
	 *
	 * @var string
	 */
	protected $table = 'ratings_guest';
	public $timestamps = false;

	public function upload(){
		return $this->belongsTo('Upload');
	}

}

?>