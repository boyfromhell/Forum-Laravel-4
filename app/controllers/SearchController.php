<?php
define('MATCH_ANY', 0);
define('MATCH_ALL', 1);
define('WHERE_TITLES', 0);
define('WHERE_TEXT', 1);
define('WHERE_BOTH', 2);
define('SHOW_POSTS', 0);
define('SHOW_TOPICS', 1);
define('SINCE_LAST_VISIT', 1);

// Messages only
define('SHOW_MESSAGES', 0);
define('SHOW_THREADS', 1);
define('FOLDER_ANY', 0);
define('FOLDER_INBOX', 1);
define('FOLDER_SENT', 2);
define('FOLDER_ARCHIVED', 3);

class SearchController extends BaseController
{

	/**
	 * New or edit search
	 *
	 * @return Response
	 */
	public function index( $id = NULL )
	{
		if( $id ) {
			$query = Query::find($id);
		}
		if( ! $query->id || $query->type != 'forum' ) {
			$query = new Query();

			// Defaults
			$query->match = MATCH_ALL;
			$query->where = WHERE_BOTH;
			$query->show  = SHOW_POSTS;
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
	 * Piece together a MySQL statement for this query
	 */
	public function get_database_query()
	{
		switch( $this->type ) {
			case 'messages':
				return $this->_get_messages_database_query();
				break;
			
			case 'forum':
			default:
				return $this->_get_forum_database_query();
				break;
		}
	}
	
	/**
	 * If it's a message query
	 */
	protected function _get_messages_database_query()
	{
		global $_db, $gmt, $me;
	
		$total = count($this->words);
		
		if( $this->show == SHOW_THREADS ) {
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
	
	/**
	 * If it's a forum query
	 */
	protected function _get_forum_database_query()
	{
		global $me;
	
		$total = count($this->words);

		if( $this->show == SHOW_TOPICS ) {
			$sql = "SELECT `posts`.`topic_id` ";
		}
		else {
			$sql = "SELECT `posts`.`id`, `posts`.`topic_id`, `users`.`name` AS `user_name`, `topics`.`title`, posts.time, post_text,
				smileys, `topics`.`forum_id`, `forums`.`name` AS `forum_name` ";
		}
		$sql .= "FROM `posts`
			JOIN `posts_text` ON `posts`.`id` = `posts_text`.`post_id`
			JOIN `users` ON `posts`.`user_id` = `users`.`id`
			JOIN `topics` ON `posts`.`topic_id` = `topics`.`id`
			JOIN `forums` ON `topics`.`forum_id` = `forums`.`id`
		WHERE `forums`.`read` <= {$me->access} ";

		if( $this->since != 0 ) { 
			if( $this->since != SINCE_LAST_VISIT ) { $sincewhen = $gmt-$this->since; }
			else { $sincewhen = $me->visited_at; }
			$sql .= " AND `posts`.`time` >= {$sincewhen} ";
		}
		if( $this->user->id ) {
			$sql .= " AND `posts`.`user_id` = '" . (int)$this->user->id . "' ";
			if( $this->starter == 1 ) {
				$sql .= " AND `topics`.`poster` = '" . (int)$this->user->id . "' ";
			}
		}
		if( !in_array(0, $this->forum_array)) {
			$sql .= ' AND `topics`.`forum_id` IN ( ' . $_db->escape($this->forums) . ' )';
		}
		
		if( $total > 0 ) {
			$sql .= " AND (";

			if( $this->where == WHERE_TITLES || $this->where == WHERE_BOTH ) {
				if( $this->where == WHERE_BOTH ) { $sql .= "("; }
				for( $i=0; $i<$total; $i++ ) {
					$sql .= "`topics`.`title` LIKE '%" . $_db->escape(trim($this->words[$i])) . "%'";
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
					$sql .= "`posts_text`.`post_text` LIKE '%" . $_db->escape(trim($this->words[$i])) . "%'";
					if( $i != $total-1 ) {
						if( $this->match == MATCH_ANY ) { $sql .= " OR "; }
						else { $sql .= " AND "; }
					}
				}
				if( $this->where == WHERE_BOTH ) { $sql .= ")"; }
			}
			$sql .= ")";
		}

		if( $this->show == SHOW_TOPICS ) {
			$sql .= " GROUP BY `posts`.`topic_id`
				ORDER BY `topics`.`last_date` DESC";
		}
		else {
			$sql .= " ORDER BY `posts`.`time` DESC";
		}

		return $sql;
	}
	
	/**
	 * Get a text description of this query
	 */
	public function get_description( $start, $num_results )
	{
		global $_db;
		
		$show_text = array(
			'forum' => array(
				SHOW_POSTS  => 'posts ',
				SHOW_TOPICS => 'topics ',
			),
			'messages' => array(
				SHOW_MESSAGES => 'messages ',
				SHOW_THREADS  => 'threads ',
			),
		);
		$where_text = array(
			WHERE_TITLES => 'titles',
			WHERE_TEXT   => 'message texts',
			WHERE_BOTH   => 'titles or texts',
		);
		$since_text = array(
			0        => '',
			SINCE_LAST_VISIT => ' since your last visit',
			86400    => ' since yesterday',
			604800   => ' in the last week',
			1209600  => ' in the last 2 weeks',
			2628000  => ' in the last month',
			7884000  => ' in the last 3 months',
			15768000 => ' in the last 6 months',
			31536000 => ' in the last year',
		);
		$folder_text = array(
			FOLDER_ANY      => 'any folder',
			FOLDER_INBOX    => 'your Inbox',
			FOLDER_SENT     => 'your Sent folder',
			FOLDER_ARCHIVED => 'your Archived folder',
		);
	
		$total = count($this->words);
		
		$html = 'Searched for ' . $show_text[$this->type][$this->show];
		if( $this->user ) {
			$html .= " by <a href=\"{$this->user->url}\">" . htmlspecialchars($this->user->name) . '</a> ';
		}
		if( $total > 0 ) {
			$html .= 'with ' . $where_text[$this->where] . ' matching ';

			for( $i=0; $i<$total; $i++ ) {
				$html .= '"<i>' . $this->words[$i] . '</i>"';
				if( $i < $total-1 && $total > 2 ) { $html .= ', '; }
				if( $i == $total-2 ) {
					if( $this->match == MATCH_ANY ) { $html .= 'or '; }
					else { $html .= 'and '; }
				}
			}
			$html .= '<br>';
		}
		
		$html .= 'in ';
		
		if( $this->type == 'forum' ) {
			$total_forums = count($this->forum_array);
			
			if( $total_forums == 0 || $this->forum_array[0] == 0 ) {
				$html .= 'all forums';
			}
			else {
				$sql = "SELECT `id`, `name`
					FROM `forums`
					WHERE `id` IN ( " . $_db->escape($this->forums) . " )";
				$exec = $_db->query($sql);
				
				$forums = array();
				while( $data = $exec->fetch_assoc() ) {
					$forum = new Forum($data['id'], $data);
					$forums[] = $forum;
				}
				
				for( $i=0; $i<$total_forums; $i++ ) {
					$html .= "<a href=\"{$forums[$i]->url}\" style=\"color:#000\"><u>" . htmlspecialchars($forums[$i]->name) . '</u></a>';
					if( $i < $total_forums-1 && $total_forums > 2 ) { $html .= ', '; }
					if( $i == $total_forums-2 ) { $html .= 'and '; }
				}
			}
		}
		else if( $this->type == 'messages' ) {
			$html .= $folder_text[$this->folder];
		}

		$html .= $since_text[$this->since] . '<br>';

		$showing = 20;
		if( $num_results < 20 ) { $showing = $num_results; }
		if( $num_results ) {
			$html .= '<br>Showing ';
			$begin = $start+1;
			$end = $start+20;
			if( $end > $num_results ) { $end = $num_results; }

			$html .= "results <b>{$begin} - {$end}</b> ";
			$html .= "out of <b>{$num_results}</b> total";
		}
		$html .= "<br><a href=\"/{$this->type}/search?id={$this->id}\">Modify</a> your search";
		
		return $html;
	}
}
