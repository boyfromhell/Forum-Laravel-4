<?php namespace Parangi;

class Newsletter extends Controller_W 
{

	/**
	 * Subscribe to this newsletter
	 */
	public function subscribe($user_id)
	{
		\DB::insert("INSERT IGNORE INTO newsletter_users SET
				newsletter_id = ?,
				user_id       = ?,
				subscribed_at = NOW()",
			[$this->id, $user_id]);
	}

	/**
	 * Unsubscribe
	 */
	public function unsubscribe($user_id)
	{
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

