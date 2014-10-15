<?php

class Message extends Earlybird\Foundry
{

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

}
