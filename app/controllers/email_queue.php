<?php
class EmailQueueModel extends Model_W
{
	protected static $_table = 'email_queue';
	protected static $_instance = null;
}

class EmailQueue extends Controller_W 
{
	protected static $_table = 'email_queue';

	public function __construct( $pri = null, $data = null )
	{
		parent::__construct($pri, $data);
	}
}
