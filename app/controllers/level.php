<?php
class LevelModel extends Model_W
{
	protected static $_table = 'levels';
	protected static $_instance = null;
}

class Level extends Controller_W 
{
	protected static $_table = 'levels';

	public function __construct( $pri = null, $data = null )
	{
		parent::__construct($pri, $data);
	}

	public function delete()
	{
		global $_db;
		
		$sql = "DELETE FROM `levels`
			WHERE `id` = {$this->id}";
		$_db->query($sql);
		
		// @todo delete image?
	}
}
