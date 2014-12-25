<?php namespace Parangi;

use App;
use Input;
use Redirect;
use Request;
use View;
use User;

class SearchController extends BaseController
{

	/**
	 * New or edit search
	 *
	 * @return Response
	 */
	public function index($id = null)
	{
		global $me;

		if ($id) {
			$query = Query::find($id);
		}
		if (! $query->id || $query->type != 'forum') {
			$query = new Query();

			// Defaults
			$query->user_id = $me->id;
			$query->type = 'forum';
			$query->match = MATCH_ALL;
			$query->where = WHERE_BOTH;
			$query->show  = SHOW_POSTS;
		}

		$forums = array();
		$create_query = false;

		if (Request::isMethod('post')) {
			$query->match = Input::get('match');
			$query->where = Input::get('where');
			$query->show  = Input::get('show');

			$query->author = Input::get('author');
			$query->keywords = Input::get('keywords');

			$forums = Input::get('forums', array());
			$query->starter = Input::get('starter');
			$query->since   = Input::get('since');

			$create_query = true;
		} else if (Input::has('user')) {
			$user = User::findOrFail(Input::get('user'));

			$query->author = $user->name;
			$query->show   = (Input::get('mode') == 'topics' ? 1 : 0);
			$query->starter = $query->show;

			$create_query = true;
		}
		else if (Input::has('show')) {
			$query->show = (Input::get('show') == 'newtopics' ? 1 : 0);
			$query->since = SINCE_LAST_VISIT;

			$create_query = true;
		}

		// Construct query and redirect to results
		if ($create_query) {
			$query->forums = implode(',', $forums);
			$query->save();

			return Redirect::to($query->results_url);
		}

		$_PAGE = array(
			'category' => 'home',
			'title'    => 'Search',
		);

		$categories = Category::orderBy('order', 'asc')->get();

		return View::make('forums.search')
			->with('_PAGE', $_PAGE)
			->with('menu', ForumController::fetchMenu('search'))
			->with('query', $query)
			->with('categories', $categories);
	}

	/**
	 * Show search results
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function results($id)
	{
		$_PAGE = array(
			'category' => 'forums',
			'title'    => 'Search Results'
		);

		$query = Query::findOrFail($id);

		if ($query->type != 'forum') {
			App::abort(404);
		}
		$forums = explode(',', $query->forums);

		// @todo use attribute?
		$keywords = str_replace([' ', ';'], ',', $query->keywords);
		$keywords = explode(',', $keywords);
		$total = count($keywords);

		if ($query->author) {
			$author = User::where('name', '=', $query->author)->first();
		}

		// @todo?
		//$data = $query->paginate('max 200');

		foreach ($data as $row) {
			if ($query->show == SHOW_POSTS) {
				$post = Post::findOrFail($row->id);

				$post->content = BBCode::strip_quotes($post->content);
				if (strlen($post->content) > 250) {
					$post->content = substr($post->content, 0, 250) . '...';
				}

				$date = datestring($post->time, 1);

				$posts[] = $post;
			}
			else {
				$topic = Topic::findOrFail($row->topic_id);
				$topic->format();

				$topics[] = $topic;
			}
		}

		if (count($topic_ids)) {
			// Check if unread
			$sql = "SELECT `session_post`, `topic_id`
				FROM `session_topics`
				WHERE `user_id` = {$me->id}
				AND `topic_id` IN ( " . implode(',', $topic_ids) . " )";
			$exec = $_db->query($sql);

			while ($data = $exec->fetch_assoc()) {
				if ($topics[$data['topic_id']]->img == 'topic') {
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

			while ($data = $exec->fetch_assoc()) {
				$topics[$data['topic_id']]->attachments = $data['total'];
			}

			// Polls
			$sql = "SELECT `poll_id`, `poll_topic`
				FROM `polls`
				WHERE `poll_topic` IN ( " . implode(',', $topic_ids) . " )";
			$exec = $_db->query($sql);

			while ($data = $exec->fetch_assoc()) {
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

			while ($data = $exec->fetch_assoc()) {
				$data['author'] = new User($data['user_id'], array('name' => $data['name']));

				$data['time'] += ($me->tz*3600);
				$data['date'] = datestring($data['time'], 2);
				$data['url'] = '/posts/' . $data['id'] . '#' . $data['id'];

				$topics[$data['topic_id']]->latest_post = $data;
			}
		}

		return View::make('forums.results')
			->with('query', $query)
			->with('topics', $topics)
			->with('posts', $posts);

		/*$searched_for_html = $query->get_description($start, $num_results);

		$Smarty->assign('searched_for_html', $searched_for_html);*/
	}

	/**
	 * If it's a forum query
	 */
	protected function _fetch($query)
	{
		global $me;

		$total = count($query->words);

		if ($query->show == SHOW_TOPICS) {
			$data = Post::select('topic_id');
		} else {
			$data = Post::select('posts.*');
		}

		$data = $data->join('posts_text', 'posts.id', '=', 'posts_text.post_id')
			->join('users', 'posts.user_id', '=', 'users.id')
			->join('topics', 'posts.topic_id', '=', 'topics.id')
			->join('forums', 'topics.forum_id', '=', 'forums.id')
			->where('forums.read', '<=', $me->access);

		if ($query->since != 0) {
			if ($query->since != SINCE_LAST_VISIT) {
				$since_when = $gmt-$query->since;
			} else {
				$since_when = $me->visited_at;
			}

			$data = $data->where('posts.time', '>=', $since_when);
		}
		if ($query->user->id) {
			$data = $data->where('posts.user_id', '=', $query->user->id);

			if ($query->starter == 1) {
				$data = $data->where('topics.poster', '=', $query->user->id);
			}
		}
		if (! in_array(0, $query->forum_array) && count($query->forum_array) > 0) {
			$data = $data->whereIn('topics.forum_id', $query->forums);
		}

		if ($total > 0) {
			$sql .= " AND (";

			if ($query->where == WHERE_TITLES || $query->where == WHERE_BOTH) {
				if ($query->where == WHERE_BOTH) {
					$sql .= "(";
				}
				for ($i=0; $i<$total; $i++) {
					$sql .= "`topics`.`title` LIKE '%" . $_db->escape(trim($query->words[$i])) . "%'";
					if ($i != $total - 1) {
						if ($query->match == MATCH_ANY) {
							$sql .= " OR ";
						}
						else { $sql .= " AND "; }
					}
				}
				if ($query->where == WHERE_BOTH) {
					$sql .= ")";
				}
			}
			if ($query->where == WHERE_TEXT || $query->where == WHERE_BOTH) {
				if ($query->where == WHERE_BOTH) {
					$sql .= " OR (";
				}
				for( $i=0; $i<$total; $i++ ) {
					$sql .= "`posts_text`.`post_text` LIKE '%" . $_db->escape(trim($query->words[$i])) . "%'";
					if( $i != $total-1 ) {
						if( $query->match == MATCH_ANY ) { $sql .= " OR "; }
						else { $sql .= " AND "; }
					}
				}
				if( $query->where == WHERE_BOTH ) { $sql .= ")"; }
			}
			$sql .= ")";
		}

		if( $query->show == SHOW_TOPICS ) {
			$data = $data->groupBy('posts.topic_id')
				->orderBy('topics.posted_at', 'desc');
		}
		else {
			$data = $data->orderBy('posts.created_at', 'desc');
		}

		$results = $data->get();

		return $results;
	}

	/**
	 * If it's a message query
	 */
	protected function _get_messages_database_query()
	{
		global $_db, $gmt, $me;
	
		$total = count($query->words);
		
		if( $query->show == SHOW_THREADS ) {
			$sql = "SELECT `messages`.`thread_id`, MIN(`messages`.`read`) AS `read` ";
		}
		else {
			$sql = "SELECT `messages`.`id`, `messages`.`thread_id`, `users`.`name` AS `user_name`, `message_threads`.`title`, messages.date_sent, `messages`.`content` ";
		}

		$sql .= "FROM `messages`
			JOIN `message_threads` ON `messages`.`thread_id` = `message_threads`.`id`
			JOIN `users` ON `messages`.`from_user_id` = `users`.`id`
		WHERE `messages`.`owner_user_id` = {$me->id} ";

		if( $this->since != 0 ) { 
			if( $this->since != SINCE_LAST_VISIT ) { $sincewhen = $gmt-$this->since; }
			else { $sincewhen = $me->visited_at; }
			$sql .= " AND `messages`.`date_sent` >= {$sincewhen} ";
		}
		if( $this->user->id ) {
			// @todo where "to_users" has multiple userss
			$sql .= " AND ( `messages`.`from_user_id` = '" . (int)$this->user->id . "'
				OR `messages`.`to_users` = '" . (int)$this->user->id . "' )";
		}
		// @todo check folder
		
		if( $total > 0 ) {
			$sql .= " AND (";

			if( $this->where == WHERE_TITLES || $this->where == WHERE_BOTH ) {
				if( $this->where == WHERE_BOTH ) { $sql .= "("; }
				for( $i=0; $i<$total; $i++ ) {
					$sql .= "`message_threads`.`title` LIKE '%" . $_db->escape(trim($this->words[$i])) . "%'";
					if( $i != $total-1 ) {
						if( $this->match == MATCH_ANY ) { $sql .= " OR "; }
						else { $sql .= " AND "; }
					}
				}
				if( $this->where == WHERE_BOTH ) { $sql .= ")"; }
			}
			if( $this->where == WHERE_TEXT || $this->where == WHERE_BOTH ) {
				if( $this->where == WHERE_BOTH ) { $sql .= " OR ("; }
				for( $i=0; $i<$total; $i++ ) {
					$sql .= "`messages`.`content` LIKE '%" . $_db->escape(trim($this->words[$i])) . "%'";
					if( $i != $total-1 ) {
						if( $this->match == MATCH_ANY ) { $sql .= " OR "; }
						else { $sql .= " AND "; }
					}
				}
				if( $this->where == WHERE_BOTH ) { $sql .= ")"; }
			}
			$sql .= ")";
		}

		if( $this->show == SHOW_THREADS ) {
			$sql .= " GROUP BY `messages`.`thread_id`
				ORDER BY `message_threads`.`date_updated` DESC";
		}
		else {
			$sql .= " ORDER BY `messages`.`date_sent` DESC";
		}

		return $sql;
	}

}

