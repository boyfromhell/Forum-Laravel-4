<?php
class AvatarModel extends Model_W
{
	protected static $_table = 'avatars';
	protected static $_instance = null;
}

class Avatar extends Controller_W 
{
	protected static $_table = 'avatars';

	public function __construct( $pri = null, $data = null )
	{
		parent::__construct($pri, $data);
	}

	public function get_path()
	{
		return ROOT . 'web/images/avatars/';
	}

	public function delete()
	{
		global $_CONFIG, $_db, $me;

		if( $this->user_id == $me->id ) {
			$sql = "DELETE FROM `avatars`
				WHERE `id` = {$this->id}";
			$_db->query($sql);
			
			if( $_CONFIG['aws'] === null ) {
				unlink($this->get_path() . $this->file);
			}
			else {
				delete_from_s3("images/avatars/{$this->file}");
			}
			
			// @todo remove cache
		}
	}
	
	public function push_to_s3()
	{
		$folder = "images/avatars";

		if( push_to_s3("{$folder}/{$this->file}", true) ) {
			unlink(ROOT . "web/{$folder}/{$this->file}");
			return true;
		}
		return false;
	}
}
