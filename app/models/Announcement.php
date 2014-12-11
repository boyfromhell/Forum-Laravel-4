<?php

class Announcement extends Eloquent
{
    use Earlybird\Foundry;

	/**
	 * User who created the announcement
	 *
	 * @return Relation
	 */
	public function user()
	{
		return $this->belongsTo('User');
	}

}

