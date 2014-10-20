<?php

View::creator(array('layout'), function($view)
{
	global $me;

	// Menu
	$main_menu = ModuleCategory::where('permission', '<=', $me->access)
		->orderBy('order', 'asc')
		->get();

	$messages = Session::get('messages');
	$notices = Session::get('notices');
	$errors = Session::get('errors');
	$ga_events = Session::get('ga_events');
	Session::forget('messages');
	Session::forget('notices');
	Session::forget('errors');
	Session::forget('ga_events');

	$_PAGE = $view->_PAGE;

	if( $me->id ) {
		$total_unread = $me->unreadMessages()->count();
	}

	// Sub menu
	foreach( $main_menu as $menu_item ) {
		if( $menu_item->page == $_PAGE['category'] ) {
			$sub_nav = $menu_item->id;
		}

		// Show unread badge for message tab
		if( $menu_item->page == 'messages' && $total_unread > 0 ) {
			$menu_item->name .= ' <span class="badge">'.$total_unread.'</span>';
			//$menu_item->class = 'btn-danger';
		}
	}

	if( $view->menu ) {
		$sub_menu = $view->menu;
	}
	else if( $sub_nav ) {
		$sub_menu = Module::where('permission', '<=', $me->access);

		if( $me->id ) { $sub_menu = $sub_menu->where('permission', '>=', 0); }

		$sub_menu = $sub_menu->where('enabled', '=', 1)
			->where('category_id', '=', $sub_nav)
			->orderBy('order', 'asc')
			->get();

		$sub_menu = $sub_menu->toArray();

		foreach( $sub_menu as $key => $item ) {
			if( $item['section'] == $_PAGE['section'] ) {
				$sub_menu[$key]['active'] = true;
			}
		}
	}

	if( ! $_PAGE['title'] ) {
		$_PAGE['window_title'] = Config::get('app.forum_name');
	}
	else {
		$_PAGE['window_title'] = $_PAGE['title'].' - '.Config::get('app.forum_name');
	}

	if( ! $_PAGE['og_title'] ) {
		$_PAGE['og_title'] = $_PAGE['title'];
	}
	if( ! $_PAGE['description'] ) {
		$_PAGE['description'] = 'Laravel powered forum and community hub';
	}

	// If no canonical URL is specified, just use the request URI
	if( isset($_PAGE['url']) ) {
		$_PAGE['url'] = Config::get('app.url') . $_PAGE['url'];
	}
	else {
		$_PAGE['url'] = Config::get('app.url') . '/' . Request::path();
	}

	if( !isset($_PAGE['og_image']) || empty($_PAGE['og_image']) ) {
		$_PAGE['og_image'] = array(Config::get('app.url') . '/images/custom/facebook.png');
	}
	else if( !is_array($_PAGE['og_image']) ) {
		$_PAGE['og_image'] = array($_PAGE['og_image']);
	}

	//$resources = DB::table('resources')->get();

	$view->with('_PAGE', $_PAGE)
		->with('main_menu', $main_menu)
		->with('sub_menu', $sub_menu)
		->with('messages', $messages)
		->with('notices', $notices)
		->with('errors', $errors)
		->with('ga_events', $ga_events)
		->with('resources', $resources);
});

/*
|--------------------------------------------------------------------------
| Application & Route Filters
|--------------------------------------------------------------------------
|
| Below you will find the "before" and "after" events for the application
| which may be used to do any work before or after a request into your
| application. Here you may also register your custom route filters.
|
*/

App::before(function($request)
{
	//
});


App::after(function($request, $response)
{
	//
});

/*
|--------------------------------------------------------------------------
| Authentication Filters
|--------------------------------------------------------------------------
|
| The following filters are used to verify that the user of the current
| session is logged into this application. The "basic" filter easily
| integrates HTTP Basic authentication for quick, simple checking.
|
*/

Route::filter('admin', function()
{
	$user = Auth::user();

	if( ! $user->is_admin )
	{
		if (Request::ajax())
		{
			return Response::make('Unauthorized', 401);
		}
		else
		{
			App::abort(403);
		}
	}
});

Route::filter('moderator', function()
{
	$user = Auth::user();

	if( ! $user->is_moderator )
	{
		if (Request::ajax())
		{
			return Response::make('Unauthorized', 401);
		}
		else
		{
			App::abort(403);
		}
	}
});

Route::filter('auth', function()
{
	if (Auth::guest())
	{
		if (Request::ajax())
		{
			return Response::make('Unauthorized', 401);
		}
		else
		{
			Session::push('notices', 'You must sign in to view this page');

			return Redirect::guest('signin');
		}
	}
});


Route::filter('auth.basic', function()
{
	return Auth::basic();
});

/*
|--------------------------------------------------------------------------
| Guest Filter
|--------------------------------------------------------------------------
|
| The "guest" filter is the counterpart of the authentication filters as
| it simply checks that the current user is not logged in. A redirect
| response will be issued if they are, which you may freely change.
|
*/

Route::filter('guest', function()
{
	if (Auth::check() || Auth::viaRemember()) return Redirect::to('/');
});

/*
|--------------------------------------------------------------------------
| CSRF Protection Filter
|--------------------------------------------------------------------------
|
| The CSRF filter is responsible for protecting your application against
| cross-site request forgery attacks. If this special token in a user
| session does not match the one given in this request, we'll bail.
|
*/

Route::filter('csrf', function()
{
	if (Session::token() != Input::get('_token'))
	{
		throw new Illuminate\Session\TokenMismatchException;
	}
});
