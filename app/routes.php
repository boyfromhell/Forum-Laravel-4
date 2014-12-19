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
Route::group(array('before' => 'admin', 'namespace' => 'Parangi'), function()
{
	Route::get('admin', 'AdminController@dashboard');
	Route::post('admin/reset-counters', 'AdminController@resetCounters');
	Route::any('admin/messages', 'AdminController@messages');
	Route::get('admin/messages/{id}', 'AdminController@viewMessage');

	Route::post('admin/handle-report', 'AdminController@handleReport');

	Route::any('admin/edit-announcement', 'AdminController@editAnnouncement');

	Route::resource('admin/forums', 'ForumController');
	Route::resource('admin/groups', 'GroupController');
	Route::resource('admin/projects', 'ProjectController');
});

// Moderators
Route::group(array('before' => 'moderator', 'namespace' => 'Parangi'), function()
{
	Route::any('move-topic/{id}', 'TopicController@move');
	Route::get('lock-topic/{id}', 'TopicController@lock');
	Route::get('unlock-topic/{id}', 'TopicController@unlock');
	Route::any('delete-topic/{id}', 'TopicController@delete');
});

// Logged-in users
Route::group(array('before' => 'auth', 'namespace' => 'Parangi'), function()
{
	Route::get('profile', 'UserController@myProfile');

	// Forum
	Route::any('reply-to-topic/{id}', 'PostController@reply');
	Route::any('quote-post/{id}', 'PostController@quote');
	Route::any('edit-post/{id}', 'PostController@edit');
	Route::any('quick-edit/{id}', 'PostController@quickEdit');
	Route::any('delete-post/{id}', 'PostController@delete');
	Route::any('flag-post/{id}', 'PostController@flag');

	Route::any('new-topic/{id}', 'PostController@newTopic');
	Route::get('topic-review/{id}', 'TopicController@review');

	Route::get('forum/smileys', 'PostController@smileys');

	// Search
	Route::any('search/{id?}', 'SearchController@index');
	Route::get('results/{id}', 'SearchController@results');

	// Attachments
	Route::get('forum/attachments/{id}', 'AttachmentController@download');

	// Messages
	Route::any('messages', 'MessageController@inbox');
	Route::any('messages/compose', 'MessageController@compose');
	Route::get('messages/{id}', 'MessageController@displayThread');
	Route::any('messages/{folder}', 'MessageController@inbox');
	Route::any('delete-message/{id}', 'MessageController@delete');

	// Settings
	Route::any('avatar', 'AvatarController@manage');
	Route::post('upload-avatar', 'AvatarController@upload');
	Route::any('edit-profile', 'UserController@editProfile');
	Route::any('settings', 'UserController@settings');
	Route::any('subscriptions', 'UserController@subscriptions');

	// Community
	/*Route::any('groups/edit/{id}', 'GroupController@edit');
	Route::any('groups/new', 'GroupController@add');*/
	Route::any('honor-rolls/submit', 'ScoreController@submit');

	// Shoutbox
	Route::get('shoutbox/history', 'ShoutboxController@history');
	Route::get('shoutbox/fetch', 'ShoutboxController@fetch');
	Route::post('shoutbox/post', 'ShoutboxController@post');

	// Gallery
	Route::any('create-album/{id?}', 'AlbumController@create');
	Route::any('edit-album/{id}', 'AlbumController@edit');
	Route::any('upload-photos/{id}', 'PhotoController@upload');
	Route::any('edit-photo/{id}', 'PhotoController@edit');
	Route::any('delete-photo/{id}', 'PhotoController@delete');
	Route::get('media/download/{id}', 'PhotoController@download');

	// Account
	Route::get('signout', 'UserController@signout');
});

/**
 * Guests only
 */
Route::group(array('before' => 'guest', 'namespace' => 'Parangi'), function()
{
	Route::any('signin', 'UserController@signin');
	Route::any('signup', 'UserController@signup');

	Route::get('lost-password', 'RemindersController@getRemind');
	Route::post('lost-password', 'RemindersController@postRemind');
	Route::get('reset-password/{token?}', 'RemindersController@getReset');
	Route::post('reset-password', 'RemindersController@postReset');
});

// All users
Route::group(array('namespace' => 'Parangi'), function()
{
	Route::get('/', 'ForumController@home');
	Route::get('forum', 'ForumController@listAll');
	Route::get('forums', function() { return Redirect::to('forum'); });
	Route::get('forums/{id}/{name?}', 'ForumController@display');
	Route::get('topics/{id}/{name?}', 'TopicController@display');
	Route::get('print/{id}/{name?}', 'TopicController@printTopic');
	Route::get('posts/{id}/{name?}', 'PostController@display');

	// Community
	Route::any('members', 'UserController@members');
	Route::get('groups', 'GroupController@showAll');
	Route::get('groups/{id}/{name?}', 'GroupController@display');
	Route::get('users/{id}/{name?}', 'UserController@display');
	Route::get('honor-rolls', 'ScoreController@index');
	Route::get('chat-popup', 'PageController@chatPopup');
	Route::get('badges', 'LevelController@display');

	// Gallery
	Route::get('media', 'AlbumController@gallery');
	Route::get('albums', 'AlbumController@display');
	Route::get('albums/{id}/{name?}', 'AlbumController@display');
	Route::any('albums/edit/{id}', 'AlbumController@edit');
	Route::any('albums/new', 'AlbumController@add');
	Route::get('photos/{id}/{name?}', 'PhotoController@display');

	// Projects
	Route::get('downloads/{category?}', 'ProjectController@category');
	Route::get('projects/{id}/{name?}', 'ProjectController@display');
	Route::get('download/{id}/{name?}', 'ProjectController@download');

	// Static pages
	Route::get('about', ['uses' => 'PageController@display', 'as' => 'about']);
	Route::any('contact', 'PageController@contact');
	Route::get('donate', ['uses' => 'PageController@display', 'as' => 'donate']);
	Route::get('privacy', ['uses' => 'PageController@display', 'as' => 'privacy']);
	Route::get('terms', ['uses' => 'PageController@display', 'as' => 'terms']);
	Route::get('community/chat', ['uses' => 'PageController@display', 'as' => 'chat']);
	Route::get('sitemap', 'PageController@sitemap');
	Route::get('links', 'PageController@links');

	// Stats
	Route::get('whos-online', 'ForumController@getOnline');
});

