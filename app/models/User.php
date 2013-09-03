<?php

use Illuminate\Auth\UserInterface;
use Illuminate\Auth\Reminders\RemindableInterface;


class User extends Eloquent {
//implements UserInterface, RemindableInterface {

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

/*
	public function uploads(){
		return $this->hasMany('Uploads');
	}
*/
	/**
	* Done defining various one to many relationships
	*/

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