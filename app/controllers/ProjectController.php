<?php namespace Parangi;

use App;
use Redirect;
use Session;
use View;

class ProjectController extends BaseController
{
    use \Earlybird\FoundryController;

	/**
	 * List projects in this category
	 *
	 * @param  string  $category
	 * @return Response
	 */
	public function category($category = 'official')
	{
		global $me;

		Session::push('notices', 'Be sure to check out the <a target="_blank" class="alert-link" href="http://github.com/Attnam/ivan">fan continuation on GitHub</a> by members of this site!');

		if (! Module::isActive('downloads')) {
			App::abort(404);
		}

		switch ($category) {
			case 'variants':
				$section = 'variants';
				$title = 'Variants';
				$category_id = 0;
				break;

			case 'other':
				$section = 'other';
				$title = 'Other Projects';
				$category_id = 2;
				break;

			case 'official': 
			case null:
				$section = 'official';
				$title = 'Official';
				$category_id = 1;
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

		if (count($projects) > 0) {
			$projects->load(['downloads']);
		}

		return View::make('projects.category')
			->with('_PAGE', $_PAGE)
			->with('projects', $projects)
			->with('menu', ProjectController::fetchMenu($section));
	}

	/**
	 * Display a project
	 *
	 * @param  int  $id
	 * @param  string  $name  For SEO only
	 * @return Response
	 */
	public function display($id, $name = null)
	{
		global $me;

		if (! Module::isActive('downloads')) {
			App::abort(404);
		}

		$project = Project::findOrFail($id);
		$project->increment('views');

		$_PAGE = array(
			'category' => 'downloads',
			'title'    => $project->name
		);

		return View::make('projects.display')
			->with('_PAGE', $_PAGE)
			->with('project', $project)
			->with('menu', ProjectController::fetchMenu($project->section));
	}

	/**
	 * Download a file
	 *
	 * @param  int  $id
	 * @param  string  $name  For SEO only
	 * @return Response
	 */
	public function download($id, $name = null)
	{
		$file = Download::findOrFail($id);
		$file->increment('views');

		return Redirect::to('files/'.$file->file);
	}

	/**
     * Menu for project pages
     *
     * @return array
     */
    public static function fetchMenu($active = null)
    {
        $menu = array();

        $menu['official'] = array(
            'url' => '/downloads',
            'name' => 'IVAN',
        );
        $menu['variants'] = array(
            'url' => '/downloads/variants',
            'name' => 'Variants',
        );
        $menu['other'] = array(
            'url' => '/downloads/other',
            'name' => 'Other Projects',
        );

		if ($active !== null) {
            $menu[$active]['active'] = true;
        }

        return $menu;
    }

}

