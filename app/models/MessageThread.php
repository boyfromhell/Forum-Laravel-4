<?php

class MessageThread extends Earlybird\Foundry
{

	public function messages()
	{
		return $this->hasMany('Message', 'thread_id')
			->orderBy('date_sent', 'asc');
	}

}
