<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateMailTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('mail', function(Blueprint $table)
		{
			$table->increments('id');
      $table->unsignedInteger('user_id');
      $table->foreign('user_id')->references('id')->on('users');
      $table->text('email');
      $table->text('verify');
			$table->timestamps();
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('mail');
	}

}
