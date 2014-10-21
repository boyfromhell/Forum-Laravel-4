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

		$threads = Input::get('threads');

		// Bulk actions
		if( Request::isMethod('post') && count($threads) > 0 )
		{
			$messages = Message::ownedBy($me->id)
				->whereIn('thread_id', $threads);

			if( isset($_POST['archive']) ) {
				$messages->update(['archived' => 1]);
			}
			else if( isset($_POST['unarchive']) ) {
				$messages->update(['archived' => 0]);
			}
			else if( isset($_POST['read']) ) {
				$messages->update(['read' => 1]);
			}
			else if( isset($_POST['unread']) ) {
				$messages->update(['read' => 0]);
			}
			else if( isset($_POST['delete']) ) {
				$messages = $messages->get();

				foreach( $messages as $message ) {
					$message->delete();
				}
			}

			return Redirect::to('messages/'.$folder);
		}

		$sort = Input::get('sort', 'date');
		$order = Input::get('order', 'desc');

		$_PAGE = array(
			'category' => 'messages',
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
			->with('menu', MessageController::fetchMenu($folder))
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
			'title'    => 'Compose Message'
		);

		$post_max_size = intval(ini_get('post_max_size'));
		$max_total = $post_max_size * 1024 * 1024;
		$upload_max_filesize = intval(ini_get('upload_max_filesize'));
		$max_bytes = $upload_max_filesize * 1024 * 1024;
		$max_file_uploads = ini_get('max_file_uploads');

		return View::make('messages.compose')
			->with('_PAGE', $_PAGE)
			->with('menu', MessageController::fetchMenu('compose'))

			->with('post_max_size', $post_max_size)
			->with('max_total', $max_total)
			->with('upload_max_filesize', $upload_max_filesize)
			->with('max_bytes', $max_bytes)
			->with('max_file_uploads', $max_file_uploads);
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

		$_PAGE = array(
			'category' => 'messages',
			'title' => $thread->title,
		);

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

		// Custom Fields
		//$user->custom = $user->load_custom_fields($access, 'topic');

		// Mark all messages read
		$thread->messages()->update([
			'read' => 1
		]);

		return View::make('messages.thread')
			->with('_PAGE', $_PAGE)
			->with('menu', MessageController::fetchMenu())
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

		$message = Message::findOrFail($id);

		if( $message->owner_user_id != $me->id ) {
			App::abort(403);
		}

		$_PAGE = array(
			'category' => 'messages',
			'title'    => 'Delete Private Message'
		);

		if( Request::isMethod('post') )
		{
			if( isset($_POST['cancel']) ) {
				return Redirect::to($message->url);
			}
			// Redirect to thread, or folder if thread has no more messages
			elseif( isset($_POST['confirm']) ) {
				$redirect = $message->delete();

				if( ! $redirect ) {
					$redirect = '/messages/'.$message->folder;
				}

				Session::push('messages', 'The private message has been successfully deleted');

				return Redirect::to($redirect);
			}
		}

		return View::make('messages.delete')
			->with('_PAGE', $_PAGE)
			->with('menu', MessageController::fetchMenu($message->folder))
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

		$data = DB::select("SELECT thread_id, MIN(archived) AS is_archived, MIN(`read`) AS is_read,
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

	/**
	 * Menu for messages pages
	 *
	 * @return array
	 */
	public static function fetchMenu( $active )
	{
		$totals = MessageController::countTotals();

		$menu = array();

		$menu['compose'] = array(
			'url' => '/messages/compose',
			'name' => 'Compose',
		);

		$folders = array('inbox', 'sent', 'archived');

		foreach( $folders as $folder ) {
			$menu[$folder] = array(
				'url' => '/messages/'.$folder,
				'name' => ucwords($folder),
			);

			if( $totals[$folder] > 0 ) {
				$menu[$folder]['name'] .= ' <span class="label label-default">'.$totals[$folder].'</span>';
			}
		}

		$menu['search'] = array(
			'url' => '/messages/search',
			'name' => 'Search',
		);

		if( $active ) {
			$menu[$active]['active'] = true;
		}

		return $menu;
	}

}

