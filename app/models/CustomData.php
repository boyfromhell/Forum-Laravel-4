<?php

class CustomData extends Earlybird\Foundry
{

	protected $table = 'custom_data';

	/**
	 * User who filled out these fields
	 *
	 * @return Relation
	 */
	public function user()
	{
		return $this->belongsTo('User');
	}

	/**
	 * Field this data belongs to
	 *
	 * @return Relation
	 */
	public function field()
	{
		return $this->belongsTo('CustomField', 'field_id');
	}

}
