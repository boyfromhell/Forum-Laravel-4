<?php

class User extends Controller_W 
{
	/**
	 * Load all users whose birthday is today
	 * @todo optional argument to check a different day
	 */
	public static function check_birthdays( $date = null )
	{
		global $_db;
	
		$sql = "SELECT `id`, `name`
			FROM `users`
			WHERE DATE_FORMAT(`birthday`, '%m %d') = DATE_FORMAT(CURDATE(), '%m %d')";
		$exec = $_db->query($sql);

		$birthdays = array();
		while( $data = $exec->fetch_assoc() ) {
			$user = new User($data['id'], $data);
			$birthdays[] = $user;
		}
		
		return $birthdays;
	}

	public function view()
	{
		global $_db;
		$sql = "UPDATE `users`
			SET `views` = `views` + 1
			WHERE `id` = {$this->id}";
		$_db->query($sql);
	}

	public function load_groups()
	{
		global $_db;
		$sql = "SELECT `groups`.*
			FROM `group_members`
				JOIN `groups`
					ON `group_members`.`group_id` = `groups`.`id`
			WHERE `group_members`.`user_id` = {$this->id}
			ORDER BY `groups`.`name` ASC";
		$exec = $_db->query($sql);

		$groups = array();
		while( $data = $exec->fetch_assoc() )
		{
			$group = new Group($data['id'], $data);
			$groups[] = $group;
		}
		return $groups;
	}

	/**
	 * @param access int global current user access level
	 */
	public function load_custom_fields( $access, $section = 'profile' )
	{
		global $_db;
		$access = (int)$access;
		$section_sql = '';

		if( $section == 'topic' ) {
			$section_sql = 'AND `custom_fields`.`topic` = 1';
		} else {
			$section_sql = 'AND `custom_fields`.`profile` = 1';
		}
	
		$sql = "SELECT `custom_fields`.`name`, `custom_data`.`value`
			FROM `custom_data`
				JOIN `custom_fields`
					ON `custom_data`.`field_id` = `custom_fields`.`id`
			WHERE `custom_data`.`user_id` = {$this->id}
				{$section_sql}
				AND `custom_fields`.`permission` <= {$access}
			ORDER BY `custom_fields`.`order` ASC";
		$exec = $_db->query($sql);

		$custom = array();
		while( $data = $exec->fetch_assoc() )
		{
			$custom[] = array(
				'name' => $data['name'],
				'value' => $data['value']
			);
		}
		return $custom;
	}

	public function load_screennames()
	{
		global $_db;

		$sql = "SELECT `id`, `protocol`, `screenname`
			FROM `screennames`
			WHERE `user_id` = {$this->id}
			ORDER BY `protocol` ASC, `screenname` ASC";
		$exec = $_db->query($sql);

		$ims = array();
		while( $data = $exec->fetch_assoc() )
		{
			$im = new Screenname($data['id'], $data);
			
			$ims[] = array(
				'protocol' => $im->get_protocol_name(),
				'image' => $im->get_image(),
				'screenname' => $im->screenname,
			);
		}
		return $ims;
	}

	/** 
	 * Look up the ID of a user based on username
	 */
	public function lookup_id( $username )
	{
		global $_db;
		$sql = "SELECT `id` FROM `users`
			WHERE `name` = '" . $_db->escape($username) . "'
			LIMIT 1";
		$exec = $_db->query($sql);
		list( $id ) = $exec->fetch_row();
		return $id;
	}

	/**
	 * Checks if the user is online or not
	 */
	public function check_online()
	{
		global $gmt, $me;
	
		if( $this->last_view <= $gmt-300 || ( $this->online && !$me->administrator )) { 
			$this->online = false;
		}
		else {
			$this->online = true;
		}
		
		if( $this->online && !$me->administrator ) {
			$this->last_online = 'Unknown';
		}
		else {
			$this->last_online = $this->last_visit ? $this->last_visit : $this->joined;
			$this->last_online = datestring($this->last_online + ($me->tz*3600),1);
		}
	}
	
	/**
	 * Fetch the name and image of the user's current level
	 */
	public function fetch_level()
	{
		global $_db;
		$sql = "SELECT `name`, `image`
			FROM `levels`
			WHERE `type` = 0
				AND `min_posts` <= {$this->posts}
			ORDER BY `min_posts` DESC
			LIMIT 1";
		$exec = $_db->query($sql);
		list( $level_name, $level_image ) = $exec->fetch_row();

		if( $this->rank ) {
			$sql = "SELECT `name`, `image`
				FROM `levels`
				WHERE `id` = {$this->rank}";
			$exec = $_db->query($sql);
			list( $level_name, $new_image ) = $exec->fetch_row();
			if( $new_image ) {
				$level_image = $new_image;
			}
		}
		
		$this->level_name = $level_name;
		$this->level_image = $level_image;
	}

	/**
	 * Fetch user's post statistics
	 */
	public function fetch_stats()
	{
		global $_db, $gmt, $me;
	
		$sql = "SELECT COUNT(1) FROM `posts`";
		$exec = $_db->query($sql);
		list( $total_posts ) = $exec->fetch_row();

		$sql = "SELECT COUNT(1) FROM `shoutbox`";
		$exec = $_db->query($sql);
		list( $total_shouts ) = $exec->fetch_row();

		$sql = "SELECT COUNT(1)
			FROM `shoutbox`
			WHERE `user_id` = {$this->id}";
		$exec = $_db->query($sql);
		list( $shouts ) = $exec->fetch_row();

		$today = $gmt+($me->tz*3600);
		$days = floor(($today-$this->joined)/86400)+1;

		$this->shouts = $shouts;
		$this->posts_per_day = round($this->posts/$days,2);
		$this->posts_percent = round(100*($this->posts/$total_posts),1);
		$this->shouts_per_day = round($shouts/$days,2);
		$this->shouts_percent = round(100*($shouts/$total_shouts),1);
	}
	
	/**
	 * Check if I should mark the "subscribe" checkbox when replying to this topic
	 */
	public function check_subscribe( $topic_id )
	{
		global $_db;
	
		$sql = "SELECT `notified`
			FROM `topic_subs`
			WHERE `user_id` = {$this->id}
				AND `topic_id` = {$topic_id}";
		$exec = $_db->query($sql);
		
		if( $exec->num_rows ) {
			$subscribed = true;
			$check_sub = true;
		}
		else {
			$subscribed = false;
			$check_sub = false;
			
			// If the default is to notify them, check if they have ANY post in this thread
			// If they do, that means they manually unsubscribed, if not, then we should subscribe them
			// @todo Instead, better to track if they did unsubscribe to a thread?
			if( $this->notify ) {
				/*$sql = "SELECT COUNT(1)
					FROM `posts`
					WHERE `user_id` = {$this->id}
						AND `topic_id` = {$topic_id}";
				$exec = $_db->query($sql);
				list( $has_posted ) = $exec->fetch_row();
				
				if( !$has_posted ) {
					$check_sub = true;
				}*/
				
				$check_sub = true;
			}
		}
		
		return array( $subscribed, $check_sub );
	}

	/**
	 * Validate a field 
	 */
	public static function validate($field, $value, $value2 = null)
	{
		global $_CONFIG, $_db;
	
		switch( $field ) {
			// Username error checking
			case 'username':
				$protected = array('admin', 'administrator', 'moderator', 'guest');
				if( strlen($value) < 2 ) {
					throw new Exception('Username is too short');
				}
				else if( in_array(strtolower($value), $protected) ) {
					throw new Exception('Sorry, that username is forbidden');
				}
				else {
					$sql = "SELECT `id`
						FROM `users`
						WHERE `name` = '" . $_db->escape($value) . "'
						LIMIT 1";
					$exec = $_db->query($sql);
					
					if( $exec->num_rows > 0 ) {
						throw new Exception('That username is taken. Did you <a href="/users/reset_password">forget your password</a>?');
					}
				}
				break;

			// Email error checking
			case 'email':
				if( !filter_var($value, FILTER_VALIDATE_EMAIL) ) {
					throw new Exception('Email is not valid');
				}
				else {
					$sql = "SELECT `id`
						FROM `users`
						WHERE `email` = '" . $_db->escape($value) . "'
						LIMIT 1";
					$exec = $_db->query($sql);
					
					if( $exec->num_rows > 0 ) {
						throw new Exception('That email is already registered. Did you <a href="/users/reset_password">forget your password</a>?');
					}
				}
				break;

			// Password error checking
			case 'password':
				if( $value !== $value2 ) {
					throw new Exception('The passwords you entered did not match');
				}
				if( strlen($value) < 6 ) {
					throw new Exception('Your password must be at least 6 characters');
				}
				break;

			// CAPTCHA (allows for multiple correct answers)
			case 'captcha':
				$answer = $_CONFIG['captcha_answer'];
				if( is_array($answer) && !in_array($value, $answer) ) {
					throw new Exception("You're probably a spam bot");
				}
				else if( !is_array($answer) && $value !== $answer ) {
					throw new Exception("You're probably a spam bot");
				}
				break;
				
			default:
				break;
		}
		
		return true;
	}
}
