<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AllowEmailVerifyAndPushbulletVerifyToBeNullOnUserMail extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('user_mail', function(Blueprint $table)
        {
            $table->text('verify')->nullable()->change();
            $table->text('pushbullet_verify')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
    }
}
