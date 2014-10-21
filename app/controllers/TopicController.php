<?php

class TopicController extends Earlybird\FoundryController
{

	/**
	 * Display a topic
	 *
	 * @param  int  $id
	 * @param  string  $name  For SEO only
	 * @return Response
	 */
	public function display( $id, $name = NULL, $print = false )
	{
		global $me;

		$topic = Topic::findOrFail($id);
		$forum = $topic->forum;

		//-----------------------------------------------------------------------------
		// Determine which forum, topic, post, and page we are on

		/*if( isset($_GET['p']) ) {
			$p = (int)$_GET['p'];
			
			try {
				$post = new Post($p);
				$topic = new Topic($post->topic_id);
				$forum = new Forum($topic->forum_id);

				$sql = "SELECT `id`
					FROM `posts`
					WHERE `id` < {$post->id}
						AND `topic_id` = {$topic->id}";
				$exec = $_db->query($sql);

				$page = ceil(($exec->num_rows+1) / 25);
			}
		}*/

		$topic->increment('views');

		$_PAGE = array(
			'category' => 'forums',
			'section'  => 'forums',
			'title'    => $topic->title
		);

		// Permissions
		if( ! $forum->check_permission('view') ) {
			App::abort(404);
		}
		else if( ! $forum->check_permission('read') ) {
			App::abort(403);
		}

		$subscribed = false;
		$check_sub = $me->notify;

		if( $me->id ) {
			// Mark topic read
			SessionTopic::where('user_id', '=', $me->id)
				->where('topic_id', '=', $topic->id)
				->delete();

			// Subscription
			if( $me->subscriptions->contains($topic->id) ) {
				$subscribed = $check_sub = true;

				// Remove a subscription
				if( isset($_GET['unsubscribe']) ) {
					$me->subscriptions()->detach($topic->id);

					Session::push('notices', 'You have unsubscribed from this topic');

					return Redirect::to($topic->url);
				}
				// Mark as notified so I'll get email alerts again
				else {
					$me->subscriptions()->updateExistingPivot($topic->id, ['notified' => 1]);
				}
			}
			else {
				// Add a subscription
				if( isset($_GET['subscribe']) ) {
					$me->subscriptions()->attach($topic->id, ['notified' => 1]);

					Session::push('notices', 'You have subscribed to this topic');

					return Redirect::to($topic->url);
				}
			}
		}

		// Fetch all posts
		$posts = $topic->posts()->paginate(25);
		$posts->load([
			'user',
			'user.avatar',
			'user.groups',
		]);

		/*
		while( $data = $exec->fetch_assoc() )
		{
			$post->count = $count;

			// Show subject line
			$showhr = 0;
			if( $post->smiley ) { $showhr = 1; }
			if( $post->subject && $post->subject != 'Re: ' . $topic->title ) {
				$showhr = 2;
			}
			if( count($posts) == 0 ) {
				$showhr = 2;
				$post->subject = $topic->title;
			}
			$post->showhr = $showhr;

			$post->formatted_date = datestring($post->time, 1);
			
			// Check if ignored
			$sql = "SELECT `entry_id`
				FROM `user_lists`
				WHERE `entry_user` = {$me->id}
					AND `entry_subject` = {$post->user_id}";
			$exec2 = $_db->query($sql);
			if( $exec2->num_rows ) {
				$post->ignored = true;
			}

			// Custom Fields
			$user->custom = $user->load_custom_fields($me->access, 'topic');
		}*/

		$template = $print ? 'topics.print' : 'topics.display';

		return View::make($template)
			->with('_PAGE', $_PAGE)
			->with('menu', ForumController::fetchMenu('forum'))
			->with('forum', $forum)
			->with('topic', $topic)
			->with('posts', $posts)

			// Quick reply settings
			->with('subscribed', $subscribed)
			->with('check_sub', $check_sub)

			->with('jump_categories', Category::orderBy('order', 'asc')->get());

		/*$Smarty->assign('total_posts', count($posts));

		/**
		if( $_POST["voted"] && $me->loggedin ) {
			$pollid = (int)$_POST['pollid'];
			$sql = "SELECT poll_max FROM polls WHERE poll_id = '" . $pollid . "'";
			$res = query($sql);
			list( $maxchoices ) = mysql_fetch_array($res);
			$voteopt[] = stripslashes($_POST["voteopt"]);
			array_pop($voteopt);
			$choices = implode(',',$voteopt);
			if( count($voteopt) > $maxchoices ) {
				require_once(ROOT . 'header.php');
				msg("You may only select $maxchoices choices",1);
			}
			if( count($voteopt) < 1 ) {
				require_once(ROOT . 'header.php');
				msg("You must select an option",1);
			}
			$sql = "SELECT vote_id FROM poll_votes WHERE vote_user = '" . $me->id . "' AND vote_poll = '" . $pollid . "'";
			$res = query($sql);
			if( mysql_num_rows($res)) {
				require_once(ROOT . 'header.php');
				msg("You have already voted in this poll",1);
			}	
			$sql = "INSERT INTO poll_votes
			( `vote_poll`, `vote_user`, `vote_choices` )
			VALUES
			( '" . $pollid . "', '" . $me->id . "', '" . $_db->escape($choices) . "' )";
			$res = query($sql);
			for( $i=0; $i<count($voteopt); $i++ ) {
				$sql = "SELECT option_votes FROM poll_options WHERE option_id = '".$voteopt[$i]."'";
				$res = query($sql);
				list( $optvotes ) = mysql_fetch_array($res);
				$optvotes++;
				$sql = "UPDATE poll_options SET option_votes = '$optvotes' WHERE option_id = '".$voteopt[$i]."'";
				$res = query($sql);
			}
			header("Location: ".$_SERVER['REQUEST_URI']);
		}
		*/
	}

	/**
	 * Review a topic
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function review( $id )
	{
		$topic = Topic::findOrFail($id);

		$posts = Post::where('topic_id', '=', $topic->id)
			->orderBy('created_at', 'desc')
			->paginate(20);

		$_PAGE = array(
			'title' => 'Topic Review'
		);

		return View::make('topics.review')
			->with('_PAGE', $_PAGE)
			->with('topic', $topic)
			->with('posts', $posts);
	}

	/**
	 * Print topic
	 *
	 * @param  int  $id
	 * @param  string  $name  For SEO only
	 * @return Response
	 */
	public function printTopic( $id, $name = NULL )
	{
		return $this->display($id, $name, true);
	}

	/**
	 * Lock a topic
	 */
	public function lock( $id )
	{
		$topic = Topic::findOrFail($id);
		$topic->status = 1;
		$topic->save();

		Session::push('notices', 'Topic locked');

		return Redirect::back();
	}

	/**
	 * Unlock a topic
	 */
	public function unlock( $id )
	{
		$topic = Topic::findOrFail($id);
		$topic->status = 0;
		$topic->save();

		Session::push('notices', 'Topic unlocked');

		return Redirect::back();
	}

	/**
	 * Confirm deletion of a topic
	 *
	 * @param  int  $id  Topic ID
	 * @return Response
	 */
	public function delete( $id )
	{
		global $me;

		$_PAGE = array(
			'category' => 'home',
			'title'    => 'Delete Topic'
		);

		$topic = Topic::findOrFail($id);

		if( Request::isMethod('post') )
		{
			if( isset($_POST['cancel']) ) {
				return Redirect::to($topic->url);
			}
			elseif( isset($_POST['confirm']) ) {
				$forum = $topic->forum;
				$topic->delete();

				Session::push('messages', 'The topic has been successfully deleted');

				return Redirect::to($forum->url);
			}
		}

		return View::make('topics.delete')
			->with('_PAGE', $_PAGE)
			->with('menu', ForumController::fetchMenu('forum'))
			->with('topic', $topic);
	}

}

