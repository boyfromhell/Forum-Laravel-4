<?php
class ScoreModel extends Model_W
{
	protected static $_table = 'scores';
	protected static $_instance = null;
}

class Score extends Controller_W 
{
	protected static $_table = 'scores';

	public function __construct( $pri = null, $data = null )
	{
		parent::__construct($pri, $data);
	}
}
