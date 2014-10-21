<?php

class PostReport extends Eloquent
{

	protected $guarded = array('id');
	protected $appends = array(
		'reason',
	);

	/**
	 * Post that was reported
	 *
	 * @return Relation
	 */
	public function post()
	{
		return $this->belongsTo('Post');
	}

	/**
	 * User who reported the post
	 *
	 * @return Relation
	 */
	public function user()
	{
		return $this->belongsTo('User');
	}

	/**
	 * Get a human-friendly reason
	 */
	public function getReasonAttribute()
	{
		$reasons = array(
			'',
			'Useless / Offtopic',
			'Advertisement',
			'Personal Attack',
			'Obscene Language',
			'Copyright Infringement',
			'Other'
		);

		return $reasons[$this->reason];
	}

}

