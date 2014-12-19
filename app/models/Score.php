<?php namespace Parangi;

class Score extends Eloquent
{
    use \Earlybird\Foundry;

	protected $guarded = array('id');

	/**
	 * User who submitted the score
	 *
	 * @return Relation
	 */
	public function user()
	{
		return $this->belongsTo('User');
	}

}

