<?php

use Illuminate\Auth\UserTrait;
use Illuminate\Auth\UserInterface;
use Illuminate\Auth\Reminders\RemindableTrait;
use Illuminate\Auth\Reminders\RemindableInterface;

class User extends Eloquent implements UserInterface, RemindableInterface {

	use UserTrait, RemindableTrait;

	// @todo
	public $timestamps = false;

	/**
	 * The database table used by the model.
	 *
	 * @var string
	 */
	protected $table = 'users';

	/**
	 * The attributes excluded from the model's JSON form.
	 *
	 * @var array
	 */
	protected $hidden = array('password', 'remember_token');
	protected $appends = array(
		'url',
		'level',
		'custom',
	);

	public function albums()
	{
		return $this->hasMany('Album');
	}

	/**
	 * All of this user's avatars
	 *
	 * @return Relation
	 */
	public function avatars()
	{
		return $this->hasMany('Avatar');
	}

	/**
	 * Current avatar
	 *
	 * @return Relation
	 */
	public function avatar()
	{
		return $this->belongsTo('Avatar');
	}

	/**
	 * Groups this user belongs to
	 *
	 * @return Relation
	 */
	public function groups()
	{
		return $this->belongsToMany('Group', 'group_members')
			->orderBy('name', 'asc');
	}

	/**
	 * User's high scores
	 *
	 * @return Relation
	 */
	public function scores()
	{
		return $this->hasMany('Score')
			->orderBy('score', 'asc');
	}

	/**
	 * Custom fields
	 *
	 * @return Relation
	 */
	public function custom()
	{
		return $this->hasMany('CustomData');
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
		return '/users/' . $this->id . '/' . $url;
	}

	/**
	 * User's level (specially assigned or based on # of posts)
	 *
	 * @return Level
	 */
	public function getLevelAttribute()
	{
		if( $this->rank ) {
			return Level::find($this->rank);
		}

		return Level::where('type', '=', 0)
			->where('min_posts', '<=', $this->posts)
			->orderBy('min_posts', 'desc')
			->first();
	}

}
