<?php

class AlbumController extends Earlybird\FoundryController
{

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
		if( count($photos) > 0 ) {
			$photos->load(['album', 'user']);
		}

		// Get recent albums
		$albums = Album::where('permission_view', '<=', $me->access)
			->whereHas('photos')
			->orderBy('updated_at', 'desc')
			->take($limit)
			->get();
		if( count($albums) > 0 ) {
			$albums->load(['user']);
		}

		return View::make('albums.gallery')
			->with('_PAGE', $_PAGE)
			->with('photos', $photos)
			->with('albums', $albums);

		/*
		if( $board_apps["videos"]["enabled"] && $board_apps["videos"]["permission"] <= $me->access ) {
			$sql = "SELECT video_id, users.id, users.name, video_filename, video_name
			FROM videos, users, video_albums
			WHERE video_approved = 1 AND video_owner = users.id 
				AND video_album = album_id AND permission_view <= '$me->access'
			ORDER BY video_date DESC LIMIT 6";
			$res = query($sql, __FILE__, __LINE__);
			while( $vid = mysql_fetch_array($res)) {
				list( $v, $ownid, $owner, $thumb, $name ) = $vid;
				$name = stripslashes($name);
				echo "<div class=\"photo\" style=\"height:185px;\">
				<a class=\"thumb\" href=\"video.php?v=$v\"><img src=\"videos/preview/".$thumb."_sm.jpg\"></a>
				<div style=\"height:16px;overflow:hidden;\">$name</div>
				<small>by <a href=\"profile.php?u=$ownid\">$owner</a></small></div>\n";
			}
		*/
	}

	/**
	 * Display an album
	 *
	 * @param  int  $id  Defaults to 1, the parent album
	 * @param  string  $name  For SEO only
	 * @return Response
	 */
	public function display( $id = 1, $name = NULL )
	{
		global $me;

		$album = Album::findOrFail($id);

		/*if( isset($_GET['gallery']) ) {
			$sql = "SELECT `id`
				FROM `albums`
				WHERE `folder` = '" . $_db->escape($_GET['gallery']) . "'";
			$exec = $_db->query($sql);
			
			list( $id ) = $exec->fetch_row();
		}*/

		// @todo
		if( $album->permission_view > $me->access ) {
			App::abort(403);
		}

		$_PAGE = array(
			'category' => 'gallery',
			'section'  => 'photos',
			'title'    => $album->name
		);

		// Permissions
		$allow = $album->check_permission();

		// Parents and child albums
		/*
			if( strlen($child->description) > 80 ) {
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
	
}
