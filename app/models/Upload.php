<?php

class Upload extends Eloquent {

	/**
	 * The database table used by the model.
	 *
	 * @var string
	 */
	protected $table = 'uploads';
	protected $softDelete = true;
	protected $touches = array('user');
	protected $appends = array('avg_rating','num_rating');
	protected $visible = array('id', 'avg_rating', 'num_rating', 'name');

	public function user(){
		return $this->belongsTo('User')->withTrashed();
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
		//$sum = ($this->ratings()->sum('rating')) + ($this->guestRatings()->sum('rating'));

		// summing manually since eager loading doesn't seem to work correctly for SUM()
		$sumNormalRatings = 0;
		foreach ($this->ratings as $rating) {
			$sumNormalRatings += $rating->rating;
		}

		$sumGuestRatings = 0;
		foreach ($this->guestRatings as $rating) {
			$sumGuestRatings += $rating->rating;
		}

		$sum = $sumNormalRatings + $sumGuestRatings;
		$numRatings = $this->getNumRatings();

		if($numRatings == 0){
			return 0;
		} else {
			return $sum / $numRatings;
		}
	}

	public function getAvgRatingAttribute(){
	    return $this->getAvgRating();
	}

	public function getNumRatingAttribute(){
	    return $this->getNumRatings();
	}

}

?>