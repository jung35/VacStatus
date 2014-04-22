<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class VBanUser extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
    Schema::create('vBanUser', function($table) {
      $table->bigIncrements('id');
      $table->bigInteger('community_id')->unique();
      $table->boolean('private_profile');
      $table->text('display_name');
      $table->integer('steam_creation');
      $table->text('steam_avatar_url_big');
      $table->text('steam_avatar_url_small');
      $table->integer('vac_banned');
      $table->boolean('community_banned');
      $table->boolean('market_banned');
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
		Schema::drop('vBanUser');
	}

}
