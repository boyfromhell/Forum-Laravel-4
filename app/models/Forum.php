<?php namespace Parangi;

class Forum extends BaseModel
{
    use \Earlybird\Foundry;

	protected $guarded = array('id');
	protected $appends = array(
		'url',
		'parents',

		'is_unread',
		'alt_text',

		'latest_topic',
	);

	/**
	 * Parent forum
	 *
	 * @return Relation
	 */
	public function parent()
	{
		return $this->belongsTo('Parangi\Forum', 'parent_id');
	}

	/**
	 * Child forums
	 *
	 * @return Relation
	 */
	public function children()
	{
		global $me;

		return $this->hasMany('Parangi\Forum', 'parent_id')
			->where('view', '<=', $me->access)
			->orderBy('order', 'asc');
	}

	/**
	 * Category this forum belongs to
	 *
	 * @return Relation
	 */
	public function category()
	{
		return $this->belongsTo('Parangi\Category');
	}

	/**
	 * Topics in this forum
	 *
	 * @return Relation
	 */
	public function topics()
	{
		return $this->hasMany('Parangi\Topic')
			->orderBy('type', 'desc')
			->orderBy('posted_at', 'desc');
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

		while ($child->parent_id) {
			$parent = Forum::find($child->parent_id);
			$parents[] = $parent;
			$child = $parent;
		}
		return array_reverse($parents);
	}

	/**
	 * Most recent topic
	 *
	 * @return Topic
	 */
	public function getLatestTopicAttribute()
	{
		return Topic::where('forum_id', '=', $this->id)
			->orderBy('posted_at', 'desc')
			->first();
	}

	/**
	 * Check if there are any unread topics in this forum
	 *
	 * @return bool
	 */
	public function getIsUnreadAttribute()
	{
		global $me;

		if (! $me->id) {
			return false;
		}

		$unread = SessionTopic::where('user_id', '=', $me->id)
			->where('forum_id', '=', $this->id)
			->count();

		return ($unread > 0 ? true : false);
	}

	/**
	 * Alt text for this forum
	 *
	 * @return string
	 */
	public function getAltTextAttribute()
	{
		return ($this->is_unread ? 'New posts' : 'No new posts');
	}

	/**
	 * Check permission
	 *
	 * @param  string  $type  view or read
	 * @return bool
	 */
	public function check_permission($type)
	{
		global $me;

		$group_type = 'group_' . $type;

		$allowed_groups = explode(',', $this->$group_type);
		$my_groups = array_pluck($me->groups, 'id');

		if ($me->access >= $this->$type) {
			return true;
		} else if (array_intersect($allowed_groups, $my_groups)) {
			return true;
		}

		return false;
	}

}

