<?php namespace Parangi;

class Event extends BaseModel
{
    use \Earlybird\Foundry;

	protected $appends = array(
		'url',
	);

	/**
	 * Permalink
	 *
	 * @return string
	 */
	public function getUrlAttribute()
	{
		$url = preg_replace('/[^A-Za-z0-9]/', '_', $this->name);
		$url = trim(preg_replace('/(_)+/', '_', $url), '_');
		return '/events/' . $this->id . '/' . $url;
	}

}

