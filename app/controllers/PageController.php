<?php

class PageController extends BaseController
{

	/**
	 * Display a static page
	 *
	 * @return Response
	 */
	public function display()
	{
		$page = Route::currentRouteName();

		$menu = PageController::fetchMenu($page);

		switch ($page) {
			case 'about':
				$_PAGE['title'] = 'About';
				$template = 'pages.about';
				break;

			case 'chat':
				$_PAGE['category'] = 'community';
				$_PAGE['title'] = 'Chat';
				$template = 'pages.chat';
				$menu = GroupController::fetchMenu('chat');
				break;

			case 'donate':
				$members_only = true;
				$_PAGE['title'] = 'Donate';
				$template = 'pages.donate';
				break;
			
			case 'privacy':
				$_PAGE['title'] = 'Privacy Policy';
				$template = 'pages.privacy';
				break;
			
			case 'terms':
				$_PAGE['title'] = 'Terms of Use';
				$template = 'pages.terms';
				break;
		}

		if (! $me->id && $members_only) {
			App::abort(403);
		}

		return View::make($template)
			->with('_PAGE', $_PAGE)
			->with('menu', $menu)
			->with('rand', rand(1,8));
	}

	/**
	 * Contact form
	 *
	 * @return Response
	 */
	public function contact()
	{
		global $me;

		$_PAGE['title'] = 'Contact';

		if (Request::isMethod('post')) {
			$rules = [
				'name' => 'required',
				'email' => 'required|email',
				'subject' => 'required',
				'message' => 'required',
				'recaptcha_response_field' => 'required|recaptcha',
			];

			$validator = Validator::make(Input::all(), $rules);

			if ($validator->fails()) {
				foreach ($validator->messages()->all() as $error) {
					Session::push('errors', $error);
				}

				return Redirect::to('contact')
					->withInput();
			} else {
				$data = [
					'user_id' => $me->id,
					'name'    => Input::get('name'),
					'email'   => Input::get('email'),
					'subject' => Input::get('subject'),
					'message' => Input::get('message'),
					'ip'      => Request::getClientIp(),
				];

				// Save a copy in the database
				$am = AdminMessage::create($data);

				$data['user_url'] = $me->url;
				$data['user_name'] = $me->name;

				try {
					// Send message
					Mail::queue('emails.contact', $data, function($message)
					{
						$message->to(Config::get('app.admin_email'))
							->subject(Input::get('subject'));
					});

					Session::push('messages', 'Your message has been sent. Thank you');
				} catch (Exception $e) {
					Session::push('errors', "Sorry, we're experiencing issues");
					Session::push('errors', 'Please contact us directly at <b>'.Config::get('app.admin_email').'</b>');
				}

				return Redirect::to('contact');
			}
		}

		return View::make('pages.contact')
			->with('_PAGE', $_PAGE)
			->with('menu', PageController::fetchMenu('contact'));
	}

	/**
	 * Sitemap
	 *
	 * @return Response
	 */
	public function sitemap()
	{
		global $me;

		$_PAGE['title'] = 'Sitemap';

		$categories = ModuleCategory::where('permission', '<=', $me->access)
			->orderBy('order', 'asc')
			->get();

		$categories->load(['modules' => function($query) use ($me)
		{
			$query->where('permission', '<=', $me->access);
		}]);

		return View::make('pages.sitemap')
			->with('_PAGE', $_PAGE)
			->with('categories', $categories);
	}

	/**
	 * Links page with bookmarks to other sites
	 *
	 * @return Response
	 */
	public function links()
	{
		$_PAGE['title'] = 'Links';

		$categories = array(
			'IVAN' => array(
				'http://ivan.sourceforge.net/' => 'Official IVAN homepage',
				'http://sourceforge.net/projects/ivan' => 'Sourceforge project page',
				'http://www.attnam.com/wiki/' => 'IVAN Wiki',
				'http://ivan.fr.yuku.com/' => 'Ancient forums (rehosted)',
				'http://www.attnam.com/wiki/Feedback_and_reviews' => 'Feedback and Reviews',
				'http://wikiwiki.jp/ivan/' => 'Japanese Wiki',
			),
			'Development' => array(
				'https://github.com/Attnam/ivan' => 'Open source fan continuation by members of this site',
			),
			'Obsolete' => array(
				'http://ivan.greatboard.com/index.php' => 'Ancient forums (original URL, dead)',
				'http://ivan.elwiki.com/index.php/Main_Page' => 'Old Wiki (dead)',
				'http://attnam.jconserv.net/' => 'Old forums (dead)',
				'http://cvs.sourceforge.net/viewcvs.py/ivan/' => 'CVS Viewer (broken)'
			)
		);

		return View::make('pages.links')
			->with('_PAGE', $_PAGE)
			->with('menu', PageController::fetchMenu('links'))
			->with('categories', $categories);
	}

	/**
	 * Menu for static pages
	 *
	 * @return array
	 */
	public static function fetchMenu($active)
	{
		$menu = array();

		$menu['about'] = array(
			'url' => '/about',
			'name' => 'About',
		);
		$menu['contact'] = array(
			'url' => '/contact',
			'name' => 'Contact',
		);
		$menu['links'] = array(
			'url' => '/links',
			'name' => 'Links',
		);
		$menu['privacy'] = array(
			'url' => '/privacy',
			'name' => 'Privacy',
		);
		$menu['terms'] = array(
			'url' => '/terms',
			'name' => 'Terms',
		);

		$menu[$active]['active'] = true;

		return $menu;
	}

}

