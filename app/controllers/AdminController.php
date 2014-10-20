<?php

class AdminController extends BaseController
{

	/**
	 * Admin dashboard
	 *
	 * @return Response
	 */
	public function dashboard()
	{
		$_PAGE = array(
			'category' => 'admin',
			'section'  => 'general',
			'title'    => 'Admin'
		);

		$stats = array(
			'Users'  => User::count(),
			'Topics' => Topic::count(),
			'Posts'  => Post::count(),
			'Albums' => Album::count(),
			'Photos' => Photo::count(),
		);

		foreach( $stats as $key => $val ) {
			$stats[$key] = number_format($val);
		}

		$post_max_size = intval(ini_get('post_max_size'));
		$upload_max_filesize = intval(ini_get('upload_max_filesize'));
		$max_file_uploads = ini_get('max_file_uploads');

		return View::make('admin.dashboard')
			->with('_PAGE', $_PAGE)

			->with('stats', $stats)

			->with('post_max_size', $post_max_size)
			->with('upload_max_filesize', $upload_max_filesize)
			->with('max_file_uploads', $max_file_uploads);
	}

	/**
	 * Reset forum, topic, user counters
	 *
	 * @return Response
	 */
	public function resetCounters()
	{
		$totals = array(
			'forums' => 0,
			'topics' => 0,
			'users' => 0,
		);
		$forum_totals = array();

		$forums = Forum::all();
		$topics = Topic::all();
		$users = User::all();

		$html = 'Counters reset. ';

		// Count posts in all topics and forums
		foreach( $topics as $topic ) {
			$posts = $topic->posts()->count();
			$topic->replies = ( $posts-1 );
			$forum_totals[$topic->forum_id] += $posts;

			if( $topic->isDirty('replies') ) {
				$topic->save();
				$totals['topics']++;
			}
		}
		// Count topics in all forums
		foreach( $forums as $forum ) {
			$forum->total_topics = $forum->topics()->count();
			$forum->total_posts = $forum_totals[$forum->id];

			if( $forum->isDirty('total_topics', 'total_posts') ) {
				$forum->save();
				$totals['forums']++;
			}
		}
		// Count posts by all users
		foreach( $users as $user ) {
			$user->total_posts = $user->posts()->count();
			if( $user->isDirty('total_posts') ) {
				$user->save();
				$totals['users']++;
			}
		}

		// Format everything nicely
		$html_totals = array();
		foreach( $totals as $key => $total ) {
			if( $total > 0 ) {
				$html_totals[] = '<b>'.$total.'</b> '.$key;
			}
		}

		if( count($html_totals) > 0 ) {
			$html .= implode(', ', $html_totals);
			$html .= ' fixed';
		}
		else {
			$html .= 'Nothing needed fixing';
		}

		Session::push('messages', $html);

		return Redirect::to('admin');
	}

	/**
	 * View message
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function viewMessage( $id )
	{
		$message = AdminMessage::findOrFail($id);

		return View::make('admin.view_message')
			->with('message', $message);
	}

}
