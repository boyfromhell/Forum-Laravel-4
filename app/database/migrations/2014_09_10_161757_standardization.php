<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class Standardization extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		// Users
		Schema::table('users', function($table)
		{
			$table->renameColumn('rank', 'level_id');
			$table->renameColumn('level', 'user_type');
			$table->renameColumn('posts', 'total_posts');
			$table->dropColumn('last');
			$table->dropColumn('lastact');
			$table->timestamps();
			$table->dateTime('visited_at')->nullable();
			$table->dateTime('viewed_at')->nullable();
		});

		// Users
		DB::table('users')
			->update([
				'created_at' => DB::raw('FROM_UNIXTIME(joined)'),
				'updated_at' => DB::raw('FROM_UNIXTIME(last_view)'),
				'visited_at' => DB::raw('FROM_UNIXTIME(last_visit)'),
				'viewed_at'  => DB::raw('FROM_UNIXTIME(last_view)'),
			]);

		// Admin messages
		Schema::table('admin_messages', function($table)
		{
			$table->timestamps();
		});

		// Admin Messages
		DB::table('admin_messages')
			->update([
				'created_at' => DB::raw('FROM_UNIXTIME(date)'),
				'updated_at' => DB::raw('FROM_UNIXTIME(date)'),
			]);

		// Albums
		Schema::table('albums', function($table)
		{
			$table->renameColumn('cover', 'cover_id');
			$table->timestamps();
		});

		// Albums
		DB::table('albums')
			->update([
				'created_at' => DB::raw('FROM_UNIXTIME(date)'),
				'updated_at' => DB::raw('FROM_UNIXTIME(modified)'),
			]);

		// Announcements
		Schema::table('announcements', function($table)
		{
			$table->timestamps();
		});

		// Announcements
		DB::table('announcements')
			->update([
				'created_at' => DB::raw('FROM_UNIXTIME(date)'),
				'updated_at' => DB::raw('FROM_UNIXTIME(date)'),
			]);

		// Attachments
		Schema::table('attachments', function($table)
		{
			$table->timestamps();
		});

		// Attachments
		DB::table('attachments')
			->update([
				'created_at' => DB::raw('FROM_UNIXTIME(date)'),
				'updated_at' => DB::raw('FROM_UNIXTIME(date)'),
			]);

		// Forums
		Schema::table('forums', function($table)
		{
			$table->renameColumn('category', 'category_id');
			$table->renameColumn('topics', 'total_topics');
			$table->renameColumn('posts', 'total_posts');
			$table->timestamps();
		});

		// Forums
		DB::table('forums')
			->update([
				'created_at' => DB::raw('NOW()'),
				'updated_at' => DB::raw('NOW()'),
			]);

		// Photos
		Schema::table('photos', function($table)
		{
			$table->timestamps();
		});

		// Photos
		DB::table('photos')
			->update([
				'created_at' => DB::raw('FROM_UNIXTIME(date)'),
				'updated_at' => DB::raw('FROM_UNIXTIME(date)'),
			]);

		// Scores
		Schema::table('scores', function($table)
		{
			$table->timestamps();
		});

		// Scores
		DB::table('scores')
			->update([
				'created_at' => DB::raw('NOW()'),
				'updated_at' => DB::raw('NOW()'),
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
