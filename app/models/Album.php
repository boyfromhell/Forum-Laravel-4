<?php

class Album extends Earlybird\Foundry
{

	protected $guarded = array('id');
	protected $appends = array(
		'url',
		'parents',
	);

	/**
	 * Parent Album
	 *
	 * @return Relation
	 */
	public function parent()
	{
		return $this->belongsTo('Album', 'parent_id');
	}

	/**
	 * All child albums
	 *
 	 * @return Relation
	 */
	public function children()
	{
		return $this->hasMany('Album', 'parent_id');
	}

	/**
	 * User who created this album
	 *
	 * @return Relation
	 */
	public function user()
	{
		return $this->belongsTo('User');
	}

	/**
	 * All photos in this album
	 *
	 * @return Relation
	 */
	public function photos()
	{
		return $this->hasMany('Photo')
			->orderBy('date', 'asc')
			->orderBy('id', 'asc');
	}

	/**
	 * Permalink
	 *
	 * @return string
	 */
	public function getUrlAttribute()
	{
		if( $this->id == 1 ) {
			return '/albums/';
		}

		$url = preg_replace('/[^A-Za-z0-9]/', '_', $this->name);
		$url = trim(preg_replace('/(_)+/', '_', $url), '_');
		return '/albums/' . $this->id . '/' . $url;
	}

	/**
	 * Get parent breadcrumbs
	 *
	 * @return array
	 */
	public function getParentsAttribute()
	{
		$parents = array();
		$child = $this;

		while( $child->parent_id )
		{
			$parent = Album::find($child->parent_id);
			$parents[] = $parent;
			$child = $parent;
		}
		return array_reverse($parents);
	}

}

