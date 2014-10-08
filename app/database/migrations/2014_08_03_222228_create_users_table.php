<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateUsersTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('users', function(Blueprint $table)
		{
			$table->increments('id');
      $table->integer('small_id')->unique();
      $table->string('display_name', 32);

      /**
       * Tour Guide - Each # that is seperated by comma represents tours completed
       * 0 - Index
       * 1 - Profile
       */
      $table->text('tour')->nullable();
      $table->boolean('site_admin')->default(0);
      $table->rememberToken();
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
		Schema::drop('users');
	}

}
