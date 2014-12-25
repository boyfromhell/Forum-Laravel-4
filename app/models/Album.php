<?php namespace Parangi;

class Album extends BaseModel
{
    use \Earlybird\Foundry;

	protected $guarded = array('id');
	protected $appends = array(
		'url',
		'parents',

		'total_photos',
		'total_albums',
	);

	/**
	 * Parent Album
	 *
	 * @return Relation
	 */
	public function parent()
	{
		return $this->belongsTo('Parangi\Album', 'parent_id');
	}

	/**
	 * All child albums
	 *
 	 * @return Relation
	 */
	public function children()
	{
		global $me;

		return $this->hasMany('Parangi\Album', 'parent_id')
			->where('permission_view', '<=', $me->access)
			->orderBy('name', 'asc');
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
		return $this->hasMany('Parangi\Photo')
			->orderBy('created_at', 'asc')
			->orderBy('id', 'asc');
	}

	/**
	 * Cover photo	
	 *
	 * @return Relation
	 */
	public function coverPhoto()
	{
		if ($this->cover_id) {
			return $this->belongsTo('Parangi\Photo', 'cover_id');
		}
		else
		{
			foreach ($this->children as $child) {
				return $child->coverPhoto();
			}
		}

		return $this->belongsTo('Parangi\Photo', 'cover_id');
	}

	/**
	 * Permalink
	 *
	 * @return string
	 */
	public function getUrlAttribute()
	{
		if ($this->id == 1) {
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

		while ($child->parent_id) {
			$parent = Album::find($child->parent_id);
			$parents[] = $parent;
			$child = $parent;
		}
		return array_reverse($parents);
	}

	/**
	 * Count how many photos there are
	 *
	 * @return int
	 */
	public function getTotalPhotosAttribute()	
	{
		return $this->photos()->count();
	}

	/**
	 * Count how many sub-albums there are
	 *
	 * @return int
	 */
	public function getTotalAlbumsAttribute()
	{
		return $this->children()->count();
	}

	/**
	 * Delete album
	 */
	public function delete()
	{
		foreach ($this->photos as $photo) {
			$photo->delete();
		}

		return parent::delete();
	}

	/**
	 * Check if the current user has permission to edit / upload
	 * @todo improve this
	 *
	 * @return bool
	 */
	public function check_permission()
	{
		global $me;

		if ($me->is_admin) {
			return true;
		} else if ($this->permission_upload == 0 && $this->user_id == $me->id) {
			return true;
		} else if ($this->permission_upload == 1 && $me->id) {
			return true;
		}

		return false;
	}

}

