<?php
class QuoteModel extends Model_W
{
	protected static $_table = 'quotes';
	protected static $_instance = null;
}

class Quote extends Controller_W 
{
	protected static $_table = 'quotes';

	public function __construct( $pri = null, $data = null )
	{
		parent::__construct($pri, $data);
		
		$this->generate_url();
	}
	
	public function generate_url()
	{
		$this->url = '/forum/?quote=' . $this->id;
	}
}
