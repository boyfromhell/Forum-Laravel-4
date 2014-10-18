<?php

class Message extends Earlybird\Foundry
{

	protected $appends = array(
		'date',
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
		return $this->belongsTo('MessageThread', 'thread_id');
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
	 * Owned by user
	 */
	public function scopeOwnedBy( $query, $user_id )
	{
		return $query->where('owner_user_id', '=', $user_id);
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
	 * List of recipients
	 *
	 * @return array
	 */
	public function getToAttribute()
	{
		$user_ids = explode(',', $this->to_users);

		if( count($user_ids) > 0 ) {
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

		if( count($user_ids) > 0 ) {
			$users = User::whereIn('id', $user_ids)->get();

			return $users;
		}

		return array();
	}

}

