<?php

class Poll extends Earlybird\Foundry
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

	/**
	 * Options to choose from
	 *
	 * @return Relation
	 */
	public function options()
	{
		return $this->hasMany('PollOption')
			->orderBy('weight', 'asc');
	}

}
