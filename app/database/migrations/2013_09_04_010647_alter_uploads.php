<?php

use Illuminate\Database\Migrations\Migration;

class AlterUploads extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('uploads', function($table)
		{
			$table->text('upload_dir', 128)->default(base_path() . '/uploads/');
			$table->text('file_name', 64);
			$table->text('thumb_name', 64);

			$table->dropColumn('upload_blob');
			$table->dropColumn('upload_thumb_blob');
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
			$table->dropColumn('upload_dir');
			$table->dropColumn('file_name');
			$table->dropColumn('thumb_name');

			$table->binary('upload_blob');
			$table->binary('upload_thumb_blob');
		});
	}

}