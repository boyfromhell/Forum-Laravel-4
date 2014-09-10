<?php

class Shout extends Earlybird\Foundry
{

	protected $table = 'shoutbox';
	protected $guarded = array('id');

	/**
	 * User who posted the message
	 *
	 * @return Relation
	 */
	public function user()
	{
		return $this->belongsTo('User');
	}

}
