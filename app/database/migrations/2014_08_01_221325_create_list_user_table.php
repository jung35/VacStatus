<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateListUserTable extends Migration {

  /**
   * Run the migrations.
   *
   * @return void
   */
  public function up()
  {
    Schema::create('list_user', function(Blueprint $table)
    {
      $table->increments('id');
      $table->unsignedInteger('list_id');
      $table->unsignedInteger('profile_id');
      $table->foreign('list_id')->references('id')->on('list');
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
    Schema::drop('list_user');
  }

}
