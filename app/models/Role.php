<?php

class Role extends Eloquent {

	/**
	 * The database table used by the model.
	 *
	 * @var string
	 */
	protected $table = 'roles';

	public function users()
	{
		return $this->belongsToMany('User', 'user_roles')->withTimestamps();
	}

	public static function scopeByRoleName($query, $roleName){
		return $query->where('role_name', '=', $roleName);
	}

	public static function getByRoleName($roleName){
		return Role::byRoleName($roleName)->firstOrFail();
	}

}

?>