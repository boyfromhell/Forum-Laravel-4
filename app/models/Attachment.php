<?php

class Attachment extends Earlybird\Foundry
{

	protected $guarded = array('id');
	protected $appends = array(
		'url',
		'original',
		'scale',
		'thumbnail',
	);

	/**
	 * Post this attachment belongs to
	 * @todo use entity
	 *
	 * @return Relation
	 */
	public function post()
	{
		return $this->belongsTo('Post');
	}

	/**
	 * Message this attachment belongs to
	 * @todo use entity
	 *
	 * @return Relation
	 */
	public function message()
	{
		return $this->belongsTo('Message');
	}

	/**
	 * User who uploaded this attachment
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
		return '/forum/attachments/' . $this->id;
	}

	/**
	 * Get the URL to this photo at the given size
	 *
	 * @param  string  $size
	 * @return string
	 */
	protected function getPhoto( $size )
	{
		list( $name, $ext ) = Helpers::parse_file_name($this->filename);

		list( $year, $month, ) = explode('-', $this->created_at);

		$folder = '/attachments/' . $year . '/' . $month . '/';

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
