<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class VBanList extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
    Schema::create('vBanList', function($table) {
      $table->increments('id');
      $table->integer('steam_user_id');
      $table->integer('v_ban_user_id');
      $table->boolean('check_banned');
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
		Schema::drop('vBanList');
	}

}
