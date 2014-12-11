<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class Timestamps extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		// Admin messages
		Schema::table('admin_messages', function($table)
		{
			$table->dropColumn('date');
		});

		// Albums
		Schema::table('albums', function($table)
		{
			$table->dropColumn('date');
			$table->dropColumn('modified');
		});

		// Announcements
		Schema::table('announcements', function($table)
		{
			$table->dropColumn('date');
		});

		// Attachments
		Schema::table('attachments', function($table)
		{
			$table->dropColumn('date');
		});

		// Forums

		// Photos
		Schema::table('photos', function($table)
		{
			$table->dropColumn('date');
		});

		// Scores

		// Users
		Schema::table('users', function($table)
		{
			$table->dropColumn('joined');
			$table->dropColumn('last_visit');
			$table->dropColumn('last_view');
			$table->dropColumn('onsb');
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
