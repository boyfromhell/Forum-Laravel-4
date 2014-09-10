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
		$photo = Photo::findOrFail($id);
		
		// @todo
		$access = 2;

		if( $photo->album->permission_view > $access ) {
			App::abort(403);
		}

		$_PAGE = array(
			'category' => 'gallery',
			'section'  => 'photos',
			'id'       => 'viewphoto',
			'title'    => $photo->album->name
		);

		/*if( $_CONFIG['aws'] === null ) {
			list($width, $height, $type, $attr) = getimagesize(ROOT . 'web' . $photo->image);
		}
		else {
			list($width, $height, $type, $attr) = getimagesize($_CONFIG['cdn'] . $photo->image);
		}*/

		$_PAGE['og_image'] = array($_CONFIG['cdn'] . $photo->image);

		$photo->width = $width;
		$photo->height = $height;
		$photo->attr = $attr;

		$photo->increment('views');

		return View::make('photos.display')
			->with('_PAGE', $_PAGE)
			->with('photo', $photo);

		$Smarty->assign('prev', $prev);
		$Smarty->assign('next', $next);
		$Smarty->assign('page', $page);

		$Smarty->assign('next_photo', $next_photo);

		if( isset($_GET['ajax']) ) {
			header("Cache-control: no-store, no-cache, must-revalidate");
			header("Expires: Mon, 26 Jun 1997 05:00:00 GMT");
			header("Pragma: no-cache");
			header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");

			$json = array(
				'html' => View::make('photos.photo')->render()
			);
			
			echo json_encode($json);
		}

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
		
		$sql = "DELETE FROM `photos`
			WHERE `id` = {$this->id}";
		$_db->query($sql);

		$sql = "SELECT `id`
			FROM `photos`
			WHERE `album_id` = {$this->album_id}
			ORDER BY `date` ASC
			LIMIT 1";
		$exec = $_db->query($sql);
		
		if( $exec->num_rows ) {
			list( $first_photo_id ) = $exec->fetch_row();
		} else {
			$first_photo_id = 'NULL';
		}
		
		// Update album cover if this photo is the current cover
		$sql = "UPDATE `albums` SET
			`cover` = {$first_photo_id}
			WHERE `id` = {$this->album_id}
				AND `cover` = {$this->id}";
		$_db->query($sql);
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
