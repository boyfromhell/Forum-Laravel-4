<?php

// @todo rewrite

/*class Event extends Controller_W 
{
	
	// Delete event
	public function delete()
	{
		global $_db;

		$sql = "DELETE FROM `events`
			WHERE `id` = {$this->id}";
		$_db->query($sql);
		
		$sql = "DELETE FROM `event_repeats`
			WHERE `event_id` = {$this->id}";
		$_db->query($sql);
	}
}



class EventRepeat extends Controller_W 
{
	public function generate_url()
	{
		$url = preg_replace('/[^A-Za-z0-9]/', '_', $this->name);
		$url = trim(preg_replace('/(_)+/', '_', $url), '_');
		$this->url = '/events/' . $this->id . '/' . $url;
	}

	// Delete event
	public function delete()
	{
		global $_db;

		$sql = "DELETE FROM `event_repeats`
			WHERE `id` = {$this->id}";
		$_db->query($sql);
	}
}
*/

