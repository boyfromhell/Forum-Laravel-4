<?php

class Topic extends Earlybird\Foundry
{

	// @todo
	public $timestamps = false;

	protected $appends = array(
		'url',

		'prefix',
		'short_title',

		'has_attachments',
		'has_poll',
	);

	public function forum()
	{
		return $this->belongsTo('Forum');
	}
	public function author()
	{
		return $this->belongsTo('User', 'poster');
	}
	public function lastUser()
	{
		return $this->belongsTo('User', 'last');
	}
	public function posts()
	{
		return $this->hasMany('Post')
			->orderBy('time', 'asc');
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
	 * Get the topic prefix based on type
	 *
	 * @return string
	 */
	public function getPrefixAttribute()
	{
		switch( $this->type ) {
			case 2:
				return 'Announcement: ';
				break;

			case 1:
				return 'Sticky: ';
				break;

			default:
				return '';
				break;
		}
	}

	/**
	 * Get a truncated title
	 *
	 * @return string
	 */
	public function getShortTitleAttribute()
	{
		if( strlen($this->title) > 50 ) {
			return substr($this->title, 0, 45) . '...';
		}
		return $this->title;
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

		return ( $total > 0 ? true : false );
	}

	/**
	 * Check if it has a poll
	 *
	 * @return bool
	 */
	public function getHasPollAttribute()
	{
		$poll = Poll::where('poll_topic', '=', $this->id)
			->first();

		return ( $poll->id ? true : false );
	}

}
