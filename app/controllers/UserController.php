<?php

class UserController extends BaseController
{
    use Earlybird\FoundryController;

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
	public function display($id, $name = null)
	{
		global $me;

		$user = User::findOrFail($id);

		if ($me->id == $user->id) {
			$_PAGE['category'] = 'home';
			$menu = ForumController::fetchMenu('profile');
		}
		else {
			$_PAGE['category'] = 'community';
			$menu = GroupController::fetchMenu('members');
		}
		$_PAGE['title'] = $user->name;

		if ($me->id && ($user->id != $me->id) && !$me->is_admin) {
			$user->increment('views');
		}

		// Birthday
		list($year, $month, $day) = explode('-', $user->birthday);
		$month = date('F', mktime(0, 0, 0, $month));
		$day = (int)$day;
		$birthday = $month . ' ' . $day;
		if ($user->bdaypref == 0) {
			$birthday .= ', ' . $year;
		}

		// Custom fields
		$custom = $user->custom()
			->join('custom_fields', 'custom_data.field_id', '=', 'custom_fields.id')
			->where('profile', '=', 1)
			->where('permission', '<=', $me->access)
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
			->with('menu', $menu)
			->with('user', $user)
			->with('custom', $custom)

			->with('stats', $stats);

		/*$Smarty->assign('user_last', $user_last);

		$Smarty->assign('show_birthday', $user->bdaypref < 2 ? true : false);
		$Smarty->assign('birthday', $birthday);

		$Smarty->assign('on_list', $on_list);
		$Smarty->assign('list_text', $list_text);

		// Contact
		$Smarty->assign('website_url', $user->website);
		$Smarty->assign('website_text', $website_text);
		$Smarty->assign('allow_email', $user->allow_email || $me->is_admin ? true : false);
		$Smarty->assign('edit_url', ($me->is_admin ? '/admin/edit_user?id=' . $u : '/edit-profile'));
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

		$_PAGE['title'] = 'Edit Profile';

		if (Request::isMethod('post')) {
			if (Input::has('password')) {
				$password = Input::get('password');
			}

			$rules = [
				'confirm' => 'same:password',
				'email'   => 'required|email|unique:users,email,'.$me->id,
				'website' => 'url',
			];

			// Do we need a password confirmation?
			if ($me->email != Input::get('email') || $password !== null) {
				$rules['old_password'] = 'required|checkHashedPass:'.$me->password;
			}
			if ($password !== null) {
				$rules['password'] = 'required|min:6';
			}

			$messages = array(
				'check_hashed_pass' => 'Current password is incorrect'
			);

			$validator = Validator::make(Input::all(), $rules, $messages);

			if ($validator->fails()) {
				foreach ($validator->messages()->all() as $error) {
					Session::push('errors', $error);
				}

				return Redirect::to('edit-profile')->withInput();
			} else {
				$me->bdaypref = Input::get('bdaypref');
				$me->sig = substr(Input::get('sig'), 0, 512);
				$me->website = $website;
				$me->email = Input::get('email');

				if ($password !== null) {
					$me->password = Hash::make($password);
				}

				$me->save();

				// Save custom fields
				$customs = CustomField::orderBy('order', 'asc')->get();

				foreach ($customs as $custom) {
					$cdata = Input::get('custom'.$custom->id);
					$me->save_field($custom->id, $cdata);
				}

				Session::push('messages', 'Profile updated');

				return Redirect::to('edit-profile');
			}
		}

		// Load custom fields
		$customs = CustomField::leftJoin('custom_data', function($join) use ($me)
			{
				$join->on('custom_data.field_id', '=', 'custom_fields.id')
					->where('custom_data.user_id', '=', $me->id);
			})
			->orderBy('order', 'asc')
			->get(['custom_fields.*', 'custom_data.value']);

		// Birthday
		list($year, $month, $day) = explode('-', $me->birthday);

		$years = [0 => '&ndash;'];
		for ($i=date('Y')-100; $i<=date('Y'); $i++) {
			$years[$i] = $i;
		}
		$months = [0 => '&ndash;'];
		for ($i=1; $i<=12; $i++) {
			$months[$i] = date('F', mktime(0, 0, 0, $i));
		}

		$days = [0 => '&ndash;'];
		for ($i=1; $i<=31; $i++) {
			$days[$i] = $i;
		}

		return View::make('users.edit')
			->with('_PAGE', $_PAGE)
			->with('menu', UserController::fetchMenu('profile'))
			->with('mode', 'edit')
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

		$_PAGE['title'] = 'Settings';

		if (Request::isMethod('post')) {
			$me->lang = Input::get('lang', 'en');
			$me->hide_online = Input::get('hide_online', 0);
			$me->notify = Input::get('notify', 0);
			$me->attach_sig = Input::get('attach_sig', 0);
			$me->notify_pm = Input::get('notify_pm', 0);
			$me->allow_email = Input::get('allow_email', 0);
			$me->enable_smileys = Input::get('enable_smileys', 0);
			$me->timezone = Input::get('timezone');
			$me->style = Input::get('style');
			$me->save();

			Session::push('messages', 'Settings saved');

			return Redirect::to('settings');
		}

		$themes = Theme::orderBy('name', 'asc')->get();

		$tzs = array(
			-12, -11, -10, -9, -8, -7, -6, -5, -4, -3.5,
			-3, -2, -1, 0, 1, 2, 3, 3.5, 4, 4.5, 5, 5.5,
			6, 6.5, 7, 8, 9, 9.5, 10, 11, 12, 13
		);
		$languages = array(
			'english' => 'English',
			'finnish' => 'Finnish',
			'french'  => 'French',
			'german'  => 'German',
			'italian' => 'Italian',
			'polish'  => 'Polish',
		);

		return View::make('users.settings')
			->with('_PAGE', $_PAGE)
			->with('menu', UserController::fetchMenu('settings'))
			->with('tzs', $tzs)
			->with('languages', $languages)
			->with('themes', $themes);
	}

	/**
	 * Members list
	 *
	 * @return Response
	 */
	public function members()
	{
		global $me;

		$_PAGE = array(
			'category' => 'community',
			'title'    => 'Members'
		);

		// Search for / sort members
		$search = Input::get('search');
		$sort = Input::get('sort');
		$order = Input::get('order');

		if ($sort == 'name') {
			$orderby = 'name';
		} else if ($sort == 'posts') {
			$orderby = 'posts';
		} else {
			$sort = $orderby = 'created_at';
		}

		if ($order != 'desc') {
			$order = 'asc';
		}

		if ($search) {
			$users = User::leftJoin('custom_data', 'users.id', '=', 'custom_data.user_id')
				->leftJoin('custom_fields', 'custom_data.field_id', '=', 'custom_fields.id')
				->where('users.name', 'LIKE', '%'.$search.'%')
				->orWhere(function ($q) use ($search) {
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
		if ($search) {
			$params['search'] = $search;
		}
		if ($orderby != 'created_at' || $order != 'asc') {
			$params['sort'] = $orderby;
			$params['order'] = $order;
		}
		$query_string = http_build_query($params);
		$url = "/members" . ($query_string ? '?' . $query_string : '');
		$sort_url = "/members?" . ($search ? "search={$search}&amp;" : '');*/

		// Load the custom fields
		$customs = CustomField::where('memberlist', '=', 1)
			->where('permission', '<=', $me->access)
			->orderBy('order', 'asc')
			->get();

		$column_width = round(45/count($customs));

		return View::make('users.members')
			->with('_PAGE', $_PAGE)
			->with('menu', GroupController::fetchMenu('members'))
			->with('users', $users)
			->with('customs', $customs)
			->with('column_width', $column_width)

			->with('search', $search);

		/*$Smarty->assign('orderby', $orderby);
		$Smarty->assign('order', $order);
		$Smarty->assign('sort_url', $sort_url);*/
	}

	/**
	 * Manage my topic subscriptions
	 *
	 * @return Response
	 */
	public function subscriptions()
	{
		global $me;

		$_PAGE['title'] = 'Topic Subscriptions';

		if (Request::isMethod('post')) {
			$topics = Input::get('topics');
			$total = count($topics);

			if ($total > 0) {
				TopicSubscription::where('user_id', '=', $me->id)
					->whereIn('topic_id', $topics)
					->delete();

				Session::push('messages', 'Unsubscribed from '.$total.' topics');
			}

			return Redirect::to('subscriptions');
		}

		$topics = $me->subscriptions()
			->orderBy('posted_at', 'desc')
			->paginate(25);

		return View::make('users.topics')
			->with('_PAGE', $_PAGE)
			->with('menu', UserController::fetchMenu('topics'))
			->with('topics', $topics);
	}

	/**
	 * Load all users whose birthday is today
	 * @todo optional argument to check a different day
	 */
	public static function check_birthdays($date = null)
	{
		global $_db;
	
		$sql = "SELECT `id`, `name`
			FROM `users`
			WHERE DATE_FORMAT(`birthday`, '%m %d') = DATE_FORMAT(CURDATE(), '%m %d')";
		$exec = $_db->query($sql);

		$birthdays = array();
		while ($data = $exec->fetch_assoc()) {
			$user = new User($data['id'], $data);
			$birthdays[] = $user;
		}
		
		return $birthdays;
	}

	/** 
	 * Look up the ID of a user based on username
	 */
	public function lookup_id($username)
	{
		global $_db;
		$sql = "SELECT `id` FROM `users`
			WHERE `name` = '" . $_db->escape($username) . "'
			LIMIT 1";
		$exec = $_db->query($sql);
		list($id) = $exec->fetch_row();
		return $id;
	}

	/**
	 * Check if I should mark the "subscribe" checkbox when replying to this topic
	 */
	public function check_subscribe($topic_id)
	{
		global $_db;
	
		$sql = "SELECT `notified`
			FROM `topic_subs`
			WHERE `user_id` = {$this->id}
				AND `topic_id` = {$topic_id}";
		$exec = $_db->query($sql);
		
		if ($exec->num_rows) {
			$subscribed = true;
			$check_sub = true;
		}
		else {
			$subscribed = false;
			$check_sub = false;
			
			// If the default is to notify them, check if they have ANY post in this thread
			// If they do, that means they manually unsubscribed, if not, then we should subscribe them
			// @todo Instead, better to track if they did unsubscribe to a thread?
			if ($this->notify) {
				/*$sql = "SELECT COUNT(1)
					FROM `posts`
					WHERE `user_id` = {$this->id}
						AND `topic_id` = {$topic_id}";
				$exec = $_db->query($sql);
				list($has_posted) = $exec->fetch_row();
				
				if (!$has_posted) {
					$check_sub = true;
				}*/
				
				$check_sub = true;
			}
		}
		
		return array($subscribed, $check_sub);
	}

	/**
	 * Old encryption method
	 *
	 * @return string
	 */
	public function encrypt($text, $reset = false)
	{
		$salt = Config::get('app.old_salt');

		if (!$reset) {
			$text = md5($text); // So that the old passwords don't have to be changed
		}

		for ($i=0; $i<10; $i++) {
			$text = sha1($salt.$text);
			$text = sha1($text.salt);
		}
		return $text;
	}

	/**
	 * Displays the signin page or processes user/pass signin
	 *
	 * @return Response
	 */
	public function signin()
	{
		$_PAGE = array(
			'category' => 'home',
			'title'    => 'Sign in',
		);

		if (Request::isMethod('post')) {
			$email    = Input::get('email');
			$password = Input::get('password');

			$field = filter_var($email, FILTER_VALIDATE_EMAIL) ? 'email' : 'name';
			/*User::setLoginAttributeName($field);

			try {
				$credentials = array(
					$field     => $email,
					'password' => $password,
				);

				// Authenticate the user
				$user = Sentry::authenticate($credentials, true);

				return Redirect::intended('/');
			}
			catch (Cartalyst\Sentry\Users\WrongPasswordException $e)
			{
				$error = 'Wrong password, try again.';
			}
			catch (Cartalyst\Sentry\Users\UserNotFoundException $e)
			{
				$error = 'User was not found.';
			}
			catch (Cartalyst\Sentry\Users\UserNotActivatedException $e)
			{
				$error = 'User is not activated.';
			}

			// The following is only required if the throttling is enabled
			catch (Cartalyst\Sentry\Throttling\UserSuspendedException $e)
			{
				$error = 'User is suspended.';
			}
			catch (Cartalyst\Sentry\Throttling\UserBannedException $e)
			{
				$error = 'User is banned.';
			}

			Session::push('errors', $error);
			*/

			if (Auth::attempt(array($field => $email, 'password' => $password), true))
			{
				return Redirect::intended('/');
			}
			else {
				// Legacy method
				// Log them in, then encrypt their password in the new method
				$hash = UserController::encrypt($password);

				$old_user = User::where($field, '=', $email)
					->where('old_pass', '=', $hash)
					->first();

				if ($old_user->id) {

					$old_user->password = Hash::make($password);
					$old_user->old_pass = null;
					$old_user->save();

					Auth::login($old_user);

					return Redirect::intended('/');
				} else {
					// Error
					Session::push('errors', 'Username or password is incorrect');
				}
			}
		}

		return View::make('users.signin')
			->with('_PAGE', $_PAGE)
			->with('menu', ForumController::fetchMenu('signin'));
	}

	/**
	 * Sign up form
	 *
	 * @return Response
	 */
	public function signup()
	{
		if (! Config::get('app.registration_enabled')) {
			return Redirect::to('apply');
		}

		$_PAGE = array(
			'category' => 'home',
			'title'    => 'Register',
		);

		if (Request::isMethod('post')) {
			$unencrypted = Input::get('password');

			$rules = [
				'name'       => 'required|alpha_dash|max:25|unique:users|not_in:admin,administrator,moderator,guest',
				'email'      => 'required|email|unique:users',
				'password'   => 'required|min:6',
				'confirm'    => 'required|same:password',
				'recaptcha_response_field' => 'required|recaptcha',
				'website'    => 'url',
				'agree'      => 'in:1',
			];
			$messages = [
				'name.not_in' => 'Sorry, that username is not allowed',
				'agree.in' => 'You must agree to the Terms and Conditions',
			];

			// Run validation
			$validator = Validator::make(Input::all(), $rules, $messages);

			if ($validator->fails()) {
				foreach ($validator->messages()->all() as $error) {
					Session::push('errors', $error);
				}

				return Redirect::to('signup')->withInput(Input::except('password'));
			} else {
				$user = User::create([
					'name'       => Input::get('name'),
					'email'      => Input::get('email'),
					'password'   => Hash::make($unencrypted),
					'user_type'  => 0,
					'activated'  => 0,
					'lang'       => 'english',
				]);

				UserController::join($user, $unencrypted);

				Auth::login($user);

				return Redirect::to('settings');
			}
		}

		$customs = CustomField::orderBy('order', 'asc')
			->get();

		return View::make('users.edit')
			->with('_PAGE', $_PAGE)
			->with('menu', ForumController::fetchMenu('register'))
			->with('mode', 'signup')

			->with('customs', $customs);
	}

	/**
	 * Actions taken for all users regardless of signup method
	 */
	public function join($user, $unencrypted = null)
	{
		Session::push('messages', '<p>Thank you for registering!</p>');

		$data = array(
			'user_name' => $user->name,
			'unencrypted' => $unencrypted,
		);

		try {
			Mail::queue('emails.welcome', $data, function($message) use ($user) {
				$message->to($user->email)
					->subject('Welcome to '.Config::get('app.forum_name'));
			});

			Session::push('messages', '<p>You will be receiving a welcome email from us shortly with your username and password for your records</p>');
		} catch (Exception $e) {
			Session::push('errors', '<p>There was a problem sending your account info to your email address. However, your account was created successfully</p>');
		}
	}

	/**
	 * De-authorize the user
	 *
	 * @return Response
	 */
	public function signout()
	{
		global $me;

		$me->visited_at = DB::raw('NOW()');
		$me->save();

		Auth::logout();
		Session::flush();

		return Redirect::to('/');
	}

	/**
	 * Menu for user pages
	 *
	 * @return array
	 */
	public static function fetchMenu($active)
	{
		$menu = array();

		$menu['profile'] = array(
			'url' => '/edit-profile',
			'name' => 'Profile',
		);
		$menu['settings'] = array(
			'url' => '/settings',
			'name' => 'Settings',
		);
		$menu['avatar'] = array(
			'url' => '/avatar',
			'name' => 'Avatar',
		);
		$menu['topics'] = array(
			'url' => '/subscriptions',
			'name' => 'Topics',
		);

		$menu[$active]['active'] = true;

		return $menu;
	}

}

