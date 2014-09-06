<?php
class ApplicationModel extends Model_W
{
	protected static $_table = 'applications';
	protected static $_instance = null;
}

class Application extends Controller_W 
{
	protected static $_table = 'applications';

	public function __construct( $pri = null, $data = null )
	{
		parent::__construct($pri, $data);
	}
}
