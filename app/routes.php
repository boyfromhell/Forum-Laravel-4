<?php

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It's a breeze. Simply tell Laravel the URIs it should respond to
| and give it the Closure to execute when that URI is requested.
|
*/

Route::pattern('id', '[0-9]+');

// Admin only
Route::group(array('before' => 'admin'), function()
{
	Route::get('admin', array('uses' => 'AdminController@dashboard'));
	Route::post('admin/reset-counters', array('uses' => 'AdminController@resetCounters'));
	Route::get('admin/messages/{id}', array('uses' => 'AdminController@viewMessage'));

	Route::resource('admin/groups', 'GroupController');
	Route::resource('admin/projects', 'ProjectController');
});

// Moderators
Route::group(array('before' => 'moderator'), function()
{
	Route::get('move-topic/{id}', array('uses' => 'TopicController@move'));
	Route::get('lock-topic/{id}', array('uses' => 'TopicController@lock'));
	Route::get('unlock-topic/{id}', array('uses' => 'TopicController@unlock'));
	Route::any('delete-topic/{id}', array('uses' => 'TopicController@delete'));
});

// Logged-in users
Route::group(array('before' => 'auth'), function()
{
	Route::get('profile', array('uses' => 'UserController@myProfile'));

	// Forum
	Route::any('reply-to-topic/{id}', array('uses' => 'PostController@reply'));
	Route::any('quote-post/{id}', array('uses' => 'PostController@quote'));
	Route::any('edit-post/{id}', array('uses' => 'PostController@edit'));
	Route::any('quick-edit/{id}', array('uses' => 'PostController@quickEdit'));
	Route::any('delete-post/{id}', array('uses' => 'PostController@delete'));
	Route::any('new-topic/{id}', array('uses' => 'PostController@newTopic'));
	Route::get('topic-review/{id}', array('uses' => 'TopicController@review'));

	Route::get('forum/smileys', array('uses' => 'PostController@smileys'));

	// Search
	Route::any('search/{id?}', array('uses' => 'SearchController@index'));

	// Attachments
	Route::get('forum/attachments/{id}', array('uses' => 'AttachmentController@download'));

	// Messages
	Route::get('messages', array('uses' => 'MessageController@inbox'));
	Route::any('messages/compose', array('uses' => 'MessageController@compose'));
	Route::get('messages/{id}', array('uses' => 'MessageController@displayThread'));
	Route::get('messages/{folder}', array('uses' => 'MessageController@inbox'));
	Route::any('delete-message/{id}', array('uses' => 'MessageController@delete'));

	// Settings
	Route::any('users/avatar', array('uses' => 'AvatarController@manage'));
	Route::post('upload-avatar', array('uses' => 'AvatarController@upload'));
	Route::any('users/edit', array('uses' => 'UserController@editProfile'));
	Route::get('users/reset_password', array('uses' => 'UserController@resetPassword'));
	Route::any('users/settings', array('uses' => 'UserController@settings'));
	Route::get('users/topics', array('uses' => 'UserController@subscriptions'));

	// Community
	Route::any('members', array('uses' => 'UserController@members'));
	/*Route::any('groups/edit/{id}', array('uses' => 'GroupController@edit'));
	Route::any('groups/new', array('uses' => 'GroupController@add'));*/
	Route::any('honor-rolls/submit', array('uses' => 'ScoreController@submit'));

	// Shoutbox
	Route::get('shoutbox/history', array('uses' => 'ShoutboxController@history'));
	Route::get('shoutbox/fetch', array('uses' => 'ShoutboxController@fetch'));
	Route::post('shoutbox/post', array('uses' => 'ShoutboxController@post'));

	// Account
	Route::get('signout', array('uses' => 'UserController@signout'));
});

/**
 * Guests only
 */
Route::group(array('before' => 'guest'), function()
{
	Route::any('signin', array('uses' => 'UserController@signin'));
	Route::any('signup', array('uses' => 'UserController@signup'));

	Route::get('lost-password', array('uses' => 'RemindersController@getRemind'));
	Route::post('lost-password', array('uses' => 'RemindersController@postRemind'));
	Route::get('reset-password/{token?}', array('uses' => 'RemindersController@getReset'));
	Route::post('reset-password', array('uses' => 'RemindersController@postReset'));
});

// All users
Route::get('/', array('uses' => 'ForumController@home'));
Route::get('forum', array('uses' => 'ForumController@index'));
Route::get('forums', function() { return Redirect::to('forum'); });
Route::get('forums/{id}/{name?}', array('uses' => 'ForumController@display'));
Route::get('topics/{id}/{name?}', array('uses' => 'TopicController@display'));
Route::get('print/{id}/{name?}', array('uses' => 'TopicController@printTopic'));
Route::get('posts/{id}/{name?}', array('uses' => 'PostController@display'));

// Community
Route::get('groups', array('uses' => 'GroupController@showAll'));
Route::get('groups/{id}/{name?}', array('uses' => 'GroupController@display'));
Route::get('users/{id}/{name?}', array('uses' => 'UserController@display'));
Route::get('honor-rolls', array('uses' => 'ScoreController@index'));
Route::get('chat-popup', array('uses' => 'PageController@chatPopup'));

// Gallery
Route::get('media', array('uses' => 'AlbumController@gallery'));
Route::get('albums', array('uses' => 'AlbumController@display'));
Route::get('albums/{id}/{name?}', array('uses' => 'AlbumController@display'));
Route::any('albums/edit/{id}', array('uses' => 'AlbumController@edit'));
Route::any('albums/new', array('uses' => 'AlbumController@add'));
Route::get('media/photo/{id}', array('uses' => 'PhotoController@display'));

// Projects
Route::get('downloads/{category?}', array('uses' => 'ProjectController@category'));
Route::get('projects/{id}/{name?}', array('uses' => 'ProjectController@display'));
Route::get('download/{id}/{name?}', array('uses' => 'ProjectController@download'));

// Static pages
Route::get('about', array('uses' => 'PageController@display', 'as' => 'about'));
Route::any('contact', array('uses' => 'PageController@contact'));
Route::get('donate', array('uses' => 'PageController@display', 'as' => 'donate'));
Route::get('privacy', array('uses' => 'PageController@display', 'as' => 'privacy'));
Route::get('terms', array('uses' => 'PageController@display', 'as' => 'terms'));
Route::get('community/chat', array('uses' => 'PageController@display', 'as' => 'chat'));
Route::get('sitemap', array('uses' => 'PageController@sitemap'));

// Stats
Route::get('whos-online', array('uses' => 'ForumController@getOnline'));

