<?php namespace Parangi;

class SessionTopic extends BaseModel
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
		return $this->belongsTo('Parangi\Topic');
	}

}

