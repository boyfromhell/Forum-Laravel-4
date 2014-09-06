<?php

class Download extends Controller_W 
{
	public function generate_url()
	{
		$this->url = '/download/' . $this->id;
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
