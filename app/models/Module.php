<?php namespace Parangi;

class Module extends BaseModel
{
    use \Earlybird\Foundry;

	protected $table = 'apps';

	/**
	 * This module's category
	 *
	 * @return Relation
	 */
	public function category()
	{
		return $this->belongsTo('Parangi\ModuleCategory', 'category_id');
	}

	/**
	 * Check if a module is active
	 *
	 * @param  string  $name
	 * @return bool
	 */
	public static function isActive($name)
	{
		global $me;

		//return ($board_apps[$appname]["enabled"] && $board_apps[$appname]["permission"] <= $me->access);

		return true;
	}

}

