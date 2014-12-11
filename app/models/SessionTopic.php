<?php

class SessionTopic extends Eloquent
{

	public $timestamps = false;
	protected $primaryKey = 'session_id';

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

