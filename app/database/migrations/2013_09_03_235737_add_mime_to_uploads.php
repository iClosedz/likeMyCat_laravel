<?php

use Illuminate\Database\Migrations\Migration;

class AddMimeToUploads extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('uploads', function($table)
		{
			$table->text('mime_type', 32)->default('image/jpg');
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::table('uploads', function($table)
		{
			$table->dropColumn('mime_type');
		});
	}

}