<?php

use Illuminate\Database\Migrations\Migration;

class AddFlaggedUploadsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('flagged_uploads', function($table)
		{
			$table->increments('id');
			$table->integer('upload_id'); 
			$table->bigInteger('ip_address'); 
			$table->timestamps();

			$table->foreign('upload_id')
				->references('id')
				->on('uploads')
				->onDelete('cascade');

			$table->unique(array('upload_id','ip_address') );
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('flagged_uploads');
	}

}