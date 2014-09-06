<?php

class ForumController extends Earlybird\FoundryController
{

	/**
	 * List all forums
	 *
	 * @return Response
	 */
	public function index()
	{
		global $me;

		// Mark all forums read
		if( Input::has('mark') && $me->id )
		{
			SessionTopic::where('user_id', '=', $me->id)->delete();

			Session::push('messages', 'All forums marked read');

			return Redirect::to('forum');
		}

		$_PAGE = array(
			'category' => 'forums',
			'section'  => 'forums',
			'title'    => 'Forum',
		);

		// Announcements
		$announcement = Announcement::where('id', '=', 2)->first();

		// Quote bot
		if( true || app_active('quotebot') )
		{
			if( Input::has('quote') ) {
				$quote = Quote::find(Input::get('quote'));
			}
			else {
				$quote = Quote::orderBy(DB::raw('RAND()'))->first();
			}
		}

		// Categories
		$categories = Category::orderBy('order', 'asc')->get();

		/*$read_ids = array();
		$forums = array();
		while( $data = $exec->fetch_assoc() )
		{
			$forum = new Forum($data['id'], $data);

			$forum->subforums = array();
			$forum->perm_view = $forum->check_permission('view', $me, $mygroups);
			$forum->perm_read = $forum->check_permission('read', $me, $mygroups);

			if( $forum->perm_read ) {
				$read_ids[] = $forum->id;
			}

			if( $forum->perm_view ) {
				if( $forum->external ) { $forum->alt = 'External'; }
				else { $forum->alt = 'No new posts'; }

				$forums[$forum->id] = $forum;
			}
		}

		if( count($read_ids) )
		{
			// Check if unread
			$sql = "SELECT `session_post`, `topic_id`, `forum_id`
				FROM `session_topics`
				WHERE `user_id` = {$me->id}
					AND `forum_id` IN ( " . implode(',', $read_ids) . " )
				ORDER BY `session_post` ASC";
			$exec = $_db->query($sql);

			while( $data = $exec->fetch_assoc() )
			{
				$forums[$data['forum_id']]->alt = 'New posts';
				$forums[$data['forum_id']]->unread = $data;
			}

			// Latest topics
			$sql = "SELECT `topics`.`id`, `topics`.`forum_id`, `topics`.`title`, `topics`.`smiley`
				FROM
				( SELECT MAX( topics.last_date ) AS date, forums.id AS id
					FROM topics, forums
					WHERE topics.forum_id = forums.id
						AND forums.id IN ( " . implode(',', $read_ids ) . " )
					GROUP BY forums.id ) t1
				JOIN topics
				ON topics.last_date = t1.date AND topics.forum_id = t1.id";
			$exec = $_db->query($sql);

			$topic_ids = array();
			$topics = array();
			while( $data = $exec->fetch_assoc() )
			{
				$topic = new Topic($data['id'], $data);

				$topic->short_title = $topic->title;
				if( strlen($topic->title) > 50 ) {
					$topic->short_title = substr($topic->title, 0, 45) . '...';
				}
				if( $topic->smiley ) {
					list($topic->smiley_img, $topic->smiley_alt) = topic_smiley($topic->smiley);
				}

				$topic->alt = 'Go to topic';

				$topic_ids[] = $topic->id;
				$topics[$topic->id] = $topic;
			}

			if( count($topic_ids) )
			{
				// Latest topic attachments
				$sql = "SELECT `posts`.`topic_id`, COUNT( `attachments`.`id` ) AS `total`
					FROM `attachments`
						LEFT JOIN `posts`
							ON `attachments`.`post_id` = `posts`.`id`
					WHERE `posts`.`topic_id` IN ( " . implode(',', $topic_ids) . " )
					GROUP BY `posts`.`topic_id`";
				$exec = $_db->query($sql);

				while( $data = $exec->fetch_assoc() )
				{
					$topics[$data['topic_id']]->attachments = $data['total'];
				}

				// Latest posts
				$sql = "SELECT `posts`.`id`, `posts`.`topic_id`, `posts`.`user_id`, `users`.`name`, `posts`.`time`
					FROM
					( SELECT MAX( posts.time ) AS date, topics.id AS id
						FROM posts, topics
						WHERE posts.topic_id = topics.id
						AND topics.id IN ( " . implode(',', $topic_ids ) . " )
						GROUP BY topics.id ) p1
					JOIN posts
					ON posts.time = p1.date AND posts.topic_id = p1.id
					JOIN users
					ON posts.user_id = users.id";
				$exec = $_db->query($sql);

				while( $data = $exec->fetch_assoc() )
				{
					$post = new Post($data['id'], $data);
					$post->user = new User($data['user_id'], array('name' => $data['name']));

					$post->time += ($me->tz*3600);
					$post->date = datestring($post->time, 2);

					$topics[$data['topic_id']]->latest_post = $post;
				}
			}

			foreach( $topics as $topic ) {
				$forums[$topic->forum_id]->latest_topic = $topic;
			}
		}

		foreach( $forums as $key => &$forum )
		{
			// If latest topic is also unread
			if( $forum->latest_topic->id && $forum->unread['topic_id'] == $forum->latest_topic->id ) {
			
				// Check for first unread post
				$sql = "SELECT `session_post`
					FROM `session_topics`
					WHERE `user_id` = {$me->id}
						AND `topic_id` = {$forum->latest_topic->id}";
				$exec = $_db->query($sql);
				list( $unread_post_id ) = $exec->fetch_row();
			
				$forum->latest_topic->url = '/posts/' . $unread_post_id . '#' . $unread_post_id;
				$forum->latest_topic->alt = 'Go to first unread post';
			}

			if( $forum->parent_id != 0 )
			{
				// Find actual latest topic from parent/children
				if( $forum->latest_topic->latest_post->time > $forums[$forum->parent_id]->latest_topic->latest_post->time ) {
					$forums[$forum->parent_id]->latest_topic = $forum->latest_topic;
				}

				// Move subforums into parent
				// @todo erm... for some reason `subforms[] = $forum` doesn't work?
				$forums[$forum->parent_id]->subforums = array_merge((array)$forums[$forum->parent_id]->subforums, array($forum));
				unset($forums[$key]);
			}
		}*/

		// Statistics
		/*$sql = "SELECT COUNT( id ) FROM topics";
		$exec = $_db->query($sql);
		list( $total_topics ) = $exec->fetch_row();

		$sql = "SELECT COUNT( id ) FROM posts";
		$exec = $_db->query($sql);
		list( $total_posts ) = $exec->fetch_row();

		$sql = "SELECT COUNT( id ) FROM users";
		$exec = $_db->query($sql);
		list( $total_users ) = $exec->fetch_row();

		// Newest user
		$sql = "SELECT `id`, `name`
			FROM `users`
			ORDER BY `id` DESC
			LIMIT 1";
		$exec = $_db->query($sql);
		$data = $exec->fetch_assoc();
		$newest_user = new User($data['id'], $data);

		sort_by($forums, 'order', 'asc', 'object');

		$stats = array(
			'total_topics' => number_format($total_topics),
			'total_posts'  => number_format($total_posts),
			'total_users'  => number_format($total_users),
		);*/

		return View::make('forums.index')
			->with('_PAGE', $_PAGE)
			->with('announcement', $announcement)
			->with('quote', $quote)
			->with('categories', $categories);

/*		$Smarty->assign('stats', $stats);
		$Smarty->assign('online_stats', $online_stats);
		$Smarty->assign('newest_user', $newest_user);*/
	}

	/**
	 * Display a forum
	 *
	 * @param  int  $id
	 * @param  string  $name  For SEO only
	 * @return Response
	 */
	public function display( $id, $name = NULL )
	{
		global $me;

		$forum = Forum::findOrFail($id);
			
		// Am I even allowed to know this forum exists?
		if( ! $forum->check_permission('view') ) {
			App::abort(404);
		}
		else if( ! $forum->check_permission('read') ) {
			App::abort(403);
		}

		if( $forum->external ) {
			return Redirect::to($forum->external);
		}

		$_PAGE = array(
			'category' => 'forums',
			'section'  => 'forums',
			'title'    => $forum->name,
		);

		// Mark all topics read
		if( Input::has('mark') && $me->id )
		{
			SessionTopic::where('user_id', '=', $me->id)
				->where('forum_id', '=', $forum->id)
				->delete();

			Session::push('messages', 'All topics marked read');

			return Redirect::to($forum->url);
		}

		// Load topics
		$topics = $forum->topics()->paginate(25);

		$topics->load([
			'user',
			'lastUser'
		]);
		
		$children = array();
		foreach( $forum->children as $child ) {
			if( $child->check_permission('view') ) {
				$children[] = $child;
			}
		}

		// Check if unread
/*		if( count($read_ids) )
		{
			$sql = "SELECT `session_post`, `topic_id`, `forum_id`
				FROM `session_topics`
				WHERE `user_id` = {$me->id}
				AND `forum_id` IN ( " . implode(',', $read_ids) . " )";
			$exec = $_db->query($sql);

			while( $data = $exec->fetch_assoc() )
			{
				$children[$data['forum_id']]->alt = 'New posts';
				$children[$data['forum_id']]->unread = $data;
			}

			// Latest topics
			$sql = "SELECT `topics`.`id`, `topics`.`forum_id`, `topics`.`title`, `topics`.`smiley`
				FROM
				( SELECT MAX( topics.last_date ) AS date, forums.id AS id
					FROM topics, forums
					WHERE topics.forum_id = forums.id
					AND forums.id IN ( " . implode(',', $read_ids ) . " )
					GROUP BY forums.id ) t1
				JOIN topics
				ON topics.last_date = t1.date AND topics.forum_id = t1.id";
			$exec = $_db->query($sql);

			$topic_ids = array();
			while( $data = $exec->fetch_assoc() )
			{
				if( $topic->smiley ) {
					list($topic->smiley_img, $topic->smiley_alt) = topic_smiley($topic->smiley);
				}

				$topic->alt = 'Go to topic';
			}
			
			if( count($topic_ids) )
			{
				// Latest topic attachments
				$sql = "SELECT `posts`.`topic_id`, COUNT( `attachments`.`id` ) AS `total`
					FROM `attachments`
						LEFT JOIN `posts`
							ON `attachments`.`post_id` = `posts`.`id`
					WHERE `posts`.`topic_id` IN ( " . implode(',', $topic_ids) . " )
					GROUP BY `posts`.`topic_id`";
				$exec = $_db->query($sql);

				while( $data = $exec->fetch_assoc() ) {
					$topics[$data['topic_id']]->attachments = $data['total'];
				}

				// Latest posts
				$sql = "SELECT `posts`.`id`, `posts`.`topic_id`, `posts`.`user_id`, `users`.`name`, `posts`.`time`
					FROM
					( SELECT MAX( posts.time ) AS date, topics.id AS id
						FROM posts, topics
						WHERE posts.topic_id = topics.id
						AND topics.id IN ( " . implode(',', $topic_ids ) . " )
						GROUP BY topics.id ) p1
					JOIN posts
					ON posts.time = p1.date AND posts.topic_id = p1.id
					JOIN users
					ON posts.user_id = users.id";
				$exec = $_db->query($sql);

				while( $data = $exec->fetch_assoc() )
				{
					$post = new Post($data['id'], $data);
					$post->user = new User($data['user_id'], array('name' => $data['name']));

					$post->time += ($me->tz*3600);
					$post->date = datestring($post->time, 2);

					$topics[$data['topic_id']]->latest_post = $post;
				}
			}

			foreach( $topics as $topic ) {
				$children[$topic->forum_id]->latest_topic = $topic;
			}
		}

		foreach( $children as $key => &$child )
		{
			// If latest topic is also unread
			if( $child->unread['topic_id'] == $child->latest_topic->id ) {
				$child->latest_topic->url = $child->latest_topic->latest_post->url;
				$child->latest_topic->alt = 'Go to first unread post';
			}
		}

			
			if( count($topic_ids) )
			{
				// Check if unread
				$sql = "SELECT `session_post`, `topic_id`
					FROM `session_topics`
					WHERE `user_id` = {$me->id}
					AND `topic_id` IN ( " . implode(',', $topic_ids) . " )";
				$exec = $_db->query($sql);

				while( $data = $exec->fetch_assoc() )
				{
					if( $topics[$data['topic_id']]->img == 'topic' ) {
						$topics[$data['topic_id']]->img_alt = 'New posts';
					}
					$topics[$data['topic_id']]->img .= '_unread';

					$data['url'] = '/posts/' . $data['session_post'] . '#' . $data['session_post'];
					$data['alt'] = 'Go to first unread post';

					$topics[$data['topic_id']]->unread = $data;
				}
			
				// Topic attachments
				$sql = "SELECT `posts`.`topic_id`, COUNT( `attachments`.`id` ) AS `total`
					FROM `attachments`
						LEFT JOIN `posts`
							ON `attachments`.`post_id` = `posts`.`id`
					WHERE `posts`.`topic_id` IN ( " . implode(',', $topic_ids) . " )
					GROUP BY `posts`.`topic_id`";
				$exec = $_db->query($sql);

				while( $data = $exec->fetch_assoc() ) {
					$topics[$data['topic_id']]->attachments = $data['total'];
				}

				// Polls
				$sql = "SELECT `poll_id`, `poll_topic`
					FROM `polls`
					WHERE `poll_topic` IN ( " . implode(',', $topic_ids) . " )";
				$exec = $_db->query($sql);

				while( $data = $exec->fetch_assoc() ) {
					$topics[$data['poll_topic']]->poll = $data['poll_id'];
				}

				// Latest posts
				$sql = "SELECT `posts`.`id`, `posts`.`topic_id`, `posts`.`user_id`, `users`.`name`, `posts`.`time`
					FROM
					( SELECT MAX( posts.time ) AS date, topics.id AS id
						FROM posts, topics
						WHERE posts.topic_id = topics.id
						AND topics.id IN ( " . implode(',', $topic_ids ) . " )
						GROUP BY topics.id ) p1
					JOIN posts
						ON posts.time = p1.date AND posts.topic_id = p1.id
					JOIN users
						ON posts.user_id = users.id";
				$exec = $_db->query($sql);

				while( $data = $exec->fetch_assoc() )
				{
					$data['author'] = new User($data['user_id'], array('name' => $data['name']));

					$data['time'] += ($me->tz*3600);
					$data['date'] = datestring($data['time'], 2);
					$data['url'] = '/posts/' . $data['id'] . '#' . $data['id'];

					$topics[$data['topic_id']]->latest_post = $data;
				}
			}

		}*/

		return View::make('forums.display')
			->with('_PAGE', $_PAGE)
			->with('forum', $forum)
			->with('children', $children)
			->with('topics', $topics);
	}

	/**
	 * Load a hierarchy of parent forums
	 */
	public function load_parents()
	{	
		$parents = array();
		$child = $this;

		while( $child->parent_id )
		{
			$parent = new Forum($child->parent_id);
			$parents[] = $parent;
			$child = $parent;
		}
		return array_reverse($parents);
	}
}
