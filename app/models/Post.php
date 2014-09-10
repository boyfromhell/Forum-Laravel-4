<?php

class Post extends Earlybird\Foundry
{

	protected $guarded = array('id');
	protected $appends = array(
		'url',

		'date',

		'subject',
		'text',
	);

	/**
	 * Post text storing extra data
	 *
	 * @return Relation
	 */
	public function postText()
	{
		return $this->hasOne('PostText', 'post_id');
	}

	/**
	 * Topic this belongs to
	 *
	 * @return Relation
	 */
	public function topic()
	{
		return $this->belongsTo('Topic');
	}

	/**
	 * User who posted
	 *
	 * @return Relation
	 */
	public function user()
	{
		return $this->belongsTo('User');
	}

	/**
	 * All attachments for this post
	 *
	 * @return Relation
	 */
	public function attachments()
	{
		return $this->hasMany('Attachment')
			->orderBy('date', 'asc');
	}

	/**
	 * Permalink
	 *
	 * @return string
	 */
	public function getUrlAttribute()
	{
		return '/posts/' . $this->id . '#' . $this->id;
	}

	/**
	 * Date, formatted and in my local timezone
	 *
	 * @return string
	 */
	public function getDateAttribute()
	{
		return Helpers::date_string($this->time, 2);
	}

	/**
	 * Get the subject from the PostText
	 *
	 * @return string
	 */
	public function getSubjectAttribute()
	{
		return $this->postText->post_subject;
	}

	/**
	 * Get the text from the PostText
	 *
	 * @return string
	 */
	public function getTextAttribute()
	{
		return $this->postText->post_text;
	}

}

