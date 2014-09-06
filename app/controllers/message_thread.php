<?php
class MessageThreadModel extends Model_W
{
	protected static $_table = 'message_threads';
	protected static $_instance = null;
}

class MessageThread extends Controller_W 
{
	protected static $_table = 'message_threads';

	public function __construct( $pri = null, $data = null )
	{
		parent::__construct($pri, $data);
		
		$this->generate_url();
	}
	
	public function generate_url()
	{
		$this->url = '/messages/' . $this->id;
	}

	/**
	 * Count how many individual messages are in this thread
	 */
	public function count_messages( $user_id )
	{
		global $_db;
	
		$sql = "SELECT COUNT(1)
			FROM `messages`
			WHERE `thread_id` = {$this->id}
				AND `owner_user_id` = {$user_id}";
		$exec = $_db->query($sql);
		list( $total ) = $exec->fetch_row();
		$this->replies = $total;
	}
	
	/**
	 * Mark all messages in this thread as read
	 */
	public function mark_read( $user_id )
	{
		global $_db;
		$sql = "UPDATE `messages` SET
			`read` = 1
			WHERE `thread_id` = {$this->id}
				AND owner_user_id = {$user_id}";
		$_db->query($sql);
	}
}
