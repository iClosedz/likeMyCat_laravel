<?php

class DatabaseSeeder extends Seeder {

	/**
	 * Run the database seeds.
	 *
	 * @return void
	 */
	public function run()
	{
		Eloquent::unguard();

		$this->call('RoleTableSeeder');
		$this->call('UserTableSeeder');
	}
}

class RoleTableSeeder extends Seeder {

	public function run()
	{
		DB::table('roles')->delete();

        // create roles
		Role::create(array('admin'));
		Role::create(array('user'));
		Role::create(array('uploader'));
		Role::create(array('reviewer'));
		Role::create(array('guest'));
	}
}

class UserTableSeeder extends Seeder {

	public function run()
	{
		DB::table('users')->delete();

        // create admin user
		User::create(array(
			'email' => 'admin@admin.com',
			'password' => Hash::make('admin'),
			'is_guest' => false,
			'ip_address' => ip2long(isset($_SERVER["REMOTE_ADDR"]) ? $_SERVER["REMOTE_ADDR"] : 1)
			));

        // create guest user
		User::create(array(
			'email' => 'guest@guest.com',
        	'password' => 'NOT_A_PASSWORD', // guest shouldn't be login-able
        	'is_guest' => true,
        	'ip_address' => ip2long(isset($_SERVER["REMOTE_ADDR"]) ? $_SERVER["REMOTE_ADDR"] : 1)
        	));

		$adminRole = Role::getByRoleName('admin');
		$userRole = Role::getByRoleName('user');
		$uploaderRole = Role::getByRoleName('uploader');
		$reviewerRole = Role::getByRoleName('reviewer');
		$guestRole = Role::getByRoleName('guest');

		$adminUser = User::getUserByEmail('admin@admin.com');
		$guestUser = User::getUserByEmail('guest@guest.com');

		$adminUser->grantRole($adminRole)
					->grantRole($userRole)
					->grantRole($uploaderRole)
					->grantRole($reviewerRole);
					
		$guestUser->grantRole($guestRole);
	}
}

