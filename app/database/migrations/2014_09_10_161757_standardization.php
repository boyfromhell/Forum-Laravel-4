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

		// Admin messages
		Schema::table('admin_messages', function($table)
		{
			$table->timestamps();
		});

		// Albums
		Schema::table('albums', function($table)
		{
			$table->renameColumn('cover', 'cover_id');
			$table->timestamps();
		});

		// Announcements
		Schema::table('announcements', function($table)
		{
			$table->timestamps();
		});

		// Attachments
		Schema::table('attachments', function($table)
		{
			$table->timestamps();
		});

		// Forums
		Schema::table('forums', function($table)
		{
			$table->renameColumn('category', 'category_id');
			$table->renameColumn('topics', 'total_topics');
			$table->renameColumn('posts', 'total_posts');
			$table->timestamps();
		});

		// Photos
		Schema::table('photos', function($table)
		{
			$table->timestamps();
		});

		// Scores
		Schema::table('scores', function($table)
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
