<?php

class SessionTopic extends Eloquent
{

	public $timestamps = false;

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
