<?php

class Group extends Earlybird\Foundry
{

	/**
	 * All members
	 *
	 * @return Relation
	 */
	public function members()
	{
		return $this->belongsToMany('User', 'group_members');
	}

}
