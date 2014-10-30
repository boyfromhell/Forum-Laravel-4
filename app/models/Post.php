<?php

class Post extends Earlybird\Foundry
{

	protected $guarded = array('id');
	protected $appends = array(
		'url',

		'date',

		'subject',
		'text',
	);

	/**
	 * Post text storing extra data
	 *
	 * @return Relation
	 */
	public function postText()
	{
		return $this->hasOne('PostText', 'post_id');
	}

	/**
	 * Topic this belongs to
	 *
	 * @return Relation
	 */
	public function topic()
	{
		return $this->belongsTo('Topic');
	}

	/**
	 * User who posted
	 *
	 * @return Relation
	 */
	public function user()
	{
		return $this->belongsTo('User');
	}

	/**
	 * All attachments for this post
	 *
	 * @return Relation
	 */
	public function attachments()
	{
		return $this->hasMany('Attachment')
			->orderBy('filetype', 'desc')
			->orderBy('created_at', 'asc');
	}

	/**
	 * Permalink
	 *
	 * @return string
	 */
	public function getUrlAttribute()
	{
		return '/posts/' . $this->id . '#' . $this->id;
	}

	/**
	 * Date, formatted and in my local timezone
	 *
	 * @return string
	 */
	public function getDateAttribute()
	{
		return Helpers::date_string(strtotime($this->created_at), 2);
	}

	/**
	 * Get the subject from the PostText
	 *
	 * @return string
	 */
	public function getSubjectAttribute()
	{
		return $this->postText->post_subject;
	}

	/**
	 * Get the text from the PostText
	 *
	 * @return string
	 */
	public function getTextAttribute()
	{
		return $this->postText->post_text;
	}

	/**
     * Delete this post
     *
	 * @todo soft delete
     * @return result array parameters with info about where I am redirecting you to
     */
    public function delete()
    {
        // Decrement post counters
		if( $this->topic->replies > 0 ) {
	        $this->topic->decrement('replies');
		}
        $this->topic->forum->decrement('total_posts');
        $this->user->decrement('total_posts');

        $this->attachments()->update([
            'post_id' => NULL,
            'hash' => 'deleted'
        ]);

        // Delete topic if this was the only post
		// Checking before this post is deleted, so it should be 1 not 0
        if( $this->topic->posts()->count() == 1 ) {
            $forum = $this->topic->forum;
			$this->topic->delete($recursive = false);

            $redirect = $forum->url;
        }
		// Otherwise check topic sessions
        else {
            $redirect = $this->topic->url;

			$new_post = Post::where('topic_id', '=', $this->topic_id)
				->where('id', '>', $this->id)
				->orderBy('id', 'asc')
				->first();

            // Update existing sessions with newer post ID
            if( $new_post->id ) {
				SessionTopic::where('session_post', '=', $this->id)
					->update([
						'session_post' => $new_post->id
					]);
            }
            else {
				SessionTopic::where('session_post', '=', $this->id)
					->delete();
            }
        }

        $this->postText->delete();
        parent::delete();

        return $redirect;
    }

	/**
	 * Posts from X days ago
	 *
	 * @param  int  $days
	 * @return Query
	 */
	public function scopeDaysAgo($query, $days)
	{
		return $query->where('created_at', '>=', DB::raw('DATE_SUB(NOW(), INTERVAL '.$days.' DAY)'));
	}

}
