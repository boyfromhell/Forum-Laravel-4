<?php

class Photo extends Earlybird\Foundry
{

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
		return '/media/photo/' . $this->id;
	}

	/**
	 * Get the URL to this photo at the given size
	 *
	 * @param  string  $size
	 * @return string
	 */
	protected function _getPhoto( $size )
	{
		list( $name, $ext ) = Helpers::parse_file_name($this->file);

		$folder = '/photos/'.$this->album->folder.'/';

		switch( $size )
		{
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
		list( $width, $height ) = getimagesize(Config::get('app.cdn').$this->scale);

		return $width;
	}

	/**
	 * Get height of photo
	 *
	 * @return int
	 */
	public function getHeightAttribute()
	{
		list( $width, $height ) = getimagesize(Config::get('app.cdn').$this->scale);

		return $height;
	}

}

