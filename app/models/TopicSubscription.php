<?php

class TopicSubscription extends Eloquent
{
	 */
	public function topic()
	{
		return $this->belongsTo('Topic');
	}

}

