<?php

class Group extends Earlybird\Foundry
{

	protected $appends = array(
		'url',
	);

	/**
	 * All members
	 *
	 * @return Relation
	 */
	public function members()
	{
		return $this->belongsToMany('User', 'group_members');
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
		return '/groups/' . $this->id . '/' . $url;
	}

}

