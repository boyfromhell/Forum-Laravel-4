<?php

class AdminMessage extends Earlybird\Foundry
{

	protected $guarded = array('id');
	protected $appends = array(
		'url',
	);

	/**
	 * User who created this message
	 *
	 * @return Relation
	 */
	public function user()
	{
		return $this->belongsTo('User');
	}

	/**
	 * Permalink to view this message
	 *
	 * @return string
	 */
	public function getUrlAttribute()
	{
		return '/admin/messages/'.$this->id;
	}

}
