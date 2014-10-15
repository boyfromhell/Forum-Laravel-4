<?php

class AdminMessage extends Earlybird\Foundry
{

	protected $guarded = array('id');

	/**
	 * User who created this message
	 *
	 * @return Relation
	 */
	public function user()
	{
		return $this->belongsTo('User');
	}

}
