<?php namespace Parangi;

class Quote extends Eloquent
{
    use \Earlybird\Foundry;

	protected $appends = array(
		'url',
	);

	/**
	 * User who the quote is attributed to
	 *
	 * @return Relation
	 */
	public function user()
	{
		return $this->belongsTo('User');
	}

	/**
	 * Permalink
	 *
	 * @return string
	 */
	public function getUrlAttribute()
	{
		return '/forum/?quote=' . $this->id;
	}

}

