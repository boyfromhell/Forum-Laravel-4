<?php

class PollOption extends Eloquent
{

	/**
	 * Poll
	 *
	 * @return Relation
	 */
	public function poll()
	{
		return $this->belongsTo('Poll');
	}

}

