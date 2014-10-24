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
		global $me;

		// @todo should be just whereNull
		return $this->hasMany('Forum')
			->where(function($q)
			{
				$q->where('parent_id', '=', 0)
					->orWhereNull('parent_id');
			})
			->where('view', '<=', $me->access)
			->orderBy('order', 'asc');
	}

}
