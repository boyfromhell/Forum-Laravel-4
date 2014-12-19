<?php namespace Parangi;

class MessageThread extends BaseModel
{
    use \Earlybird\Foundry;

	protected $appends = array(
		'url',
		'users',
		'replies',
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
	public function lastMessage()
	{
		return $this->messages()
			->take(1);
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
	 * Total number of messages in this thread
	 *
	 * @return int
	 */
	public function getRepliesAttribute()
	{
		return $this->messages()->count();
	}

}

