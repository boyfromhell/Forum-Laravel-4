<?php

class Topic extends Controller_W 
{

	public function view()
	{
		global $_db;
		$sql = "UPDATE `topics`
			SET `views` = `views` + 1
			WHERE `id` = {$this->id}";
		$_db->query($sql);
	}
	
	/**
	 * Format some of the common display information
	 */
	public function format()
	{
		$this->short_title = $this->title;
		if( strlen($this->title) > 50 ) {
			$this->short_title = substr($this->title, 0, 45) . '...';
		}
		if( $this->smiley ) {
			list($this->smiley_img, $this->smiley_alt) = topic_smiley($this->smiley);
		}

		$this->pages = ceil(($this->replies+1)/25);
		$this->alt = 'Go to topic';

		if( $this->type == 2 ) {
			$this->img     = 'topic_announce';
			$this->img_alt = 'Announcement';
			$this->prefix  = 'Announcement: ';
		}
		elseif( $this->type == 1 ) {
			$this->img     = 'topic_sticky';
			$this->img_alt = 'Sticky';
			$this->prefix  = 'Sticky: ';
		}
		elseif( $this->status == 1 ) {
			$this->img     = 'topic_locked';
			$this->img_alt = 'Locked';
		}
		else {
			$this->img     = 'topic';
			$this->img_alt = 'No new posts';
			if( $this->pages > 1 ) { $this->img .= '_hot'; }
		}
	}

	/**
	 * Delete this topic
	 * This function should never be called - it is only called from inside the Post::delete() function
	 */
	public function delete()
	{
		global $_db;

		$sql = "DELETE FROM `topics`
			WHERE id = {$this->id}";
		$_db->query($sql);

		$sql = "DELETE FROM `topic_subs`
			WHERE `topic_id` = {$this->id}";
		$_db->query($sql);

		$sql = "DELETE FROM `session_topics`
			WHERE `topic_id` = {$this->id}";
		$_db->query($sql);

		$sql = "UPDATE `forums` SET
			`topics` = `topics` - 1
			WHERE `id` = {$this->forum_id}";
		$_db->query($sql);

		// Delete poll data
		$sql = "SELECT `poll_id`
			FROM `polls`
			WHERE `poll_topic` = {$this->id}";
		$exec = $_db->query($sql);

		if( $exec->num_rows )
		{
			list( $poll_id ) = $exec->fetch_row();

			$sql = "DELETE FROM `poll_options`
				WHERE `option_poll` = {$poll_id}";
			$_db->query($sql);

			$sql = "DELETE FROM `poll_votes`
				WHERE `vote_poll` = {$poll_id}";
			$_db->query($sql);

			$sql = "DELETE FROM `polls`
				WHERE `poll_topic` = {$this->id}";
			$_db->query($sql);
		}
	}
}
