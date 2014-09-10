<?php

use Illuminate\Console\Command;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;

class StandardizeCommand extends Command {

	/**
	 * The console command name.
	 *
	 * @var string
	 */
	protected $name = 'standardize';

	/**
	 * The console command description.
	 *
	 * @var string
	 */
	protected $description = 'Standardize table info.';

	/**
	 * Create a new command instance.
	 *
	 * @return void
	 */
	public function __construct()
	{
		parent::__construct();
	}

	/**
	 * Execute the console command.
	 *
	 * @return mixed
	 */
	public function fire()
	{
		/*// Downloads
		DB::table('downloads')
			->update([
				'created_at' => DB::raw('FROM_UNIXTIME(date)'),
				'updated_at' => DB::raw('FROM_UNIXTIME(date)'),
			]);

		// Posts
		DB::table('posts')
			->update([
				'created_at' => DB::raw('FROM_UNIXTIME(time)'),
				'updated_at' => DB::raw('FROM_UNIXTIME(edit_time)'),
			]);

		// Topics
		DB::table('topics')
			->update([
				'created_at' => DB::raw('FROM_UNIXTIME(time)'),
				'updated_at' => DB::raw('FROM_UNIXTIME(last_date)'),
			]);

		// Admin Messages
		DB::table('admin_messages')
			->update([
				'created_at' => DB::raw('FROM_UNIXTIME(date)'),
				'updated_at' => DB::raw('FROM_UNIXTIME(date)'),
			]);

		// Albums
		DB::table('albums')
			->update([
				'created_at' => DB::raw('FROM_UNIXTIME(date)'),
				'updated_at' => DB::raw('FROM_UNIXTIME(modified)'),
			]);

		// Announcements
		DB::table('announcements')
			->update([
				'created_at' => DB::raw('FROM_UNIXTIME(date)'),
				'updated_at' => DB::raw('FROM_UNIXTIME(date)'),
			]);

		// Attachments
		DB::table('attachments')
			->update([
				'created_at' => DB::raw('FROM_UNIXTIME(date)'),
				'updated_at' => DB::raw('FROM_UNIXTIME(date)'),
			]);

		// Forums
		DB::table('forums')
			->update([
				'created_at' => DB::raw('NOW()'),
				'updated_at' => DB::raw('NOW()'),
			]);

		// Photos
		DB::table('photos')
			->update([
				'created_at' => DB::raw('FROM_UNIXTIME(date)'),
				'updated_at' => DB::raw('FROM_UNIXTIME(date)'),
			]);

		// Scores
		DB::table('scores')
			->update([
				'created_at' => DB::raw('NOW()'),
				'updated_at' => DB::raw('NOW()'),
			]);

		// Users
		DB::table('users')
			->update([
				'created_at' => DB::raw('FROM_UNIXTIME(joined)'),
				'updated_at' => DB::raw('FROM_UNIXTIME(last_view)'),
			]);*/
	}

	/**
	 * Get the console command arguments.
	 *
	 * @return array
	 */
	protected function getArguments()
	{
		return array();
	}

	/**
	 * Get the console command options.
	 *
	 * @return array
	 */
	protected function getOptions()
	{
		return array();
	}

}
