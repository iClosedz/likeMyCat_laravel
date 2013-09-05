<?php

use Illuminate\Database\Migrations\Migration;

class AddUniqueToRatings extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('ratings', function($table)
		{
			$table->unique(array('user_id','upload_id') );
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::table('ratings', function($table)
		{
			$table->dropUnique('ratings_user_id_upload_id_unique');
		});
	}

}