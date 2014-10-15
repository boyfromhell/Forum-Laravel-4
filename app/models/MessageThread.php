<?php

class MessageThread extends Earlybird\Foundry
{

	/**
	 * Messages in this thread
	 *
	 * @return Relation
	 */
	public function messages()
	{
		return $this->hasMany('Message', 'thread_id')
			->orderBy('date_sent', 'asc');
	}

}
