<?php

class Module extends Earlybird\Foundry
{

	protected $table = 'apps';

	public function $category()
	{
		return $this->belongsTo('ModuleCategory', 'category_id');
	}

}
