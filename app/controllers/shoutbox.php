<?php
class ShoutModel extends Model_W
{
	protected static $_table = 'shoutbox';
	protected static $_instance = null;
}

class Shout extends Controller_W 
{
	protected static $_table = 'shoutbox';

	public function __construct( $pri = null, $data = null )
	{
		parent::__construct($pri, $data);
	}

	public function get_date( $format = null )
	{
		return $format ? date($format, $this->time) : $this->time;
	}
}
