<?php

class LevelController extends Earlybird\FoundryController
{

	/**
	 * Show all levels and how to get to them
	 */
	public function display()
	{
		$_PAGE = array(
			'category' => 'community',
			'title' => 'Badges',
		);

		$levels = Level::where('type', '=', 0)
			->orderBy('min_posts', 'asc')
			->get();
		$special = Level::where('type', '=', 1)
			->where('name', '!=', 'Banned')
			->orderBy('name', 'asc')
			->get();

		return View::make('levels.display')
			->with('_PAGE', $_PAGE)
			->with('menu', GroupController::fetchMenu('badges'))
			->with('levels', $levels)
			->with('special', $special);
	}

}
