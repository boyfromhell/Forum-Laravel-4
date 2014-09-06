<?php

class Post extends Earlybird\Foundry
{

	public function topic()
	{
		return $this->belongsTo('Topic');
	}
	public function user()
	{
		return $this->belongsTo('User');
	}
	public function attachments()
	{
		return $this->hasMany('Attachment')
			->orderBy('date', 'asc');
	}

}
