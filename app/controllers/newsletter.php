<?php

class Newsletter extends Controller_W 
{

	/**
	 * Subscribe to this newsletter
	 */
	public function subscribe( $user_id )
	{
		global $_db, $gmt;

		$sql = "INSERT IGNORE INTO `newsletter_users` SET
			`newsletter_id`   = {$this->id},
			`user_id`         = {$user_id},
			`date_subscribed` = {$gmt}";
		$_db->query($sql);
	}

	/**
	 * Unsubscribe
	 */
	public function unsubscribe( $user_id )
	{
		global $_db;

		$sql = "DELETE FROM `newsletter_users`
			WHERE `newsletter_id` = {$this->id}
				AND `user_id`     = {$user_id}";
		$_db->query($sql);
	}

	public function delete()
	{
		global $_db;
		
		$sql = "DELETE FROM `newsletter_users`
			WHERE `newsletter_id` = {$this->id}";
		$_db->query($sql);
		
		$sql = "DELETE FROM `newsletters`
			WHERE `id` = {$this->id}";
		$_db->query($sql);
	}
}
