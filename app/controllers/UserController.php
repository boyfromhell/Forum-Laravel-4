<?php

class UserController extends Earlybird\FoundryController
{

	/**
	 * Display a user
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function display( $id, $name = NULL )
	{
		global $me;

		// @todo
		$access = 2;

		$user = User::findOrFail($id);

		$_PAGE = array(
			'category' => 'forums',
			'section'  => 'forums',
			'title'    => $user->name,
		);

		if( $me->id && ( $user->id != $me->id ) && !$me->administrator ) {
			$user->increment('views');
		}

		// Birthday
		list($year, $month, $day) = explode('-', $user->birthday);
		$month = date('F', mktime(0, 0, 0, $month));
		$day = (int)$day;
		$birthday = $month . ' ' . $day;
		if( $user->bdaypref == 0 ) {
			$birthday .= ', ' . $year;
		}

		// Custom fields
		$custom = $user->custom()
			->join('custom_fields', 'custom_data.field_id', '=', 'custom_fields.id')
			->where('profile', '=', 1)
			->where('permission', '<=', $access)
			->orderBy('order', 'asc')
			->get(['custom_fields.name', 'custom_data.value']);

		// Stats
		//$user->fetch_stats();

		return View::make('users.profile')
			->with('user', $user)
			->with('custom', $custom);

		/*$Smarty->assign('online_text', $user->online ? 'online' : 'offline');
		$Smarty->assign('user_last', $user_last);

		$Smarty->assign('custom', $custom);

		$Smarty->assign('show_birthday', $user->bdaypref < 2 ? true : false);
		$Smarty->assign('birthday', $birthday);

		$Smarty->assign('on_list', $on_list);
		$Smarty->assign('list_text', $list_text);

		// Contact
		$Smarty->assign('website_url', $user->website);
		$Smarty->assign('website_text', $website_text);
		$Smarty->assign('allow_email', $user->allow_email || $me->administrator ? true : false);
		$Smarty->assign('edit_url', ( $me->administrator ? '/admin/edit_user?id=' . $u : '/users/edit' ));
		*/
	}

	/**
	 * Members list
	 *
	 * @return Response
	 */
	public function members()
	{
		// @todo
		$access = 2;

		$_PAGE = array(
			'category' => 'community',
			'section'  => 'members',
			'title'    => 'Members'
		);

		// Search for / sort members
		$search = Input::get('search');
		$sort = Input::get('sort');
		$order = Input::get('order');

		if( $sort == 'name' ) { $orderby = 'name'; }
		else if( $sort == 'posts' ) { $orderby = 'posts'; }
		else { $sort = $orderby = 'joined'; }
		if( $order != 'desc' ) { $order = 'asc'; }

		if( $search ) {
			$users = User::leftJoin('custom_data', 'users.id', '=', 'custom_data.user_id')
				->leftJoin('custom_fields', 'custom_data.field_id', '=', 'custom_fields.id')
				->where('users.name', 'LIKE', '%'.$search.'%')
				->orWhere( function($q) use ($search)
				{
					$q->where('custom_data.value', 'LIKE', '%'.$search.'%')
						->where('custom_fields.memberlist', '=', 1)
						->where('custom_fields.permission', '<=', 2);
				})
				->groupBy('users.id')
				->orderBy($orderby, $order)
				->orderBy('users.id', 'asc');
		}
		else {
			$users = User::orderBy($orderby, $order)
				->orderBy('users.id', 'asc');
		}

		$users = $users->paginate(25, ['users.*']);

		/*$params = array();
		if( $search ) { $params['search'] = $search; }
		if( $orderby != 'joined' || $order != 'asc' ) {
			$params['sort'] = $orderby;
			$params['order'] = $order;
		}
		$query_string = http_build_query($params);
		$url = "/community/members" . ( $query_string ? '?' . $query_string : '' );
		$sort_url = "/community/members?" . ( $search ? "search={$search}&amp;" : '' );*/

		// Load the custom fields
		$customs = CustomField::where('memberlist', '=', 1)
			->where('permission', '<=', $access)
			->orderBy('order', 'asc')
			->get();

		$column_width = round(55/count($customs));

		/*$counter = $start;
		while( $data = $exec->fetch_assoc() )
		{
			$member = new User($data['id'], $data);
			$member->joined += ($me->tz*3600);
			$member_custom = array();
			$member->fetch_level();

			if( !$is_mobile ) {
				foreach( $customs as $custom ) {
					$sql = "SELECT `value`
						FROM `custom_data`
						WHERE `user_id` = {$member->id}
							AND `field_id` = {$custom['id']}
						LIMIT 1";
					$exec2 = $_db->query($sql);
					list( $c_value ) = $exec2->fetch_row();
					$member_custom[$custom['id']] = $c_value;
				}
			}
			
			$member->custom = $member_custom;
			$member->counter = ++$counter;
		}*/

		return View::make('users.members')
			->with('_PAGE', $_PAGE)
			->with('users', $users)
			->with('customs', $customs)
			->with('column_width', $column_width);

		/*$Smarty->assign('search', $search);
		$Smarty->assign('orderby', $orderby);
		$Smarty->assign('order', $order);
		$Smarty->assign('sort_url', $sort_url);*/
	}

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
