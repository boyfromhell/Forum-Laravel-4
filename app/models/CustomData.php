<?php

class CustomData extends Earlybird\Foundry
{

	protected $table = 'custom_data';

	public function user()
	{
		return $this->belongsTo('User');
	}
	public function field()
	{
		return $this->belongsTo('CustomField', 'field_id');
	}

}
