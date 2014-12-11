<?php

class Download extends Eloquent
{
    use Earlybird\Foundry;

	protected $appends = array(
		'url',
		'size',
	);

	/**
	 * Project this belongs to
	 *
	 * @return Relation
	 */
	public function project()
	{
		return $this->belongsTo('Project');
	}

	/**
	 * Permalink
	 *
	 * @return string
	 */
	public function getUrlAttribute()
	{
		return '/download/' . $this->id;
	}

	/**
	 * Get a human readable file size
	 * @todo cache this in the database when file is uploaded
	 *
	 * @return string
	 */
	public function getSizeAttribute()
	{
		return Helpers::english_size(public_path() . '/files/' . $this->file);
	}

}

