<?php

class ProjectController extends Earlybird\FoundryController
{

	/**
	 * List projects in this category
	 *
	 * @param  string  $category
	 * @return Response
	 */
	public function category( $category = 'official' )
	{
		if( ! Module::isActive('downloads') ) {
			App::abort(404);
		}
		/*else {
			if( $board_apps['downloads']['permission'] > $access ) {
				require_once(ROOT . 'web/login.php');
				exit;
			}
		}*/

		switch( $category ) {
			case 'variants':
				$section = 'variants'; $title = 'Variants'; $category_id = 0;
				break;

			case 'other':
				$section = 'other'; $title = 'Other Projects'; $category_id = 2;
				break;

			case 'official': 
			case NULL:
				$section = 'official'; $title = 'Official'; $category_id = 1;
				break;

			default:
				App::abort(404);
				break;
		}

		$_PAGE = array(
			'category' => 'downloads',
			'section'  => $section,
			'title'    => $title
		);

		// Load projects that have downloads
		$projects = Project::where('category', '=', $category_id)
			->whereHas('downloads')
			->orderBy('name', 'asc')
			->get();

		if( count($projects) > 0 ) {
			$projects->load(['downloads']);
		}

		return View::make('projects.category')
			->with('_PAGE', $_PAGE)
			->with('projects', $projects);
	}

	/**
	 * Display a project
	 *
	 * @param  int  $id
	 * @param  string  $name  For SEO only
	 * @return Response
	 */
	public function display( $id, $name = NULL )
	{
		if( ! Module::isActive('downloads') ) {
			App::abort(404);
		}
		/*else {
			if( $board_apps['downloads']['permission'] > $access ) {
				require_once(ROOT . 'web/login.php');
				exit;
			}
		}*/

		$project = Project::findOrFail($id);
		$project->increment('views');

		$_PAGE = array(
			'category' => 'downloads',
			'section'  => $project->section,
			'title'    => $project->name
		);

		return View::make('projects.display')
			->with('_PAGE', $_PAGE)
			->with('project', $project);
	}

	/**
	 * Download a file
	 *
	 * @param  int  $id
	 * @param  string  $name  For SEO only
	 * @return Response
	 */
	public function download( $id, $name = NULL )
	{
		$file = Download::findOrFail($id);
		$file->increment('views');

		return Redirect::to('files/'.$file->file);
	}

}
