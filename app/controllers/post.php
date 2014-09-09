<?php

class Post extends Controller_W 
{

	/**
	 * Delete this post
	 *
	 * @return result array parameters with info about where I am redirecting you to
	 */
	public function delete()
	{
		global $_db;
	
		$sql = "SELECT `topics`.`replies`, `forums`.`id`
			FROM `topics`
				JOIN `forums`
					ON `topics`.`forum_id` = `forums`.`id`
			WHERE `topics`.`id` = {$this->topic_id}";
		$exec = $_db->query($sql);
		list( $replies, $forum_id ) = $exec->fetch_row();

		// Decrement post counters
		$sql = "UPDATE `topics`
			SET `replies` = `replies` - 1
			WHERE `id` = {$this->topic_id}";
		$_db->query($sql);
		$replies--;

		$sql = "UPDATE `forums`
			SET `posts` = `posts` - 1
			WHERE `id` = {$forum_id}";
		$_db->query($sql);

		$sql = "UPDATE `users`
			SET `posts` = `posts` - 1
			WHERE `id` = {$this->user_id}";
		$_db->query($sql);

		// Delete attachments
		$sql = "UPDATE `attachments` SET
			`post_id` = NULL,
			`hash` = 'deleted'
			WHERE `post_id` = {$this->id}";
		$_db->query($sql);
		
		$topic = new Topic($this->topic_id);

		// Delete topic if this was the only post
		if( $replies < 0 ) {
			$topic->delete();

			$forum = new Forum($forum_id);

			$result = array(
				'where' => 'forum',
				'url'   => $forum->url
			);
		}
		// Otherwise check topic sessions
		else {
			$result = array(
				'where' => 'topic',
				'url'   => $topic->url
			);

			$sql = "SELECT `id`
				FROM `posts`
				WHERE `topic_id` = {$this->topic_id}
					AND `id` > {$this->id}
				ORDER BY `id` ASC
				LIMIT 1";
			$exec = $_db->query($sql);

			// Update existing sessions with newer post ID
			if( $exec->num_rows ) {
				list( $new_post_id ) = $exec->fetch_row();

				$sql = "UPDATE `session_topics` SET
					`session_post` = {$new_post_id}
					WHERE `session_post` = {$this->id}";
				$_db->query($sql);
			}
			else {
				$sql = "DELETE FROM `session_topics`
					WHERE `session_post` = {$this->id}";
				$_db->query($sql);
			}
		}

		$sql = "DELETE FROM `posts`
			WHERE `id` = {$this->id}";
		$_db->query($sql);

		$sql = "DELETE FROM `posts_text`
			WHERE `post_id` = {$this->id}";
		$_db->query($sql);

		return $result;
	}
	
	public function load_attachments( $access )
	{
		global $_db;

		$sql = "SELECT *
			FROM `attachments`
			WHERE `post_id` = {$this->id}
				AND `filetype` <= {$access}
			ORDER BY `filetype` DESC, `date` ASC";
		$exec = $_db->query($sql);

		$attachments = array();
		while( $data = $exec->fetch_assoc() ) {
			$attachment = new Attachment($data['id'], $data);
			$attachment->thumb = substr($attachment->filename, 0, -4) . '.jpg';
			
			$attachments[] = $attachment;
		}
		
		return $attachments;
	}
}





class PostTextModel extends Model
{
	const TABLE = 'posts_text';
	protected static $myclass = 'PostText';
	protected $table = 'posts_text';

	// single object
	protected static $instance;

	public static function singleton()
	{
		if( !isset(self::$instance) ) {
			$c = __CLASS__;
			self::$instance = new $c;
		}

		return self::$instance;
	}
}

class PostText extends Controller
{
	const TABLE = 'posts_text';
	protected static $myclass = 'PostText';
	protected $table = 'posts_text';

	function __construct( $id = 0, $data = array() )
	{
		$this->model = PostTextModel::singleton();
		parent::__construct( $id, $data );

		$this->_extra = array(
			'permalink' => ''
		);
	}
}
