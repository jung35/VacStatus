<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddSoftDelete extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		// $table->softDeletes();
        Schema::table('user_list', function(Blueprint $table)
        {
        	$table->softDeletes();
        });
        
        Schema::table('user_list_profile', function(Blueprint $table)
        {
        	$table->softDeletes();
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
