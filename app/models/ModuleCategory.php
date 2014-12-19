<?php namespace Parangi;

class ModuleCategory extends Eloquent
{
    use \Earlybird\Foundry;

	protected $table = 'app_categories';
	protected $appends = array(
		'is_active',
	);

	/**
	 * All modules in this category	
	 *
	 * @return Relation
	 */
	public function modules()
	{
		return $this->hasMany('Module', 'category_id')
			->where('enabled', '=', 1)
			->orderBy('order', 'asc');
	}

	/**
	 * Primary module
	 *
	 * @return Relation
	 */
	public function primary()
	{
		return $this->belongsTo('Module', 'app_id');
	}

	/**
	 * Check if this category is active
	 *
	 * @return bool
	 */
	public function getIsActiveAttribute()
	{
		return Module::isActive($this->primary->short_name);
	}

}

