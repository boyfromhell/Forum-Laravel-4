<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class TimestampsTwo extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		// Downloads
		Schema::table('downloads', function($table)
		{
			$table->dropColumn('date');
		});

		// Posts
		Schema::table('posts', function($table)
		{
			$table->dropColumn('time');
			$table->dropColumn('edit_time');
		});

		// Topics
		Schema::table('topics', function($table)
		{
			$table->dropColumn('time');
			$table->dropColumn('last_date');
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
