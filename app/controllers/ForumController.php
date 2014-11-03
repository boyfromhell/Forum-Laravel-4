<?php

class ForumController extends Earlybird\FoundryController
{

	/**
	 * Welcome page with recent activity
	 *
	 * @return Response
	 */
	public function home()
	{
		global $me;

		// Mark all forums read
		if( isset($_GET['mark']) && $me->id )
		{
			SessionTopic::where('user_id', '=', $me->id)->delete();

			Session::push('messages', 'All forums marked read');

			return Redirect::to('/');
		}

		$_PAGE = array(
			'category' => 'home',
			'title'    => 'Welcome',
		);

		// Announcements
		$announcement = Announcement::where('id', '=', 2)->first();

		//$birthdays = User::check_birthdays();

		// Newest user
		$newest_user = User::orderBy('id', 'desc')->first();

		$stats = array(
			'total_topics' => number_format(Topic::count()),
			'total_posts'  => number_format(Post::count()),
			'total_users'  => number_format(User::count()),
		);

		// Most recent topics
		$topics = Topic::join('forums', 'topics.forum_id', '=', 'forums.id')
			->where('forums.read', '<=', $me->access)
			->orderBy('posted_at', 'desc')
			->take(10)
			->get(['topics.*']);

		// Random photo and recent album
		$photo = Photo::join('albums', 'photos.album_id', '=', 'albums.id')
			->where('permission_view', '<=', $me->access)
			->orderBy(DB::raw('RAND()'), 'asc')
			->first(['photos.*']);

		$album = Album::where('permission_view', '<=', $me->access)
			->orderBy('updated_at', 'desc')
			->first();

		// Categories
		$categories = Category::orderBy('order', 'asc')->get();

		$categories->load([
			'forums',
		]);

		// Fetch most recent shouts
		$shouts = Shout::orderBy('id', 'desc')
			->take(30)
			->get();
		$shouts = ShoutboxController::format($shouts);

		return View::make('forums.welcome')
			->with('_PAGE', $_PAGE)
			->with('menu', ForumController::fetchMenu('welcome'))
			->with('announcement', $announcement)
			->with('birthdays', $birthdays)
			->with('topics', $topics)

			->with('photo', $photo)
			->with('album', $album)

			->with('categories', $categories)
			->with('stats', $stats)
			->with('newest_user', $newest_user)
			->with('online_stats', $this->getOnline(false))

			->with('shouts', $shouts);
	}

	/**
	 * List all forums
	 *
	 * @return Response
	 */
	public function listAll()
	{
		global $me;

		// Mark all forums read
		if( isset($_GET['mark']) && $me->id )
		{
			SessionTopic::where('user_id', '=', $me->id)->delete();

			Session::push('messages', 'All forums marked read');

			return Redirect::to('forum');
		}

		$_PAGE = array(
			'category' => 'home',
			'title'    => 'Forum',
		);

		// Announcements
		$announcement = Announcement::where('id', '=', 2)->first();

		// Quote bot
		if( Module::isActive('quotebot') )
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

		$categories->load([
			'forums',
		]);

		/*while( $data = $exec->fetch_assoc() )
		{
			$forum->perm_view = $forum->check_permission('view', $me, $mygroups);
			$forum->perm_read = $forum->check_permission('read', $me, $mygroups);

			if( $forum->external ) { $forum->alt = 'External'; }
			else { $forum->alt = 'No new posts'; }
		}

		if( count($read_ids) )
		{
			while( $data = $exec->fetch_assoc() )
			{
				if( $topic->smiley ) {
					list($topic->smiley_img, $topic->smiley_alt) = topic_smiley($topic->smiley);
				}

				$topic->alt = 'Go to topic';
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

		// Newest user
		$newest_user = User::orderBy('id', 'desc')->first();

		$stats = array(
			'total_topics' => number_format(Topic::count()),
			'total_posts'  => number_format(Post::count()),
			'total_users'  => number_format(User::count()),
		);

		return View::make('forums.all')
			->with('_PAGE', $_PAGE)
			->with('menu', ForumController::fetchMenu('forum'))
			->with('announcement', $announcement)
			->with('quote', $quote)
			->with('categories', $categories)

			->with('stats', $stats)
			->with('newest_user', $newest_user)
			->with('online_stats', $this->getOnline(false));
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
			'category' => 'home',
			'title'    => $forum->name,
		);

		// Mark all topics read
		if( isset($_GET['mark']) && $me->id )
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
			while( $data = $exec->fetch_assoc() )
			{
				if( $topic->smiley ) {
					list($topic->smiley_img, $topic->smiley_alt) = topic_smiley($topic->smiley);
				}
			}
		}
		}*/

		return View::make('forums.display')
			->with('_PAGE', $_PAGE)
			->with('menu', ForumController::fetchMenu('forum'))
			->with('forum', $forum)
			->with('children', $children)
			->with('topics', $topics)
			->with('jump_categories', Category::orderBy('order', 'asc')->get());
	}

	/**
	 * Check who's online
	 *
	 * @return Response
	 */
	public function getOnline( $ajax = true )
	{
		global $me;

		$online_record = BoardConfig::where('config_name', '=', 'online_record')->first();
		$online_when = BoardConfig::where('config_name', '=', 'online_when')->first();

		// Track that I'm online
		if( $me->id ) {
			$me->update(['viewed_at' => DB::raw('NOW()')]);
		}

		$fivemin = time() - 300;

		// Fetch visitors who are online
		$visitors = Visitor::where('last_view', '>', $fivemin)->count();

		// Fetch members who are online and not hidden
		$members = User::where('viewed_at', '>', DB::raw('DATE_SUB(NOW(), INTERVAL 5 MINUTE)'));

		if( ! $me->is_admin ) {
			$members = $members->where('hide_online', '=', 0);
		}

		$members = $members->orderBy('name')
			->get();

		$total = count($members);

		// New record is reached
		if( $visitors + $total > $online_record->config_value ) {
			$online_record->config_value = ( $visitors + $total );
			$online_record->save();

			$online_when->config_value = time();
			$online_when->save();
		}

		$html = View::make('forums.online')
			->with('visitors', $visitors)
			->with('members', $members)
			->with('total', $total)
			->with('record', $online_record->config_value)
			->with('record_date', Helpers::local_date('M j, Y, g:i a', $online_when->config_value))
			->render();

		if( ! $ajax ) {
			return $html;
		}

		//if( $user->level == 2 ) { $user->class = 'admin'; }
		//elseif( $user->level == 1 ) { $user->class = 'mod'; }

		return Response::json(['html' => $html]);
	}

	/**
	 * Menu for forum pages
	 *
	 * @return array
	 */
	public static function fetchMenu( $active )
	{
		global $me;

		$menu = array();

		$menu['welcome'] = array(
			'url' => '/',
			'name' => 'Welcome',
		);
		$menu['forum'] = array(
			'url' => '/forum',
			'name' => 'Forum',
		);
		if( $me->id ) {
			$menu['profile'] = array(
				'url' => '/profile',
				'name' => 'Profile',
			);
		}
		$menu['search'] = array(
			'url' => '/search',
			'name' => 'Search',
		);
		if( $me->id ) {
			$menu['signout'] = array(
				'url' => '/signout',
				'name' => 'Sign out',
			);
		}
		else {
			$menu['register'] = array(
				'url' => '/signup',
				'name' => 'Register',
			);
			$menu['signin'] = array(
				'url' => '/signin',
				'name' => 'Sign in',
			);
		}

		$menu[$active]['active'] = true;

		return $menu;
	}

}

