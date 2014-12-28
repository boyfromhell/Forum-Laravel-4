<?php namespace Parangi;

use User;

define('MATCH_ANY', 0);
define('MATCH_ALL', 1);
define('WHERE_TITLES', 0);
define('WHERE_TEXT', 1);
define('WHERE_BOTH', 2);
define('SHOW_POSTS', 0);
define('SHOW_TOPICS', 1);
define('SINCE_LAST_VISIT', 1);

// Messages only
define('SHOW_MESSAGES', 0);
define('SHOW_THREADS', 1);
define('FOLDER_ANY', 0);
define('FOLDER_INBOX', 1);
define('FOLDER_SENT', 2);
define('FOLDER_ARCHIVED', 3);

class Query extends BaseModel
{

	protected $table = 'queries';
	protected $guarded = array('id');
	protected $appends = array(
		'url',
		'results_url',
	);

	/**
	 * Link to edit this query
	 *
	 * @return string
	 */
	public function getUrlAttribute()
	{
		if ($this->type == 'forum') {
			return '/search' . ($this->id ? '/'.$this->id : '');
		}
		else {
			return '/search-messages' . ($this->id ? '/'.$this->id : '');
		}
	}

	/**
	 * Link to view query results
	 *
	 * @return string
	 */
	public function getResultsUrlAttribute()
	{
		return '/results/' . $this->id;
	}

	/**
	 * Get a text description of this query
	 */
	public function getDescription($start, $num_results)
	{
		$show_text = array(
			'forum' => array(
				SHOW_POSTS  => 'posts ',
				SHOW_TOPICS => 'topics ',
			),
			'messages' => array(
				SHOW_MESSAGES => 'messages ',
				SHOW_THREADS  => 'threads ',
			),
		);
		$where_text = array(
			WHERE_TITLES => 'titles',
			WHERE_TEXT   => 'message texts',
			WHERE_BOTH   => 'titles or texts',
		);
		$since_text = array(
			0        => '',
			SINCE_LAST_VISIT => ' since your last visit',
			86400    => ' since yesterday',
			604800   => ' in the last week',
			1209600  => ' in the last 2 weeks',
			2628000  => ' in the last month',
			7884000  => ' in the last 3 months',
			15768000 => ' in the last 6 months',
			31536000 => ' in the last year',
		);
		$folder_text = array(
			FOLDER_ANY      => 'any folder',
			FOLDER_INBOX    => 'your Inbox',
			FOLDER_SENT     => 'your Sent folder',
			FOLDER_ARCHIVED => 'your Archived folder',
		);

		// @todo use attribute
		$keywords = str_replace([' ', ';'], ',', $this->keywords);
        $keywords = explode(',', $keywords);
        $total = count($keywords);

		// @todo use attribute
		if ($this->author) {
            $author = User::where('name', '=', $this->author)->first();
        }

		$html = 'Searched for ' . $show_text[$this->type][$this->show];
		if ($author->id) {
			$html .= " by <a href=\"{$author->url}\">" . e($author->name) . '</a> ';
		}
		if ($total > 0) {
			$html .= 'with ' . $where_text[$this->where] . ' matching ';

			for ($i=0; $i<$total; $i++) {
				$html .= '"<i>' . $keywords[$i] . '</i>"';
				if ($i < $total-1 && $total > 2) {
					$html .= ', ';
				}
				if ($i == $total-2) {
					if ($this->match == MATCH_ANY) {
						$html .= 'or ';
					} else {
						$html .= 'and ';
					}
				}
			}
			$html .= '<br>';
		}

		$html .= 'in ';

		if ($this->type == 'forum') {
			$total_forums = count($this->forum_array);

			if ($total_forums == 0 || $this->forum_array[0] == 0) {
				$html .= 'all forums';
			} else {
				$forums = Forum::whereIn('id', $this->forum_array)->get();

				for ($i=0; $i<$total_forums; $i++) {
					$html .= "<a href=\"{$forums[$i]->url}\" style=\"color:#000\"><u>" . e($forums[$i]->name) . '</u></a>';
					if ($i < $total_forums-1 && $total_forums > 2) {
						$html .= ', ';
					}
					if ($i == $total_forums-2) {
						$html .= 'and ';
					}
				}
			}
		} else if ($this->type == 'messages') {
			$html .= $folder_text[$this->folder];
		}

		$html .= $since_text[$this->since] . '<br>';

		$showing = 20;
		if ($num_results < 20) {
			$showing = $num_results;
		}
		if ($num_results) {
			$html .= '<br>Showing ';
			$begin = $start+1;
			$end = $start+20;
			if ($end > $num_results) {
				$end = $num_results;
			}

			$html .= "results <b>{$begin} - {$end}</b> ";
			$html .= "out of <b>{$num_results}</b> total";
		}
		$html .= "<br><a href=\"{$this->url}\">Modify</a> your search";

		return $html;
	}

}

