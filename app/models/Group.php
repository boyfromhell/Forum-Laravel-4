<?php namespace Parangi;

class Group extends \Cartalyst\Sentry\Groups\Eloquent\Group
{
    use \Earlybird\Foundry;

	protected $guarded = array('id');
	protected $appends = array(
		'url',
	);

	/**
	 * User who owns the group
	 *
	 * @return Relation
	 */
	public function user()
	{
		return $this->belongsTo('User');
	}

	/**
	 * All members
	 *
	 * @return Relation
	 */
	public function allMembers()
	{
		return $this->belongsToMany('User', 'users_groups');
	}

	/**
	 * Moderators
	 *
	 * @return Relation
	 */
	public function moderators()
	{
		return $this->belongsToMany('User', 'users_groups')
			->where('users_groups.type', '=', 1);
	}

	/**
	 * Normal members
	 *
	 * @return Relation
	 */
	public function members()
	{
		return $this->belongsToMany('User', 'users_groups')
			->where('users_groups.type', '=', 0);
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

