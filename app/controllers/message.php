<?php
class MessageModel extends Model_W
{
	protected static $_table = 'messages';
	protected static $_instance = null;
}

class Message extends Controller_W 
{
	protected static $_table = 'messages';

	public function __construct( $pri = null, $data = null )
	{
		parent::__construct($pri, $data);
	}
	
	/**
	 * Loads all users who are involved (from or to) a message, excluding me
	 */
	public function load_all_users()
	{
		global $me;
	
		$user_array = explode(',', $this->to_users);
		$user_array[] = $this->from_user_id;
		$users = array();

		foreach( $user_array as $user_id ) {
			if( $user_id != $me->id ) {
				try {
					$user = new User($user_id);
					$users[] = $user;
				}
				catch( Exception $e ) {
				}
			}
		}

		return $users;
	}
	
	/**
	 * Delete this post
	 *
	 * @param  $section  the folder that they are currently in 
	 * @return result array parameters with info about where I am redirecting you to
	 */
	public function delete( $section )
	{
		global $_db, $me;

		$sql = "DELETE FROM `messages`
			WHERE `id` = {$this->id}";
		$_db->query($sql);
		
		// Delete attachments
		$sql = "UPDATE `attachments` SET
			`message_id` = NULL,
			`hash` = 'deleted'
			WHERE `message_id` = {$this->id}";
		$_db->query($sql);

		$sql = "SELECT COUNT(1)
			FROM `messages`
			WHERE `thread_id` = {$this->thread_id}
				AND `owner_user_id` = {$me->id}";
		$exec = $_db->query($sql);

		list( $total_messages ) = $exec->fetch_row();

		if( $total_messages ) {
			$result = array(
				'url'   => "/messages/{$this->thread_id}",
				'where' => 'private message thread'
			);
		}
		else {
			$result = array(
				'url'   => "/messages/{$section}",
				'where' => 'private messages'
			);
		}
		
		return $result;
	}
	
	public function load_attachments()
	{
		global $_db;

		$sql = "SELECT *
			FROM `attachments`
			WHERE `message_id` = {$this->id}
			ORDER BY `filetype` DESC, `date` ASC";
		$exec = $_db->query($sql);

		$attachments = array();
		while( $data = $exec->fetch_assoc() ) {
			$attachment = new Attachment($data['id'], $data);
			$attachment->thumb = substr($attachment->filename, 0, -4) . '.jpg';
			
			$attachments[] = $attachment;
		}
		
		return $attachments;
	}
}
