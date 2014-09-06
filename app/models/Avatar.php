<?php

class Avatar extends Eloquent
{

	public function user()
	{
		return $this->belongsTo('User');
	}

}
