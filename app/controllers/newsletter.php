<?php
class NewsletterModel extends Model_W
{
	protected static $_table = 'newsletters';
	protected static $_instance = null;
}

class Newsletter extends Controller_W 
{
	protected static $_table = 'newsletters';

	public function __construct( $pri = null, $data = null )
	{
		parent::__construct($pri, $data);
	}
	
	/**
	 * Count number of subscribers
	 */
	public function count_subscribers()
	{
		global $_db;
		
		$sql = "SELECT COUNT(1)
			FROM `newsletter_users`
			WHERE `newsletter_id` = {$this->id}";
		$exec = $_db->query($sql);
		list( $count ) = $exec->fetch_row();
		
		return $count;
	}

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
