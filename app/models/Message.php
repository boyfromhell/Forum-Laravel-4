<?php

class Message extends Earlybird\Foundry
{

	public function thread()
	{
		return $this->belongsTo('MessageThread', 'thread_id');
	}
	public function owner()
	{
		return $this->belongsTo('User', 'owner_user_id');
	}
	public function from()
	{
		return $this->belongsTo('User', 'from_user_id');
	}

}
