<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class VBanUserAlias extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
    Schema::create('vBanUserAlias', function($table) {
      $table->bigIncrements('id');
      $table->bigInteger('v_ban_user_id');
      $table->text('alias');
      $table->integer('time_used');
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
		Schema::drop('vBanUserAlias');
	}

}
