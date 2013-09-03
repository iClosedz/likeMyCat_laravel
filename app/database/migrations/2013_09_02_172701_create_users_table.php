<?php

use Illuminate\Database\Migrations\Migration;

class CreateUsersTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('users', function($table)
		{
			$table->increments('id');
			$table->string('email')->unique();
			$table->string('password');
			$table->boolean('is_guest');
			$table->bigInteger('ip_address'); // see http://stackoverflow.com/questions/6427786/ip-address-storing-in-mysql-database
			//$table->timestamp('created_at')->default(date("Y-m-d H:i:s"));
			//$table->timestamp('modified_at')->default('0000-00-00 00:00');
			$table->timestamps();
		});
	}

	public function down()
	{
		Schema::drop('users');
	}

}