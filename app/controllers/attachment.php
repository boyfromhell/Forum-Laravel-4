<?php

class Attachment extends Controller_W 
{

	public function generate_url()
	{
		$this->url = '/forum/attachment?id=' . $this->id;
	}

	/**
	 * Upload an attachment
	 */
	public static function upload( $files, $i, $hash )
	{
		global $me, $gmt, $board_config;
	
		// Determine the final, legal name and extension
		list( $name, $ext ) = parse_file_name($files['name'][$i], true);
		
		if( $ext == 'jpeg' ) { $ext = 'jpg'; }
		
		// Check for errors
		if( $files['size'][$i] > 8192000 ) {
			throw new Exception('Image is too large (limit is 8 MB)');
		}
		if( in_array($ext, array('gif', 'jpg', 'png')) ) {
			$attach_type = 0; // image
		}
		else if( in_array($ext, array('doc', 'docx', 'gz', 'pdf', 'rtf', 'svg', 'tar', 'txt', 'zip')) ) {
			$attach_type = 1; // non-image
		}
		else {
			throw new Exception('You may only upload DOC, DOCX, GIF, GZ, JPG, PDF, PNG, RTF, SVG, TAR, TXT, or ZIP files');
		}
		
		$year = date('Y', $gmt);
		$month = date('m', $gmt);
		$folder = ROOT . "web/attachments/{$year}/";
		
		if( !file_exists($folder) ) { mkdir($folder, 0755); }
		$folder .= $month . '/';
		if( !file_exists($folder) ) { mkdir($folder, 0755); }
		if( !file_exists($folder . 'scale/') ) { mkdir($folder . 'scale/', 0755); }
		if( !file_exists($folder . 'thumbs/') ) { mkdir($folder . 'thumbs/', 0755); }

		// Full path of the file
		$orig_name = $name . '.' . $ext;
		
		$name .= '_' . $gmt;
		$original  = "{$folder}{$name}.{$ext}";
		$scale     = "{$folder}scale/{$name}.jpg";
		$thumbnail = "{$folder}thumbs/{$name}.jpg";
		
		// Move file from temporary location
		$success = false;
		if( !count($file_errors) ) {
			$success = move_uploaded_file($files['tmp_name'][$i], $original);
		}

		// Resize, if it's an image
		if( $success && $attach_type == 0 ) {
			// Create an image object from original
			if( $ext == 'gif' ) { $src_img = imagecreatefromgif($original); }
			else if( $ext == 'png' ) { $src_img = imagecreatefrompng($original); }
			else { $src_img = imagecreatefromjpeg($original); }
			
			// Calculate new dimensions
			$width    = ImageSx($src_img);
			$height   = ImageSy($src_img);
			$persp    = $width / $height;
			$ideal    = 4/3;
			$n_width  = $board_config['scale_width'];
			$n_height = $board_config['scale_height'];
			$t_width  = $board_config['thumb_width'];
			$t_height = $board_config['thumb_height'];

			if( $persp > $ideal ) {
				$n_height = round($board_config['scale_width'] / $persp);
				$t_height = round($board_config['thumb_width'] / $persp);
			}
			else { 
				$n_width = round($board_config['scale_height'] * $persp);
				$t_width = round($board_config['thumb_height'] * $persp);
			}
			if( $n_width > $width && $n_height > $height ) {
				$n_width = $width;
				$n_height = $height;
			}

			// Create scaled versions
			$dst_img = ImageCreateTrueColor($n_width, $n_height);
			imagecopyresampled($dst_img, $src_img, 0, 0, 0, 0, $n_width, $n_height, $width, $height); 

			if(( $n_width < $width && $n_height < $height ) || $ext != 'jpg') {
				ImageJpeg($dst_img, $scale, 95);
			}
			else {
				copy($original, $scale);
			}

			if(( $t_width < $width && $t_height < $height ) || $ext != 'jpg') {
				$dst_thumb = ImageCreateTrueColor($t_width, $t_height);
				imagecopyresampled($dst_thumb, $dst_img, 0, 0, 0, 0, $t_width, $t_height, $n_width, $n_height); 
				ImageJpeg($dst_thumb, $thumbnail, 95);
			}
			else {
				copy($original, $thumbnail);
			}
			
			@imagedestroy($dst_img);
			@imagedestroy($dst_thumb);
			imagedestroy($src_img);
		}
		
		if( $success ) {
			// Add to database
			$data = array(
				'post_id'      => null,
				'message_id'   => null,
				'user_id'      => $me->id,
				'date'         => $gmt + $i,
				'hash'         => $hash,
				'filename'     => $name . '.' . $ext,
				'origfilename' => $orig_name,
				'mimetype'     => $files['type'][$i],
				'filetype'     => $attach_type,
				'downloads'    => 0
			);
			$attachment = new Attachment(null, $data);
			$attachment->save();
			$attachment->push_to_s3();
		}
		
		return $success;
	}
	
	public function push_to_s3()
	{
		$year = date('Y', $this->date);
		$month = date('m', $this->date);
		$folder = "attachments/{$year}/{$month}";
		
		list( $name, $ext ) = parse_file_name($this->filename);

		if( push_to_s3("{$folder}/{$name}.{$ext}", false) ) {
			unlink(ROOT . "web/{$folder}/{$name}.{$ext}");

			if( $this->filetype == 0 ) {
				if( push_to_s3("{$folder}/scale/{$name}.jpg", true) ) {
					unlink(ROOT . "web/{$folder}/scale/{$name}.jpg");
				}
				if( push_to_s3("{$folder}/thumbs/{$name}.jpg", true) ) {
					unlink(ROOT . "web/{$folder}/thumbs/{$name}.jpg");
				}
			}
			return true;
		}
		return false;
	}
	
	/**
	 * Get the folder path of this attachment
	 */
	public function get_path( $subfolder = '' )
	{
		if( $subfolder ) {
			$subfolder = trim($subfolder, '/') . '/';
		}
		$this->year = date('Y', $this->date);
		$this->month = date('m', $this->date);
		$path = '/attachments/' . $this->year . '/' . $this->month . '/' . $subfolder;
		return $path;
	}

	/**
	 * Delete this attachment
	 */
	public function delete()
	{
		global $_CONFIG, $_db, $me;
		
		list( $name, $ext ) = parse_file_name($this->filename);

		if( $this->user_id == $me->id || $me->administrator || $me->moderator ) {
			$sql = "DELETE FROM `attachments`
				WHERE `id` = {$this->id}";
			$_db->query($sql);

			// Do not delete files for private message attachments because there might be duplicates
			if( $_CONFIG['aws'] === null && $this->message_id === null ) {
				unlink(ROOT . 'web' . $this->get_path() . $this->filename);
				
				if( $this->filetype == 0 ) {
					unlink(ROOT . 'web' . $this->get_path('scale') . "{$name}.jpg");
					unlink(ROOT . 'web' . $this->get_path('thumbs') . "{$name}.jpg");
				}
			}
			else if( $this->message_id === null ) {
				delete_from_s3(ltrim($this->get_path(), '/') . "{$name}.{$ext}");
				
				if( $this->filetype == 0 ) {
					delete_from_s3(ltrim($this->get_path('scale'), '/') . "{$name}.jpg");
					delete_from_s3(ltrim($this->get_path('thumbs'), '/') . "{$name}.jpg");
				}
			}
			
			// @todo remove cache
			return true;
		}
		else {
			return false;
		}
	}

	/**
	 * Get a human readable file size
	 * @todo cache this in the database when file is uploaded
	 */
	public function get_size()
	{
		if( file_exists(ROOT . 'web' . $this->get_path() . $this->filename) ) {
			return english_size(ROOT . 'web' . $this->get_path() . $this->filename);
		} else {
			return 0;
		}
	}

	/**
	 * Increments download counter
	 */
	public function increment_download()
	{
		global $_db;
		$sql = "UPDATE `attachments`
			SET `downloads` = `downloads` + 1
			WHERE `id` = {$this->id}";
		$_db->query($sql);
	}
}
