<?php

class UserController extends Earlybird\FoundryController
{

	/**
	 * My profile
	 *
	 * @return Response
	 */
	public function myProfile()
	{
		global $me;

		return $this->display($me->id);
	}

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
			'section'  => ( $me->id == $user->id ? 'profile' : 'forums' ),
			'title'    => $user->name,
		);

		if( $me->id && ( $user->id != $me->id ) && !$me->is_admin ) {
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
		$stats = [
			'total_posts' => Post::count(),
			'total_shouts' => Shout::count(),
			'user_shouts' => Shout::where('user_id', '=', $user->id)->count(),
			'days' => floor((gmmktime()-strtotime($user->created_at))/86400) + 1,
		];
		$stats['posts_per_day'] = round($user->total_posts/$stats['days'], 2);
		$stats['posts_percent'] = round(100*($user->total_posts/$stats['total_posts']), 1);
		$stats['shouts_per_day'] = round($stats['user_shouts']/$stats['days'], 2);
		$stats['shouts_percent'] = round(100*($stats['user_shouts']/$stats['total_shouts']), 1);

		return View::make('users.profile')
			->with('_PAGE', $_PAGE)
			->with('user', $user)
			->with('custom', $custom)

			->with('stats', $stats);

		/*$Smarty->assign('online_text', $user->online ? 'online' : 'offline');
		$Smarty->assign('user_last', $user_last);

		$Smarty->assign('show_birthday', $user->bdaypref < 2 ? true : false);
		$Smarty->assign('birthday', $birthday);

		$Smarty->assign('on_list', $on_list);
		$Smarty->assign('list_text', $list_text);

		// Contact
		$Smarty->assign('website_url', $user->website);
		$Smarty->assign('website_text', $website_text);
		$Smarty->assign('allow_email', $user->allow_email || $me->is_admin ? true : false);
		$Smarty->assign('edit_url', ( $me->is_admin ? '/admin/edit_user?id=' . $u : '/users/edit' ));
		*/
	}

	/**
	 * Edit profile
	 *
	 * @return Response
	 */
	public function editProfile()
	{
		global $me;

		$_PAGE = array(
			'category' => 'usercp',
			'section'  => 'profile',
			'title'    => 'Edit Profile',
		);

		if( Request::isMethod('post') )
		{
			$rules = [
				'confirm' => 'same:password',
				'email' => 'email|unique:users,email,'.$me->id,
				'website' => 'url',
			];

			// Do we need a password confirmation?
			if( $me->email != Input::get('email') || Input::has('password') ) {
				$rules['old_password'] = 'required';
			}
			if( strlen(Input::get('password')) > 0 ) {
				$rules['password'] = 'required|min:6';
			}

			$validator = Validator::make(Input::all(), $rules);

			if( $validator->fails() ) {
				foreach( $validator->messages()->all() as $error ) {
					Session::push('errors', $error);
				}

				return Redirect::to('users/edit')->withInput();
			}
			else {
				$website = Input::get('website');
				if( $website && ! str_contains($website, '://') ) {
					$website = 'http://'.$website;
				}

				$me->bdaypref = Input::get('bdaypref');
				$me->sig = substr(Input::get('sig'), 0, 512);
				$me->website = $website;
				$me->email = Input::get('email');
				$me->save();

				Session::push('messages', 'Profile updated');

				return Redirect::to('users/edit')->withCookie($forever);
			}
		}

		// Custom fields
		$customs = CustomField::leftJoin('custom_data', function($join) use ($me)
			{
				$join->on('custom_data.field_id', '=', 'custom_fields.id')
					->where('custom_data.user_id', '=', $me->id);
			})
			->orderBy('order', 'asc')
			->get(['custom_fields.*', 'custom_data.value']);

		// Birthday
		list( $year, $month, $day ) = explode('-', $me->birthday);

		$years = [0 => 'Year'];
		for( $i=date('Y')-100; $i<=date('Y'); $i++ ) {
			$years[$i] = $i;
		}
		$months = [0 => 'Month'];
		for( $i=1; $i<=12; $i++ ) {
			$months[$i] = date('F', mktime(0, 0, 0, $i));
		}

		$days = [0 => 'Day'];
		for( $i=1; $i<=31; $i++ ) {
			$days[$i] = $i;
		}

		return View::make('users.edit')
			->with('_PAGE', $_PAGE)
			->with('customs', $customs)

			->with('years', $years)
			->with('year', $year)
			->with('months', $months)
			->with('month', $month)
			->with('days', $days)
			->with('day', $day);
	}

	/**
	 * Personal settings
	 *
	 * @return Response
	 */
	public function settings()
	{
		global $me;

		$_PAGE = array(
			'category' => 'usercp',
			'section'  => 'settings',
			'title'    => 'Settings',
		);

		if( Request::isMethod('post') ) {
			$me->online = Input::get('online', 0);
			$me->notify = Input::get('notify', 0);
			$me->attach_sig = Input::get('attach_sig', 0);
			$me->notify_pm = Input::get('notify_pm', 0);
			$me->allow_email = Input::get('allow_email', 0);
			$me->enable_smileys = Input::get('enable_smileys', 0);
			$me->timezone = Input::get('timezone');
			$me->style = Input::get('style');
			$me->save();

			Session::push('messages', 'Settings saved');

			return Redirect::to('users/settings');
		}

		$themes = Theme::orderBy('name', 'asc')->get();

		$tzs = array(
			-12, -11, -10, -9, -8, -7, -6, -5, -4, -3.5,
			-3, -2, -1, 0, 1, 2, 3, 3.5, 4, 4.5, 5, 5.5,
			6, 6.5, 7, 8, 9, 9.5, 10, 11, 12, 13
		);

		return View::make('users.settings')
			->with('_PAGE', $_PAGE)
			->with('tzs', $tzs)
			->with('themes', $themes);
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
		else { $sort = $orderby = 'created_at'; }
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
		if( $orderby != 'created_at' || $order != 'asc' ) {
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
			$member_custom = array();

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
	
		if( $this->last_view <= $gmt-300 || ( $this->online && !$me->is_admin )) { 
			$this->online = false;
		}
		else {
			$this->online = true;
		}
		
		if( $this->online && !$me->is_admin ) {
			$this->last_online = 'Unknown';
		}
		else {
			$this->last_online = $this->last_visit ? $this->last_visit : $this->created_at;
			$this->last_online = datestring($this->last_online,1);
		}
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

	/**
	 * Log me out
	 *
	 * @return Response
	 */
	public function logout()
	{
		$me->last_visit = gmmktime();
		$me->save();

		Auth::logout();
		Session::flush();

		return Redirect::to('/');
	}

}

