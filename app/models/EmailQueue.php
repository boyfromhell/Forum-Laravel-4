<?php namespace Parangi;

class EmailQueue extends Eloquent
{

	protected $table = 'email_queue';
	protected $guarded = array('id');
	public $timestamps = false;

}
