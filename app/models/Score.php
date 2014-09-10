<?php

class Score extends Earlybird\Foundry
{

	protected $guarded = array('id');
	public $timestamps = false;

	/**
	 * User who submitted the score
	 *
	 * @return Relation
	 */
	public function user()
	{
		return $this->belongsTo('User');
	}

}
