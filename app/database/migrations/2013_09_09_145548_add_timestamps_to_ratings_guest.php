<?php

use Illuminate\Database\Migrations\Migration;

class AddTimestampsToRatingsGuest extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('ratings_guest', function($table)
		{
			$table->timestamp('created_at')->default('09-08-2013 00:00:00');
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::table('ratings_guest', function($table)
		{
			$table->dropColumn('created_at');
		});
	}

}