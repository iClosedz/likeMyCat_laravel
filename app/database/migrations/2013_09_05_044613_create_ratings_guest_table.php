<?php

use Illuminate\Database\Migrations\Migration;

class CreateRatingsGuestTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('ratings_guest', function($table)
		{
			$table->increments('id');
			$table->text('session_id'); // instead of user_id
			$table->integer('upload_id');
			$table->integer('rating');
			$table->bigInteger('ip_address'); // for banning purposes
			//$table->timestamps();

			$table->foreign('upload_id')
				->references('id')
				->on('uploads')
				->onDelete('cascade');

			$table->unique(array('session_id','upload_id') );
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('ratings_guest');
	}

}