<?php namespace Parangi;

use Config;

class Avatar extends BaseModel
{

	protected $guarded = array('id');

	/**
	 * User who uploaded this avatar
	 *
	 * @return Relation
	 */
	public function user()
	{
		return $this->belongsTo('User');
	}

	/**
	 * Delete this avatar
	 */
	public function delete()
	{
		// if ($this->user_id == $me->id) {
		Helpers::delete_from_s3('images/avatars/' . $this->file);

		return parent::delete();
	}

	/**
	 * Move file from local storage to S3
	 *
	 * @return bool
	 */
	public function push_to_s3()
	{
		return Helpers::push_to_s3(
			'uploads/avatars/' . $this->file,
			'images/avatars/' . $this->file,
			true
		);
	}

}

