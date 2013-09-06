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

		$this->call('UserTableSeeder');
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
    }

}