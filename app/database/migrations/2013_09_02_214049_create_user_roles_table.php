<?php

use Illuminate\Database\Migrations\Migration;

class CreateUserRolesTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('user_roles', function($table)
		{
			$table->increments('id');
			$table->integer('user_id');
			$table->integer('role_id');
			$table->timestamps();

			$table->foreign('user_id')
				->references('id')
				->on('users')
				->onDelete('cascade');

			$table->foreign('role_id')
				->references('id')
				->on('roles')
				->onDelete('cascade');
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('user_roles');
	}

}