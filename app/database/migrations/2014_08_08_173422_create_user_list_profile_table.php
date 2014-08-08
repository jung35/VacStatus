<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateUserListProfileTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('user_list_profile', function(Blueprint $table)
		{
			$table->increments('id');
      $table->unsignedInteger('user_list_id');
      $table->unsignedInteger('profile_id');
      $table->foreign('user_list_id')->references('id')->on('user_list');
      $table->foreign('profile_id')->references('id')->on('profile');

      // This is the username that was seen when user added this profile to list
      $table->string('profile_name', 32);
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
		Schema::drop('user_list_profile');
	}

}
