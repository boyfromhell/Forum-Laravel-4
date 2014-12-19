<?php namespace Parangi;

class AlbumController extends BaseController
{
	use \Earlybird\FoundryController;

	/**
	 * Gallery page
	 *
	 * @return Response
	 */
	public function gallery()
	{
		global $me;

		$_PAGE = array(
			'category' => 'gallery',
			'section'  => 'gallery',
			'title'    => 'Media'
		);

		$limit = $is_mobile ? 1 : 6;

		// Get random photos
		$photos = Photo::join('albums', 'photos.album_id', '=', 'albums.id')
			->where('albums.permission_view', '<=', $me->access)
			->orderBy(DB::raw('RAND()'), 'asc')
			->take($limit)
			->get(['photos.*']);
		if (count($photos) > 0) {
			$photos->load(['album', 'user']);
		}

		// Get recent albums
		$albums = Album::where('permission_view', '<=', $me->access)
			->whereHas('photos')
			->orderBy('updated_at', 'desc')
			->take($limit)
			->get();
		if (count($albums) > 0) {
			$albums->load(['user']);
		}

		return View::make('albums.gallery')
			->with('_PAGE', $_PAGE)
			->with('photos', $photos)
			->with('albums', $albums);
	}

	/**
	 * Display an album
	 *
	 * @param  int  $id  Defaults to 1, the parent album
	 * @param  string  $name  For SEO only
	 * @return Response
	 */
	public function display($id = 1, $name = null)
	{
		global $me;

		if ($id) {
			$album = Album::findOrFail($id);
		} else if (Input::has('gallery')) {
			$album = Album::where('folder', '=', Input::get('gallery'))
				->first();
		}

		if (! $album->id) {
			App::abort(404);
		}

		// @todo
		if ($album->permission_view > $me->access) {
			App::abort(403);
		}

		$_PAGE = array(
			'category' => 'gallery',
			'section'  => 'photos',
			'title'    => $album->name
		);

		// Permissions
		$allow = $album->check_permission();

		// @todo
		/*
			if (strlen($child->description) > 80) {
				$child->description = substr($child->description, 0, 79) . '...';
			}
			$child->description = BBCode::simplify($child->description);
		}*/

		$photos = $album->photos()
			->paginate(20);

		return View::make('albums.display')
			->with('_PAGE', $_PAGE)
			->with('album', $album)
			->with('photos', $photos)

			->with('allow', $allow);
	}

	/**
	 * Edit an album
	 */
	public function edit($id)
	{
		global $me;

		$album = Album::findOrFail($id);

		if ($me->id != $album->user_id && !$me->is_admin) {
			App::abort(403);
		}

		$_PAGE = array(
			'category' => 'gallery',
			'section'  => 'photos',
			'title'    => 'Edit Album',
		);

		return View::make('albums.edit')
			->with('_PAGE', $_PAGE)
			->with('album', $album);
	}
	
}

