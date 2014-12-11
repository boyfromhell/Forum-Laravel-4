<?php

class PostText extends Eloquent
{
    use Earlybird\Foundry;

	protected $table = 'posts_text';
	protected $primaryKey = 'post_id';
	protected $guarded = array();
	public $timestamps = false;

}

