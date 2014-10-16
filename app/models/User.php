<?php

use Illuminate\Auth\UserTrait;
use Illuminate\Auth\UserInterface;
use Illuminate\Auth\Reminders\RemindableTrait;
use Illuminate\Auth\Reminders\RemindableInterface;

class User extends Eloquent implements UserInterface, RemindableInterface {

	use UserTrait, RemindableTrait;

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

		'is_admin',
		'is_mod',
	);

	/**
	 * Albums this user has created
	 *
	 * @return Relation
	 */
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
		return $this->hasMany('Avatar')
			->orderBy('created_at', 'asc');
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
			->orderBy('score', 'desc');
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
	 * Posts
	 *
	 * @return Relation
	 */
	public function posts()
	{
		return $this->hasMany('Post');
	}

	/**
	 * Screennames
	 *
	 * @return Relation
	 */
	public function screennames()
	{
		return $this->hasMany('Screenname')
			->orderBy('protocol', 'asc')
			->orderBy('screenname', 'asc');
	}

	/**
	 * Ignored users
	 *
	 * @return Relation
	 */
	public function ignoredUsers()
	{
		return $this->belongsToMany('User', 'user_lists', 'entry_user', 'entry_subject')
			->where('entry_type', '=', 0);
	}

	/**
	 * Buddies
	 *
	 * @return Relation
	 */
	public function buddies()
	{
		return $this->belongsToMany('User', 'user_lists', 'entry_user', 'entry_subject')
			->where('entry_type', '=', 1);
	}

	/**
	 * Topic subscriptions
	 *
	 * @return Relation
	 */
	public function subscriptions()
	{
		return $this->belongsToMany('Topic', 'topic_subs');
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
		if( $this->level_id ) {
			$level = Level::find($this->level_id);
		}

		if( ! $this->level_id || ! $level->image ) {
			$post_level = Level::where('type', '=', 0)
				->where('min_posts', '<=', $this->total_posts)
				->orderBy('min_posts', 'desc')
				->first();

			if( ! $level->image ) {
				$level->image = $post_level->image;
			}
		}

		return ( $level ? $level : $post_level );
	}

	/**
	 * Check if this user is an admin
	 *
	 * @return bool
	 */
	public function getIsAdminAttribute()
	{
		return ( $this->user_type >= 2 );

		/*$group = Group::where('name', '=', 'Administrators')->first();

		return $group->allMembers->contains($this->id);*/
	}

	/**
	 * Check if this user is a moderator
	 *
	 * @return bool
	 */
	public function getIsModeratorAttribute()
	{
		return ( $this->user_type >= 1 );

		/*$group = Group::where('name', '=', 'Moderators')->first();

		return $group->allMembers->contains($this->id);*/
	}

	/**
	 * Check if I voted in a poll
	 * Fetch array of results if so, or false if not
	 *
	 * @return mixed
	 */
	public function votedIn( $poll_id )
	{
		$vote = PollVote::where('user_id', '=', $this->id)
			->where('poll_id', '=', $poll_id)
			->first();

		if( $vote->id ) {
			return explode(',', $vote->choices);
		}

		return false;
	}

}

