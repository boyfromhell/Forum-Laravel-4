<?php namespace Parangi;

class PhotoController extends BaseController
{
    use Earlybird\FoundryController;

	/**
	 * Display a photo
	 *
	 * @param  int  $id
	 * @param  string  $name  For SEO only
	 * @return Response
	 */
	public function display($id, $name = null)
	{
		global $me;

		$photo = Photo::findOrFail($id);
		
		if ($photo->album->permission_view > $me->access) {
			App::abort(403);
		}

		$_PAGE = array(
			'category' => 'gallery',
			'section'  => 'photos',
			'id'       => 'viewphoto',
			'title'    => $photo->album->name
		);

		$_PAGE['og_image'] = array(Config::get('app.cdn').$photo->image);

		$photo->increment('views');

		return View::make('photos.display')
			->with('_PAGE', $_PAGE)
			->with('photo', $photo);

		/*$Smarty->assign('prev', $prev);
		$Smarty->assign('next', $next);
		$Smarty->assign('page', $page);

		$Smarty->assign('next_photo', $next_photo);

		if (isset($_GET['ajax'])) {
			$html = View::make('photos.photo')->render()

			return Response::json(['html' => $html]);
		}*/

		/*
		if (function_exists(exif_read_data)) {
			$exif = @exif_read_data("../photos/$folder/$file", "IFD0,COMPUTED,EXIF", true);
			if ($exif) {
				echo "<small><b>EXIF Data:</b><br>";
				if ($exif['IFD0']['Make']) {
					echo "Make: ".$exif['IFD0']['Make']."<br>";
				}
				if ($exif['IFD0']['Model']) {
					echo "Model: ".$exif['IFD0']['Model']."<br>";
				}
				if ($exif['EXIF']['ExposureTime']) {
					$shutspd = explode("/",$exif['EXIF']['ExposureTime'],2);
					echo "Shutter Speed: ";
					if ($shutspd[0] >= $shutspd[1]) {
						echo round($shutspd[0]/$shutspd[1],1);
					} else {
						echo $shutspd[0]."/".$shutspd[1];
					}
					echo " second";
					if ($shutspd[0] > $shutspd[1]) {
						echo "s";
					}
					echo "<br>";
				}
				if ($exif['COMPUTED']['ApertureFNumber']) {
					echo "F Number: ".$exif['COMPUTED']['ApertureFNumber']."<br>";
				}
				if ($exif['EXIF']['FocalLength']) {
					$flength = explode("/",$exif['EXIF']['FocalLength'],2);
					$flen = ($flength[0]/$flength[1]);
					echo "Focal Length: ".$flen." mm<br>";
				}
				if ($exif['EXIF']['ISOSpeedRatings']) {
					echo "ISO Speed: ".$exif['EXIF']['ISOSpeedRatings']."<br>";
				}
				$datetaken = $exif['EXIF']['DateTimeOriginal'];
				$dtaken = substr($datetaken,0,11);
				$dtaken = str_replace(":","/",$dtaken);
				$ttaken = substr($datetaken,11,9);
				if (substr($dtaken,0,4) > 2000) {
					echo "Date Taken: ".datestring(strtotime($dtaken.$ttaken),1)."<br>";
				}
				echo "</small>";
			}
		}
		*/
	}

	/**
	 * Download the full resolution version of a photo
	 *
	 * @param  int  $id
	 * return Response
	 */
	public function download($id)
	{
		global $me;

		$photo = Photo::findOrFail($id);

		if ($photo->album->permission_view > $me->access) {
			App::abort(403);
		}

		$photo->increment('downloads');

		$path = 'photos/'.$photo->album->folder.'/'.$photo->file;

		$s3 = new S3(
			Config::get('services.aws.access_key'),
			Config::get('services.aws.secret_key')
		);
		$url = $s3->getAuthenticatedURL(Config::get('services.aws.bucket'), $path, 60*60, true);

		return Redirect::to($url);
	}

	/**
	 * Upload new photos to an album
	 *
	 * @param  int  $id  Album ID
	 * @return Response
	 */
	public function upload($id)
	{
		global $me;

		$album = Album::findOrFail($id);

		if (! $album->check_permission()) {
			App::abort(403);
		}

		if (Request::isMethod('post') && Input::hasFile('photos')) {
			$successful = 0;
			$total = count(Input::file('photos'));

			foreach (Input::file('photos') as $file) {
				if ($file->isValid()) {
					try {
						$ext = strtolower($file->getClientOriginalExtension());
						$name = time().'_'.str_random().'.'.$ext;
						$file->move(storage_path().'/uploads', $name);

						$image = new Image(storage_path().'/uploads/'.$name);

						// Scaled permalink version
						$image->scale(800, 600)
							->setSuffix('_sm')
							->saveJpg(96);

						// Thumbnail
						$image->scale(200, 150)
							->setSuffix('_tn')
							->saveJpg(96);

						$photo = Photo::create([
							'album_id' => $album->id,
							'user_id'  => $me->id,
							'file'     => $name,
						]);

						$photo->pushToS3();

						if ($album->cover_id === null) {
							$album->cover_id = $photo->id;
							$album->save();
						}

						$successful++;
					} catch (Exception $e) {
					}
				}
			}

			$album->touch();

			if ($successful == $total) {
				Session::push('messages', '<b>'.$successful.' out of '.$total.'</b> photos uploaded');
			} else {
				Session::push('errors', '<b>'.$successful.' out of '.$total.'</b> photos uploaded');
			}

			return Redirect::to('upload-photos/'.$album->id);
		}

		$_PAGE = array(
			'category' => 'gallery',
			'section'  => 'photos',
			'title'    => 'Upload Photos'
		);

		$post_max_size = intval(ini_get('post_max_size'));
		$max_total = $post_max_size * 1024 * 1024;
		$upload_max_filesize = intval(ini_get('upload_max_filesize'));
		$max_bytes = $upload_max_filesize * 1024 * 1024;
		$max_file_uploads = ini_get('max_file_uploads');

		return View::make('photos.upload')
			->with('_PAGE', $_PAGE)
			->with('album', $album)

			->with('post_max_size', $post_max_size)
			->with('max_total', $max_total)
			->with('upload_max_filesize', $upload_max_filesize)
			->with('max_bytes', $max_bytes)
			->with('max_file_uploads', $max_file_uploads);
	}

	/**
	 * Edit a photo
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function edit($id)
	{
		global $me;

		$photo = Photo::findOrFail($id);
		$album = $photo->album;

		if ($photo->user_id != $me->id && ! $me->is_admin) {
			App::abort(403);
		}

		if (Request::isMethod('post')) {
			$album_id = (int)$_POST['album_id'];
			$cover    = Input::get('cover', 0);
			
			$photo->description = Input::get('description');

			if ($me->is_admin) {
				$user = User::where('name', '=', Input::get('author'))->first();
			
				$photo->user_id = $user->id;
				$photo->created_at = Input::get('date');
				$photo->views = Input::get('views');
			}

			$photo->save();

			// Set or unset cover photo
			if ($cover) {
				$album->cover_id = $photo->id;
				$album->save();
			} else if ($album->cover_id == $photo->id) {
				$album->cover_id = null;
				$album->save();
			}

			Session::push('messages', 'Photo saved successfully');

			return Redirect::to('edit-photo/'.$photo->id);
		}

		$_PAGE = array(
			'category' => 'gallery',
			'section' => 'photos',
			'title' => 'Edit Photo',
		);

		return View::make('photos.edit')
			->with('_PAGE', $_PAGE)
			->with('photo', $photo)
			->with('album', $album);
	}

	/**
	 * Confirm deletion of a photo
	 *
	 * @return Response
	 */
	public function delete($id)
	{
		global $me;

		$_PAGE = array(
			'category' => 'gallery',
			'section'  => 'phtoos',
			'title'    => 'Delete Photo'
		);

		$photo = Photo::findOrFail($id);

		if ($photo->user_id != $me->id && !$me->is_moderator) {
			App::abort(403);
		}

		if (Request::isMethod('post')) {
			if (isset($_POST['cancel'])) {
				return Redirect::to($photo->url);
			}
			// Redirect to album
			else if (isset($_POST['confirm'])) {
				$album = $photo->album;
				$photo->delete();

				Session::push('messages', 'The photo has been successfully deleted');

				return Redirect::to($album->url);
			}
		}

		return View::make('photos.delete')
			->with('_PAGE', $_PAGE)
			->with('photo', $photo);
	}

}

