<?php namespace Parangi;

class TopicSubscription extends BaseModel
{
	protected $table = 'topic_subs';

	public function topic()
	{
		return $this->belongsTo('Parangi\Topic');
	}

}

