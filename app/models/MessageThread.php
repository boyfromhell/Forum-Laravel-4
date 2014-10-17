<?php

class MessageThread extends Earlybird\Foundry
{

	protected $appends = array(
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
			->orderBy('created_at', 'asc');
	}

	/**
	 * Last posted message
	 *
	 * @return Message
	 */
	public function lastMessage()
	{
		global $me;

		return $this->hasMany('Message', 'thread_id')
			->ownedBy($me->id)
			->orderBy('created_at', 'desc')
			->take(1);
	}

	/**
	 * All users involved in this thread
	 */
	public function getUsersAttribute()
	{
	}

}

