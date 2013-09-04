<?php

use Illuminate\Database\Migrations\Migration;

class AddBlobToUploads extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('uploads', function($table)
		{
			$table->binary('upload_blob');
			$table->binary('upload_thumb_blob');

			$table->dropColumn('file_name');
			$table->dropColumn('is_validated');
			$table->dropColumn('extension');
			$table->dropColumn('location_dir');
			$table->dropColumn('common_name');
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
			$table->dropColumn('upload_blob');
			$table->dropColumn('upload_thumb_blob');

			$table->string('file_name', 13);
			$table->boolean('is_validated')->default('true');
			$table->string('extension', 10);
			$table->string('location_dir', 255);
			$table->string('common_name', 32);
		});
	}

}