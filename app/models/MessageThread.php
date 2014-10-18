<?php

class MessageThread extends Earlybird\Foundry
{

	protected $appends = array(
		'url',
		'users',
	);

	/**
	 * Messages in this thread
	 *
	 * @return Relation
	 */
	public function messages()
	{
		return $this->hasMany('Message', 'thread_id')
			->ownedBy(Auth::id())
			->orderBy('created_at', 'asc');
	}

	/**
	 * Last posted message
	 *
	 * @return Message
	 */
	public function lastMessage()
	{
		return $this->messages()
			->take(1);
	}

	/**
	 * Permalink
	 *
	 * @return string
	 */
	public function getUrlAttribute()
	{
		return '/messages/'.$this->id;
	}

	/**
	 * All users involved in this thread
	 */
	public function getUsersAttribute()
	{
	}

}

