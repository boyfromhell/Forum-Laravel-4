<?php

class Announcement extends Earlybird\Foundry
{

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
