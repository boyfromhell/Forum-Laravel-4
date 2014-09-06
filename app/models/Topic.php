<?php

class Topic extends Earlybird\Foundry
{

	protected $appends = array(
		'url',
		'short_title',
	);

	public function forum()
	{
		return $this->belongsTo('forum');
	}
	public function author()
	{
		return $this->belongsTo('User', 'poster');
	}
	public function lastUser()
	{
		return $this->belongsTo('User', 'last');
	}
	public function posts()
	{
		return $this->hasMany('Post')
			->orderBy('time', 'asc');
	}

	/**
	 * Permalink
	 *
	 * @return string
	 */
	public function getUrlAttribute()
	{
		$url = preg_replace('/[^A-Za-z0-9]/', '_', $this->title);
		$url = trim(preg_replace('/(_)+/', '_', $url), '_');
		return '/topics/' . $this->id . '/' . $url;
	}

	/**
	 * Get a truncated title
	 *
	 * @return string
	 */
	public function getShortTitleAttribute()
	{
		if( strlen($this->title) > 50 ) {
			return substr($this->title, 0, 45) . '...';
		}
		return $this->title;
	}

}
