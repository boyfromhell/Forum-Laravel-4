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

		// Downloads
		DB::table('downloads')
			->update([
				'created_at' => DB::raw('FROM_UNIXTIME(date)'),
				'updated_at' => DB::raw('FROM_UNIXTIME(date)'),
			]);

		// Posts
		Schema::table('posts', function($table)
		{
			$table->timestamps();
		});

		// Posts
		DB::table('posts')
			->update([
				'created_at' => DB::raw('FROM_UNIXTIME(time)'),
				'updated_at' => DB::raw('FROM_UNIXTIME(edit_time)'),
			]);

		// Topics
		Schema::table('topics', function($table)
		{
			$table->renameColumn('poster', 'user_id');
			$table->dropColumn('last');
			$table->timestamps();
			$table->dateTime('posted_at')->nullable();
		});

		// Topics
		DB::table('topics')
			->update([
				'created_at' => DB::raw('FROM_UNIXTIME(time)'),
				'updated_at' => DB::raw('FROM_UNIXTIME(last_date)'),
				'posted_at' => DB::raw('FROM_UNIXTIME(last_date)'),
			]);
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
