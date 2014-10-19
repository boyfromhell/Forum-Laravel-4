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
		$_PAGE = array(
			'category' => 'messages',
			'section'  => 'compose',
			'title'    => 'Compose Message'
		);

		return View::make('messages.compose')
			->with('_PAGE', $_PAGE);
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

	/**
	 * Count totals for each folder
	 *
	 * @return array
	 */
	public static function countTotals()
	{
		global $me;

		$data = DB::select("SELECT thread_id, MIN(archived) AS is_archived, MIN(read) AS is_read,
					( MAX(from_user_id = ?) = 1 ) AS from_me,
					( MIN(from_user_id = ?) = 0 ) AS to_me
				FROM messages
				WHERE owner_user_id = ?
				GROUP BY thread_id",
			[$me->id, $me->id, $me->id]);

		$totals = array(
			'inbox'    => 0,
			'sent'     => 0,
			'archived' => 0,
			'unread'   => 0,
		);

		foreach( $data as $datum )
		{
			if( $datum->is_archived == 1 ) {
				$totals['archived']++;
			}
			else if( $datum->to_me ) {
				$totals['inbox']++;

				if( ! $datum->is_read ) {
					$totals['unread']++;
				}
			}
			if( $datum->from_me ) {
				$totals['sent']++;
			}
		}

		return $totals;
	}

}

