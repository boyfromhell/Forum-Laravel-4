<?php

class PhotoController extends Earlybird\FoundryController
{

	/**
	 * Display a photo
	 *
	 * @return Response
	 */
	public function display( $id )
	{
		global $me;

		$photo = Photo::findOrFail($id);
		
		if( $photo->album->permission_view > $me->access ) {
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

		if( isset($_GET['ajax']) ) {
			$html = View::make('photos.photo')->render()

			return Response::json(['html' => $html]);
		}*/

		/*
		if( function_exists(exif_read_data)) {
			$exif = @exif_read_data( "../photos/$folder/$file", "IFD0,COMPUTED,EXIF", true );
			if( $exif ) {
				echo "<small><b>EXIF Data:</b><br>";
				if( $exif['IFD0']['Make'] ) {	echo "Make: ".$exif['IFD0']['Make']."<br>";	}
				if( $exif['IFD0']['Model'] ) {	echo "Model: ".$exif['IFD0']['Model']."<br>";	}
				if( $exif['EXIF']['ExposureTime'] ) {
					$shutspd = explode("/",$exif['EXIF']['ExposureTime'],2);
					echo "Shutter Speed: ";
					if( $shutspd[0] >= $shutspd[1] ) { echo round($shutspd[0]/$shutspd[1],1); }
					else { echo $shutspd[0]."/".$shutspd[1]; }
					echo " second";
					if( $shutspd[0] > $shutspd[1] ) { echo "s"; }
					echo "<br>";
				}
				if( $exif['COMPUTED']['ApertureFNumber'] ) {
					echo "F Number: ".$exif['COMPUTED']['ApertureFNumber']."<br>";
				}
				if( $exif['EXIF']['FocalLength'] ) {
					$flength = explode("/",$exif['EXIF']['FocalLength'],2);
					$flen = ($flength[0]/$flength[1]);
					echo "Focal Length: ".$flen." mm<br>";
				}
				if( $exif['EXIF']['ISOSpeedRatings'] ) {
					echo "ISO Speed: ".$exif['EXIF']['ISOSpeedRatings']."<br>";
				}
				$datetaken = $exif['EXIF']['DateTimeOriginal'];
				$dtaken = substr($datetaken,0,11);
				$dtaken = str_replace(":","/",$dtaken);
				$ttaken = substr($datetaken,11,9);
				if( substr($dtaken,0,4) > 2000 ) {
					echo "Date Taken: ".datestring(strtotime($dtaken.$ttaken),1)."<br>";
				}
				echo "</small>";
			}
		}
		*/
	}

	/**
	 * Delete this photo, pass album folder as argument
	 */
	public function delete($folder, $database = true)
	{
		global $_CONFIG, $_db;
		
		list( $name, $ext ) = parse_file_name($this->file);
		
		if( $_CONFIG['aws'] === null ) {
			unlink(ROOT . "web/photos/{$folder}/{$name}.{$ext}");
			unlink(ROOT . "web/photos/{$folder}/scale/{$name}.jpg");
			unlink(ROOT . "web/photos/{$folder}/thumbs/{$name}.jpg");
		}
		else {
			delete_from_s3("photos/{$folder}/{$name}.{$ext}");
			delete_from_s3("photos/{$folder}/scale/{$name}.jpg");
			delete_from_s3("photos/{$folder}/thumbs/{$name}.jpg");
		}
		
		// Stop if we are only deleting the file (i.e. album deletion)
		if( !$database ) { return; }

		// Fetch first photo in this album
		$first_photo = Photo::where('album_id', '=', $this->album_id)
			->where('id', '!=', $this->id)
			->orderBy('date', 'asc')
			->first();

		// Replace album covers
		$this->album->where('cover_id', '=', $this->id)
			->update([
				'cover_id' => $first_photo->id
			]);

		parent::delete();
	}
	
	public function push_to_s3($folder)
	{
		list( $name, $ext ) = parse_file_name($this->file);

		if( push_to_s3("photos/{$folder}/{$name}.{$ext}", false) ) {
			unlink(ROOT . "web/photos/{$folder}/{$name}.{$ext}");

			if( push_to_s3("photos/{$folder}/scale/{$name}.jpg", true) ) {
				unlink(ROOT . "web/photos/{$folder}/scale/{$name}.jpg");
			}
			if( push_to_s3("photos/{$folder}/thumbs/{$name}.jpg", true) ) {
				unlink(ROOT . "web/photos/{$folder}/thumbs/{$name}.jpg");
			}
			return true;
		}
		return false;
	}
}
