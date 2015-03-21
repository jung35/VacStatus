<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateProfileOldAliasTable extends Migration {

  /**
   * Run the migrations.
   *
   * @return void
   */
  public function up()
  {
    Schema::create('profile_old_alias', function(Blueprint $table)
    {
      $table->increments('id');
      $table->unsignedInteger('profile_id');
      $table->foreign('profile_id')->references('id')->on('profile');
      $table->integer('seen');
      $table->text('seen_alias');
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
    Schema::drop('profile_old_alias');
  }

}
