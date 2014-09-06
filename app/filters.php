<?php

View::creator(array('layout'), function($view)
{
	// @todo
	$access = 2;

	// Menu
	$menu = ModuleCategory::where('permission', '<=', $access)
		->orderBy('order', 'asc')
		->get();

	$messages = Session::get('messages');
	$errors = Session::get('errors');
	$ga_events = Session::get('ga_events');
	Session::forget('messages');
	Session::forget('errors');
	Session::forget('ga_events');

	$_PAGE = $view->_PAGE;

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
		$_PAGE['description'] = 'Roundown: buy stuff and save!';
	}

	// If no canonical URL is specified, just use the request URI
	if( isset($_PAGE['url']) ) {
		$_PAGE['url'] = 'http://' . Config::get('app.domain') . $_PAGE['url'];
	}
	else {
		$_PAGE['url'] = 'http://' . Config::get('app.domain') . '/' . Request::path();
	}

	if( !isset($_PAGE['og_image']) || empty($_PAGE['og_image']) ) {
		$_PAGE['og_image'] = array('http://' . Config::get('app.domain') . '/images/facebook.png');
	}
	else if( !is_array($_PAGE['og_image']) ) {
		$_PAGE['og_image'] = array($_PAGE['og_image']);
	}

	//$resources = DB::table('resources')->get();

	$view->with('_PAGE', $_PAGE)
		->with('menu', $menu)
		->with('messages', $messages)
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
			return Redirect::guest('login');
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
	if (Auth::check()) return Redirect::to('/');
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
