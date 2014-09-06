<?php
class ProjectModel extends Model_W
{
	protected static $_table = 'projects';
	protected static $_instance = null;
}

class Project extends Controller_W 
{
	protected static $_table = 'projects';

	public function __construct( $pri = null, $data = null )
	{
		parent::__construct($pri, $data);
		
		$this->generate_url();
	}
	
	public function generate_url()
	{
		$url = preg_replace('/[^A-Za-z0-9]/', '_', $this->name);
		$url = trim(preg_replace('/(_)+/', '_', $url), '_');
		$this->url = '/projects/' . $this->id . '/' . $url;
	}
	
	/**
	 * Increments view counter
	 */
	public function increment_views()
	{
		global $_db;
		$sql = "UPDATE `projects`
			SET `views` = `views` + 1
			WHERE `id` = {$this->id}";
		$_db->query($sql);
	}
}
