<?php

class Rating extends Eloquent {

	/**
	 * The database table used by the model.
	 *
	 * @var string
	 */
	protected $table = 'ratings';
	protected $touches = array('user');

	public function user(){
		return $this->belongsTo('User');
	}

	public function upload(){
		return $this->belongsTo('Upload');
	}

}

?>