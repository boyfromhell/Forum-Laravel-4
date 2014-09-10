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

}
