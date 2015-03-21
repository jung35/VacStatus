<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterPushbulletVerify extends Migration {

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('user_mail', function(Blueprint $table)
        {
            $table->text('pushover_verify');
            $table->text('pushbullet_verify');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('user_mail', function(Blueprint $table)
        {
            //
        });
    }

}