<?php

class GroupController extends Earlybird\FoundryController
{

	public function get_type()
	{
		switch( $this->type ) {
			case 'open':
				return 'Open'; break;
			case 'closed':
				return 'Closed'; break;
			case 'invite': default:
				return 'Invite Only'; break;
		}
	}

	public function get_info( $my_groups = array() )
	{
		if( in_array( $this->id, $my_groups ) ) {
			return 'You are a member of this group';
		}

		switch( $this->type ) {
			case 'open':
				return 'You may join this group';
				break;
			case 'closed':
				return 'You may request to join this group';
				break;
			case 'invite': default:
				return 'This group is invite-only';
				break;
		}
	}
	
	/**
	 * Count number of members
	 */
	public function count_members()
	{
		global $_db;
		
		$sql = "SELECT COUNT(1)
			FROM `group_members`
			WHERE `group_id` = {$this->id}";
		$exec = $_db->query($sql);
		list( $count ) = $exec->fetch_row();
		
		return $count;
	}

	/**
	 * Load member data into two arrays (moderators & members)
	 */
	public function load_members()
	{
		global $_db;
	
		// Members
		$sql = "SELECT `users`.`id`, `users`.`name`, `users`.`rank`, `users`.`posts`, `group_members`.`type`
			FROM `users`
				JOIN `group_members`
					ON `users`.`id` = `group_members`.`user_id`
			WHERE `group_members`.`group_id` = {$this->id}
			ORDER BY `group_members`.`type` DESC, `users`.`name` ASC";
		$exec = $_db->query($sql);

		$this->moderators = $this->members = array();
		
		$counter = 0;
		while( $data = $exec->fetch_assoc() )
		{
			$user = new User($data['id'], array('name' => $data['name'], 'rank' => $data['rank'], 'posts' => $data['posts']));
			$user->fetch_level();
			$user->counter = ++$counter;

			if( $data['type'] == 1 ) {
				$this->_extra['moderators'][] = $user;
			} else {
				$this->_extra['members'][] = $user;
			}
		}
	}

	/**
	 * Add a member based on ID
	 */
	public function add_member( $user_id, $type )
	{
		global $_db;

		$sql = "INSERT INTO `group_members` SET
			`group_id` = {$this->id},
			`user_id`  = {$user_id},
			`type`     = {$type}
			ON DUPLICATE KEY UPDATE
			`type`     = {$type}";
		$_db->query($sql);
	}

	/**
	 * Delete a member based on ID
	 */
	public function delete_member( $user_id )
	{
		global $_db;

		$sql = "DELETE FROM `group_members`
			WHERE `group_id` = {$this->id}
				AND `user_id` = {$user_id}";
		$_db->query($sql);
	}

	/** 
	 * Check if user ID is a member
	 * @return type mixed 1 if member, 2 if moderator, false if not a member
	 */
	public function check_membership( $user_id )
	{
		global $_db, $me;

		if( $user_id == $me->id && $me->administrator ) {
			return 2;
		}
	
		$sql = "SELECT `type`
			FROM `group_members`
			WHERE `group_id` = {$this->id}
				AND `user_id` = {$user_id}";
		$exec = $_db->query($sql);

		if( $exec->num_rows ) {
			list( $type ) = $exec->fetch_row();
			return( $type+1 );
		}
		return false;
	}
	
	public function delete()
	{
		global $_db;
		
		$sql = "DELETE FROM `group_members`
			WHERE `group_id` = {$this->id}";
		$_db->query($sql);
		
		$sql = "DELETE FROM `groups`
			WHERE `id` = {$this->id}";
		$_db->query($sql);
		
		// todo: delete badge image?
	}
}
