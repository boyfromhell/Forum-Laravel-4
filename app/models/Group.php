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
	public function allMembers()
	{
		return $this->belongsToMany('user', 'group_members');
	}

	/**
	 * Moderators
	 *
	 * @return Relation
	 */
	public function moderators()
	{
		return $this->belongsToMany('User', 'group_members')
			->where('group_members.type', '=', 1);
	}

	/**
	 * Normal members
	 *
	 * @return Relation
	 */
	public function members()
	{
		return $this->belongsToMany('User', 'group_members')
			->where('group_members.type', '=', 0);
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

