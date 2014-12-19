<?php namespace Parangi;

class CustomData extends BaseModel
{
    use \Earlybird\Foundry;

	protected $table = 'custom_data';

	/**
	 * User who filled out these fields
	 *
	 * @return Relation
	 */
	public function user()
	{
		return $this->belongsTo('User');
	}

	/**
	 * Field this data belongs to
	 *
	 * @return Relation
	 */
	public function field()
	{
		return $this->belongsTo('Parangi\CustomField', 'field_id');
	}

}

