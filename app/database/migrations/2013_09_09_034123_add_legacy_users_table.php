<?php

use Illuminate\Database\Migrations\Migration;

class AddLegacyUsersTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('users_legacy', function($table)
		{
			$table->increments('id');
			$table->string('email')->unique();
			$table->string('password');
			$table->string('salt');
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('users_legacy');
	}

}