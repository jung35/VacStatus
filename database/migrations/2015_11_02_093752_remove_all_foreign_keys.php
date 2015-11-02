<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class RemoveAllForeignKeys extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('user_list', function(Blueprint $table)
        {
            $table->dropForeign('user_list_user_id_foreign');
        });

        Schema::table('user_list_profile', function(Blueprint $table)
        {
            $table->dropForeign('user_list_profile_profile_id_foreign');
            $table->dropForeign('user_list_profile_user_list_id_foreign');
        });
        
        Schema::table('profile_ban', function(Blueprint $table)
        {
            $table->dropForeign('profile_ban_profile_id_foreign');
        });
        
        Schema::table('profile_old_alias', function(Blueprint $table)
        {
            $table->dropForeign('profile_old_alias_profile_id_foreign');
        });

        Schema::table('user_mail', function(Blueprint $table)
        {
            $table->dropForeign('user_mail_user_id_foreign');
        });
        
        Schema::table('subscription', function(Blueprint $table)
        {
            $table->dropForeign('subscription_user_id_foreign');
            $table->dropForeign('subscription_user_list_id_foreign');
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
