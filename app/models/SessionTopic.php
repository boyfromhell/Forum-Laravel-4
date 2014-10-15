<?php

class SessionTopic extends Eloquent
{

	/**
	 * Topic
	 *
	 * @return Relation
	 */
	public function topic()
	{
		return $this->belongsTo('Topic');
	}

}
