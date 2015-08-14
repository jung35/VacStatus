<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddBanChangesToProfileBansTables extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('profile_ban', function(Blueprint $table)
        {
            $table->renameColumn('vac_banned_on', 'last_ban_date')->nullable();
            $table->renameColumn('vac', 'vac_ban')->default(0);
            $table->integer('game_ban')->default(0);
            $table->dropColumn('unban');
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
