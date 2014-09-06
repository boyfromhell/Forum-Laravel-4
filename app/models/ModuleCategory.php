<?php

class ModuleCategory extends Earlybird\Foundry
{

	protected $table = 'app_categories';

	public function modules()
	{
		return $this->hasMany('Module', 'category_id')
			->orderBy('order', 'asc');
	}

}
