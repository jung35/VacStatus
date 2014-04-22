<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class SteamUser extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
    Schema::create('steamUser', function($table) {
      $table->bigIncrements('id');
      $table->bigInteger('community_id')->unique();
      $table->text('display_name');
      $table->boolean('site_admin')->default(0);
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
		Schema::drop('steamUser');
	}

}
