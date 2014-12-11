<?php

class TopicSubscription extends Eloquent
{
    use Earlybird\Foundry;

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

