<?php

class Level extends Earlybird\Foundry
{

	protected $guarded = array('id');

	/**
	 * Users who are assigned this level
	 * (Only works for special levels)
	 *
	 * @return Relation
	 */
	public function users()
	{
		return $this->hasMany('User')
			->orderBy('name', 'asc');
	}

}
