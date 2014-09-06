<?php
class ScreennameModel extends Model_W
{
	protected static $_table = 'screennames';
	protected static $_instance = null;
}

class Screenname extends Controller_W 
{
	protected static $_table = 'screennames';

	public function __construct( $pri = null, $data = null )
	{
		parent::__construct($pri, $data);
	}

	/**
	 * Convert enum data to formatted Protocol name
	 */
	function get_protocol_name()
	{
		$protocols = array(
			'aim' => 'AIM',
			'yahoo' => 'Yahoo!',
			'msn' => 'MSN',
			'icq' => 'ICQ'
		);
		return $protocols[$this->protocol];
	}

	/**
	 * Fetch the appropriate online/offline image
	 */
	function get_image()
	{
		global $_CONFIG;

		$msn_domain = $_CONFIG['domain'] . '/images/';
		$domain = 'http://' . $msn_domain;

		switch( $this->protocol )
		{
			case 'aim':
				return "http://big.oscar.aol.com/{$this->screenname}?on_url={$domain}aim_online.png&off_url={$domain}aim_offline.png";
				break;

			case 'yahoo':
				return "http://opi.yahoo.com/online?u={$this->screenname}&m=g&t=0&l=us&zzz=.gif";
				break;

			case 'msn':
				return "http://www.funnyweb.dk:8080/msn/{$this->screenname}/onurl={$msn_domain}msn_online.png/".
					"offurl={$msn_domain}msn_offline.png/unknownurl={$msn_domain}msn_offline.png";
				break;

			case 'icq':
				return "http://web.icq.com/whitepages/online?icq={$this->screenname}&img=26";
				break;
		}
	}
}
