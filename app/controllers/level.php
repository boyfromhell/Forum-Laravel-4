<?php

class Level extends Controller_W 
{
	public function delete()
	{
		global $_db;
		
		$sql = "DELETE FROM `levels`
			WHERE `id` = {$this->id}";
		$_db->query($sql);
		
		// @todo delete image?
	}
}
