<?php

class Download extends Earlybird\Foundry
{

	public function project()
	{
		return $this->belongsTo('Project');
	}

}
