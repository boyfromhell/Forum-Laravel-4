<?php

class Photo extends Earlybird\Foundry
{

	protected $appends = array(
		'url',
		'original',
		'scale',
		'thumbnail',
	);

	public function album()
	{
		return $this->belongsTo('Album');
	}
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
		return '/media/photo?id=' . $this->id;
	}

	/**
	 * Get the URL to this photo at the given size
	 *
	 * @param  string  $size
	 * @return string
	 */
	protected function getPhoto( $size )
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

	public function getOriginalAttribute()
	{
		return $this->getPhoto('original');
	}
	public function getScaleAttribute()
	{
		return $this->getPhoto('scale');
	}
	public function getThumbnailAttribute()
	{
		return $this->getPhoto('thumbnail');
	}

}

