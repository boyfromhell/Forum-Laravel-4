<?php

use Illuminate\Auth\UserTrait;
use Illuminate\Auth\UserInterface;
use Illuminate\Auth\Reminders\RemindableTrait;
use Illuminate\Auth\Reminders\RemindableInterface;

class User extends Parangi\BaseModel implements UserInterface, RemindableInterface {

	use UserTrait, RemindableTrait, \Earlybird\Foundry;

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
	protected $hidden = array('password', 'persist_code');
	protected $guarded = array('id');
	protected $appends = array(
		'url',
		'post_level',
		'level',
		'access',
		'custom',

		'avatar_url',

		'online',
		'last_online',

		'is_admin',
		'is_mod',
		'is_moderator',

		'victory_rank',
		'defeat_rank',
	);

	/**
	 * Albums this user has created
	 *
	 * @return Relation
	 */
	public function albums()
	{
		return $this->hasMany('Parangi\Album');
	}

	/**
	 * All of this user's avatars
	 *
	 * @return Relation
	 */
	public function avatars()
	{
		return $this->hasMany('Parangi\Avatar')
			->orderBy('created_at', 'asc');
	}

	/**
	 * Current avatar
	 *
	 * @return Relation
	 */
	public function avatar()
	{
		return $this->belongsTo('Parangi\Avatar');
	}

	/**
	 * Groups this user belongs to
	 *
	 * @return Relation
	 */
	public function groups()
	{
		return $this->belongsToMany('Parangi\Group', 'users_groups')
			->orderBy('name', 'asc');
	}

	/**
	 * User's high scores
	 *
	 * @return Relation
	 */
	public function scores()
	{
		return $this->hasMany('Parangi\Score')
			->orderBy('score', 'desc');
	}

	/**
	 * Custom fields
	 *
	 * @return Relation
	 */
	public function custom()
	{
		return $this->hasMany('Parangi\CustomData');
	}

	/**
	 * Posts
	 *
	 * @return Relation
	 */
	public function posts()
	{
		return $this->hasMany('Parangi\Post');
	}

	/**
	 * Screennames
	 *
	 * @return Relation
	 */
	public function screennames()
	{
		return $this->hasMany('Parangi\Screenname')
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
		return $this->belongsToMany('Parangi\Topic', 'topic_subs');
	}

	/**
	 * Theme user has selected
	 *
	 * @return Relation
	 */
	public function theme()
	{
		return $this->belongsTo('Parangi\Theme');
	}

	/**
	 * Unread Messages
	 * Check where at least one message in the thread is unread (MIN(read) = 0)
	 * and at least one message isn't archived (MIN(archived) = 0)
	 * So an archived thread will ignore unread messages
	 *
	 * @return Relation
	 */
	public function unreadMessages()
	{
		return $this->hasMany('Parangi\Message', 'owner_user_id')
			->groupBy('thread_id')
			->having(DB::raw('MIN(`read`)'), '=', 0)
			->having(DB::raw('MIN(`archived`)'), '=', 0);
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
		//return '/users/' . $this->id . '/' . $url;
		return '/users/' . urlencode($this->name);
	}

	/**
	 * User's level based on # of posts
	 *
	 * @return Parangi\Level
	 */
	public function getPostLevelAttribute()
	{
		$post_level = Parangi\Level::where('type', '=', 0)
			->where('min_posts', '<=', $this->total_posts)
			->orderBy('min_posts', 'desc')
			->first();

		return $post_level;
	}

	/**
	 * User's level (specially assigned or based on # of posts)
	 *
	 * @return Parangi\Level
	 */
	public function getLevelAttribute()
	{
		// A custom assigned title
		if ($this->level_id) {
			$level = Parangi\Level::find($this->level_id);
		}

		// Title based on number of posts
		if (! $this->level_id || ! $level->image) {
			if ($level && ! $level->image) {
				$level->image = $this->post_level->image;
			}
		}

		return ($level ? $level : $this->post_level);
	}

	/**
	 * Get user's access level
	 *
	 * @return int
	 */
	public function getAccessAttribute()
	{
		$access = 0;
		if ($this->id) {
			$access++;
		}
		if ($this->is_mod) {
			$access++;
		}
		if ($this->is_admin) {
			$access++;
		}

		return $access;
	}

	/**
	 * Check if the user is online and visible
	 *
	 * @return string
	 */
	public function getOnlineAttribute()
	{
		global $me;

		if (strtotime($this->viewed_at) <= (time()-300) ||
			($this->hide_online && !$me->is_admin)) {
			return 'offline';
		}

		return 'online';
	}

	/**
	 * Check and format when the user last visited
	 *
	 * @return string
	 */
	public function getLastOnlineAttribute()
	{
		global $me;

		if ($this->hide_online && !$me->is_admin) {
			return 'Unknown';
		}
		else {
			$date = $this->visited_at ? $this->visited_at : $this->created_at;
			return Parangi\Helpers::date_string($date, 1);
		}
	}

	/**
	 * Check if this user is an admin
	 *
	 * @return bool
	 */
	public function getIsAdminAttribute()
	{
		return ($this->user_type >= 2);

		/*$group = Group::where('name', '=', 'Administrators')->first();

		return $group->allMembers->contains($this->id);*/
	}

	/**
	 * Check if this user is a moderator
	 *
	 * @return bool
	 */
	public function getIsModAttribute()
	{
		return ($this->user_type >= 1);

		/*$group = Group::where('name', '=', 'Moderators')->first();

		return $group->allMembers->contains($this->id);*/
	}

	/**
	 * Alias
	 *
	 * @return bool
	 */
	public function getIsModeratorAttribute()
	{
		return $this->is_mod;
	}

	/**
	 * Get Avatar URL, with default
	 *
	 * @return string
	 */
	public function getAvatarUrlAttribute()
	{
		if ($this->avatar->id) {
			return Config::get('app.cdn') . '/images/avatars/' . $this->avatar->file;
		} else {
			return '/images/custom/default-avatar.png';
		}
	}

	/**
	 * Get user's rank in victory scores
	 *
	 * @return int
	 */
	public function getVictoryRankAttribute()
	{
		$victory = DB::select("SELECT *, FIND_IN_SET( score, (
			SELECT GROUP_CONCAT( score
				ORDER BY score DESC )
				FROM scores
				WHERE victory = 1 )
			) AS rank
			FROM scores
			WHERE user_id = ?
				AND victory = 1
			ORDER BY score DESC
			LIMIT 1",
			[$this->id]);

		return $victory[0];
	}

	/**
	 * Get user's rank in defeat scores
	 *
	 * @return int
	 */
	public function getDefeatRankAttribute()
	{
		$defeat = DB::select("SELECT *, FIND_IN_SET( score, (
			SELECT GROUP_CONCAT( score
				ORDER BY score DESC )
				FROM scores 
				WHERE victory = 0 )
			) AS rank
			FROM scores
			WHERE user_id = ?
				AND victory = 0
			ORDER BY score DESC
			LIMIT 1",
			[$this->id]);

		return $defeat[0];
	}

	/**
	 * Save a custom field
	 *
	 * @param  int  $field_id
	 * @param  mixed  $data
	 */
	public function save_field($field_id, $data)
	{
		// Delete data if empty
		if ($data === '' || $data === null) {
			Parangi\CustomData::where('user_id', '=', $this->id)
				->where('field_id', '=', $field_id)
				->delete();
		} else {
			DB::insert("INSERT INTO custom_data SET
					user_id  = ?,
					field_id = ?,
					value    = ?
				ON DUPLICATE KEY UPDATE
					value = ?",
				[$this->id, $field_id, $data, $data]);
		}
	}

	/**
	 * Check if I voted in a poll
	 * Fetch array of results if so, or false if not
	 *
	 * @return mixed
	 */
	public function votedIn($poll_id)
	{
		$vote = Parangi\PollVote::where('user_id', '=', $this->id)
			->where('poll_id', '=', $poll_id)
			->first();

		if ($vote->id) {
			return explode(',', $vote->choices);
		}

		return false;
	}

}

