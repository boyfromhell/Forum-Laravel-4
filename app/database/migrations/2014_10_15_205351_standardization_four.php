<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class StandardizationFour extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		//
		Schema::table('themes', function($table)
		{
			$table->renameColumn('theme_id', 'id');
			$table->renameColumn('theme_name', 'name');
			$table->renameColumn('theme_folder', 'folder');
			$table->renameColumn('theme_css', 'css_file');
			$table->renameColumn('theme_worksafe', 'is_worksafe');
			$table->renameColumn('theme_bg', 'background');
			$table->renameColumn('theme_preview', 'preview');
		});

		Schema::table('polls', function($table)
		{
			$table->renameColumn('poll_id', 'id');
			$table->renameColumn('poll_topic', 'topic_id');
			$table->renameColumn('poll_question', 'question');
			$table->renameColumn('poll_max', 'max_options');
			$table->renameColumn('poll_hash', 'hash');
			$table->renameColumn('poll_public', 'is_public');
		});

		Schema::table('poll_options', function($table)
		{
			$table->renameColumn('option_id', 'id');
			$table->renameColumn('option_order', 'weight');
			$table->renameColumn('option_poll', 'poll_id');
			$table->renameColumn('option_text', 'content');
			$table->renameColumn('option_votes', 'total_votes');
		});

		Schema::table('poll_votes', function($table)
		{
			$table->renameColumn('vote_id', 'id');
			$table->renameColumn('vote_poll', 'poll_id');
			$table->renameColumn('vote_user', 'user_id');
			$table->renameColumn('vote_choices', 'choices');
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
