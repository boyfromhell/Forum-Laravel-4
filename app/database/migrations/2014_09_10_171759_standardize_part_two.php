<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class StandardizePartTwo extends Migration {

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
			$table->timestamps();
		});

		// Posts
		Schema::table('posts', function($table)
		{
			$table->timestamps();
		});

		// Topics
		Schema::table('topics', function($table)
		{
			$table->renameColumn('poster', 'user_id');
			$table->dropColumn('last');
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
