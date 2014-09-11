<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class StandardizationThree extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		// Groups
		Schema::table('groups', function($table)
		{
			$table->timestamps();
		});

		// Shoutbox
		Schema::table('shoutbox', function($table)
		{
			$table->timestamps();
		});

		// Applications
		Schema::table('applications', function($table)
		{
			$table->timestamps();
		});

		// Avatars
		Schema::table('avatars', function($table)
		{
			$table->timestamps();
		});

		// Messages
		Schema::table('messages', function($table)
		{
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
		//
	}

}
