<?php

class Forum extends Earlybird\Foundry
{

	protected $appends = array(
		'url',
		'parents',
	);

	public function parent()
	{
		return $this->belongsTo('Forum', 'parent_id');
	}

	public function children()
	{
		return $this->hasMany('Forum', 'parent_id')
			->orderBy('order', 'asc');
	}
	public function category()
	{
		return $this->hasMany('Category', 'category');
	}
	public function topics()
	{
		return $this->hasMany('Topic')
			->orderBy('type', 'desc')
			->orderBy('last_date', 'desc');
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
		return '/forums/' . $this->id . '/' . $url;
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
			$parent = Forum::find($child->parent_id);
			$parents[] = $parent;
			$child = $parent;
		}
		return array_reverse($parents);
	}

	/**
	 * Check permission
	 *
	 * @param  string  $type  view or read
	 * @return bool
	 */
	public function check_permission( $type )
	{
		global $me;

		// @todo
		return true;

		$group_type = 'group_' . $type;

		$allowed_groups = explode(',', $this->$group_type);
		$my_groups = array_pluck($me->groups, 'id');

		if( $me->access >= $this->$type ) {
			return true;
		}
		else if( array_intersect($allowed_groups, $my_groups) ) {
			return true;
		}

		return false;
	}

}

