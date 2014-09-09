<?php

class Post extends Earlybird\Foundry
{

	protected $guarded = array('id');
	protected $appends = array(
		'url',
	);

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

}

