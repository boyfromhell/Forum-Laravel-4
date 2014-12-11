<?php

class Avatar extends Eloquent
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

		if (Config::get('services.aws.enabled')) {
			Helpers::delete_from_s3("images/avatars/".$this->file);
		}

		return parent::delete();
	}

	/**
	 * Move file from local storage to S3
	 *
	 * @return bool
	 */
	public function push_to_s3()
	{
		if (Helpers::push_to_s3("images/avatars/".$this->file, true)) {
			unlink(storage_path()."/uploads/avatars/".$this->file);
			return true;
		}
		return false;
	}

}

