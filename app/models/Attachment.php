<?php

class Attachment extends Earlybird\Foundry
{

	protected $guarded = array('id');

	public function post()
	{
		return $this->belongsTo('Post');
	}
	public function message()
	{
		return $this->belongsTo('Message');
	}
	public function user()
	{
		return $this->belongsTo('User');
	}

}
