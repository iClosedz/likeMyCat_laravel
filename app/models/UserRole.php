<?php

class UserRole extends Eloquent {

	/**
	 * The database table used by the model.
	 *
	 * @var string
	 */
	protected $table = 'user_roles';
    public $timestamps = true;



	public function role()
    {
        return $this->hasOne('Role', 'id');
        //return $this->hasOne('Role', 'role_id');

    }

    public function user(){
        return $this->hasOne('User', 'user_id');
    }

/*
	public function user(){
        return $this->belongsTo('User', 'user_id');
    }

    public function role(){
        return $this->belongsTo('Role', 'role_id');
    }
    */
}

?>