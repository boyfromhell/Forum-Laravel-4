<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class DatabaseCleanup extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		//
		Schema::table('topics', function($table)
		{
			$table->renameColumn('status', 'is_locked');
		});

		Schema::table('users', function($table)
		{
			$table->dropColumn('attach_disp');
			$table->dropColumn('mailing');
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
