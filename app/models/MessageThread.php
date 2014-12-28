<?php namespace Parangi;

use Auth;
use User;

class MessageThread extends BaseModel
{
    use \Earlybird\Foundry;

	protected $appends = array(
		'url',
		'users',
		'replies',
		'lastMessage',
	);

	/**
	 * Messages in this thread
	 *
	 * @return Relation
	 */
	public function messages()
	{
		return $this->hasMany('Parangi\Message', 'thread_id')
			->ownedBy(Auth::id())
			->orderBy('created_at', 'asc');
	}

	/**
	 * Last posted message
	 *
	 * @return Message
	 */
	public function getLastMessageAttribute()
	{
		$last = Message::where('thread_id', '=', $this->id)
			->ownedBy(Auth::id())
			->orderBy('created_at', 'desc')
			->take(1)
			->get();

		return $last[0];
	}

	/**
	 * Permalink
	 *
	 * @return string
	 */
	public function getUrlAttribute()
	{
		return '/messages/'.$this->id;
	}

	/**
     * List of all users involved with this thread
     *
     * @return array
     */
    public function getUsersAttribute()
    {
		$user_ids = array();

		foreach ($this->messages as $message) {
	        $user_ids = array_merge($user_ids, explode(',', $message->to_users));
	        $user_ids[] = $message->from_user_id;
		}

		$user_ids = array_unique($user_ids);

        if(($key = array_search(Auth::id(), $user_ids)) !== false ) {
            unset($user_ids[$key]);
        }

        if (count($user_ids) > 0) {
            $users = User::whereIn('id', $user_ids)->get();

            return $users;
        }

        return array();
    }

	/**
	 * Total number of messages in this thread
	 *
	 * @return int
	 */
	public function getRepliesAttribute()
	{
		return $this->messages()->count();
	}

}

