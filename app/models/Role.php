<?php

class Role extends Eloquent {

	/**
	 * The database table used by the model.
	 *
	 * @var string
	 */
	protected $table = 'roles';

	public function role()
    {
        return $this->hasMany('UserRole', 'role_id');
        //return $this->hasOne('Role', 'role_id');

    }
}

?>