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
			$latest_post = $topic->posts()
				->orderBy('id', 'desc')
				->first();

			$posts = $topic->posts()->count();
			$topic->replies = ( $posts-1 );
			$forum_totals[$topic->forum_id] += $posts;

			if( $topic->posted_at != $latest_post->created_at ) {
				$topic->posted_at = $latest_post->created_at;
			}

			if( $topic->isDirty(['replies', 'posted_at']) ) {
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
	 * View a message
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function viewMessage( $id )
	{
		$message = AdminMessage::findOrFail($id);

		$_PAGE = array(
			'category' => 'admin',
			'section'  => 'messages',
			'title'    => $message->subject,
		);

		$message->read = 1;
		$message->save();

		return View::make('admin.messages.display')
			->with('_PAGE', $_PAGE)
			->with('message', $message);
	}

	/**
	 * View flagged posts and messages sent through the contact form
	 *
	 * @return Response
	 */
	public function messages()
	{
		$_PAGE = array(
			'category' => 'admin',
			'section'  => 'messages',
			'title'    => 'Admin Messages'
		);

		if( Request::isMethod('post') ) {
			$messages = Input::get('messages');

			if( count($messages) > 0 ) {
				$data = AdminMessage::whereIn('id', $messages);

				if( isset($_POST['archive']) ) {
					$data->update(['archived' => 1]);
				}
				else if( isset($_POST['unarchive']) ) {
					$data->update(['archived' => 0]);
				}
				else if( isset($_POST['delete']) ) {
					$data->delete();
				}
				else if( isset($_POST['read']) ) {
					$data->update(['read' => 1]);
				}
				else if( isset($_POST['unread']) ) {
					$data->update(['read' => 0]);
				}

				Session::push('messages', 'Action complete');
			}

			return Redirect::to('admin/messages');
		}

		// Reported posts
		$reports = PostReport::join('posts', 'post_reports.post_id', '=', 'posts.id')
			->where('status', '=', 'open')
			->orderBy('id', 'desc')
			->get(['post_reports.*']);

		if( count($reports) > 0 ) {
			$reports->load([
				'post',
				'post.topic',
				'post.user',
				'user',
			]);
		}

		// Messages sent through the contact form
		$admin_messages = AdminMessage::where('archived', '=', 0)
			->orderBy('created_at', 'desc')
			->paginate(20);

		if( count($admin_messages) > 0 ) {
			$admin_messages->load(['user']);
		}

		return View::make('admin.messages.index')
			->with('_PAGE', $_PAGE)
			->with('reports', $reports)
			->with('admin_messages', $admin_messages);
	}

	/**
	 * Edit an announcement
	 */
	public function editAnnouncement()
	{
		$id = Input::get('id');
		$announcement = Announcement::findOrFail($id);

		if( Request::isMethod('post') ) {
			$announcement->text = Input::get('text');
			$announcement->save();

			$html = BBCode::parse($announcement->text);
		}
		else {
			$html = View::make('admin.announcements.edit')
				->with('announcement', $announcement)
				->render();
		}

		return Response::json([
			'success' => true,
			'html' => $html
		]);
	}

}
