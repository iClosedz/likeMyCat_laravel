<?php

class UserRole extends Eloquent {

	/**
	 * The database table used by the model.
	 *
	 * @var string
	 */
	protected $table = 'user_roles';

	public function role()
    {
        return $this->hasOne('Role', 'id');
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