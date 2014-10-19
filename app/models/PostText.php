<?php

class PostText extends Earlybird\Foundry
{

	protected $table = 'posts_text';
	protected $primaryKey = 'post_id';
	protected $guarded = array();
	public $timestamps = false;

}
