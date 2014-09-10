<?php

class Category extends Earlybird\Foundry
{

	/**
	 * Top level forums in this category
	 *
	 * @return Relation
	 */
	public function forums()
	{
		// @todo should be just whereNull
		return $this->hasMany('Forum')
			->where('parent_id', '=', 0)
			->orWhereNull('parent_id')
			->orderBy('order', 'asc');
	}

}
