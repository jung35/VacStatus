<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateProfileTable extends Migration {

  /**
   * Run the migrations.
   *
   * @return void
   */
  public function up()
  {
    Schema::create('profile', function(Blueprint $table)
    {
      $table->increments('id');
      $table->integer('small_id')->unique();
      $table->string('display_name', 32);
      $table->tinyInteger('privacy');
      $table->text('avatar_thumb');
      $table->text('avatar');
      $table->integer('profile_created')->nullable();
      $table->text('alias')->nullable();
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
    Schema::drop('profile');
  }

}
