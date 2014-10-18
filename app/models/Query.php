<?php

class Query extends Eloquent
{

	protected $table = 'queries';
	protected $guarded = array('id');
	protected $appends = array(
		'url',
	);

	/**
	 * Link to edit this query
	 *
	 * @return string
	 */
	public function getUrlAttribute()
	{
		return '/search'.( $this->id ? '/'.$this->id : '' );
	}

}
