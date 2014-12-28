<?php namespace Parangi;

use App;
use Collection;
use DB;
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

		$data = $this->forumQuery($query);
		$results = $data->paginate(10); // max 200

		$posts = new Collection;
		$topics = new Collection;

		foreach ($results as $row) {
			if ($query->show == SHOW_POSTS) {
				$post = Post::findOrFail($row->id);

				$post->text = BBCode::strip_quotes($post->text);
				if (strlen($post->text) > 250) {
					$post->text = substr($post->text, 0, 250) . '...';
				}

				$posts->add($post);
			}
			else {
				$topic = Topic::findOrFail($row->topic_id);

				$topics->add($topic);
			}
		}

		if (count($posts) > 0) {
			$posts->load(['topic', 'topic.forum']);
		} else if (count($topics) > 0) {
			$topics->load(['forum']);
		}

		/*if (count($topic_ids)) {
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
		*/

		return View::make('forums.results')
			->with('_PAGE', $_PAGE)
			->with('menu', ForumController::fetchMenu('search'))

			->with('query', $query)
			->with('results', $results)
			->with('topics', $topics)
			->with('posts', $posts)

			->with('searched_for_html', $query->getDescription($start, $num_results));
	}

	/**
	 * If it's a forum query
	 */
	protected function forumQuery($query)
	{
		global $me;

		$forums = explode(',', $query->forums);

        // @todo use attribute
        $keywords = str_replace([' ', ';'], ',', $query->keywords);
        $keywords = explode(',', $keywords);
        $total = count($keywords);

		// @todo use attribute
		if ($query->author) {
            $author = User::where('name', '=', $query->author)->first();
        }

		$data = DB::table('posts');

		if ($query->show == SHOW_TOPICS) {
			$data = $data->select('topic_id');
		} else {
			$data = $data->select('posts.*');
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
		if ($author->id) {
			$data = $data->where('posts.user_id', '=', $author->id);

			if ($query->starter == 1) {
				$data = $data->where('topics.user_id', '=', $author->id);
			}
		}
		if (! in_array(0, $query->forum_array) && count($query->forum_array) > 0) {
			$data = $data->whereIn('topics.forum_id', $query->forums);
		}

		if ($total > 0) {
			$data = $data->where(function ($q) use ($query, $total, $keywords) {

				if ($query->where == WHERE_TITLES || $query->where == WHERE_BOTH) {
					$q->where(function ($q2) use ($query, $total, $keywords) {
						for ($i=0; $i<$total; $i++) {
							if ($query->match == MATCH_ANY) {
								$q2->orWhere('topics.title', 'LIKE', '%'.trim($keywords[$i]).'%');
							} else {
								$q2->where('topics.title', 'LIKE', '%'.trim($keywords[$i]).'%'); // @todo escape
							}
						}
					});
				}
				if ($query->where == WHERE_TEXT || $query->where == WHERE_BOTH) {
					$q->orWhere(function ($q2) use ($query, $total, $keywords) {
						for( $i=0; $i<$total; $i++ ) {
							if ($query->match == MATCH_ANY) {
								$q2->orWhere('posts_text.post_text', 'LIKE', '%'.trim($keywords[$i]).'%');
							} else {
								$q2->where('posts_text.post_text', 'LIKE', '%'.trim($keywords[$i]).'%');
							}
						}
					});
				}
			});
		}

		if( $query->show == SHOW_TOPICS ) {
			$data = $data->groupBy('posts.topic_id')
				->orderBy('topics.posted_at', 'desc');
		}
		else {
			$data = $data->orderBy('posts.created_at', 'desc');
		}

		return $data;
	}

	/**
	 * If it's a message query
	 */
	protected function _get_messages_database_query()
	{
		global $_db, $gmt, $me;
	
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

