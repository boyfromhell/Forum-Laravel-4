<?php namespace Parangi;

class Topic extends Eloquent
{
    use \Earlybird\Foundry;

	protected $appends = array(
		'url',

		'image',
		'alt_text',
		'short_title',

		'unread_post',
		'latest_post',

		'has_attachments',
		'has_poll',
		'pages',
	);

	/**
	 * Forum this topic is in
	 *
	 * @return Relation
	 */
	public function forum()
	{
		return $this->belongsTo('Forum');
	}

	/**
	 * User who started this topic
	 *
	 * @return Relation
	 */
	public function author()
	{
		return $this->belongsTo('User', 'user_id');
	}

	/**
	 * Posts in this topic
	 *
	 * @return Relation
	 */
	public function posts()
	{
		return $this->hasMany('Post')
			->orderBy('created_at', 'asc');
	}

	/**
	 * Subscribed users
	 *
	 * @return Relation
	 */
	public function subscribers()
	{
		return $this->belongsToMany('User', 'topic_subs');
	}

	/**
	 * Subscriptions
	 *
	 * @return Relation
	 */
	public function subscriptions()
	{
		return $this->hasMany('TopicSubscription');
	}

	/**
	 * Unread sessions for users
	 *
	 * @return Relation
	 */
	public function sessions()
	{
		return $this->hasMany('SessionTopic');
	}

	/**
	 * Poll for this topic
	 *
	 * @return Relation
	 */
	public function poll()
	{
		return $this->hasOne('Poll');
	}

	/**
	 * Permalink
	 *
	 * @return string
	 */
	public function getUrlAttribute()
	{
		$url = preg_replace('/[^A-Za-z0-9]/', '_', $this->title);
		$url = trim(preg_replace('/(_)+/', '_', $url), '_');
		return '/topics/' . $this->id . '/' . $url;
	}

	/**
	 * Get the image based on type & unread status
	 *
	 * @return string
	 */
	public function getImageAttribute()
	{
		if ($this->type == 2) {
			$image = 'topic_announce';
		} else if ($this->type == 1) {
			$image = 'topic_sticky';
		} else if ($this->is_locked == 1) {
			$image = 'topic_locked';
		} else {
			$image = 'topic';

			if ($this->pages > 1) {
				$image .= '_hot';
			}
		}

		if ($this->unread_post->id) {
			$image .= '_unread';
		}

		return $image;
	}

	/**
	 * Alt text
	 *
	 * @return string
	 */
	public function getAltTextAttribute()
	{
		if ($this->type == 2) {
			return 'Announcement';
		} else if ($this->type == 1) {
			return 'Sticky';
		} else if ($this->is_locked == 1) {
			return 'Locked';
		} else if ($this->unread_post->id) {
			return 'New posts';
		} else {
			return 'No new posts';
		}
	}

	/**
	 * Get a truncated title
	 *
	 * @return string
	 */
	public function getShortTitleAttribute()
	{
		if (strlen($this->title) > 50) {
			return substr($this->title, 0, 45) . '...';
		}
		return $this->title;
	}

	/**
	 * Get first unread post
	 *
	 * @return Post
	 */
	public function getUnreadPostAttribute()
	{
		global $me;

		if (! $me->id) {
			return null;
		}

		$session = SessionTopic::where('user_id', '=', $me->id)
			->where('topic_id', '=', $this->id)
			->first();

		if ($session->session_post) {
			return Post::find($session->session_post);
		}

		return null;
	}

	/**
	 * Get most recent post
	 *
	 * @return Post
	 */
	public function getLatestPostAttribute()
	{
		return Post::where('topic_id', '=', $this->id)
			->orderBy('created_at', 'desc')
			->first();
	}

	/**
	 * Check if it has attachments
	 *
	 * @return bool
	 */
	public function getHasAttachmentsAttribute()
	{
		$total = Attachment::leftJoin('posts', 'attachments.post_id', '=', 'posts.id')
			->where('posts.topic_id', '=', $this->id)
			->count();

		return ($total > 0 ? true : false);
	}

	/**
	 * Check if it has a poll
	 *
	 * @return bool
	 */
	public function getHasPollAttribute()
	{
		return ($this->poll->id ? true : false);
	}

	/**
	 * Get the number of pages
	 *
	 * @return int
	 */
	public function getPagesAttribute()
	{
		return ceil(($this->replies + 1) / 25);
	}

	/**
	 * Delete this topic
	 * This is automatically triggered from a Post::delete if the post is the last in its topic
	 * @todo soft delete
	 */
	public function delete($recursive = true)
	{
		foreach ($this->subscriptions as $subscription) {
			$subscription->delete();
		}
		foreach ($this->sessions as $session) {
			$session->delete();
		}
		$this->forum->decrement('total_topics');

		// If this is triggered from a Post::delete, do not delete posts!
		if ($recursive) {
			$this->forum->decrement('total_posts', count($this->posts));

			foreach ($this->posts as $post) {
				$post->delete();
			}
		}
		// $this->poll->delete();
		// This should trigger deleting: poll_options, poll_votes

		return parent::delete();
	}

}

