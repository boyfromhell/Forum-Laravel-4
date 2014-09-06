<?php

class Album extends Earlybird\Foundry
{

	protected $guarded = array('id');

	public function parent()
	{
		return $this->belongsTo('Album', 'parent_id');
	}
	public function children()
	{
		return $this->hasMany('Album', 'parent_id');
	}
	public function user()
	{
		return $this->belongsTo('User');
	}
	public function photos()
	{
		return $this->hasMany('Photo')
			->orderBy('date', 'asc')
			->orderBy('id', 'asc');
	}

}
