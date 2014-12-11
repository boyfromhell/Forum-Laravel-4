<?php

class Photo extends Eloquent
{
    use Earlybird\Foundry;

	protected $guarded = array('id');
	protected $appends = array(
		'url',
		'original',
		'scale',
		'thumbnail',

		'width',
		'height',
	);

	/**
	 * Album this photo is part of
	 *
	 * @return Relation
	 */
	public function album()
	{
		return $this->belongsTo('Album');
	}

	/**
	 * User who uploaded this photo
	 *
	 * @return Relation
	 */
	public function user()
	{
		return $this->belongsTo('User');
	}

	/**
	 * Permalink
	 *
	 * @return string
	 */
	public function getUrlAttribute()
	{
		return '/photos/' . $this->id;
	}

	/**
	 * Get the URL to this photo at the given size
	 *
	 * @param  string  $size
	 * @return string
	 */
	protected function _getPhoto($size)
	{
		list($name, $ext) = Helpers::parse_file_name($this->file);

		$folder = '/photos/'.$this->album->folder.'/';

		switch ($size) {
			case 'original':
				return $folder.$name.'.'.$ext;
				break;

			case 'scale':
				return $folder.'scale/'.$name.'.jpg';
				break;

			case 'thumbnail':
				return $folder.'thumbs/'.$name.'.jpg';
				break;
		}
	}

	/**
	 * Get URL of original size photo
	 *
	 * @return string
	 */
	public function getOriginalAttribute()
	{
		return $this->_getPhoto('original');
	}

	/**
	 * Get URL of scaled photo
	 *
	 * @return string
	 */
	public function getScaleAttribute()
	{
		return $this->_getPhoto('scale');
	}

	/**
	 * Get URL of thumbnail
	 *
	 * @return string
	 */
	public function getThumbnailAttribute()
	{
		return $this->_getPhoto('thumbnail');
	}

	/**
	 * Get width of photo
	 *
	 * @return int
	 */
	public function getWidthAttribute()
	{
		list($width, $height) = getimagesize(Config::get('app.cdn').$this->scale);

		return $width;
	}

	/**
	 * Get height of photo
	 *
	 * @return int
	 */
	public function getHeightAttribute()
	{
		list($width, $height) = getimagesize(Config::get('app.cdn').$this->scale);

		return $height;
	}

	/**
	 * Move original and scaled photos to S3
	 */
	public function pushToS3()
	{
		$pathinfo = pathinfo($this->file);
		$basename = $pathinfo['filename'];
		$ext = $pathinfo['extension'];

		$local = storage_path().'/uploads/';
		$remote = 'photos/'.$this->album->folder.'/';

		if (Helpers::push_to_s3(
				$local.$basename.'.'.$ext,
				$remote.$basename.'.'.$ext,
				false
			)) {
			unlink($root.$basename.'.'.$ext);
		}
		if (Helpers::push_to_s3(
				$local.$basename.'_sm.jpg',
				$remote.'scale/'.$basename.'.jpg',
				true
			)) {
			unlink($root.$basename.'_sm.jpg');
		}
		if (Helpers::push_to_s3(
				$local.$basename.'_tn.jpg',
				$remote.'thumbs/'.$basename.'.jpg',
				true
			)) {
			unlink($root.$basename.'_tn.jpg');
		}
	}

	/**
	 * Delete a photo
	 */
	public function delete()
	{
		/*if (! Config::get('services.aws.enabled')) {
			unlink(ROOT . "web/photos/{$folder}/{$name}.{$ext}");
			unlink(ROOT . "web/photos/{$folder}/scale/{$name}.jpg");
			unlink(ROOT . "web/photos/{$folder}/thumbs/{$name}.jpg");
		}
		else {
			delete_from_s3("photos/{$folder}/{$name}.{$ext}");
			delete_from_s3("photos/{$folder}/scale/{$name}.jpg");
			delete_from_s3("photos/{$folder}/thumbs/{$name}.jpg");
		}*/

		if ($this->album->cover_id == $this->id) {
			// Fetch first photo in this album
			$first_photo = Photo::where('album_id', '=', $this->album_id)
				->where('id', '!=', $this->id)
				->orderBy('created_at', 'asc')
				->first();

			$this->album->cover_id = $first_photo->id;
			$this->album->save();
		}

		return parent::delete();
	}

}

