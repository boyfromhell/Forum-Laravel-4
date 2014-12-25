<?php namespace Parangi;

class TopicSubscription extends BaseModel
{

	public function topic()
	{
		return $this->belongsTo('Parangi\Topic');
	}

}

