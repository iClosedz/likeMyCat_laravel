<?php

class Upload extends Eloquent {

	/**
	 * The database table used by the model.
	 *
	 * @var string
	 */
	protected $table = 'uploads';
	protected $softDelete = true;

	public function user(){
		return $this->belongsTo('User');
	}

	public function ratings(){
		return $this->hasMany('Rating');
	}

	public function guestRatings(){
		return $this->hasMany('RatingGuest');
	}

	public function flagged(){
		return $this->hasMany('FlaggedUpload');
	}

	public function getNumFlagged(){
		return $this->flagged->count();
	}

	public function getNumRatings(){
		return ($this->ratings->count()) + ($this->guestRatings->count());		
	}

	public function getAvgRating(){
		$sum = ($this->ratings()->sum('rating')) + ($this->guestRatings()->sum('rating'));
		$numRatings = $this->getNumRatings();

		if($numRatings == 0){
			return 0;
		} else {
			return $sum / $numRatings;
		}
	}

}

?>