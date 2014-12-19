<?php namespace Parangi;

class Screenname extends Eloquent
{
    use \Earlybird\Foundry;

	protected $appends = array(
		'name',
		'image',
	);

	/**
	 * User who owns this screenname
	 *
	 * @return Relation
	 */
	public function user()
	{
		return $this->belongsTo('User');
	}

	/**
	 * Convert enum data to formatted Protocol name
	 *
	 * @return string
	 */
	public function getNameAttribute()
	{
		$protocols = [
			'aim'   => 'AIM',
			'yahoo' => 'Yahoo!',
			'msn'   => 'MSN',
			'icq'   => 'ICQ'
		];

		return $protocols[$this->protocol];
	}

	/**
	 * Fetch the appropriate online/offline image
	 *
	 * @return string
	 */
	public function getImageAttribute()
	{
		$msn_domain = Config::get('app.domain').'/images/';
		$domain = 'http://'.$msn_domain;

		switch ($this->protocol) {
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

