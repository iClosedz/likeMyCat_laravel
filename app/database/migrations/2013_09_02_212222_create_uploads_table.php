<?php

use Illuminate\Database\Migrations\Migration;

class CreateUploadsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('uploads', function($table)
		{
			$table->increments('id');
			$table->string('file_name', 13);
			$table->integer('user_id');
			$table->boolean('is_validated')->default('true');
			$table->string('extension', 10);
			$table->string('location_dir', 255);
			$table->string('common_name', 32);
			$table->timestamps();

			$table->foreign('user_id')
				->references('id')
				->on('users')
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
		Schema::drop('uploads');
	}

}