<?php namespace Parangi;

class EmailQueue extends BaseModel
{

	protected $table = 'email_queue';
	protected $guarded = array('id');
	public $timestamps = false;

}
