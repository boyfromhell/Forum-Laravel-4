<?php namespace Parangi;

use Config;
use Exception;
use Redirect;
use Response;
use S3;

class AttachmentController extends BaseController
{
    use \Earlybird\FoundryController;

	/**
	 * Download
	 *
	 * @param  int  $id
	 * @throws Exception
	 * @return Response
	 */
	public function download($id)
	{
		global $me;

		$attachment = Attachment::findOrFail($id);

		// Check if they have read permission on this forum / message
		if ($attachment->post_id) {
			$forum = $attachment->post->topic->forum;

			// @todo support group view/read permission
			/*if ($forum->id == 19) {
				if (in_array(1, $mygroups) || $me->is_mod) {
					$access = $forum->read;
				} else {
					$access = $forum->read-1;
				}
			}*/

			if ($me->access < $forum->read) {
				throw new Exception('You do not have permission to view this attachment');
			}
		} else {
			if (!$me->id || $me->id != $attachment->user_id) {
				throw new Exception('You do not have permission to view this attachment');
			}
		}

		$attachment->increment('downloads');

		if (! Config::get('services.aws.enabled')) {
			$path = storage_path() . $attachment->original;
		} else {
			$path = ltrim($attachment->original, '/');

			$s3 = new S3(Config::get('services.aws.access_key'), Config::get('services.aws.secret_key'));
			$url = $s3->getAuthenticatedURL(Config::get('services.aws.bucket'), $path, 60*60, true);
		}

		/*header("Content-type: " . $attachment->mimetype);
		header("Content-transfer-encoding: binary");
		header("Content-length: " . filesize($path));
			
		if ($attachment->filetype == 0) {
			header("Content-Disposition: inline; filename=". $attachment->origfilename);
		} else {
			header("Content-Disposition: attachment; filename=" . $attachment->origfilename);
		}*/

		if (! Config::get('services.aws.enabled')) {
			@readfile($path);
		} else {
			return Redirect::to($url);
		}

		exit;
	}

	/**
	 * Upload an attachment
	 */
	public static function upload($file, $i, $hash)
	{
		global $me;

		$orig_name = $file->getClientOriginalName();
		$file_size = $file->getSize();
		$mime_type = $file->getMimeType();
		$ext = strtolower($file->getClientOriginalExtension());

		if ($ext == 'jpeg') {
			$ext = 'jpg';
		}

		$date = gmmktime() + $i;
		$year = date('Y', $date);
		$month = date('m', $date);
		$name = str_random(30) . '_' . $date;

		// Validate extension
		if (in_array($ext, ['gif', 'jpg', 'png'])) {
			// Image
			$attach_type = 0;
		} else if (in_array($ext, ['doc', 'docx', 'gz', 'pdf', 'rtf', 'svg', 'tar', 'txt', 'zip'])) {
			// Non-image
			$attach_type = 1;
		} else {
			throw new Exception('You may only upload DOC, DOCX, GIF, GZ, JPG, PDF, PNG, RTF, SVG, TAR, TXT, or ZIP files');
		}

		// Move file from temporary location
		$success = $file->move(storage_path() . '/uploads/', $name . '.' . $ext);

		// Resize if it's an image
		if ($attach_type == 0) {
			$img = new Image(storage_path() . '/uploads/' . $name . '.' . $ext);

			$sizes = array();

			$sizes['original'] = $name . '.' . $ext;

			$sizes['scale'] = $img->scaleLong(800)
				->setSuffix('_scale')
				->saveJpg()
				->getNewName();

			$sizes['thumbs'] = $img->scaleLong(200)
				->setSuffix('_thumb')
				->saveJpg()
				->getNewName();

			foreach ($sizes as $folder => $size) {
				$remote_path = 'attachments/'.$year.'/'.$month.'/';
				if ($folder != 'original') {
					$remote_path .= $folder . '/';
				}

				Helpers::push_to_s3(
					$img->getLocalDirectory() . '/' . $size,
					$remote_path . $name . '.' . ($folder == 'original' ? $ext : 'jpg'),
					($folder == 'original' ? false : true)
				);
			}
		}

		if ($success) {
			// Add to database
			$attachment = Attachment::create([
				'user_id'      => $me->id,
				'hash'         => $hash,
				'filename'     => $name.'.'.$ext,
				'origfilename' => $orig_name,
				'mimetype'     => $mime_type,
				'filetype'     => $attach_type,
				'filesize'     => $file_size,
			]);
		}
		
		return $success;
	}

	/**
	 * Delete an attachment
	 */
	public function delete($id)
	{
		global $me;

		$attachment = Attachment::findOrFail($id);

		if ($attachment->user_id != $me->id && !$me->is_mod) {
            App::abort(403);
        }

		$attachment->delete();

		return Response::json(['success' => true]);
	}

}

