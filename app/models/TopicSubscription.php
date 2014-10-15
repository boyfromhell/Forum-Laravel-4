<?php

class TopicSubscription extends Earlybird\Foundry
{

	protected $table = 'topic_subs';

	/**
	 * Topic subscribed to
	 *
	 * @return Relation
	 */
	public function topic()
	{
		return $this->belongsTo('Topic');
	}

}
