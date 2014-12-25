<?php namespace Parangi;

use Auth;
use User;

class Message extends BaseModel
{
    use \Earlybird\Foundry;

	protected $appends = array(
		'url',
		'date',
		'folder',

		'to',
		'users',
	);

	/**
	 * Message thread this is a part of
	 *
	 * @return Relation
	 */
	public function thread()
	{
		return $this->belongsTo('Parangi\MessageThread', 'thread_id');
	}

	/**
	 * Person who this message belongs to
	 * Each recipient gets their own copy
	 *
	 * @return Relation
	 */
	public function owner()
	{
		return $this->belongsTo('User', 'owner_user_id');
	}

	/**
	 * Person who sent the message
	 *
	 * @return Relation
	 */
	public function from()
	{
		return $this->belongsTo('User', 'from_user_id');
	}

	/**
	 * All attachments for this message
	 *
	 * @return Relation
	 */
	public function attachments()
	{
		return $this->hasMany('Parangi\Attachment')
			->orderBy('filetype', 'desc')
			->orderBy('created_at', 'asc');
	}

	/**
	 * Owned by user
	 *
	 * @param  Query  $query
	 * @param  int  $user_id
	 * @return Query
	 */
	public function scopeOwnedBy($query, $user_id)
	{
		return $query->where('owner_user_id', '=', $user_id);
	}

	/**
	 * Permalink
	 *
	 * @return string
	 */
	public function getUrlAttribute()
	{
		return $this->thread->url.'#'.$this->id;
	}

	/**
	 * Date, formatted and in my local timezone
	 *
	 * @return string
	 */
	public function getDateAttribute()
	{
		return Helpers::date_string(strtotime($this->created_at), 1);
	}

	/**
	 * Determine which folder this message belongs to
	 *
	 * @return string
	 */
	public function getFolderAttribute()
	{
		if ($this->archived) {
			return 'archived';
		} else if ($this->from_user_id == Auth::id()) {
			return 'sent';
		}

		return 'inbox';
	}

	/**
	 * List of recipients
	 *
	 * @return array
	 */
	public function getToAttribute()
	{
		$user_ids = explode(',', $this->to_users);

		if (count($user_ids) > 0) {
			$users = User::whereIn('id', $user_ids)->get();

			return $users;
		}

		return array();
	}


	/**
	 * List of all users involved with this message
	 *
	 * @return array
	 */
	public function getUsersAttribute()
	{
		$user_ids = explode(',', $this->to_users);
		$user_ids[] = $this->from_user_id;

		if(($key = array_search(Auth::id(), $user_ids)) !== false ) {
			unset($user_ids[$key]);
		}

		if (count($user_ids) > 0) {
			$users = User::whereIn('id', $user_ids)->get();

			return $users;
		}

		return array();
	}

	/**
	 * Delete message
	 */
	public function delete()
	{
		$this->attachments()->update([
			'message_id' => null,
			'hash' => 'deleted'
		]);

		$thread = $this->thread;

		parent::delete();

		if ($thread->replies > 0) {
			return $thread->url;
		}

		return false;
	}

}

