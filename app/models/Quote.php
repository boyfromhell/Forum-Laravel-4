<?php

class Quote extends Earlybird\Foundry
{

	public function user()
	{
		return $this->belongsTo('User');
	}

}
