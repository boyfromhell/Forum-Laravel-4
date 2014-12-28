<?php namespace Parangi;

use App;
use DB;
use Input;
use Redirect;
use Request;
use Session;
use Validator;
use View;
use User;

class MessageController extends BaseController
{

	/**
	 * View inbox or other folder
	 *
	 * @return Response
	 */
	public function inbox($folder = 'inbox')
	{
		global $me;

		$threads = Input::get('threads');

		// Bulk actions
		if (Request::isMethod('post') && count($threads) > 0) {
			$messages = Message::ownedBy($me->id)
				->whereIn('thread_id', $threads);

			if (isset($_POST['archive'])) {
				$messages->update(['archived' => 1]);
			} else if (isset($_POST['unarchive'])) {
				$messages->update(['archived' => 0]);
			} else if (isset($_POST['read'])) {
				$messages->update(['read' => 1]);
			} else if (isset($_POST['unread'])) {
				$messages->update(['read' => 0]);
			} else if (isset($_POST['delete'])) {
				$messages = $messages->get();

				foreach ($messages as $message) {
					$message->delete();
				}
			}

			return Redirect::to('messages/'.$folder);
		}

		// Sorting
		$sort = Input::get('sort', 'date');
		$order = Input::get('order', 'desc');

		$_PAGE = array(
			'category' => 'messages',
			'title'    => ucwords($folder)
		);

		switch ($sort) {
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
		if ($order != 'asc') {
			$order = 'desc';
		}

		// Group messages by thread for this folder
		$threads = MessageThread::join('messages', 'messages.thread_id', '=', 'message_threads.id')
			->where('messages.owner_user_id', '=', $me->id)
			->groupBy('messages.thread_id');

		switch ($folder) {
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
			->orderBy($orderby, $order)
			->paginate(25, ['message_threads.*', DB::raw('MIN(messages.read) AS `read`')]);

		if (count($threads) > 0) {
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
		global $me;

		$hash = Input::get('hash', md5($me->name.time().rand(0,9999)));

		$_PAGE = array(
			'category' => 'messages',
			'title'    => 'Compose Message'
		);

		if (Input::has('user')) {
			// Composing to a user
			$user = User::find(Input::get('user'));

			if ($user->id != $me->id) {
				$recipients = $user->name;
			}
		} else if (Input::has('t')) {
			// Replying to a thread
			$thread = MessageThread::findOrFail(Input::get('t'));

			// If no messages are found (filtered by owner)
			// then this is not my thread, or I deleted them all
			if (! count($thread->messages)) {
				App::abort(404);
			}

			if ($thread->lastMessage->from_user_id != $me->id && ! isset($_GET['all'] )) {
				$recipients = $thread->lastMessage->from->name;
			} else {
				$users = $thread->users;
				$names = array_pluck($users, 'name');
				$recipients = implode(', ', $names);
			}
		} else if (Input::has('p')) {
			// Replyling to an individual message
			$message = Message::findOrFail(Input::get('p'));
			$thread = $message->thread;

			if ($message->owner_user_id != $me->id) {
				App::abort(404);
			}

			$content = BBCode::quote($message->from->name, $message->content);

			$users = $message->thread->users;
			$names = array_pluck($users, 'name');
			$recipients = implode(', ', $names);
		}

		$subject = Input::get('subject');
		$recipients = Input::get('recipients', $recipients);

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

				// @todo Save stuff

				// Update attachments with message ID
                Attachment::whereNull('message_id')
                    ->where('user_id', '=', $me->id)
                    ->where('hash', '=', $hash)
                    ->update([
                        'hash'       => null,
                        'message_id' => $message->id
                    ]);

				// @todo Send emails

				return Redirect::to($post->url);
			}
		}

		// Load pending attachments
		$attachments = Attachment::whereNull('message_id')
            ->where('user_id', '=', $me->id)
            ->where('hash', '=', $hash)
        	->orderBy('filetype', 'desc')
            ->orderBy('created_at', 'asc')
            ->get();

		$post_max_size = intval(ini_get('post_max_size'));
		$max_total = $post_max_size * 1024 * 1024;
		$upload_max_filesize = intval(ini_get('upload_max_filesize'));
		$max_bytes = $upload_max_filesize * 1024 * 1024;
		$max_file_uploads = ini_get('max_file_uploads');

		return View::make('messages.compose')
			->with('_PAGE', $_PAGE)
			->with('menu', MessageController::fetchMenu('compose'))
			->with('attachments', $attachments)
			->with('hash', $hash)

			->with('thread', $thread)

			// From input or pre-filled
			->with('recipients', $recipients)
			->with('subject', $subject)
			->with('content', $content)

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
	public function displayThread($id)
	{
		$thread = MessageThread::findOrFail($id);

		$_PAGE = array(
			'category' => 'messages',
			'title' => $thread->title,
		);

		// If no messages are found (filtered by owner)
		// then this is not my thread, or I deleted them all
		if (! count($thread->messages)) {
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
	public function delete($id)
	{
		global $me;

		$message = Message::findOrFail($id);

		if ($message->owner_user_id != $me->id) {
			App::abort(403);
		}

		$_PAGE = array(
			'category' => 'messages',
			'title'    => 'Delete Private Message'
		);

		if (Request::isMethod('post')) {
			if (isset($_POST['cancel'])) {
				return Redirect::to($message->url);
			}
			// Redirect to thread, or folder if thread has no more messages
			else if (isset($_POST['confirm'])) {
				$redirect = $message->delete();

				if (! $redirect) {
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

		foreach ($data as $datum) {
			if ($datum->is_archived == 1) {
				$totals['archived']++;
			} else if ($datum->to_me) {
				$totals['inbox']++;

				if (! $datum->is_read) {
					$totals['unread']++;
				}
			}
			if ($datum->from_me) {
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
	public static function fetchMenu($active = null)
	{
		$totals = MessageController::countTotals();

		$menu = array();

		$menu['compose'] = array(
			'url' => '/messages/compose',
			'name' => 'Compose',
		);

		$folders = array('inbox', 'sent', 'archived');

		foreach ($folders as $folder) {
			$menu[$folder] = array(
				'url' => '/messages/'.$folder,
				'name' => ucwords($folder),
			);

			if ($totals[$folder] > 0) {
				$menu[$folder]['name'] .= ' <span class="label label-default">'.$totals[$folder].'</span>';
			}
		}

		$menu['search'] = array(
			'url' => '/messages/search',
			'name' => 'Search',
		);

		if ($active !== null) {
			$menu[$active]['active'] = true;
		}

		return $menu;
	}

}

