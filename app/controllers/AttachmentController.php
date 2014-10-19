<?php

class AttachmentController extends Earlybird\FoundryController
{

	/**
	 * Download
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function download( $id )
	{
		$attachment = Attachment::findOrFail($id);

		// Check if they have read permission on this forum / message
		if( $attachment->post_id )
		{
			$forum = $attachment->post->topic->forum;

			// @todo support group view/read permission
			if( $forum->id == 19 ) {
				if( in_array(1, $mygroups) || $me->is_mod ) {
					$access = $forum->read;
				}
				else {
					$access = $forum->read-1;
				}
			}

			if( $access < $forum->read ) {
				throw new Exception('You do not have permission to view this attachment');
			}
		}
		else {
			if( !$me->loggedin || $me->id != $attachment->user_id ) {
				throw new Exception('You do not have permission to view this attachment');
			}
		}

		$attachment->increment('downloads');

		if( $_CONFIG['aws'] === null ) {
			$path = ROOT . 'web' . $attachment->get_path() . $attachment->filename;
		}
		else {
			$path = ltrim($attachment->get_path(), '/') . $attachment->filename;
			
			$s3 = new S3($_CONFIG['aws']['access_key'], $_CONFIG['aws']['secret_key']);
			$url = $s3->getAuthenticatedURL($_CONFIG['s3_bucket'], $path, 60*60, true);
		}

		header("Content-type: " . $attachment->mimetype);
		header("Content-transfer-encoding: binary");
		header("Content-length: " . filesize($path));
			
		if( $attachment->filetype == 0 ) {
			header("Content-Disposition: inline; filename=". $attachment->origfilename);
		} else {
			header("Content-Disposition: attachment; filename=" . $attachment->origfilename);
		}

		if( $_CONFIG['aws'] === null ) {	
			@readfile($path);
		}
		else {
			echo file_get_contents($url);
		}

		exit;
	}

	/**
	 * Upload an attachment
	 */
	public static function upload( $files, $i, $hash )
	{
		global $me;
	
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

}
