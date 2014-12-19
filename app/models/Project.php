<?php namespace Parangi;

class Project extends BaseModel
{
    use \Earlybird\Foundry;

	protected $guarded = array('id');
	protected $appends = array(
		'url',

		'section',

		'total_files',
		'total_downloads',
	);
	public $timestamps = false;

	/**
	 * User who owns this project
	 *
	 * @return Relation
	 */
	public function user()
	{
		return $this->belongsTo('User');
	}

	/**
	 * Downloads which belong to this project
	 *
	 * @return Relation
	 */
	public function downloads()
	{
		return $this->hasMany('Parangi\Download')
			->orderBy('created_at', 'desc');
	}

	/**
	 * Permalink
	 *
	 * @return string
	 */
	public function getUrlAttribute()
	{
		$url = preg_replace('/[^A-Za-z0-9]/', '_', $this->name);
		$url = trim(preg_replace('/(_)+/', '_', $url), '_');
		return '/projects/' . $this->id . '/' . $url;
	}

	/**
	 * Section this project is part of
	 *
	 * @return string
	 */
	public function getSectionAttribute()
	{
		$sections = ['variants', 'official', 'other'];

		return $sections[$this->category];
	}

	/**
	 * Get the total number of files
	 *
	 * @return int
	 */
	public function getTotalFilesAttribute()
	{
		return count($this->downloads);
	}

	/**
	 * Get the total number of downloads
	 *
	 * @return int	
	 */
	public function getTotalDownloadsAttribute()
	{
		return array_sum(array_pluck($this->downloads, 'views'));
	}

}

