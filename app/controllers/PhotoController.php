<?php

class PhotoController extends Earlybird\FoundryController
{

	/**
	 * Display a photo
	 *
	 * @return Response
	 */
	public function display()
	{
		$id = Input::get('id');
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
