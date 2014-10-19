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
				$threads = $threads->having(DB::raw('MAX(from_user_id = '.$me->id.' )'), '=', 1);
				break;

			case 'archived':
				$threads = $threads->having(DB::raw('MIN(messages.archived)'), '=', 1);
				break;

			case 'inbox':
			default:
				$folder = 'inbox';
				$threads = $threads->having(DB::raw('MIN(messages.archived)'), '=', 0)
					->having(DB::raw('MIN(from_user_id = '.$me->id.' )'), '=', 0);
				break;
		}

		$threads = $threads->orderBy($orderby, $sort)
			->orderBy('message_threads.date_updated', 'desc')
			->paginate(25, ['message_threads.*', DB::raw('MIN(messages.read) AS `read`')]);

		if( count($threads) > 0 ) {
			$threads->load([
				'from',
				'lastMessage'
			]);
		}

		return View::make('messages.folder')
			->with('_PAGE', $_PAGE)
			->with('folder', $folder)
			->with('threads', $threads);
	}

	/**
	 * Compose a new message
	 *
	 * @return Response
	 */
	public function compose()
	{
		
	}

	/**
	 * Display a thread
	 *
	 * @param  int  $id  MessageThread id
	 * @return Response
	 */
	public function displayThread( $id )
	{
		$thread = MessageThread::findOrFail($id);

		// If no messages are found (filtered by owner)
		// then this is not my thread, or I deleted them all
		if( ! count($thread->messages) ) {
			App::abort(404);
		}

		$thread->messages->load([
			'from',
			'from.avatar',
			'from.groups',
		]);

		return View::make('messages.thread')
			->with('thread', $thread);
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
	 * Confirm deletion of a message
	 *
	 * @return Response
	 */
	public function delete( $id )
	{
		global $me;

		$_PAGE = array(
			'category' => 'messages',
			'title'    => 'Delete Private Message'
		);

		$message = Message::findOrFail($id);

		if( $message->owner_user_id != $me->id ) {
			App::abort(403);
		}

		if( $message->archived ) {
			$folder = 'archived';
		}
		else if( $message->from_user_id == $me->id ) {
			$folder = 'sent';
		}
		else {
			$folder = 'inbox';
		}

		$_PAGE['section'] = $folder;

		if( Request::isMethod('post') )
		{
			if( isset($_POST['cancel']) ) {
				return Redirect::to($message->url);
			}
			// Redirect to thread, or folder if thread has no more messages
			elseif( isset($_POST['confirm']) ) {
				$redirect = $message->delete();

				if( ! $redirect ) {
					$redirect = '/messages/'.$folder;
				}

				Session::push('messages', 'The private message has been successfully deleted');

				return Redirect::to($redirect);
			}
		}

		return View::make('messages.delete')
			->with('_PAGE', $_PAGE)
			->with('message', $message);
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
