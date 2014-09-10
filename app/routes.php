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
	Route::get('admin/messages/{id}', array('uses' => 'AdminController@viewMessage'));
});

// Moderators
Route::group(array('before' => 'moderator'), function()
{
});

// Logged-in users
Route::group(array('before' => 'loggedin'), function()
{
	Route::get('profile', array('uses' => 'UserController@myProfile'));

	// Settings
	Route::get('users/avatar', array('uses' => 'AvatarController@manage'));
	Route::get('users/edit', array('uses' => 'UserController@editProfile'));
	Route::get('users/reset_password', array('uses' => 'UserController@resetPassword'));
	Route::get('users/settings', array('uses' => 'UserController@settings'));
	Route::get('users/topics', array('uses' => 'UserController@subscriptions'));

	// Community
	Route::any('community/members', array('uses' => 'UserController@members'));
	Route::any('groups/edit/{id}', array('uses' => 'GroupController@edit'));
	Route::any('groups/new', array('uses' => 'GroupController@add'));
	Route::any('community/submit_score', array('uses' => 'ScoreController@submit'));
	Route::get('community/shoutbox', array('uses' => 'ShoutboxController@embed'));
});

// All users
Route::get('/', array('uses' => 'ForumController@home'));
Route::get('forum', array('uses' => 'ForumController@index'));
Route::get('forums/{id}/{name?}', array('uses' => 'ForumController@display'));
Route::get('topics/{id}/{name?}', array('uses' => 'TopicController@display'));
Route::get('print/{id}/{name?}', array('uses' => 'TopicController@print'));
Route::get('posts/{id}/{name?}', array('uses' => 'PostController@display'));

// Community
Route::get('groups', array('uses' => 'GroupController@index'));
Route::get('groups/{id}/{name?}', array('uses' => 'GroupController@display'));
Route::get('community/honor_rolls', array('uses' => 'ScoreController@index'));
Route::get('users/{id}/{name?}', array('uses' => 'UserController@display'));
Route::get('honor-rolls', array('uses' => 'ScoreController@index'));
Route::get('chat-popup', array('uses' => 'PageController@chatPopup'));

// Projects
Route::get('downloads/{category?}', array('uses' => 'ProjectController@index'));
Route::get('projects/{id}/{name?}', array('uses' => 'ProjectController@display'));
Route::get('download/{id}/{name?}', array('uses' => 'ProjectController@download'));

// Static pages
Route::get('about', array('uses' => 'PageController@display', 'as' => 'about'));
Route::any('contact', array('uses' => 'PageController@contact'));
Route::get('donate', array('uses' => 'PageController@display', 'as' => 'donate'));
Route::get('privacy', array('uses' => 'PageController@display', 'as' => 'privacy'));
Route::get('terms', array('uses' => 'PageController@display', 'as' => 'terms'));
Route::get('community/chat', array('uses' => 'PageController@display', 'as' => 'chat'));
