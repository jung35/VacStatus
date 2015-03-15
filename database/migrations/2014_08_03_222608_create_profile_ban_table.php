<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateProfileBanTable extends Migration {

  /**
   * Run the migrations.
   *
   * @return void
   */
  public function up()
  {
    Schema::create('profile_ban', function(Blueprint $table)
    {
      $table->increments('id');
      $table->unsignedInteger('profile_id');
      $table->foreign('profile_id')->references('id')->on('profile');
      $table->boolean('community');
      $table->integer('vac');
      $table->boolean('trade');
      $table->boolean('unban');
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
    Schema::drop('profile_ban');
  }

}
