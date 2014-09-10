<?php

class AdminMessage extends Earlybird\Foundry
{

	protected $guarded = array('id');
	public $timestamps = false;

	public function user()
	{
		return $this->belongsTo('User');
	}

}
