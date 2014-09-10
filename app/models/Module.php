<?php

class Module extends Earlybird\Foundry
{

	protected $table = 'apps';

	public function category()
	{
		return $this->belongsTo('ModuleCategory', 'category_id');
	}

	/**
	 * Check if a module is active
	 *
	 * @return bool
	 */
	public static function isActive( $id )
	{
		//return( $board_apps[$appname]["enabled"] && $board_apps[$appname]["permission"] <= $access );

		return true;
	}

}
