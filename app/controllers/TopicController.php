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
	public function display( $id, $name = NULL )
	{
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

		//-----------------------------------------------------------------------------
		// Check permissions
		// @todo support group view/read permission
		if( $f == 19 ) {
			if( in_array(1, $mygroups) || $me->administrator || $me->moderator ) {
				$access = $forum->read;
			}
			else {
				$access = $forum->read-1;
			}
		}

		//-----------------------------------------------------------------------------
		// Mark topic read

		if( $me->id ) {
			SessionTopic::where('user_id', '=', $me->id)
				->where('topic_id', '=', $topic->id)
				->delete();
		}

		//-----------------------------------------------------------------------------
		// Subscription

		$subscribed = $check_sub = false;

		/*if( $me->loggedin ) {
			$sql = "SELECT `notified`
				FROM `topic_subs`
				WHERE `user_id` = {$me->id}
					AND `topic_id` = {$topic->id}";
			$exec = $_db->query($sql);
			
			if( $exec->num_rows ) {
				$subscribed = true;
				
				if( isset($_GET['unsubscribe']) ) {
					// Unsubscribe
					$sql = "DELETE FROM `topic_subs`
						WHERE `user_id` = {$me->id}
							AND `topic_id` = {$topic->id}";
					$_db->query($sql);
					
					$notices[] = 'You have unsubscribed from this topic';
					$subscribed = false;
				}
				else {
					// Mark as notified
					$sql = "UPDATE `topic_subs` SET
						`notified` = 1
						WHERE `user_id` = {$me->id}
							AND `topic_id` = {$topic->id}";
					$_db->query($sql);
				}
			}
			else {
				$subscribed = false;
				
				if( isset($_GET['subscribe']) ) {
					// Subscribe
					$sql = "INSERT IGNORE INTO `topic_subs` SET
						`user_id` = {$me->id},
						`topic_id` = {$topic->id},
						`notified` = 1";
					$_db->query($sql);
					
					$notices[] = 'You have subscribed to this topic';
					$subscribed = true;
				}
			}
			
			list( $subscribed, $check_sub ) = $me->check_subscribe($topic->id);
		}*/

		//-----------------------------------------------------------------------------
		// Fetch all posts

		$posts = $topic->posts()->paginate(25);

		/*
		while( $data = $exec->fetch_assoc() )
		{
			$post = new Post($data['id']);
			
			// Post text data
			$post->subject = $data['post_subject'];
			$post->content = $data['post_text'];
			$post->smiley = $data['post_smiley'];
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

			$post->time += ($me->tz*3600);
			$post->formatted_date = datestring($post->time, 1);
			$post->edit_time += ($me->tz*3600);
			$user->joined += ($me->tz*3600);
			
			// Check if ignored
			$sql = "SELECT `entry_id`
				FROM `user_lists`
				WHERE `entry_user` = {$me->id}
					AND `entry_subject` = {$post->user_id}";
			$exec2 = $_db->query($sql);
			if( $exec2->num_rows ) {
				$post->ignored = true;
			}

			// Online
			$user->check_online();
			$user->online_text = $user->online ? 'online' : 'offline';
			
			// Custom Fields
			$user->custom = $user->load_custom_fields($access, 'topic');
		}*/

		return View::make('topics.display')
			->with('_PAGE', $_PAGE)
			->with('forum', $forum)
			->with('topic', $topic)
			->with('posts', $posts);

		/*$Smarty->assign('total_posts', count($posts));

		$Smarty->assign('subscribed', $subscribed);
		$Smarty->assign('check_sub', $check_sub);*/

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






		$sql = "
		SELECT poll_id, poll_question, poll_max, poll_public
		FROM polls
		WHERE poll_topic = '$t'";
		$res = query($sql);
		?>

			<table class="table2" cellpadding="0" cellspacing="0" border="0" width="100%">
		<?php
		if( mysql_num_rows($res)) {
			list( $pollid, $pollq, $pollmax, $pollpub ) = mysql_fetch_array($res);
			$pollq = stripslashes($pollq);

			if( $pollmax <= 1 ) { $polltype = "radio"; }
			else { $polltype = "checkbox"; }
		?>

		<form method="post" action="<?= $_SERVER['REQUEST_URI']; ?>">
		<input type="hidden" name="pollid" value="<?= $pollid; ?>">
		<table class="layout" cellpadding="0" cellspacing="0" border="0" width="100%">
		<tr>
			<td class="middle"><center><br>
			<b><?= $pollq; ?></b>
			<table class="layout" cellpadding="0" cellspacing="0" border="0" style="margin:10px 0px;">
		<?php

		$sql = "
		SELECT vote_choices 
		FROM poll_votes
		WHERE vote_poll = '$pollid' AND vote_user = '" . $me->id . "'";
		$res2 = query($sql);
		if( mysql_num_rows($res2) || $_GET["vote"] == "results" || !$me->loggedin ) {
			list( $choices ) = mysql_fetch_array($res2);
			$choices = explode(',',$choices);
			
			$sql = "SELECT COUNT( vote_id ) FROM poll_votes WHERE vote_poll = '$pollid'";
			$res3 = query($sql);
			list( $totalvotes ) = mysql_fetch_array($res3);
			$sql = "SELECT SUM( option_votes ) FROM poll_options WHERE option_poll = '$pollid'";
			$res3 = query($sql);
			list( $totalpicks ) = mysql_fetch_array($res3);
			$sql = "SELECT option_votes	FROM poll_options WHERE option_poll = '$pollid'";
			$res3 = query($sql);
			$maxpercent = 1;
			while( $pollopt = mysql_fetch_array($res3)) {
				list( $optvotes ) = $pollopt;
				if( $totalpicks != 0 ) {
					$percent = round((100*($optvotes/$totalpicks)),0);
					if( $percent > $maxpercent ) { $maxpercent = $percent; }
				}
			}
			$proportion = 200 / $maxpercent;
			$sql = "
			SELECT option_id, option_text, option_votes
			FROM poll_options 
			WHERE option_poll = '$pollid'
			ORDER BY option_id ASC";
			$res3 = query($sql);
			while( $pollopt = mysql_fetch_array($res3)) {
				list( $optid, $opttext, $optvotes ) = $pollopt;
				$opttext = stripslashes($opttext);
				if( $totalpicks > 0 ) {
					$percent = round((100*($optvotes/$totalpicks)),0);
					$barwidth = round($percent*$proportion,0);
				}
				else {
					$percent = 0; $barwidth = 0;
				}
				
				echo "<tr><td>";
				if( in_array($optid, $choices)) { echo "<b>"; } 
				echo $opttext;
				if( in_array($optid, $choices)) { echo "</b>"; } 
				echo "</td>
				<td style=\"padding:1px 0px 1px 10px;\"><div class=\"showimgs\">
				<img src=\"".$cssdir."img/vote_left.png\"><img src=\"".$cssdir."img/vote.png\" width=\"$barwidth\" height=\"12\">";
				echo "<img src=\"".$cssdir."img/vote_right.png\"></div></td>
				<td style=\"padding-left:10px;\"><b>$percent%</b></td>
				<td style=\"padding-left:10px;\">[ $optvotes ]</td></tr>\n";
			}
			$voteless = str_replace("&vote=results","",$_SERVER['REQUEST_URI']);
			echo "</table>
			<b>Total Votes: ";
			if( $me->administrator || $me->moderator || ( $pollpub && $me->loggedin )) { echo "<a href=\"/showresults.php?id=$pollid\">"; }
			echo $totalvotes;
			if( $me->administrator || $me->moderator ) { echo "</a>"; }
			echo "</b>";
			if( !mysql_num_rows($res2)) {
				echo "<br>
				<small><a href=\"/".$voteless."\">Place vote</a></small>";
			}
		}
		else {
			$sql = "
			SELECT option_id, option_text 
			FROM poll_options 
			WHERE option_poll = '$pollid'
			ORDER BY option_id ASC";
			$res3 = query($sql);
			while( $pollopt = mysql_fetch_array($res3)) {
				list( $optid, $opttext ) = $pollopt;
				$opttext = stripslashes($opttext);
				echo "<tr><td><input tabindex=\"3\" type=\"$polltype\" name=\"voteopt[]\" value=\"$optid\"></td>
				<td style=\"padding-left:10px;\">".$opttext."</td></tr>\n";
			}
			echo "</table>";
			if( $pollmax > 1 ) { echo "<small><i>Select up to $pollmax options</i></small><br>"; }
			echo "<input tabindex=\"3\" type=\"submit\" name=\"voted\" value=\"Submit Vote\" style=\"font-size:9pt\"><br>
			<small><a href=\"".$_SERVER['REQUEST_URI']."&amp;vote=results\">View results</a></small>";
		}
		?>
			<br><br>
			</center></td></tr>
		</table>
		</form>
		<?php
		}




		*/
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
