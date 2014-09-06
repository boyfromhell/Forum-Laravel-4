<?php

class Project extends Earlybird\Foundry
{

	public function user()
	{
		return $this->belongsTo('User');
	}
	public function downloads()
	{
		return $this->hasMany('Download')
			->orderBy('version', 'asc');
	}

}
