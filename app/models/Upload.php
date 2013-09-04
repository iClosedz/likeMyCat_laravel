<?php

class Upload extends Eloquent {

	/**
	 * The database table used by the model.
	 *
	 * @var string
	 */
	protected $table = 'uploads';

	public function user(){
		return $this->belongsTo('User');
	}

	public function getNumRatings(){
		return 0; //TODO: fix me
	}

	public function getAvgRating(){
		return 0; //TODO: fix me
	}

}

?>