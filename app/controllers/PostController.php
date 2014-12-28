<?php namespace Parangi;

use Exception;
use App;
use DB;
use Input;
use Redirect;
use Request;
use Response;
use Session;
use Validator;
use View;

class PostController extends BaseController
{
    use \Earlybird\FoundryController;

	protected $mode;
	protected $title;

	protected $post;
	protected $topic;
	protected $forum;

	protected $subject = '';
	protected $content = '';

	/**
	 * Display a post in the context of the topic
	 *
	 * @return Response
	 */
	public function display($id)
	{
		$post = Post::findOrFail($id);

		$controller = new TopicController();
		return $controller->display($post->topic_id);
	}


	/**
	 * Reply to a topic
	 *
	 * @param  int  $id  topic ID you are repyling to
	 * @return Response
	 */
	public function reply($id)
	{
		global $me;

		$this->topic = Topic::findOrFail($id);
		$this->forum = $this->topic->forum;

		$this->mode = 'reply';
		$this->title = 'Post a reply';

		if ($this->topic->is_locked && !$me->is_moderator) {
			App::abort(403);
		}

		$this->subject = 'Re: '.$this->topic->title;

		return $this->createPost();
	}

	/**
	 * Edit a post
	 *
	 * @param  int  $id  post ID to edit
	 * @return Response
	 */
	public function edit($id)
	{
		global $me;

		$this->post = Post::findOrFail($id);
		$this->topic = $this->post->topic;
		$this->forum = $this->topic->forum;

		$this->mode = 'edit';
		$this->title = 'Edit post';

		if ($this->post->user_id != $me->id && !$me->is_moderator) {
			App::abort(403);
		}
		if ($this->topic->is_locked && !$me->is_moderator) {
			App::abort(403);
		}

		$this->subject = $this->post->subject;
		$this->content = $this->post->text;

		return $this->createPost();
	}

	/**
	 * Quick edit a post
	 *
	 * @param  int  $id  post ID to edit
	 * @return Response
	 */
	public function quickEdit($id)
	{
		global $me;

		$post = Post::findOrFail($id);
		$topic = $post->topic;

		if ($post->user_id != $me->id && !$me->is_moderator) {
			App::abort(403);
		}
		if ($topic->is_locked && !$me->is_moderator) {
			App::abort(403);
		}

		if (Request::isMethod('post')) {
			if (isset($_POST['save'])) {
				$content = Input::get('content');
				$content = BBCode::prepare($content);

				// Track edit
				DB::table('posts')->where('id', '=', $post->id)
					->increment('edit_count', 1, [
						'edit_user_id' => $me->id
					]);

				// Update post text
				// @todo store revision
				$post->postText()->update([
					'post_text' => $content
				]);
			}

			$html = View::make('posts.body')
				->with('post', $post)
				->render();
		} else {
			$check_sub = $me->notify;
			if ($topic->id && $me->subscriptions->contains($topic->id)) {
				$check_sub = true;
			}

			$html = View::make('posts.quick_edit')
				->with('post', $post)
				->with('check_sub', $check_sub)
				->render();
		}

		return Response::json(['html' => $html]);
	}

	/**
	 * Quote a post
	 *
	 * @param  int  $id  post ID to quote
	 * @return Response
	 */
	public function quote($id)
	{
		global $me;

		$this->post = Post::findOrFail($id);
		$this->topic = $this->post->topic;
		$this->forum = $this->topic->forum;

		$this->mode = 'quote';
		$this->title = 'Post a reply';

		if ($this->topic->is_locked && !$me->is_moderator) {
			App::abort(403);
		}

		$this->subject = 'Re: '.$this->topic->title;
		$this->content = BBCode::quote($this->post->user->name, $this->post->text);

		return $this->createPost();
	}

	/**
	 * Create a new topic
	 *
	 * @param  int  $id  forum ID the topic is going into
	 * @return Response
	 */
	public function newTopic($id)
	{
		global $me;

		$this->forum = Forum::findOrFail($id);

		$this->mode = 'newtopic';
		$this->title = 'Post a new topic';

		return $this->createPost();
	}

	/**
	 * Handle all post creation
	 *
	 * @return Response
	 */
	public function createPost()
	{
		global $me;

		$post = $this->post;
		$topic = $this->topic;
		$forum = $this->forum;

		$subject = Input::get('subject', $this->subject);
		$content = Input::get('content', $this->content);
		$hash = Input::get('hash', md5($me->name.time().rand(0,9999)));

		// Permissions
		if (! $forum->check_permission('view')) {
			App::abort(404);
		} else if (! $forum->check_permission('read')) {
			App::abort(403);
		}

		// Check for existing subscription
		$subscribed = false;
		$check_sub = $me->notify;
		if ($topic->id && $me->subscriptions->contains($topic->id)) {
			$subscribed = $check_sub = true;
		}

		// Form submitted
		if (Input::has('preview')) {
			// Don't validate anything for a preview
		} else if (Request::isMethod('post')) {
			$rules = [
				'content' => 'required',
			];
			if ($this->mode == 'newtopic') {
				$rules['subject'] = 'required';
			}

			// Upload attachments
			$successful = $total = 0;
			if (Input::hasFile('files')) {
				foreach (Input::file('files') as $i => $file) {
					if ($file->isValid()) {
						try {
							$success = AttachmentController::upload($file, $i, $hash);
						} catch (Exception $e) {
							Session::push('errors', $e->getMessage());
						}
						if ($success) {
							$successful++;
						}
					}
	
					$total++;
				}
			}

			$validator = Validator::make(Input::all(), $rules);

			if ($validator->fails()) {
				foreach ($validator->messages()->all() as $error) {
					Session::push('errors', $error);
				}
			} else {
				$content = BBCode::prepare($content);

				// Only moderators can set type
				$type = Input::get('type', 0);
				$smiley = Input::get('smiley', 0);

				if (!$me->is_moderator) {
					$type = 0;
				}

				// Editing a post is vastly different from the other modes
				if ($this->mode == 'edit') {
					if ($me->is_moderator) {
						$topic->type = $type;
					}

					// If this is the first post in the topic, change the subject
					if ($topic->posts()->first()->id == $post->id && $subject) {
						$topic->title = $subject;
						$topic->smiley = $smiley;
					}

					$topic->save();

					DB::table('posts')->where('id', '=', $post->id)
						->increment('edit_count', 1, [
							'edit_user_id' => $me->id,
							'smileys'      => Input::get('show_smileys'),
							'signature'    => Input::get('attach_sig'),
						]);

					$post->postText->update([
						'post_subject' => $subject,
						'post_text'    => $content,
						'post_smiley'  => $smily
					]);
				} else {
					// New topics
					if ($this->mode == 'newtopic') {
						$topic = Topic::create([
							'forum_id'  => $forum->id,
							'title'     => $subject,
							'user_id'   => $me->id,
							'type'      => $type,
							'smiley'    => $smiley,
							'posted_at' => DB::raw('NOW()')
						]);

						$forum->increment('total_topics');
					}
					// Replies
					else if ($this->mode == 'reply' || $this->mode == 'quote') {
						$topic->increment('replies');
						$topic->posted_at = DB::raw('NOW()');
						$topic->save();
					}

					$forum->increment('total_posts');
					
					$post = Post::create([
						'topic_id'   => $topic->id,
						'user_id'    => $me->id,
						'ip'         => Request::getClientIp(),
						'smileys'    => Input::get('show_smileys'),
						'signature'  => Input::get('attach_sig'),
					]);

					if ($subject == 'Re: '.$topic->title || $subject == $topic->title) {
						$subject = '';
					}

					// Save post text
					PostText::create([
						'post_id'      => $post->id,
						'post_subject' => $subject,
						'post_text'    => $content,
						'post_smiley'  => $smiley
					]);

					// Create unread notices for users who are currently logged in
					// and don't already have notices for this topic
					/*$sql = "SELECT `user_id`
						FROM `session_users`
						WHERE `expiration` >= {$gmt}
							AND `user_id` != {$me->id}";
					$exec = $_db->query($sql);
					
					while ($data = $exec->fetch_assoc()) {
						$sql = "SELECT `session_id`
							FROM `session_topics`
							WHERE `topic_id` = {$topic->id}
								AND `forum_id` = {$forum->id}
								AND `user_id` = {$data['user_id']}";
						$exec2 = $_db->query($sql);
						if (!$exec2->num_rows) {
							// @todo set primary key on topic_id, user_id and just use insert ignore
							$sql = "INSERT INTO `session_topics` SET
								`topic_id`     = {$topic->id},
								`forum_id`     = {$forum->id},
								`user_id`      = {$data['user_id']},
								`session_post` = {$post->id}";
							$_db->query($sql);
						}
					}*/

					$me->increment('total_posts');
				}

				// Update attachments with post ID
				Attachment::whereNull('post_id')
					->where('user_id', '=', $me->id)
					->where('hash', '=', $hash)
					->update([
						'hash'    => null,
						'post_id' => $post->id
					]);

				// Subscribe/unsubscribe

				// Send topic subscribers an email notification
				foreach ($topic->subscribers as $subscriber) {
					if ($subscriber->notified == 1 && $subscriber->id != $me->id) {
						$html = View::make('emails.topic_reply')
							->with('user', $me)
							->with('topic', $topic)
							->with('post', $post)
							->with('content', $content)
							->render();

						EmailQueue::create([
							'user_id' => $subscriber->id,
							'subject' => 'Topic Reply Notification',
							'content' => $html,
							'date_queued' => DB::raw('NOW()'),
						]);

						$topic->subscribers()->updateExistingPivot($subscriber->id, ['notified' => 0]);
					}
				}

				return Redirect::to($post->url);
			}
		}

		// Load attachments, including pending
		if ($post->id) {
			$attachments = Attachment::where('post_id', '=', $post->id)
				->orWhere(function ($query) use ($me, $hash) {
					$query->whereNull('post_id')
						->where('user_id', '=', $me->id)
						->where('hash', '=', $hash);
				});
		} else {
			$attachments = Attachment::whereNull('post_id')
				->where('user_id', '=', $me->id)
				->where('hash', '=', $hash);
		}
		$attachments = $attachments->orderBy('filetype', 'desc')
			->orderBy('created_at', 'asc')
			->get();

		$_PAGE = array(
			'category' => 'home',
			'title'    => $this->title
		);

		$post_max_size = intval(ini_get('post_max_size'));
		$max_total = $post_max_size * 1024 * 1024;
		$upload_max_filesize = intval(ini_get('upload_max_filesize'));
		$max_bytes = $upload_max_filesize * 1024 * 1024;
		$max_file_uploads = ini_get('max_file_uploads');

		return View::make('posts.create')
			->with('_PAGE', $_PAGE)
			->with('menu', ForumController::fetchMenu('forum'))
			->with('mode', $this->mode)
			->with('attachments', $attachments)
			->with('hash', $hash)

			->with('post', $post)
			->with('topic', $topic)
			->with('forum', $forum)

			// From input or pre-filled
			->with('subject', $subject)
			->with('content', $content)

			// Default settings
			->with('show_smileys', $this->mode == 'edit' ? $post->smileys : 1)
			->with('attach_sig', $this->mode == 'edit' ? $post->signature : $me->attach_sig)
			->with('check_sub', $check_sub)

			->with('post_max_size', $post_max_size)
			->with('max_total', $max_total)
			->with('upload_max_filesize', $upload_max_filesize)
			->with('max_bytes', $max_bytes)
			->with('max_file_uploads', $max_file_uploads);

		/*
			$sql = "
			UPDATE polls SET poll_hash = '', poll_topic = '" . (int)$t . "'
			WHERE (poll_topic = '0' AND poll_hash = '" . $_db->escape($hash) . "') OR (poll_topic = '" . (int)$t . "')";
			$res = query($sql);
			

		$pollq = $_POST["pollq"];
			if ($pollq && !$_POST["advanced"]) {
				$pollmax = (int)$_POST["pollmax"];
				$pollpub = (int)$_POST["pollpub"];
				if ($pollmax < 1) {
					$pollmax = 1;
				}

				$sql = "SELECT poll_id FROM polls WHERE (poll_topic = '0' AND poll_hash = '" . $_db->escape($hash) . "')";
				if ($mode == "edit") {
					$sql .= " OR (poll_topic = '" . (int)$t . "')";
				}
				$res = query($sql);
				if (!mysql_num_rows($res)) {
					$sql = "INSERT INTO polls
					( `poll_topic`, `poll_question`, `poll_max`, `poll_hash`, `poll_public` )
					VALUES
					( '" . (int)$t . "', '" . $_db->escape($pollq) . "',
					  '" . (int)$pollmax . "', '" . $_db->escape($hash) . "', '" . (int)$pollpub . "' )";
					$res2 = query($sql);
				}
				$sql = "SELECT poll_id FROM polls WHERE (poll_topic = '0' AND poll_hash = '" . $_db->escape($hash) . "')";
				if ($mode == "edit") {
					$sql .= " OR (poll_topic = '" . (int)$t . "')";
				}
				$res = query($sql);
				list($pollid) = mysql_fetch_array($res);

				$sql = "UPDATE polls SET 
					poll_question = '" . $_db->escape($pollq) . "',
					poll_max = '" . (int)$pollmax . "',
					poll_public = '" . (int)$pollpub . "'
					WHERE poll_id = '" . (int)$pollid . "'";
				$res = query($sql);

				$sql = "SELECT option_id FROM poll_options WHERE option_poll = '" . (int)$pollid . "'";
				$res = query($sql);
				while ($option = mysql_fetch_array($res)) {
					list($optid) = $option;
					if ($_POST["opt".$optid]) {
						$opttext = $_POST["opt".$optid];
						$sql = "UPDATE poll_options SET option_text = '" . $_db->escape($opttext) . "' WHERE option_id = '" . (int)$optid . "'";
						$res2 = query($sql);
					} else {
						$sql = "DELETE FROM poll_options WHERE option_id = '" . $optid . "'";
						$res2 = query($sql);
						$sql = "SELECT vote_id, vote_choices FROM poll_votes WHERE vote_poll = '" . (int)$pollid . "'";
						$res2 = query($sql);
						while ($delvote = mysql_fetch_array($res2)) {
							list($voteid, $choices) = $delvote;
							$choicelist = explode(',',$choices);
							$pos = array_search($optid, $choicelist);
							unset($choicelist[$pos]);
							$choices = implode(',',$choicelist);
							if (count($choicelist)) {
								$sql = "UPDATE poll_votes SET vote_choices = '" . (int)$choices . "' WHERE vote_id = '" . (int)$voteid . "'";
								$res3 = query($sql);
							}
							else {
								$sql = "DELETE FROM poll_votes WHERE vote_id = '" . (int)$voteid . "'";
								$res3 = query($sql);
							}
						}
					}
				}
				for ($i=1; $i<=15; $i++) {
					if ($_POST["new".$i]) {
						$opttext = $_POST["new".$i];
						$sql = "INSERT INTO poll_options 
						( `option_poll`, `option_text`, `option_votes` )
						VALUES
						( '" . (int)$pollid . "', '" . $_db->escape($opttext) . "', '0' )";
						$res2 = query($sql);
					}
				}
			} else if (!$_POST["advanced"]) {
				if ($mode == "edit") {
					$sql = "SELECT poll_id FROM polls WHERE poll_topic = '" . (int)$t . "'";
					$res = query($sql);
					list($delpoll) = mysql_fetch_array($res);
					$sql = "DELETE FROM polls WHERE poll_topic = '" . (int)$t . "'";
					$res = query($sql);
					$sql = "DELETE FROM poll_options WHERE option_poll = '" . (int)$delpoll . "'";
					$res = query($sql);
					$sql = "DELETE FROM poll_votes WHERE vote_poll = '" . (int)$delpoll . "'";
					$res = query($sql);			
				}
			}
			
		if ($mode == "newtopic" || ( $mode == "edit" && ( $me->administrator || $me->moderator ))) { 
			$sql = "
			SELECT poll_id, poll_question, poll_max, poll_public
			FROM polls 
			WHERE (poll_topic = '0' AND poll_hash = '" . $_db->escape($hash) . "')";
			if ($mode == "edit") {
				$sql .= " OR (poll_topic = '" . (int)$t . "')";
			}
			$res = query($sql);
			if (mysql_num_rows($res)) {
				list($pollid, $pollq, $pollmax, $pollpub) = mysql_fetch_array($res);
				$pollq = stripslashes($pollq);
			} else {
				$pollid = 0; $pollmax = 1; $pollpub = 0;
			}
		?>
		<tr>
			<td class="category" colspan="2">Poll</td>
		</tr>
		<tr>
			<td class="left">Question:</td>
			<td class="right"><input tabindex="2" name="pollq" type="text" style="width:300px"
			value="{{ $pollq; }}"></td>
		</tr>
		<tr>
			<td class="left">Maximum Selection:</td>
			<td class="right"><input tabindex="2" name="pollmax" type="text" style="width:50px"
			value="<?= $pollmax; ?>">
			&nbsp;&nbsp;&nbsp;Results:&nbsp;&nbsp;&nbsp;
			<select name="pollpub" tabindex="1" style="width:100px">
			<option value="0"{ if( $pollpub == 0 ) { echo " selected"; } }>Private</option>
			<option value="1"{ if( $pollpub == 1 ) { echo " selected"; } }>Public</option>
			</select>
			<input tabindex="1" type="submit" name="poll" value="Update">
			</td>
		</tr>

		<script type="text/javascript"><!--
			function addanother() {
				for (i=1; i<=15; i++) {
					var newrow = document.getElementById("option"+i);
					if( newrow.style.display == "none" ) {
						newrow.style.display = "table-row";
						break;
					}
				}
			}
		--></script> 
			$counter = 1;
			$sql = "
			SELECT option_id, option_text 
			FROM poll_options 
			WHERE option_poll = '" . (int)$pollid . "'
			ORDER BY option_id ASC";
			$res = query($sql);
			$total = mysql_num_rows($res);
			while( $pollopt = mysql_fetch_array($res)) {
				list( $optid, $opttext ) = $pollopt;
				$opttext = stripslashes($opttext);
		<tr id="option{{ $counter }}" style="display:table-row">
			<td class="left">Option:</td>
			<td class="right"><input tabindex="2" name="opt<?= $optid; ?>" type="text" 
			style="width:300px" value="<?= $opttext; ?>">
			@if( $counter == 1 )
			<a href="#" onClick="javascript:addanother(); return false">Add another</a>
			@endif
			</td>
		</tr> 
				$counter++;
			}
			while( $counter <= 15 ) {
		<tr id="option<?= $counter; ?>" style="display:<?php if( $counter > $total+1 ) { echo "none"; } else { echo "table-row"; } ?>">
			<td class="left">New Option:</td>
			<td class="right"><input tabindex="2" name="new<?= $counter; ?>" type="text" style="width:300px">
			<?php if( $counter == 1 ) { ?>
			<a href="#" onClick="javascript:addanother(); return false">Add another</a>
			<?php } ?>
			</td>
		</tr>
				$counter++;
			}
		}

		</form>

		*/
	}

	/**
	 * Flag a post
	 *
	 * @param  int  $id  post ID to flag
	 * @return Response
	 */
	public function flag($id)
	{
		global $me;

		$_PAGE['title'] = 'Flag post';

		$post = Post::findOrFail($id);

		if (Request::isMethod('post')) {
			if (isset($_POST['cancel'])) {
			} else {
				$report = PostReport::create([
					'post_id'  => $post->id,
					'user_id'  => $me->id,
					'reason'   => Input::get('reason'),
					'comments' => Input::get('comments'),
					'status'   => 'open',
				]);

				Session::push('messages', 'Thank you. The post has been flagged for moderator attention');
			}

			return Redirect::to($post->url);
		}

		return View::make('posts.flag')
			->with('_PAGE', $_PAGE)
			->with('menu', ForumController::fetchMenu('forum'))
			->with('post', $post);
	}

	/**
	 * Confirm deletion of a post
	 *
	 * @param  int  $id  post ID to delete
	 * @return Response
	 */
	public function delete($id)
	{
		global $me;

		$_PAGE = array(
			'category' => 'home',
			'title'    => 'Delete Post'
		);

		$post = Post::findOrFail($id);

		if ($post->user_id != $me->id && !$me->is_moderator) {
			App::abort(403);
		}
		if ($post->topic->is_locked && !$me->is_moderator) {
			App::abort(403);
		}

		if (Request::isMethod('post')) {
			if (isset($_POST['cancel'])) {
				return Redirect::to($post->url);
			}
			// Redirect to topic, or forum if topic has no more posts
			else if (isset($_POST['confirm'])) {
				$redirect = $post->delete();

				Session::push('messages', 'The post has been successfully deleted');

				return Redirect::to($redirect);
			}
		}

		return View::make('posts.delete')
			->with('_PAGE', $_PAGE)
			->with('menu', ForumController::fetchMenu('forum'))
			->with('post', $post);
	}

	/**
	 * Choose smileys in a small popup window
	 *
	 * @return Response
	 */
	public function smileys()
	{
		$_PAGE['title'] = 'Smileys';

		$smileys = Smiley::where('show', '>', 0)
			->orderBy('order', 'asc')
			->get();

		return View::make('posts.smileys')
			->with('_PAGE', $_PAGE)
			->with('smileys', $smileys);
	}

}

