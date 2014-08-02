<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateUsersFriendsTable extends Migration {

  /**
   * Run the migrations.
   *
   * @return void
   */
  public function up()
  {
    Schema::create('users_friends', function(Blueprint $table)
    {
      $table->increments('id');
      $table->unsignedInteger('user_id');
      $table->foreign('user_id')->references('id')->on('users');
      $table->text('friend_ids');
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
    Schema::drop('users_friends');
  }

}
