<?php

class MessageController extends BaseController
{

	/**
	 * View inbox or other folder
	 *
	 * @return Response
	 */
	public function inbox( $folder = 'inbox' )
	{
		global $me;

		$sort = Input::get('sort', 'date');
		$order = Input::get('order', 'desc');

		$_PAGE = array(
			'category' => 'messages',
			'section'  => $folder,
			'title'    => ucwords($folder)
		);

		switch( $sort ) {
			case 'name':
				$orderby = 'users.name';
				break;

			case 'subject':
				$orderby = 'message_threads.title';
				break;

			default:
				$sort = 'date';
				$orderby = 'message_threads.date_updated';
				break;
		}

		$threads = MessageThread::join('messages', 'messages.thread_id', '=', 'message_threads.id')
			->where('messages.owner_user_id', '=', $me->id)
			->groupBy('messages.thread_id');

		switch( $folder ) {
			case 'sent':
				$threads = $threads->having(DB::raw('( MAX(from_user_id) = '.$me->id.' )'), '=', 1);
				break;

			case 'archived':
				$threads = $threads->having(DB::raw('MIN(messages.archived)'), '=', 1);
				break;

			case 'inbox':
			default:
				$folder = 'inbox';
				$threads = $threads->having(DB::raw('MIN(messages.archived)'), '=', 0)
					->having(DB::raw('( MIN(from_user_id) = '.$me->id.' )'), '=', 0);
				break;
		}

		$threads = $threads->orderBy($orderby, $sort)
			->orderBy('message_threads.date_updated', 'desc')
			->paginate(25, ['message_threads.*', DB::raw('MIN(messages.read) AS `read`')]);

		return View::make('messages.folder')
			->with('_PAGE', $_PAGE)
			->with('folder', $folder)
			->with('threads', $threads);
	}

	/**
	 * Loads all users who are involved (from or to) a message, excluding me
	 */
	public function load_all_users()
	{
		global $me;
	
		$user_array = explode(',', $this->to_users);
		$user_array[] = $this->from_user_id;
		$users = array();

		foreach( $user_array as $user_id ) {
			if( $user_id != $me->id ) {
				try {
					$user = new User($user_id);
					$users[] = $user;
				}
				catch( Exception $e ) {
				}
			}
		}

		return $users;
	}
	
	/**
	 * Delete this post
	 *
	 * @param  $section  the folder that they are currently in 
	 * @return result array parameters with info about where I am redirecting you to
	 */
	public function delete( $section )
	{
		global $_db, $me;

		$sql = "DELETE FROM `messages`
			WHERE `id` = {$this->id}";
		$_db->query($sql);
		
		// Delete attachments
		$sql = "UPDATE `attachments` SET
			`message_id` = NULL,
			`hash` = 'deleted'
			WHERE `message_id` = {$this->id}";
		$_db->query($sql);

		$sql = "SELECT COUNT(1)
			FROM `messages`
			WHERE `thread_id` = {$this->thread_id}
				AND `owner_user_id` = {$me->id}";
		$exec = $_db->query($sql);

		list( $total_messages ) = $exec->fetch_row();

		if( $total_messages ) {
			$result = array(
				'url'   => "/messages/{$this->thread_id}",
				'where' => 'private message thread'
			);
		}
		else {
			$result = array(
				'url'   => "/messages/{$section}",
				'where' => 'private messages'
			);
		}
		
		return $result;
	}
	
	public function load_attachments()
	{
		global $_db;

		$sql = "SELECT *
			FROM `attachments`
			WHERE `message_id` = {$this->id}
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
