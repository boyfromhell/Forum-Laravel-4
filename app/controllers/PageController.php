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
		$_PAGE = array(
			'category' => 'home',
			'section'  => 'welcome',
		);

		$page = Route::currentRouteName();

		switch( $page ) {
			case 'about':
				$_PAGE['section'] = 'about';
				$_PAGE['title']   = 'About';
				$template = 'pages.about';
				break;

			case 'chat':
				$_PAGE['category'] = 'community';
				$_PAGE['section']  = 'chat';
				$_PAGE['title']    = 'Chat';
				$template = 'pages.chat';
				break;

			case 'donate':
				$members_only = true;
				$_PAGE['section'] = 'donate';
				$_PAGE['title']   = 'Donate';
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

		if( ! $me->id && $members_only ) {
			App::abort(403);
		}

		return View::make($template)
			->with('_PAGE', $_PAGE)
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

		$_PAGE = array(
			'category' => 'home',
			'section'  => 'contact',
			'title'    => 'Contact',
		);

		if( Request::isMethod('post') )
		{
			/*$no_spam = strtolower(trim($_POST['no_spam']));

			try {
				User::validate('captcha', $no_spam);
			}*/

			$rules = [
				'name' => 'required',
				'email' => 'required|email',
				'subject' => 'required',
				'message' => 'required',
			];

			$validator = Validator::make(Input::all(), $rules);

			if( $validator->fails() )
			{
				return Redirect::to('contact')
					->withErrors($validator)
					->withInput();
			}
			else
			{
				AdminMessage::create([
					'user_id' => $me->id,
					'name'    => Input::get('name'),
					'email'   => Input::get('email'),
					'subject' => Input::get('subject'),
					'message' => Input::get('message'),
					'ip'      => Request::getClientIp(),
				]);

				// @todo
				/*Mail::queue('emails.contact', $data, function($message) use ($subject)
				{
					$message->to('capristo@attnam.com')
						->subject($subject);
				});*/

				Session::push('messages', 'Your message has been sent. Thank you');

				return Redirect::to('contact');
			}
		}

		return View::make('pages.contact')
			->with('_PAGE', $_PAGE);
	}

	/**
	 * Show the chat popup
	 *
	 * @return Response
	 */
	public function chatPopup()
	{
		global $me;

		if( $me->id ) {
			$nick = preg_replace('/[^A-Za-z0-9]/', '_', $me->name);
		} else {
			$nick = 'Guest????';
		}

		$smileys = BBCode::load_smileys();

		$_PAGE['title'] = '#attnam';

		return View::make('pages.chat_popup')
			->with('_PAGE', $_PAGE)
			->with('nick', $nick)
			->with('smileys', $smileys);
	}

}
