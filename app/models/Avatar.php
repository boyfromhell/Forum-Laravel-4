<?php

class Avatar extends Eloquent
{

	/**
	 * User who uploaded this avatar
	 *
	 * @return Relation
	 */
	public function user()
	{
		return $this->belongsTo('User');
	}

}
