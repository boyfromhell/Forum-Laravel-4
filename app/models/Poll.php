<?php

class Poll extends Eloquent
{
    use Earlybird\Foundry;

	protected $appends = array(
		'type',
		'total_votes',
		'total_picks',
		'max_percent',
	);

	/**
	 * Topic
	 *
	 * @return Relation
	 */
	public function topic()
	{
		return $this->belongsTo('Topic');
	}

	/**
	 * Options to choose from
	 *
	 * @return Relation
	 */
	public function options()
	{
		return $this->hasMany('PollOption')
			->orderBy('weight', 'asc');
	}

	/**
	 * Votes on this poll. Mostly used for totaling
	 *
	 * @return Relation
	 */
	public function votes()
	{
		return $this->hasMany('PollVote');
	}

	/**
	 * Type of input for options
	 *
	 * @return string
	 */
	public function getTypeAttribute()
	{
		return ($this->max_options <= 1 ? 'radio' : 'checkbox');
	}

	/**
	 * Get the total number of users who have voted
	 *
	 * @return int
	 */
	public function getTotalVotesAttribute()
	{
		return $this->votes()->count();
	}

	/**
	 * Get the total number of options that have been chosen
	 *
	 * @return int
	 */
	public function getTotalPicksAttribute()
	{
		$total = 0;

		foreach ($this->options as $option) {
			$total += $option->total_votes;
		}

		return $total;
	}

	/**
	 * Get the percent of the most popular option
	 *
	 * @return int
	 */
	public function getMaxPercentAttribute()
	{
		$max = 1;

		foreach ($this->options as $option) {
			if ($option->percent > $max) {
				$max = $option->percent;
			}
		}

		return $max;
	}

}

