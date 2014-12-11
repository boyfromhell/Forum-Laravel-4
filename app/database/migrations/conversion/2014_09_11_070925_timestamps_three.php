<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class TimestampsThree extends Migration {

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
			$table->dropColumn('date');
		});

		// Shoutbox
		Schema::table('shoutbox', function($table)
		{
			$table->dropColumn('time');
		});

		// Applications
		Schema::table('applications', function($table)
		{
			$table->dropColumn('date_applied');
		});

		// Avatars
		Schema::table('avatars', function($table)
		{
			$table->dropColumn('date');
		});

		// Messages
		Schema::table('messages', function($table)
		{
			$table->dropColumn('date_sent');
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
