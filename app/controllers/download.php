<?php
class DownloadModel extends Model_W
{
	protected static $_table = 'downloads';
	protected static $_instance = null;
}

class Download extends Controller_W 
{
	protected static $_table = 'downloads';

	public function __construct( $pri = null, $data = null )
	{
		parent::__construct($pri, $data);

		$this->generate_url();
	}

	public function generate_url()
	{
		$this->url = '/download/' . $this->id;
	}
	
	/**
	 * Increments view counter
	 */
	public function increment_views()
	{
		global $_db;
		$sql = "UPDATE `downloads`
			SET `views` = `views` + 1
			WHERE `id` = {$this->id}";
		$_db->query($sql);
	}
	
	/**
	 * Get a human readable file size
	 * @todo cache this in the database when file is uploaded
	 */
	public function get_size()
	{
		if( file_exists(ROOT . 'web/files/' . $this->file) ) {
			return english_size(ROOT . 'web/files/' . $this->file);
		} else {
			return 0;
		}
	}
}
