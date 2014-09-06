<?php

class Project extends Earlybird\Foundry
{

	protected $appends = array(
		'url',
	);

	/**
	 * User who owns this project
	 *
	 * @return Relation
	 */
	public function user()
	{
		return $this->belongsTo('User');
	}

	/**
	 * Downloads which belong to this project
	 *
	 * @return Relation
	 */
	public function downloads()
	{
		return $this->hasMany('Download')
			->orderBy('version', 'asc');
	}

	/**
	 * Permalink
	 *
	 * @return string
	 */
	public function getUrlAttribute()
	{
		$url = preg_replace('/[^A-Za-z0-9]/', '_', $this->name);
		$url = trim(preg_replace('/(_)+/', '_', $url), '_');
		return '/projects/' . $this->id . '/' . $url;
	}

}

