<?php

use Illuminate\Database\Migrations\Migration;

class AddUniquesToUserRoles extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('user_roles', function($table)
		{
			$table->unique(array('user_id','role_id') );
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::table('user_roles', function($table)
		{
			$table->dropUnique('user_roles_user_id_role_id_unique');
		});
	}

}