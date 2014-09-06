<?php

class Score extends Earlybird\Foundry
{

	public function user()
	{
		return $this->belongsTo('User');
	}

}
