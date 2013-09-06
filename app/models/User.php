<?php

use Illuminate\Auth\UserInterface;
use Illuminate\Auth\Reminders\RemindableInterface;


class User extends Eloquent 
implements UserInterface, RemindableInterface {

	/**
	 * The database table used by the model.
	 *
	 * @var string
	 */
	protected $table = 'users';

	/**
	 * The attributes excluded from the model's JSON form.
	 *
	 * @var array
	 */
	protected $hidden = array('password');

	/**
	 * Get the unique identifier for the user.
	 *
	 * @return mixed
	 */
	public function getAuthIdentifier()
	{
		return $this->getKey();
	}

	/**
	 * Get the password for the user.
	 *
	 * @return string
	 */
	public function getAuthPassword()
	{
		return $this->password;
	}

	/**
	 * Get the e-mail address where password reminders are sent.
	 *
	 * @return string
	 */
	public function getEmail()
	{
		return $this->email;
	}

	public function getUsername(){
		return $this->getEmail();
	}

	// to conform to reminder interface
	public function getReminderEmail()
	{
		return $this->email;
	}



	/**
	* Define various one to many relationships
	*/
	public function ratings(){
		return $this->hasMany('Rating');
	}

	public function userRoles(){
		return $this->hasMany('UserRole');
	}

	public function uploads(){
		return $this->hasMany('Upload')->orderBy('uploads.id', 'desc');
	}

	// we can define a many-to-many relation using the belongsToMany method:
	public function roles()
	{
		return $this->belongsToMany('Role'); // is this right?
	}

	/**
	* Done defining various one to many relationships
	*/

	public function hasRole($roleName){
		$roles = $this->userRoles;
		foreach ($roles as $role) {
			//Log::info('role: ' . $role->role->role_name);
			if($role->role->role_name == $roleName){
				return true;
			}
		}	

		return false;
	}

	public function addRole($roleName){
		if($this->hasRole($roleName)){
			return true;
		}

		$role = Role::where('role_name', '=', $roleName)->firstOrFail();

		$userRole = new UserRole();
		$userRole->role = $role;
		$userRole->user_id = $this->id;

		$this->userRoles()->save($userRole);
		return true;
	}

	public function scopeByEmail($query, $email){
		return $query->where('email', '=', $email);
	}

	public static function getUserByEmail($email){
		$users = User::byEmail($email)->get();
		if(isset($users) && count($users) === 1){
			return $users[0];
		} else {
			return false;
		}
	}
}

?>