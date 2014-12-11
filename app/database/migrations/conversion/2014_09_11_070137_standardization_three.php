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

		// Groups
		DB::table('groups')
			->update([
				'created_at' => DB::raw('FROM_UNIXTIME(date)'),
				'updated_at' => DB::raw('FROM_UNIXTIME(date)'),
			]);

		// Shoutbox
		Schema::table('shoutbox', function($table)
		{
			$table->timestamps();
		});

		// Shoutbox
		DB::table('shoutbox')
			->update([
				'created_at' => DB::raw('FROM_UNIXTIME(time)'),
				'updated_at' => DB::raw('FROM_UNIXTIME(time)'),
			]);

		// Applications
		Schema::table('applications', function($table)
		{
			$table->timestamps();
		});

		// Applications
		DB::table('applications')
			->update([
				'created_at' => DB::raw('FROM_UNIXTIME(date_applied)'),
				'updated_at' => DB::raw('FROM_UNIXTIME(date_applied)'),
			]);

		// Avatars
		Schema::table('avatars', function($table)
		{
			$table->timestamps();
		});

		// Avatars
		DB::table('avatars')
			->update([
				'created_at' => DB::raw('FROM_UNIXTIME(date)'),
				'updated_at' => DB::raw('FROM_UNIXTIME(date)'),
			]);

		// Messages
		Schema::table('messages', function($table)
		{
			$table->timestamps();
		});

		// Messages
		DB::table('messages')
			->update([
				'created_at' => DB::raw('FROM_UNIXTIME(date_sent)'),
				'updated_at' => DB::raw('FROM_UNIXTIME(date_sent)'),
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
